<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * User Model
 * 
 * A Jelly model for user tags in the Umusic application
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Model_Tracktag extends Jelly_Model {

    /**
     * Initialization for the model
     * 
     * @param Jelly_Meta Jelly meta information 
     */
    public static function initialize(Jelly_Meta $meta) {
        // An optional database group you want to use
        $meta->db('umusic');

        $meta->table('tracktags');

        $meta->fields(array(
            'rowid' => Jelly::field('primary'),
            'track_id' => Jelly::field('Text'),
            'tags' => Jelly::field('Text'),
        ));
    }

}
