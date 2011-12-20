<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Music controller
 * 
 * Static music pages
 * Music service until the API is fixed
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Music extends Controller_Main {

    public function before() {
        parent::before();
        //load Million Song Dataset files
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
        
        $this->user = Session::instance()->get('user');
        if (!$this->user)
            $this->request->redirect('user/index');
        
        $this->view->set('user',$this->user->as_array());
    }

    public function action_index() {
        $this->view->partial('content', 'content/welcome');
    }
    
    public function action_search() {
        if($this->request->method() == "POST") {
            $search = $this->request->post('q');
            $res = DB::select('title','artist_name','release','duration')->from('songs')->where('title','LIKE','%' . $search . '%')->limit(100)->execute('umusic');
            
            $this->view->partial('content', 'content/music/results');
            $this->view->set('results',$res);
        }
    }

}

// End Welcome
