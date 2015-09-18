<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use GuzzleHttp\Client;
use DB;

class CheckLive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for Live Games (IF Live ? Trigger Node)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $res = $client->get('http://goal.cdn.md/FootballApi/Mobile/Default.aspx?d={"source":3,"lang":2,"APP":"1011","hbl":1,"filterBy":"Today","T":91,"tZ":-3}');
        $jsonResponse = $res->getBody();
        $jsonData = json_decode($jsonResponse,true);
        $fixturesIds = [];

        foreach($jsonData['G'] as $tournaments)
        {
            foreach($tournaments['m'] as $fixture)
            {
                $fixtureData = explode(',',$fixture);
                $fixtureStatus = $fixtureData[8];
                if( $fixtureStatus == "1" || $fixtureStatus == "2" || $fixtureStatus == "3" )
                {
                    // Insert Fixture ID Into Live Matches ID's Array
                    array_push($fixturesIds,$fixtureData[0]);
                }
            }
        }

        $fixturesIds = array_unique($fixturesIds);
        var_dump($fixturesIds);
        //$fixturesGsmIds = $this->filterMatchCast($fixturesIds);
        $fixturesNotTrackeds = $this->filterIfNew($fixturesIds);


        foreach($fixturesIds as $fixtureGsmId) {
$fixture = DB::table('live_events')->where('match_goallive_id', $fixtureGsmId)->first();
if (!isset($fixture)) { $this->startSocketTrack($fixtureGsmId); }
        }


    }

    public function filterMatchCast($fixturesIds)
    {
        $fixturesIdsMatchCast = [];
        foreach($fixturesIds as $fixture)
        {
            $url = 'http://goallive.cdn.md/Mobile/Default.aspx?d={ib365e:1,t:1,APP:1011,lang:2,m:'.$fixture.',ibe:0,T:7,iSe:1,tZ:-3,fMC:0}';
            //$this->info($url);
            $client = new Client();
            $res = $client->get($url);
            $jsonResponse = $res->getBody();
            $jsonData = json_decode($jsonResponse,true);

           if($jsonData['D']['mS'] >= "1" && $jsonData['D']['mS'] <= "3" && $jsonData['D']['mRBI'] > "100")
           {
               array_push($fixturesIdsMatchCast,$jsonData['D']['mGsmI']);
           }
        }
        return $fixturesIdsMatchCast;
    }

    public function filterIfNew($fixturesGsmIds)
    {

        $notTrackedFixturesGsmIds=[];
        foreach($fixturesGsmIds as $fixtureGsmId)
        {
            $fixture = DB::table('live_events')->where('match_gsm_id', $fixtureGsmId)->first();
            !isset($fixture) && array_push($notTrackedFixturesGsmIds,$fixtureGsmId);
        }

        return $notTrackedFixturesGsmIds;
    }

    public function startSocketTrack($fixtureGsmId)
    {
        exec('cd /var/www/inplayomatic/');
        exec('node socket-client.js '.$fixtureGsmId.' > /dev/null 2>/dev/null &');
    }
}
