<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kostache extends Kohana_Kostache {
     
     public function __construct($template = NULL, array $partials = NULL) {
         parent::__construct($template, $partials);
         
         $this->set('base', URL::base(null, true));
     }
     
    
}
