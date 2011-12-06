<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * User Model
 * 
 * A Jelly model for user actions in the Umusic application
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Model_Action extends Jelly_Model {

    /**
     * Initialization for the model
     * 
     * @param Jelly_Meta Jelly meta information 
     */
    public static function initialize(Jelly_Meta $meta) {
        // An optional database group you want to use
        $meta->db('umusic');

        $meta->table('actions');

        $meta->fields(array(
            'rowid'     => Jelly::field('primary'),
            'song_id'   => Jelly::field('string'),
            'date'      => Jelly::field('timestamp', array(
                'format'    => "Y-m-d H:i:s.000",
                'auto_now_create'   => true,
            )),
            'action'    => Jelly::field('enum', array(
                'choices'   => array(
                    'added',
                    'liked',
                    'disliked',
                    'played',
                ),
            )),
        ));
    }


    
}

?>
