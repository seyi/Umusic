<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic Helper class
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Umusic {
    /*
      imput:        2 arrays of length $size and containing only ones and zeros (the latter is not checked to minimise computation times).
      returns:    For an expected imput consisting of 2 arrays of length $size and containing only ones and zeros cosSim returns a value between 0 and 1. 
     */
    
    /**
     * Cosine Similarity calculation function
     * 
     * [!!] The input arrays should only contain zeros and ones
     * 
     * @param   array   the first array
     * @param   array   the second array
     * @return  double  a value between 0 and 1 representing the similarity of the two input arrays
     */
    public static function cosSim(array $a, array $b) {
        $size = sizeof($a);
        if (size == sizeof($b)) {
            $dota = 0;
            $dotb = 0;
            $dotab = 0;
            for ($i = 0; i < size; $i++) {
                $dota += $a[$i] * $a[$i];
                $dotb += $b[$i] * $b[$i];
                $dotab += $a[$i] * $b[$i];
            }
            $cosTheta = $dotab / (sqrt($dota) * sqrt($dotb));
            return $cosTheta;
        }
        else
            throw new Kohana_Exception ("Incorrect input");
    }

}

// End Umusic