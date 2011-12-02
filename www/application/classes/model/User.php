<?php

defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model {
    
    private $database;
    protected $username, $email, $password, $salt;
    
    public function __construct() {
        $this->database = Database::instance();
        if(func_num_args() > 0) {
            $username = func_get_arg(0);
            $user = DB::select()->from('users')->where("username", "=", $username)->execute('umusic');
            echo Debug::dump($user);
        }
        return $this;
    }
    
    public function username($username) {
        //$username = 
        if(DB::select()->from('users')->where("username", "=", $username)->execute('umusic')->count() == 0) {
            $this->username = $username;
        } else {
            throw new Kohana_Exception("Username already taken");
        }
    }
    
    
    
    
}

?>
