<?php

defined('SYSPATH') or die('No direct script access.');

class View_Base extends Kostache {

    public $title = 'UMusic';
    
    protected $_partials = array(
    'header' => 'base/header',         // Loads templates/header.mustache
    'footer' => 'base/footer', // Loads templates/footer/default.mustache
);

}