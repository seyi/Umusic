<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic Main Controller
 * 
 * initializes database, user etc.
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Main extends Controller {

    public $view;
    public $auto_render = true;
    public $database, $session, $user;
    
    public function before() {
        parent::before();
        $this->database = Database::instance();
        $this->session = Session::instance();
        $this->user = $this->session->get('user_id');
        $this->view = new View_Main();
    }
    
    public function after() {
        if($this->auto_render)
            echo $this->view;
        parent::after();
    }

}

// End Base
