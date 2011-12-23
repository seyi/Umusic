<?php

defined('SYSPATH') or die('No direct script access.');

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
    
    public function action_users() {
        $actions = Jelly::query('action')->select();
        echo Debug::dump($actions);
    }
    
}