<?php

defined("SYSPATH") or die("No direct script access.");

/**
 * Umusic Conversion controller
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Conversion extends Controller {

    private $database, $vc;

    public function before() {
        parent::before();
        if (PHP_SAPI !== "cli")
            exit("Execute this from command line.");

        $this->database = Database::instance()->attach("lastfm_tags");
        $this->vc = new Umusic_Vectorcalc($this->database);
    }

    public function action_generate_users() {
        $this->log("Removing old user data");
        DB::delete("usertags")->execute();

        $users = Jelly::query("user")->select();
        $count = $users->count();
        $i = 0;
        foreach ($users as $user) {
            $tags = $this->vc->calc_user_vector($user);
            $vector = Umusic_Vectorcalc::simplify_vector($tags);
            $vector = Umusic_Vectorcalc::normalize($vector);
            Jelly::factory("usertag")->set(array(
                "user_id" => $user->rowid,
                "tags" => json_encode($vector)
            ))->save();
            $this->log("(" . number_format(++$i / $count * 100, 1) . "%)\tProcessed user " . $user->username);
        }
        $this->log("Finished.");
    }
    
    public function action_delete_tracks() {
        $this->log("Removing old track tags...");
        DB::delete("tracktags")->execute();
    }

    public function action_generate_tracks() {
        
        $options = CLI::options('max','offset');
        
        $batch = 50;

        $count = DB::select(array("COUNT(*)", "num_tracks"))->from("tids")->execute()->get("num_tracks");
        
        if(isset($options['max']))
            $maxcount = min($count, $options['max'] + $options['offset']);
        else
            $maxcount = $count;
        
        $offset = isset($options['offset']) ? $options['offset'] : 0;
                
        $this->log("(".  number_format(0,3)."%)\tStarting...");
        
        for ($record = $offset; $record < $maxcount; $record += $batch) {
            $max = min($record + $batch, $maxcount);
            $tracks = DB::select("rowid", "tid")->from("tids")->limit($batch)->offset($record)->execute();
            foreach ($tracks as $track) {
                $tags = $this->vc->calc_track_vector($track["rowid"]);
                $vector = Umusic_Vectorcalc::simplify_vector($tags);
                Jelly::factory("tracktag")->set(array(
                    "track_id" => $track["tid"],
                    "tags" => json_encode($vector)
                ))->save();
            }
            $this->log("Processed records " . $record . " to " . $max . "...");
        }
        $this->log("Finished.");
    }

    private function log($message) {
        echo "[" . date("H:i:s") . "] " . $message . "\n";
        ob_flush();
    }

}
