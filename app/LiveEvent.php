<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveEvent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "live_events";
    protected $fillable = array('match_rball_id', 'match_gsm_id', 'match_goallive_id', 'game_half', 'game_time', 'score_home', 'score_away',
                                'event_number','event_id', 'event_name', 'state', 'start_time', 'end_time', 'duration', 'status');


    protected function insertEvent($jsonData, $eventState, $eventName, $eventStatus, $goalliveid)
    {
        $event = new LiveEvent();

        $eventCheck = $this->where('match_rball_id', $jsonData->{'MLU'}->{'ID'})->where('event_number', $jsonData->{'MLU'}->{'EN'})->first();
        if ($eventCheck) return "duplicated";

        // Statment to get the game half.
        if( isset( $jsonData->{'MLU'}->{'PSID'} ) )
        {
            switch($jsonData->{'MLU'}->{'PSID'})
            {
                case "0":
                    $eventHalf = "1H";
                    break;
                case "2":
                    $eventHalf = "HT";
                    break;
                case "3":
                    $eventHalf = "2H";
                    break;
                case "4":
                    $eventHalf = "FT";
                    break;
            }
        }
        // Statment to get the game time (Format mm:ss)
        if( isset( $jsonData->{'MLU'}->{'CPT'} ) )
        {
            $eventGameTime = floor($jsonData->{'MLU'}->{'CPT'} / 60000) . ":" . floor(($jsonData->{'MLU'}->{'CPT'} / 1000) % 60);
        }

        $event->match_rball_id = ( isset( $jsonData->{'MLU'}->{'ID'} ) ) ? $jsonData->{'MLU'}->{'ID'} : 'NULL';
        $event->match_gsm_id = ( isset( $jsonData->{'MLU'}->{'GSMID'} ) ) ? $jsonData->{'MLU'}->{'GSMID'} : 'NULL';
        $event->match_goallive_id = ( isset( $goalliveid ) ) ? $goalliveid : 'NULL';
        $event->game_half = ( isset( $jsonData->{'MLU'}->{'PSID'} ) ) ? $jsonData->{'MLU'}->{'PSID'} : 'NULL';
        $event->game_time = ( isset( $eventGameTime ) ) ? $eventGameTime : 'NULL';
        $event->score_home = ( isset( $jsonData->{'MLU'}->{'SCH'} ) ) ? $jsonData->{'MLU'}->{'SCH'} : '0';
        $event->score_away = ( isset( $jsonData->{'MLU'}->{'SCA'} ) ) ? $jsonData->{'MLU'}->{'SCA'} : '0';
        $event->event_number = ( isset( $jsonData->{'MLU'}->{'EN'} ) ) ? $jsonData->{'MLU'}->{'EN'} : 'NULL';
        $event->event_id = ( isset( $jsonData->{'MLU'}->{'EID'} ) ) ? $jsonData->{'MLU'}->{'EID'} : 'NULL';
        $event->event_name = ( isset( $eventName ) ) ? $eventName : 'NULL';
        $event->state = ( isset( $eventState ) ) ? $eventState : 'NULL';
        $event->start_time = ( isset( $jsonData->{'MLU'}->{'CPT'} ) ) ? $jsonData->{'MLU'}->{'CPT'} : 'NULL';
        $event->status = ( isset( $eventStatus ) ) ? $eventStatus : 'NULL';
        $event->save();
    }

    protected function updateEvent($jsonData)
    {
        $event = new LiveEvent();

        // Get Old Event Time
        $eventTimeCheck = $this->where('match_rball_id', $jsonData->{'MLU'}->{'ID'})->where('event_number', '!=', $jsonData->{'MLU'}->{'EN'})->orderBy('created_at', 'DESC')->take(1)->first();

        // Define Duration
        $eventDuration = $jsonData->{'MLU'}->{'CPT'} - $eventTimeCheck->{'start_time'};

        $eventEndTime = $jsonData->{'MLU'}->{'CPT'};
        $eventCheck = $this->where('match_rball_id', $jsonData->{'MLU'}->{'ID'})->where('event_number', '!=', $jsonData->{'MLU'}->{'EN'})->orderBy('created_at', 'DESC')->take(1)->update(['end_time' => $eventEndTime,'duration' => $eventDuration]);

    }

    protected function getInPlayTotalTime($fixtureId)
    {
        $event = new LiveEvent();

        $inPlayTotalTime = $this->where('match_goallive_id', $fixtureId)->where('status', 'InPlay')->sum('duration');
        $inPlayTotalTimeSeconds = floor($inPlayTotalTime / 1000);
        return $inPlayTotalTimeSeconds;
    }

    protected function getTrackedTotalTime($fixtureId)
    {
        $event = new LiveEvent();

        // Get Old Event Time
        $trackedTotalTime = $this->where('match_goallive_id', $fixtureId)->where('status', '!=', 'HalfTime')->sum('duration');
        $trackedTotalTimeMinutes = floor($trackedTotalTime / 60000);
        return $trackedTotalTimeMinutes;
    }

    protected function getInPlayTime($fixtureId,$stateType)
    {
        $event = new LiveEvent();

        // Get Old Event Time
        $inPlayTime = $this->where('match_goallive_id', $fixtureId)->where('status', 'InPlay')->where('state', $stateType)->sum('duration');
        $inPlayTimeSeconds = floor($inPlayTime / 1000);
        return $inPlayTimeSeconds;
    }

    /*--------------------------------------------- Modules For After Last Goal --------------------------------------*/

    protected function getInPlayTotalTimeAfterLastGoal($fixtureId)
    {
        $event = new LiveEvent();

        // Get Last Goal Event Number
        $eventNumberLastGoal = $this->where('match_goallive_id', $fixtureId)->where('event_name', 'LIKE', '%Goal Away%')->orWhere('event_name', 'LIKE', '%Goal Home%')->orderBy('created_at', 'DESC')->take(1)->first();
        $eventNumberLastGoalEventNumber = (isset($eventNumberLastGoal->{'event_number'}) && $eventNumberLastGoal->{'event_number'}  >= 0) ? $eventNumberLastGoal->{'event_number'} : '1' ;

        $inPlayTotalTimeAfterLastGoal = $this->where('match_goallive_id', $fixtureId)->where('status', 'InPlay')->where('event_number', '>', $eventNumberLastGoalEventNumber)->sum('duration');
        $inPlayTotalTimeAfterLastGoalSeconds = floor($inPlayTotalTimeAfterLastGoal / 1000);

        return $inPlayTotalTimeAfterLastGoalSeconds;

    }

    protected function getTrackedTotalTimeAfterLastGoal($fixtureId)
    {
        $event = new LiveEvent();

        // Get Last Goal Event Number
        $eventNumberLastGoal = $this->where('match_goallive_id', $fixtureId)->where('event_name', 'LIKE', '%Goal Away%')->orWhere('event_name', 'LIKE', '%Goal Home%')->orderBy('created_at', 'DESC')->take(1)->first();
        $eventNumberLastGoalEventNumber = (isset($eventNumberLastGoal->{'event_number'}) && $eventNumberLastGoal->{'event_number'}  >= 0) ? $eventNumberLastGoal->{'event_number'} : '1' ;

        $trackedTotalTimeAfterLastGoal = $this->where('match_goallive_id', $fixtureId)->where('event_number', '>', $eventNumberLastGoalEventNumber)->where('status', '!=', 'HalfTime')->sum('duration');
        $trackedTotalTimeAfterLastGoalMinutes = floor($trackedTotalTimeAfterLastGoal / 60000);
        return $trackedTotalTimeAfterLastGoalMinutes;
    }

    protected function getInPlayTimeAfterLastGoal($fixtureId,$stateType)
    {
        $event = new LiveEvent();

        // Get Last Goal Event Number
        $eventNumberLastGoal = $this->where('match_goallive_id', $fixtureId)->where('event_name', 'LIKE', '%Goal Away%')->orWhere('event_name', 'LIKE', '%Goal Home%')->orderBy('created_at', 'DESC')->take(1)->first();
        $eventNumberLastGoalEventNumber = (isset($eventNumberLastGoal->{'event_number'}) && $eventNumberLastGoal->{'event_number'}  >= 0) ? $eventNumberLastGoal->{'event_number'} : '1' ;

        // Get Old Event Time
        $inPlayTimeAfterLastGoal = $this->where('match_goallive_id', $fixtureId)->where('status', 'InPlay')->where('state', $stateType)->where('event_number', '>', $eventNumberLastGoalEventNumber)->sum('duration');
        $inPlayTimeAfterLastGoalSeconds = floor($inPlayTimeAfterLastGoal / 1000);
        return $inPlayTimeAfterLastGoalSeconds;

    }

    /*--------------------------------------------- Modules For Second Half --------------------------------------*/

    protected function getInPlayTotalTimeSecondHalf($fixtureId)
    {
        $event = new LiveEvent();

        // Get Last Goal Event Number
        $eventNumberStartRT2 = $this->where('match_goallive_id', $fixtureId)->where('event_name', 'LIKE', '%Start RT2%')->orderBy('created_at', 'DESC')->take(1)->first();
        $eventNumberStartRT2EventNumber = (isset($eventNumberStartRT2->{'event_number'}) && $eventNumberStartRT2->{'event_number'}  >= 0) ? $eventNumberStartRT2->{'event_number'} : '1' ;

        $inPlayTotalTimeSecondHalf = $this->where('match_goallive_id', $fixtureId)->where('status', 'InPlay')->where('event_number', '>', $eventNumberStartRT2EventNumber)->sum('duration');
        $inPlayTotalTimeSecondHalfSeconds = floor($inPlayTotalTimeSecondHalf / 1000);

        return $inPlayTotalTimeSecondHalfSeconds;

    }

    protected function getTrackedTotalTimeSecondHalf($fixtureId)
    {
        $event = new LiveEvent();

        // Get Last Goal Event Number
        $eventNumberStartRT2 = $this->where('match_goallive_id', $fixtureId)->where('event_name', 'LIKE', '%Start RT2%')->orderBy('created_at', 'DESC')->take(1)->first();
        $eventNumberStartRT2EventNumber = (isset($eventNumberStartRT2->{'event_number'}) && $eventNumberStartRT2->{'event_number'}  >= 0) ? $eventNumberStartRT2->{'event_number'} : '1' ;

        $trackedTotalTimeSecondHalf = $this->where('match_goallive_id', $fixtureId)->where('event_number', '>', $eventNumberStartRT2EventNumber)->where('status', '!=', 'HalfTime')->sum('duration');
        $trackedTotalTimeSecondHalfMinutes = floor($trackedTotalTimeSecondHalf / 60000);
        return $trackedTotalTimeSecondHalfMinutes;
    }

    protected function getInPlayTimeSecondHalf($fixtureId,$stateType)
    {
        $event = new LiveEvent();

        // Get Last Goal Event Number
        $eventNumberStartRT2 = $this->where('match_goallive_id', $fixtureId)->where('event_name', 'LIKE', '%Start RT2%')->orderBy('created_at', 'DESC')->take(1)->first();
        $eventNumberStartRT2EventNumber = (isset($eventNumberStartRT2->{'event_number'}) && $eventNumberStartRT2->{'event_number'}  >= 0) ? $eventNumberStartRT2->{'event_number'} : '1' ;

        // Get Old Event Time
        $inPlayTimeSecondHalf = $this->where('match_goallive_id', $fixtureId)->where('status', 'InPlay')->where('state', $stateType)->where('event_number', '>', $eventNumberStartRT2EventNumber)->sum('duration');
        $inPlayTimeSecondHalfSeconds = floor($inPlayTimeAfterLastGoal / 1000);
        return $inPlayTimeSecondHalfSeconds;

    }

    }
