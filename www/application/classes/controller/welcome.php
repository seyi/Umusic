<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Main {

    private $database;

    public function before() {
        parent::before();
        $this->database = Database::instance();

        //load Million Song Dataset files
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
    }
    
    public function action_index() {
        $user = new Model_User();
        $user->username('hylke');
        $this->view->partial('content', 'content/welcome');
    }

}

// End Welcome
