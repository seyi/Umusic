<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Main extends Controller {
    /**
     *
     * @var View_Main 
     */
    public $view;
    public $auto_render = true;
    
    public function before() {
        parent::before();
        $this->view = new View_Main();
    }
    
    public function after() {
        if($this->auto_render)
            echo $this->view;
        parent::after();
    }

}

// End Base
