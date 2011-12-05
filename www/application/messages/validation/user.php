<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    'Security::csrf' => 'Form has expired. Please try again.',
    'username' => array(
        'not_empty' => 'Please enter a username',
        'max_length' => 'The maximum length for a username is :param2 characters',
        'min_length' => 'The minimum length for a username is :param2 characters',
        'unique' => 'This username is already taken.',
    ),
    'password' => array(
        'not_empty' => 'Please enter a password',
        'min_length' => 'The minimum length for a password is :param2 characters',
    ),
);
