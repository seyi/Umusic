<?php

defined('SYSPATH') or die('No direct script access.');

class View_Main extends Kostache {

    public $title = 'UMusic';
    protected $_template = 'main';
    
    protected $_partials = array(
        'header' => 'base/header',
        'footer' => 'base/footer',
    );

}