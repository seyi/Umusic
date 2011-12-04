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

    public function before() {
        parent::before();
        $this->database = Database::instance();
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
        $this->response->headers("Content-type", "text/plain");
        $this->result = array(
            'api-version'   => '0.1',
        );
    }
    
    public function action_index() {
        $this->result = Arr::merge($this->result, array(
            'status' => 0,
        ));
    }
    
    public function after() {
        echo json_encode($this->result);
        parent::after();
    }

}

// End Api
