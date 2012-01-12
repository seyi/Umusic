<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic API controller
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Api extends Controller {

    /**
     *
     * @var array 
     */
    private $result;

    /**
     *
     * @var boolean
     */
    private $render = true;

    /**
     *
     * @var Database 
     */
    private $database;

    /**
     *
     * @var Session 
     */
    private $session;

    /**
     *
     * @var Model_User 
     */
    private $user;

    public function before() {
        parent::before();
        $this->database = Database::instance();
        $this->database->attach('lastfm_tags', 'track_metadata');
        $this->response->headers("Content-type", "text/json");
        $this->session = Session::instance();
        $this->user = $this->session->get('user');
        $this->result = array();
        
    }

    public function action_index() {
        $this->result = Arr::merge($this->result, array(
                    'status' => 0,
                    'message' => 'Welcome',
                ));
    }

    public function action_log() {
        if ($this->request->method() == 'POST') {
            $log = json_decode($this->request->get('log'));
            print_r($log);
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }

    public function action_register() {
        if ($this->request->method() == 'POST') {
            try {        
                $user = Jelly::factory('user');
                $user->username = $this->request->post('username');
                $user->email = $this->request->post('email');
                $user->password = $this->request->post('password');
                $user->playlist = json_encode(array());

                $extra_rules = Validation::factory($this->request->post())
                        ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));

                $user->save($extra_rules);
                $this->_login($user);
            } catch (Jelly_Validation_Exception $e) {
                $this->result['status'] = 2;
                $this->result['errors'] = Arr::flatten($e->errors('validation'));
                $this->result['values'] = $this->request->post();
                unset($this->result['values']['password']);
                unset($this->result['values']['password_confirm']);
            } catch (Exception $e) {
                $this->respond($e->getMessage(), 1);
            }
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }

    public function action_signin() {
        if ($this->request->method() == 'POST') {
            $post = Validation::factory($this->request->post())
                    ->rule('username', 'not_empty')
                    ->rule('password', 'not_empty');

            $errors = array();

            if ($post->check()) {
                $user = Jelly::query('user')->by_username($this->request->post('username'))->execute();
                if ($user && $user->login($this->request->post('password')))
                    $this->_login($user);
                else
                    $errors['password'] = "Username or password incorrect.";
            }
            $errors = Arr::merge($errors, $post->errors('validation/user'));
            $this->result['status'] = 2;
            $this->result['errors'] = Arr::flatten($errors);
            $this->result['values'] = $this->request->post();
            unset($this->result['values']['password']);
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }

    /**
     * Signs a user in. 
     * 
     * [!!] This function does not check if the user is initialized or if the password is correct.
     * 
     * @param Model_User The user that has to be logged in 
     */
    private function _login($user) {
        $this->user = $user;
        $this->session->set('user', $user);
        
        $playlist = json_decode($user->playlist);
        $results = array();
        foreach ($playlist as $track_id) {
            $song = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                    ->from('songs')
                    ->where('track_id', '=', $track_id)
                    ->limit(1)
                    ->execute('umusic')
                    ->as_array();
            $results[] = $song[0];
        }
        
        $user = $user->as_array();
        if(count($results) > 0)
            $user['playlist'] = $results;
        else
            $user['playlist'] = array();
        
        $this->respond("Successfully signed in", 0, array('user' => $user));
    }
    
    public function action_test() {
        $this->response->headers('Content-Type','text/plain');
        $user = Jelly::factory('user', 1);
        print_r($user);
    }

    public function action_signout() {
        $this->_logout();
    }

    private function _logout() {
        $this->user = null;
        $this->session->set('user', $this->user);
        $this->respond("Successfully signed out", 0);
    }

    public function action_search() {
        if ($this->request->method() == 'POST') {
            $title = $this->request->post('title');
            $artist = $this->request->post('artist');

            try {
                $res = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                        ->from('songs')
                        ->where('artist_name', 'LIKE', '%' . $artist . '%')
                        ->and_where('title', 'LIKE', '%' . $title . '%')
                        ->limit(1000)
                        ->execute('umusic');

                if ($res->count() > 0)
                    $this->respond('Success', 0, array('results' => $res->as_array()));
                else
                    $this->respond('No Results', 2);
            } catch (Exception $e) {
                $this->respond('Failed', 1, array('error_message' => $e->getMessage()));
            }
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }

    public function action_action() {
        if ($this->request->method() == 'POST') {
            if (!$this->user)
                $this->respond('You are not signed in', 1);

            try {
                $action = Jelly::factory('action');
                $action->action = $this->request->post('action');
                $action->track_id = $this->request->post('track_id');
                $action->user_id = $this->user->rowid;
                $action->save();
                $this->respond('Success', 0);
            } catch (Jelly_Validation_Exception $e) {
                $this->respond('Validation Exception', 1, array('error' => $e->errors()));
            } catch (Exception $e) {
                $this->respond('Failed', 1, array('error_message' => $e->getTraceAsString()));
            }
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }

    public function action_user_recommendations() {
        $limit = 100;
        
        if (!$this->user)
            $this->respond('You are not signed in', 1);
        
        $usertag = Jelly::query('usertag')->where('user_id', '=', $this->user->rowid)->limit(1)->select();
        if($usertag) {
            $rc = new Umusic_Recommendation($this->database);
            $recommendations = $rc->get_recommendations(json_decode($usertag->get('tags')));
            
            $output = array(); $i = 0;
            foreach ($recommendations as $track=>$sim) {
                if($i >= $limit)
                    break;
                else if($sim < 0.8)
                    continue;
                else {
                    $track = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                        ->from('songs')
                        ->where('track_id', '=', $track)
                        ->limit(1)
                        ->cached()
                        ->execute()
                        ->as_array();
                    $track = $track[0];
                    
                    $track['sim'] = $sim;
                    $output[] = $track;
                    $i++;
                }
            }
            
            $this->respond("Success", 0, array('data' => $output));
        }
    }
    
    public function action_playlist_get() {
        if (!$this->user)
            $this->respond('You are not signed in', 1);
        try {
            //$user = Jelly::query('users')->where('user_id', '=', $this->user->rowid)->limit(1)->select();
            $user = $this->user;
            $playlist = array();
            $playlist = json_decode($user->playlist);
            
            $results = array();
            foreach($playlist as $track_id) {
                $song = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                        ->from('songs')
                        ->where('track_id', '=', $track_id)
                        ->limit(1)
                        ->execute('umusic')
                        ->as_array();
                $results[] = $song;
            }

            $this->respond('Success', 0, array('results' => $results));
        } catch (Jelly_Validation_Exception $e) {
            $this->respond('Validation Exception', 1, array('error' => $e->errors()));
        } catch (Exception $e) {
            $this->respond('Failed', 1, array('error_message' => $e->getTraceAsString()));
        }
    }
    
    public function action_playlist_add() {
        if ($this->request->method() == 'POST') {
            if (!$this->user)
                $this->respond('You are not signed in', 1);
            try {
                $user = $this->user;
                $playlist = array();
                $playlist = json_decode($user->playlist);
                
                $track_id = $this->request->post('track_id');
                
                if(!in_array($track_id, $playlist)) {
                    $playlist[] = $track_id;
                }
                
                $user->playlist = json_encode($playlist);
                $user->save();
                
                $song = DB::select('track_id', 'title', 'artist_name', 'release', 'duration')
                        ->from('songs')
                        ->where('track_id', '=', $track_id)
                        ->limit(1)
                        ->execute('umusic')
                        ->as_array();
                
                $this->respond('Success', 0, array('songinfo' => $song[0]));
            } catch (Jelly_Validation_Exception $e) {
                $this->respond('Validation Exception', 1, array('error' => $e->errors()));
            } catch (Exception $e) {
                $this->respond('Failed', 1, array('error_message' => (array)$e));
            }
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }
    
    public function action_playlist_remove() {
        if($this->request->method() == 'POST') {
            if (!$this->user)
                $this->respond('You are not signed in', 1);
            try {
                $user = $this->user;
                $playlist = array();
                $playlist = json_decode($user->playlist);
                
                $track_id = $this->request->post('track_id');
                
                $new_playlist = array();
                foreach($playlist as $song) {
                    if($song != $track_id) {
                        $new_playlist[] = $song;
                    }
                }
                               
                $user->playlist = json_encode($new_playlist);
                $user->save();
                
                $this->respond('Success', 0);
            } catch (Jelly_Validation_Exception $e) {
                $this->respond('Validation Exception', 1, array('error' => $e->errors()));
            } catch (Exception $e) {
                $this->respond('Failed', 1, array('error_message' => $e->getTraceAsString()));
            }
        } else {
            $this->respond('This method requires POST data', 1);
        }
    }

    private function respond($message, $code=0, $data=array()) {
        exit(json_encode(Arr::merge($data, array(
                            'status' => $code,
                            'message' => $message,
                        ))));
    }

    public function after() {
        if ($this->render)
            echo json_encode($this->result);
        parent::after();
    }

}

// End Api
