<?php

namespace App\Http\Controllers;

use Session;
use App\LiveEvent;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ListenerController extends Controller
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
    public function getEventName($eventId, $rballId)
    {
        switch($eventId)
        {
            case "0":
                $eventName = "Start RT1 Start first half";
                $eventStatus = "InPlay";
                break;
            case "1":
                $eventName = "HALF TIME";
                $eventStatus = "HalfTime";
                break;
            case "2":
                $eventName = "Start RT2 Start second half";
                $eventStatus = "InPlay";
                break;
            case "3":
                $eventName = "Stop RT2 Stop second half";
                $eventStatus = "Finished";
                break;
            case "4":
                $eventName = "Start OT1 Start first half extra time";
                $eventStatus = "InPlay";
                break;
            case "5":
                $eventName = "Stop OT1 Stop first half extra time";
                $eventStatus = "Finished";
                break;
            case "6":
                $eventName = "Start OT2 Start second half extra time";
                $eventStatus = "InPlay";
                break;
            case "7":
                $eventName = "Stop OT2 Stop second half extra time";
                $eventStatus = "Finished";
                break;
            case "8":
                $eventName = "Start PEN Start penalty shootout";
                $eventStatus = "Penalty";
                break;
            case "9":
                $eventName = "Stop PEN Stop penalty shootout";
                $eventStatus = "Finished";
                break;
            case "10":
                $eventName = "Start RT1 Team 1";
                $eventStatus = "InPlay";
                break;
            case "11":
                $eventName = "Start RT1 Team 2";
                $eventStatus = "InPlay";
                break;
            case "12":
                $eventName = "Start RT2 Team 1";
                $eventStatus = "InPlay";
                break;
            case "13":
                $eventName = "Start RT2 Team 2";
                $eventStatus = "InPlay";
                break;
            case "14":
                $eventName = "Start OT1 Team 1";
                $eventStatus = "InPlay";
                break;
            case "15":
                $eventName = "Start OT1 Team 2";
                $eventStatus = "InPlay";
                break;
            case "16":
                $eventName = "Start OT2 Team 1";
                $eventStatus = "InPlay";
                break;
            case "17":
                $eventName = "Start OT2 Team 2";
                $eventStatus = "InPlay";
                break;
            case "18":
                $eventName = "Start PEN Team 1";
                $eventStatus = "Paused";
                break;
            case "19":
                $eventName = "Start PEN Team 2";
                $eventStatus = "Paused";
                break;
            case "20":
                $eventName = "Full Time";
                $eventStatus = "Finished";
                break;
            case "128":
                $eventName = "Safe Safe";
                $eventStatus = "Not Started";
                break;
            case "129":
                $eventName = "Danger";
                $eventStatus = "Paused";
                break;
            case "132":
                $eventName = "Injury Break";
                $eventStatus = "Paused";
                break;
            case "133":
                $eventName = "Players are coming out";
                $eventStatus = "Not Started";
                break;
            case "134":
                $eventName = "Players lined up";
                $eventStatus = "Not Started";
                break;
            case "135":
                $eventName = "National anthem singing";
                $eventStatus = "Not Started";
                break;
            case "136":
                $eventName = "Shake hands";
                $eventStatus = "Not Started";
                break;
            case "137":
                $eventName = "Flip coin";
                $eventStatus = "Not Started";
                break;
            case "138":
                $eventName = "Minute of silent";
                $eventStatus = "Not Started";
                break;
            case "139":
                $eventName = "Prize giving ceremony";
                $eventStatus = "Not Started";
                break;
            case "140":
                $eventName = "Photo taking";
                $eventStatus = "Not Started";
                break;
            case "141":
                $eventName = "Game about to start";
                $eventStatus = "Not Started";
                break;
            case "142":
                $eventName = "Missed Penalty";
                $eventStatus = "Paused";
                break;
            case "143":
                $eventName = "PRC Possible red card";
                $eventStatus = "Paused";
                break;
            case "144":
                $eventName = "PPEN Possible penalty";
                $eventStatus = "Paused";
                break;
            case "145":
                $eventName = "No RC No RC after PRC";
                $eventStatus = "Paused";
                break;
            case "146":
                $eventName = "No Pen No PEN after PPEN";
                $eventStatus = "Paused";
                break;
            case "147":
                $eventName = "Retake Pen Retake Penalty";
                $eventStatus = "Paused";
                break;
            case "148":
                $eventName = "Restart Restart game (eg after injury break)";
                $eventStatus = "InPlay";
                break;
            case "150":
                $eventName = "Next Penalty Scorer";
                $eventStatus = "Paused";
                break;
            case "207":
                $eventName = "Possible Free Kick";
                $eventStatus = "Paused";
                break;
            case "208":
                $eventName = "No Free Kick";
                $eventStatus = "InPlay";
                break;
            case "209":
                $eventName = "Referee Ball";
                $eventStatus = "Paused";
                break;
            case "260":
                $eventName = "Extra Time Indicates the extra time.";
                $eventStatus = "Paused";
                break;
            case "524":
                $eventName = "Jersey Changed";
                break;
            case "1024":
                $eventName = "Attack Home";
                $eventStatus = "InPlay";
                $eventState = "AT1";
                break;
            case "1025":
                $eventName = "Corner Home";
                $eventStatus = "Paused";
                $eventState = "DAT1";
                break;
            case "1026":
                $eventName = "Dangerous attack Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1027":
                $eventName = "Dangerous free kick Home";
                $eventStatus = "Paused";
                $eventState = "DAT1";
                break;
            case "1028":
                $eventName = "Free kick Home";
                $eventStatus = "Paused";
                break;
            case "1029":
                $eventName = "Goal Home";
                $eventStatus = "Paused";
                break;
            case "1030":
                $eventName = "Cancel goal Home";
                $eventStatus = "Paused";
                break;
            case "1031":
                $eventName = "Penalty Home";
                $eventStatus = "Paused";
                break;
            case "1032":
                $eventName = "Red card Home";
                $eventStatus = "Paused";
                break;
            case "1033":
                $eventName = "Shot Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1034":
                $eventName = "Yellow card Home";
                $eventStatus = "Paused";
                break;
            case "1039":
                $eventName = "Shot on target Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1040":
                $eventName = "Shot off target Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1041":
                $eventName = "Shot woodwork Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1042":
                $eventName = "Foul Home";
                $eventStatus = "Paused";
                break;
            case "1043":
                $eventName = "Offside Home";
                $eventStatus = "Paused";
                $eventState = "SAFE2";
                break;
            case "1044":
                $eventName = "Kickoff Home";
                $eventStatus = "InPlay";
                break;
            case "1045":
                $eventName = "Yellow / red card Home";
                $eventStatus = "Paused";
                break;
            case "1046":
                $eventName = "Cancel yellow / red card Home";
                $eventStatus = "Paused";
                break;
            case "1047":
                $eventName = "Cancel red card Home";
                $eventStatus = "Paused";
                break;
            case "1048":
                $eventName = "Cancel yellow card Home";
                $eventStatus = "Paused";
                break;
            case "1049":
                $eventName = "Cancel penalty Home";
                $eventStatus = "Paused";
                break;
            case "1050":
                $eventName = "Cancel corner Home";
                $eventStatus = "Paused";
                break;
            case "1051":
                $eventName = "Safe Home";
                $eventStatus = "InPlay";
                $eventState = "SAFE1";
                break;
            case "1052":
                $eventName = "Danger Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1053":
                $eventName = "Goal kick Home";
                $eventStatus = "Paused";
                $eventState = "SAFE1";
                break;
            case "1054":
                $eventName = "Throw in Home";
                $eventStatus = "Paused";
                break;
            case "1055":
                $eventName = "Substitution Home";
                $eventStatus = "Paused";
                break;
            case "1056":
                $eventName = "Dangerous shot Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1057":
                $eventName = "Shot saved Home";
                $eventStatus = "InPlay";
                $eventState = "DAT2";
                break;
            case "1058":
                $eventName = "Shot blocked Home";
                $eventStatus = "InPlay";
                $eventState = "DAT1";
                break;
            case "1059":
                $eventName = "Retake penalty Home";
                $eventStatus = "Paused";
                break;
            case "1060":
                $eventName = "Missed penalty Home";
                $eventStatus = "InPlay";
                break;
            case "2048":
                $eventName = "Attack Away";
                $eventStatus = "InPlay";
                $eventState = "AT2";
                break;
            case "2049":
                $eventName = "Corner Away";
                $eventStatus = "Paused";
                $eventState = "DAT2";
                break;
            case "2050":
                $eventName = "Dangerous attack Away";
                $eventStatus = "InPlay";
                $eventState = "DAT2";
                break;
            case "2051":
                $eventName = "Dangerous free kick Away";
                $eventStatus = "Paused";
                $eventState = "DAT2";
                break;
            case "2052":
                $eventName = "Free kick Away";
                $eventStatus = "Paused";
                $eventState = "SAFE2";
                break;
            case "2053":
                $eventName = "Goal Away";
                $eventStatus = "Paused";
                break;
            case "2054":
                $eventName = "Cancel goal Away";
                $eventStatus = "Paused";
                break;
            case "2055":
                $eventName = "Penalty Away";
                $eventStatus = "Paused";
                break;
            case "2056":
                $eventName = "Red card Away";
                $eventStatus = "Paused";
                break;
            case "2057":
                $eventName = "Shot Away";
                $eventStatus = "InPlay";
                $eventState = "DAT2";
                break;
            case "2058":
                $eventName = "Yellow card Away";
                $eventStatus = "Paused";
                break;
            case "2063":
                $eventName = "Shot on target Away";
                $eventStatus = "InPlay";
                $eventState = "DAT2";
                break;
            case "2064":
                $eventName = "Shot off target Away";
                $eventStatus = "InPlay";
                $eventState = "DAT2";
                break;
            case "2065":
                $eventName = "Shot woodwork Away";
                $eventStatus = "InPlay";
                $eventState = "DAT2";
                break;
            case "2066":
                $eventName = "Foul Away";
                $eventStatus = "Paused";
                break;
            case "2067":
                $eventName = "Offside Away";
                $eventStatus = "Paused";
                $eventState = "SAFE1";
                break;
            case "2068":
                $eventName = "Kickoff Away";
                $eventStatus = "InPlay";
                break;
            case "2069":
                $eventName = "Yellow / red card Away";
                $eventStatus = "Paused";
                break;
            case "2070":
                $eventName = "Cancel yellow / red car Away";
                $eventStatus = "Paused";
                break;
            case "2071":
                $eventName = "Cancel red card Away";
                $eventStatus = "Paused";
                break;
            case "2072":
                $eventName = "Cancel yellow card Away";
                $eventStatus = "Paused";
                break;
            case "2073":
                $eventName = "Cancel penalty Away";
                $eventStatus = "Paused";
                break;
            case "2074":
                $eventName = "Cancel corner Away";
                $eventStatus = "Paused";
                break;
            case "2075":
                $eventName = "Safe Away";
                $eventState = "SAFE2";
                $eventStatus = "InPlay";
                break;
            case "2076":
                $eventName = "Danger Away";
                $eventState = "DAT2";
                $eventStatus = "InPlay";
                break;
            case "2077":
                $eventName = "Goal kick Away";
                $eventState = "SAFE2";
                $eventStatus = "Paused";
                break;
            case "2078":
                $eventName = "Throw in Away";
                $eventStatus = "Paused";
                break;
            case "2079":
                $eventName = "Substitution Away";
                $eventStatus = "Paused";
                break;
            case "2080":
                $eventName = "Dangerous shot Away";
                $eventState = "DAT2";
                $eventStatus = "InPlay";
                break;
            case "2081":
                $eventName = "Shot saved Away";
                $eventState = "DAT1";
                $eventStatus = "InPlay";
                break;
            case "2082":
                $eventName = "Shot blocked Away";
                $eventState = "DAT2";
                $eventStatus = "InPlay";
                break;
            case "2083":
                $eventName = "Retake penalty Away";
                $eventStatus = "Paused";
                break;
            case "2084":
                $eventName = "Missed penalty Away";
                $eventStatus = "InPlay";
                break;
            case "10001":
                $eventName = "Kick off";
                $eventStatus = "InPlay";
        }
        if(isset($eventState))
        {
            $myfile = fopen("/var/www/inplayomatic/public/".$rballId.".txt", "w");
            fwrite($myfile, $eventState);
            fclose($myfile);
            return array($eventName,$eventState,$eventStatus);
        }
        if(!isset($eventState))
        {
            $myfile = fopen("/var/www/inplayomatic/public/".$rballId.".txt", "r");
            $fileresult = fread($myfile,filesize("/var/www/inplayomatic/public/".$rballId.".txt"));
            return array($eventName, $fileresult,$eventStatus);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listen($goalliveid, $json)
    {


        // Decode the Json Response
        $jsonData = json_decode($json);
        $jsonDecoded = json_decode($jsonData);

        $rballId = $jsonDecoded->{'MLU'}->{'ID'};
        $eventId = $jsonDecoded->{'MLU'}->{'EID'};
        $eventStartTime = $jsonDecoded->{'MLU'}->{'CPT'};

        $eventTimeFile = "/var/www/inplayomatic/public/".$rballId."_time.txt";
        if (!file_exists($eventTimeFile)) {
            $myfile = fopen($eventTimeFile, "w");
        }

        $eventInfo = $this->getEventName($eventId,$rballId);
        $eventName = $eventInfo[0];
        $eventState = $eventInfo[1];
        $eventStatus = $eventInfo[2];



        $file = '/var/www/inplayomatic/public/test.txt';
        // Open the file to get existing content
        $current = file_get_contents($file);
        // Append a new person to the file
        $current .= $eventId." - ".$eventName." - ".$eventState."\n";
        // Write the contents back to the file
        file_put_contents($file, $current);

        $insertEvent = LiveEvent::insertEvent($jsonDecoded, $eventState, $eventName, $eventStatus, $goalliveid);
        $updateEvent = LiveEvent::updateEvent($jsonDecoded);
    }
}
