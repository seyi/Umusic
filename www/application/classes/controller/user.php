<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic User Controller
 * 
 * Handles registration, login and logout
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_User extends Controller_Main {

    private $user;

    public function before() {
        parent::before();
        $this->database = Database::instance();
        $this->user = Session::instance()->get('user');
    }

    public function action_index() {
        if ($this->user)
            $this->request->redirect('user/welcome');

        $this->view->partial('content', 'content/user/index');
    }

    public function action_welcome() {
        if (!$this->user)
            $this->request->redirect('user/index');

        $this->view->set('user', $this->user->as_array());
        $this->view->partial('content', 'content/user/welcome');
    }

    public function action_register() {
        if ($this->request->method() == 'POST') {
            try {
                $user = Jelly::factory('user');
                $user->username = $this->request->post('username');
                $user->password = $this->request->post('password');

                $extra_rules = Validation::factory($this->request->post())
                        ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'))
                        ->rule('csrf', 'Security::csrf');

                $user->save($extra_rules);
                $this->_login($user);
            } catch (Jelly_Validation_Exception $e) {
                echo "Error.";
                $errors = $e->errors('user');
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
        $this->view->set('token', Security::token());
    }

    public function action_login() {
        if ($this->request->method() == 'POST') {
            $post = Validation::factory($this->request->post())
                    ->rule('username','not_empty')
                    ->rule('password','not_empty')
                    ->rule('csrf', 'Security::csrf');
            
            $errors = array();
            
            if ($post->check()) {
                $user = Jelly::query('user')->by_username($this->request->post('username'))->execute();
                if($user && $user->login($this->request->post('password')))
                    $this->_login($user);
                else
                    $errors['password'] = __("Username or password incorrect.");
            }
            $errors = Arr::merge($errors, $post->errors('user'));
            $this->view->set('errors', $errors);
            $this->view->set('values', $this->request->post());
            echo "<pre>" . Debug::dump($errors) . "</pre>";
        }
        if ($this->user)
            $this->request->redirect('user/welcome');

        $this->view->partial('content', 'content/user/login');
        $this->view->partial('form', 'forms/login');
        $this->view->set('form-url', 'user/login');
        $this->view->set('token', Security::token());
    }

    private function _login($user) {
        $this->user = $user;
        $this->session->set('user', $user);
        $this->request->redirect('user/welcome');
    }

    public function action_logout() {
        $this->_logout();

        if (!$this->user)
            $this->request->redirect('user/login');

        $this->view->partial('content', 'content/user/logout');
    }

    private function _logout() {
        $this->user = null;
        $this->session->set('user', $this->user);
    }

}

// End User
