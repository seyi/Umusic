<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic main view
 * 
 * Selects header and footer partials
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */

class View_Main extends Kostache {

    public $title = 'UMusic';
    protected $_template = 'main';

}