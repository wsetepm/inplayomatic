<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\LiveEvent;

class LiveGamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkForCast($fixtureLiveIds)
    {

        $TrackedFixturesIds=[];
        foreach($fixtureLiveIds as $fixtureId)
        {
            $fixture = DB::table('live_events')->where('match_goallive_id', $fixtureId)->first();
            isset($fixture) && array_push($TrackedFixturesIds,$fixtureId);
        }

        return $TrackedFixturesIds;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getLiveFixtures()
    {
        $client = new Client();
        $res = $client->get('http://goal.cdn.md/FootballApi/Mobile/Default.aspx?d={"source":3,"lang":2,"APP":"1011","hbl":1,"filterBy":"Today","T":91,"tZ":-3}');
        $jsonResponse = $res->getBody();
        $jsonData = json_decode($jsonResponse,true);
        $fixturesLive = [];
        $fixturesData = [];

        foreach($jsonData['G'] as $tournaments)
        {
            foreach($tournaments['m'] as $fixture)
            {
                $fixtureData = explode(',',$fixture);
                $fixtureStatus = $fixtureData[8];
                $fixturesData[$fixtureData[0]] = [];
                $fixturesData[$fixtureData[0]]['goal_live_id'] = $fixtureData[0];
                $fixturesData[$fixtureData[0]]['ht_name'] = $fixtureData[1];
                $fixturesData[$fixtureData[0]]['at_name'] = $fixtureData[2];
                $fixturesData[$fixtureData[0]]['ht_score'] = ($fixtureData[9] != "") ? $fixtureData[9] : 0;
                $fixturesData[$fixtureData[0]]['at_score'] = ($fixtureData[10] != "") ? $fixtureData[10] : 0;
                $fixturesData[$fixtureData[0]]['time'] = $fixtureData[11];
                $fixturesData[$fixtureData[0]]['status'] = $fixtureData[8];
                if( $fixtureStatus == "1" || $fixtureStatus == "2" || $fixtureStatus == "3" )
                {
                    // Insert Fixture ID Into Live Matches ID's Array
                    array_push($fixturesLive,$fixtureData[0]);
                }
            }
        }

        $fixturesLive = array_unique($fixturesLive);
        $fixturesLiveWithCast = $this->checkForCast($fixturesLive);




        foreach ($fixturesLiveWithCast as $fixtureId)
        {
            $liveStats = [];
            $liveStats['total_inplay_time'] = LiveEvent::getInPlayTotalTime($fixtureId);
            $liveStats['total_tracked_time'] = LiveEvent::getTrackedTotalTime($fixtureId);
            $liveStats['safe1_inplay_time'] = LiveEvent::getInPlayTime($fixtureId, "SAFE1");
            $liveStats['safe2_inplay_time'] = LiveEvent::getInPlayTime($fixtureId, "SAFE2");
            $liveStats['at1_inplay_time'] = LiveEvent::getInPlayTime($fixtureId, "AT1");
            $liveStats['at2_inplay_time'] = LiveEvent::getInPlayTime($fixtureId, "AT2");
            $liveStats['dat1_inplay_time'] = LiveEvent::getInPlayTime($fixtureId, "DAT1");
            $liveStats['dat2_inplay_time'] = LiveEvent::getInPlayTime($fixtureId, "DAT2");
            $liveStats['safe1_inplay_%'] = floor(($liveStats['safe1_inplay_time'] / $liveStats['total_inplay_time']) * 100)."%";
            $liveStats['safe2_inplay_%'] = floor(($liveStats['safe2_inplay_time'] / $liveStats['total_inplay_time']) * 100)."%";
            $liveStats['at1_inplay_%'] = floor(($liveStats['at1_inplay_time'] / $liveStats['total_inplay_time']) * 100)."%";
            $liveStats['at2_inplay_%'] = floor(($liveStats['at2_inplay_time'] / $liveStats['total_inplay_time']) * 100)."%";
            $liveStats['dat1_inplay_%'] = floor(($liveStats['dat1_inplay_time'] / $liveStats['total_inplay_time']) * 100)."%";
            $liveStats['dat2_inplay_%'] = floor(($liveStats['dat2_inplay_time'] / $liveStats['total_inplay_time']) * 100)."%";

            /* ----------------------------------------- STATS AFTER LAST GOAL ------------------------------- */
            $liveStats['total_inplay_time_alg'] = LiveEvent::getInPlayTotalTimeAfterLastGoal($fixtureId);
            $liveStats['total_tracked_time_alg'] = LiveEvent::getTrackedTotalTimeAfterLastGoal($fixtureId);
            $liveStats['safe1_inplay_time_alg'] = LiveEvent::getInPlayTimeAfterLastGoal($fixtureId, "SAFE1");
            $liveStats['safe2_inplay_time_alg'] = LiveEvent::getInPlayTimeAfterLastGoal($fixtureId, "SAFE2");
            $liveStats['at1_inplay_time_alg'] = LiveEvent::getInPlayTimeAfterLastGoal($fixtureId, "AT1");
            $liveStats['at2_inplay_time_alg'] = LiveEvent::getInPlayTimeAfterLastGoal($fixtureId, "AT2");
            $liveStats['dat1_inplay_time_alg'] = LiveEvent::getInPlayTimeAfterLastGoal($fixtureId, "DAT1");
            $liveStats['dat2_inplay_time_alg'] = LiveEvent::getInPlayTimeAfterLastGoal($fixtureId, "DAT2");
            $liveStats['safe1_inplay_%_alg'] = ($liveStats['total_inplay_time_alg'] > 0) ? floor(($liveStats['safe1_inplay_time_alg'] / $liveStats['total_inplay_time_alg']) * 100)."%" : $noGoal = true;
            $liveStats['safe2_inplay_%_alg'] = ($liveStats['total_inplay_time_alg'] > 0) ? floor(($liveStats['safe2_inplay_time_alg'] / $liveStats['total_inplay_time_alg']) * 100)."%" : $noGoal = true;
            $liveStats['at1_inplay_%_alg'] = ($liveStats['total_inplay_time_alg'] > 0) ? floor(($liveStats['at1_inplay_time_alg'] / $liveStats['total_inplay_time_alg']) * 100)."%" : $noGoal = true;
            $liveStats['at2_inplay_%_alg'] = ($liveStats['total_inplay_time_alg'] > 0) ? floor(($liveStats['at2_inplay_time_alg'] / $liveStats['total_inplay_time_alg']) * 100)."%" : $noGoal = true;
            $liveStats['dat1_inplay_%_alg'] = ($liveStats['total_inplay_time_alg'] > 0) ? floor(($liveStats['dat1_inplay_time_alg'] / $liveStats['total_inplay_time_alg']) * 100)."%" : $noGoal = true;
            $liveStats['dat2_inplay_%_alg'] = ($liveStats['total_inplay_time_alg'] > 0) ? floor(($liveStats['dat2_inplay_time_alg'] / $liveStats['total_inplay_time_alg']) * 100)."%" : $noGoal = true;

            /* ----------------------------------------- STATS SECOND HALF ------------------------------- */
            $liveStats['total_inplay_time_sh'] = LiveEvent::getInPlayTotalTimeSecondHalf($fixtureId);
            $liveStats['total_tracked_time_sh'] = LiveEvent::getTrackedTotalTimeSecondHalf($fixtureId);
            $liveStats['safe1_inplay_time_sh'] = LiveEvent::getInPlayTimeSecondHalf($fixtureId, "SAFE1");
            $liveStats['safe2_inplay_time_sh'] = LiveEvent::getInPlayTimeSecondHalf($fixtureId, "SAFE2");
            $liveStats['at1_inplay_time_sh'] = LiveEvent::getInPlayTimeSecondHalf($fixtureId, "AT1");
            $liveStats['at2_inplay_time_sh'] = LiveEvent::getInPlayTimeSecondHalf($fixtureId, "AT2");
            $liveStats['dat1_inplay_time_sh'] = LiveEvent::getInPlayTimeSecondHalf($fixtureId, "DAT1");
            $liveStats['dat2_inplay_time_sh'] = LiveEvent::getInPlayTimeSecondHalf($fixtureId, "DAT2");
            $liveStats['safe1_inplay_%_sh'] = ($liveStats['total_inplay_time_sh'] > 0) ? floor(($liveStats['safe1_inplay_time_sh'] / $liveStats['total_inplay_time_sh']) * 100)."%" : $noSecondHalf = true;
            $liveStats['safe2_inplay_%_sh'] = ($liveStats['total_inplay_time_sh'] > 0) ? floor(($liveStats['safe2_inplay_time_sh'] / $liveStats['total_inplay_time_sh']) * 100)."%" : $noSecondHalf = true;
            $liveStats['at1_inplay_%_sh'] = ($liveStats['total_inplay_time_sh'] > 0) ? floor(($liveStats['at1_inplay_time_sh'] / $liveStats['total_inplay_time_sh']) * 100)."%" : $noSecondHalf = true;
            $liveStats['at2_inplay_%_sh'] = ($liveStats['total_inplay_time_sh'] > 0) ? floor(($liveStats['at2_inplay_time_sh'] / $liveStats['total_inplay_time_sh']) * 100)."%" : $noSecondHalf = true;
            $liveStats['dat1_inplay_%_sh'] = ($liveStats['total_inplay_time_sh'] > 0) ? floor(($liveStats['dat1_inplay_time_sh'] / $liveStats['total_inplay_time_sh']) * 100)."%" : $noSecondHalf = true;
            $liveStats['dat2_inplay_%_sh'] = ($liveStats['total_inplay_time_sh'] > 0) ? floor(($liveStats['dat2_inplay_time_sh'] / $liveStats['total_inplay_time_sh']) * 100)."%" : $noSecondHalf = true;

            echo "[ ".$fixturesData[$fixtureId]['time']." ] (".$liveStats['dat1_inplay_%'].") ".$fixturesData[$fixtureId]['ht_name']."<strong> "
                .$fixturesData[$fixtureId]['ht_score']." - ".$fixturesData[$fixtureId]['at_score']."</strong> "
                .$fixturesData[$fixtureId]['at_name']." (".$liveStats['dat2_inplay_%'].") Tracked -> ".$liveStats['total_tracked_time']." Minutes<br /><br />";

            if( $liveStats['total_inplay_time_sh'] > 0 )
            {            echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <strong>On Second Half -></strong> (".$liveStats['dat1_inplay_%_sh'].") ".$fixturesData[$fixtureId]['ht_name']." - "
                .$fixturesData[$fixtureId]['at_name']." (".$liveStats['dat2_inplay_%_sh'].") Tracked -> ".$liveStats['total_tracked_time_sh']." Minutes<br /><br />";
            }

            if( $liveStats['total_inplay_time_alg'] != 1 && $liveStats['total_inplay_time_alg'] != $liveStats['total_inplay_time'] && $liveStats['dat1_inplay_%_alg'] != 1  )
            {            echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <span><span style='color:red;'>After Last Goal</span> -></strong> (".$liveStats['dat1_inplay_%_alg'].") ".$fixturesData[$fixtureId]['ht_name']." - "
                .$fixturesData[$fixtureId]['at_name']." (".$liveStats['dat2_inplay_%_alg'].") Tracked -> ".$liveStats['total_tracked_time_alg']." Minutes<br /><br />";
            }
        }
    }

}
