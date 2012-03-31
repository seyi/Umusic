<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Welcome controller
 * 
 * Default pages
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Welcome extends Controller {

    /**
     *
     * @var View_Main 
     */
    public $view;

    public function action_index() {
        $this->view = new View_Main();
        $user = Session::instance()->get('user');

        if($user) {
        Database::instance();
        $playlist = json_decode($user->playlist);
        $results = array();
        foreach ($playlist as $track_id) {
            $song = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                    ->from('songs')
                    ->where('track_id', '=', $track_id)
                    ->limit(1)
                    ->execute('umusic')
                    ->as_array();
            $results[] = $song[0];
        }
        
        $user = $user->as_array();
        $this->view->bind('user', $user);
        $user['playlist'] = json_encode($results);
        }
        echo $this->view;
    }
    
    public function action_eval() {
        $this->view = new View_Main();
        $user = Session::instance()->get('user');

        if($user) {
        //Database::instance()->attach('lastfm_tags', 'track_metadata');
        $playlist = json_decode($user->playlist);
        $results = array();
        foreach ($playlist as $track_id) {
            $song = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                    ->from('songs')
                    ->where('track_id', '=', $track_id)
                    ->limit(1)
                    ->execute('umusic')
                    ->as_array();
            $results[] = $song[0];
        }
        
        $user = $user->as_array();
        $this->view->bind('user', $user);
        $user['playlist'] = json_encode($results);
        }
        Umusic_Eval::compare_recommendations(1,0.8);
        echo $this->view;
    }
}

// End Welcome
