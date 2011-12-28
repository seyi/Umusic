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
class Umusic_Vectorcalc {

    private $used_tags, $db_used_tags;
    private $ratings;
    private $database;

    public function __construct(Database $database) {
        $config = Kohana::$config->load('umusic');
        $this->used_tags = $config->get('tags');
        $this->db_used_tags = DB::select('rowid', 'tag')->from('tags')->where('tag', 'IN', $this->used_tags);
        $this->ratings = $config->get('ratings');

        $this->database = $database;
    }

    public function calc_track_vector($tid, $find_id=false) {
        $result = array();

        $tags = DB::select('usedtags.tag', 'tid_tag.val')
                        ->from('tid_tag')
                        ->join(array($this->db_used_tags, 'usedtags'))->on('tid_tag.tag', '=', 'usedtags.rowid');
        if ($find_id) {
            $tags = $tags->join('tids')->on('tid_tag.tid', '=', 'tids.rowid')->where('tids.tid', '=', $tid);
        } else {
            $tags = $tags->where('tid_tag.tid', '=', $tid);
        }

        foreach ($tags->cached()->execute() as $tag) {
            $result[$tag['usedtags.tag']] = $tag['tid_tag.val'] / 100;
        }

        return $result;
    }

    public function calc_user_vector(Model_User $user) {
        $user_id = $user->rowid;

        // Initialize Result vector for this user
        $resultvector = array();
        foreach ($this->used_tags as $tagname) {
            $resultvector[$tagname] = 0;
        }

        // Get user actions
        $actions = Jelly::query('action')->where('user_id', '=', $user_id)->select();
        foreach ($actions as $action) {
            $tags = $this->calc_track_vector($action->track_id, true);

            foreach ($tags as $tag => $val) {
                $rating = $val * $this->ratings[$action->action];
                $resultvector[$tag] += $rating;
            }
        }

        return $resultvector;
    }

    public static function simplify_vector(array $array) {
        $tags = Kohana::$config->load('umusic')->get('tags');
        $result = array();
        foreach ($tags as $tag) {
            $result[] = isset($array[$tag]) ? $array[$tag] : 0;
        }
        return $result;
    }

    public static function normalize(array $array, $round=5) {
        $sum = array_sum($array);

        // Hate division by zero
        if ($sum == 0)
            $sum = 1;

        $output = array();
        foreach ($array as $key => $val)
            $output[$key] = round($val / $sum, $round);
        return $output;
    }

    public static function similar_songs($master, $array) {
        $output = array();
        foreach ($array as $key=>$value) {
            $sim = Umusic::cosSim($value, $master);
            $output[$key] = $sim;
        }

        arsort($output);
        
        return $output;
    }

}