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
        $this->view->bind('user', $user);
        echo $this->view;
    }
}

// End Welcome
