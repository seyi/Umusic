<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Base extends Controller {
        
    public function before() {
        parent::before();
    }
    
    public function after() {
        parent::after();
    }
    
    public function action_index()
    {
        echo new View_Base;
    }

}

// End Base
