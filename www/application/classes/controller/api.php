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
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');
        $this->response->headers("Content-type", "text/plain");
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
        if($this->request->method() == 'POST') {
            $log = json_decode($this->request->get('log'));
            print_r($log);
        } else {
            $this->respond('This method requires POST data',1);
        }
    }
    
    public function action_register() {
        if ($this->request->method() == 'POST') {
            try {
                $user = Jelly::factory('user');
                $user->username = $this->request->post('username');
                $user->email = $this->request->post('email');
                $user->password = $this->request->post('password');

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
                $this->respond($e->getMessage(),1);
            }
        } else {
            $this->respond('This method requires POST data',1);
        }
    }
    
    public function action_signin() {
        if ($this->request->method() == 'POST') {
            $post = Validation::factory($this->request->post())
                    ->rule('username','not_empty')
                    ->rule('password','not_empty');
            
            $errors = array();
            
            if ($post->check()) {
                $user = Jelly::query('user')->by_username($this->request->post('username'))->execute();
                if($user && $user->login($this->request->post('password')))
                    $this->_login($user);
                else
                    $errors['password'] = __("Username or password incorrect.");
            }
            $errors = Arr::merge($errors, $post->errors('validation/user'));
            $this->result['status'] = 2;
            $this->result['errors'] = Arr::flatten($errors);
            $this->result['values'] = $this->request->post();
            unset($this->result['values']['password']);
        } else {
            $this->respond('This method requires POST data',1);
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
        $this->respond("Successfully signed in",0,array('user' => $user->as_array()));
    }
    
    public function action_signout() {
        $this->_logout();
    }

    private function _logout() {
        $this->user = null;
        $this->session->set('user', $this->user);
        $this->respond("Successfully signed out",0);
    }
    
    private function respond($message,$code=0,$data=array()) {
        echo json_encode(Arr::merge($data, array(
            'status' => $code,
            'message' => $message,
        )));
        $this->render = false;
    }
    
    public function after() {
        if($this->render)
            echo json_encode($this->result);
        parent::after();
    }

}

// End Api
