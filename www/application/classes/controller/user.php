<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic User Controller
 * 
 * Handles registration, login and logout
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_User extends Controller_Main {

    /**
     * Initialize the session
     */
    public function before() {
        parent::before();
    }

    /**
     * The user index page for users that aren't logged in
     */
    public function action_index() {
        if ($this->user)
            $this->request->redirect('user/welcome');

        $this->view->partial('content', 'content/user/index');
    }

    /**
     * The welcome page for known users
     */
    public function action_welcome() {
        if (!$this->user)
            $this->request->redirect('user/index');

        $this->view->set('user', $this->user->as_array());
        $this->view->partial('content', 'content/user/welcome');
    }

    /**
     * The register page
     */
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
                echo "Error.";
                $errors = $e->errors('validation');
                $errors = Arr::flatten($errors);
                $this->view->set('errors', $errors);
                $this->view->set('values', $this->request->post());
            }
        }

        if ($this->user)
            $this->request->redirect('user/welcome');

        $this->view->partial('content', 'content/user/register');
        $this->view->partial('form', 'forms/register');
        $this->view->set('form-url', 'user/register');
    }

    /**
     * The signin page
     */
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
            $this->view->set('errors', $errors);
            $this->view->set('values', $this->request->post());
        }
        if ($this->user)
            $this->request->redirect('user/welcome');

        $this->view->partial('content', 'content/user/signin');
        $this->view->partial('form', 'forms/signin');
        $this->view->set('form-url', 'user/signin');
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
        $this->request->redirect('user/welcome');
    }

    public function action_signout() {
        $this->_logout();

        if (!$this->user)
            $this->request->redirect('user/signin');

        $this->view->partial('content', 'content/user/signout');
    }

    private function _logout() {
        $this->user = null;
        $this->session->set('user', $this->user);
    }

}

// End User
