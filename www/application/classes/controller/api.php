<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic API controller
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Api extends Controller {

    private $result;
    private $render = true;

    public function before() {
        parent::before();
        $this->database = Database::instance();
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
        $this->response->headers("Content-type", "text/plain");
        $this->result = array();
    }
    
    public function action_index() {
        $this->result = Arr::merge($this->result, array(
            'status' => 0,
            'message' => 'Welcome',
        ));
    }
    
    public function action_log() {
        if($this->request->method() == 'POST') {
            $log = json_decode($this->request->get('log'));
            print_r($log);
        } else {
            $this->error('This method requires POST data');
        }
    }
    
    private function error($message,$code=1) {
        echo json_encode(array(
            'status' => $code,
            'message' => $message,
        ));
        $this->render = false;
    }
    
    public function after() {
        if($this->render)
            echo json_encode($this->result);
        parent::after();
    }

}

// End Api
