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
class Controller_Welcome extends Controller_Main {

    

    public function before() {
        parent::before();
         //load Million Song Dataset files
        //$this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
    }

    public function action_index() {
        $user = Jelly::query('user')->where('username', '=', 'hylke')->limit(1)->execute();
                
        if ($user->rowid){
            $logged_in = $user->login('wachtwoord');
        } else {
            try {
                $user = Jelly::factory('user');
                $user->username = "hylke";
                $user->password = "wachtwoord";
                $user->save();
            } catch (Jelly_Validation_Exception $e) {
                // Get the error messages
                $errors = $e->errors();
            }
        }

        $this->view->partial('content', 'content/welcome');
    }

}

// End Welcome
