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
    
    public function get_recommendations($master,$min = 0.8) {
        $tracktags = Jelly::query('tracktag')->select_all();
        $vectors = array();
        foreach($tracktags as $tracktag) {
            $trackid = $tracktag->track_id;
            $trackvector = json_decode($tracktag->tags);
            $sim = Umusic::cosSim($value, $master);
            if($sim > $min)
                $vectors[$trackid] = $sim;
        }

        arsort($vectors);
                
        return $similar;
    }
    
}