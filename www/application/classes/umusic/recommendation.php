<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic Vector calculation and manipulation class
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Umusic_Recommendation {
    
    private $database;
    private $vc;

    public function __construct(Database $database) {
        $this->database = $database;
        $this->vc = new Umusic_Vectorcalc($this->database);
    }
    
    public function get_recommendations($vector) {
        $tracktags = Jelly::query('tracktag')->select_all();
        $vectors = array();
        foreach($tracktags as $tracktag) {
            $vectors[$tracktag->track_id] = json_decode($tracktag->tags);
        }
        $similar = $this->vc->similar_songs($vector, $vectors);
        return $similar;
    }
    
}