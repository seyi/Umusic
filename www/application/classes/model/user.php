<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * User Model
 * 
 * A Jelly model for users in the Umusic application
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Model_User extends Jelly_Model implements Model_ACL_User {

    /**
     * Initialization for the model
     * 
     * @param Jelly_Meta Jelly meta information 
     */
    public static function initialize(Jelly_Meta $meta) {
        // An optional database group you want to use
        $meta->db('umusic');

        // The table the model is attached to
        // It defaults to the name of the model pluralized
        $meta->table('users');

        // Optionally you can auto-load relationships every time you load the model
        //$meta->load_with('author');
        // Fields defined by the model
        $meta->fields(array(
            'rowid' => Jelly::field('primary'),
            'username' => Jelly::field('string', array(
                'allow_null' => false,
                'unique' => true,
                'rules' => array(
                    array('not_empty'),
                    array('max_length', array(':value', 32)),
                    array('alpha_dash'),
                ),
                'filters' => array(
                    array('strtolower'),
                ),
            )),
            'email' => Jelly::field('email', array(
                'allow_null' => false,
                'unique' => true,
                'rules' => array(
                    array('not_empty'),
                ),
            )),
            'password' => Jelly::field('string', array(
                'allow_null' => false,
                'rules' => array(
                    array('not_empty'),
                ),
                'filters' => array(
                    array(array(':model', 'hash')),
                ),
            )),
            'playlist' => Jelly::field('Text'),
        ));
    }

    /**
     * Password hashing
     * 
     * @param string The password
     * @return string 
     */
    public function hash($password) {
        // Do we need to hash the password?
        if (!empty($password) AND $this->changed('password')) {
            return md5($password);
        }

        // Return plain password if no hashing is done
        return $password;
    }

    /**
     * Check if the given password matches with the users password
     * 
     * @param string the given password
     * @return boolean 
     */
    public function login($password) {
        return md5($password) == $this->password;
    }

    /**
    * Wrapper method to execute ACL policies. Only returns a boolean, if you
    * need a specific error code, look at Policy::$last_code
    * @param string $policy_name the policy to run
    * @param array $args arguments to pass to the rule
    * @return boolean
    */
    public function can($policy_name, $args = array())
    {
        $status = FALSE;
        try
        {
            $refl = new ReflectionClass('Policy_' . $policy_name);
            $class = $refl->newInstanceArgs();
            $status = $class->execute($this, $args);
            if (TRUE === $status)
                return TRUE;
        }
        catch (ReflectionException $ex) // try and find a message based policy
        {
            // Try each of this user's roles to match a policy
            foreach ($this->roles->find_all() as $role)
            {
                $status = Kohana::message('policy', $policy_name.'.'.$role->id);
                if ($status)
                    return TRUE;
            }
        }
        // We don't know what kind of specific error this was
        if (FALSE === $status)
        {
            $status = Policy::GENERAL_FAILURE;
        }
        Policy::$last_code = $status;
        return TRUE === $status;
    }

    /**
    * Wrapper method for self::can() but throws an exception instead of bool
    * @param string $policy_name the policy to run
    * @param array $args arguments to pass to the rule
    * @throws Policy_Exception
    * @return null
    */
    public function assert($policy_name, $args = array())
    {
        $status = $this->can($policy_name, $args);
        if (TRUE !== $status)
        {
            throw new Policy_Exception(
                'Could not authorize policy :policy',
                array(':policy' => $policy_name),
                Policy::$last_code
            );
        }
    }  
    
}

?>
