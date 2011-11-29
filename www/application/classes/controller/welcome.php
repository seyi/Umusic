<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

    private $database;

    public function before() {
        parent::before();
        $this->database = Database::instance();

        //load Million Song Dataset files
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
    }

    public function action_index() {
        $view = new View_Base;
        $view->set('content', 'Type the name of an artist in the search box.');
        echo $view;
    }

    public function action_search() {
        $view = new View_Base;
        $query = Arr::get($this->request->post(), 'q', false);
        if ($query) {
            $res = DB::select("title","artist_name")->from("track_metadata.songs")->where("artist_name", "LIKE", "%".$query."%")->limit(20)->execute("umusic");
            $view->set('results',$res);
        }
        echo $view;
    }

}

// End Welcome
