<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic User Controller
 * 
 * Handles registration, login and logout
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_User extends Controller_Main {

    public function before() {
        parent::before();
        $this->database = Database::instance();
    }

    public function action_index() {
        $this->view->partial('content', 'content/user');
    }

}

// End User
