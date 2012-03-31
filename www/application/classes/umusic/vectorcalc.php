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
    
    public static function flatten($array) {
        $a = array();
        foreach($array as $val) {
            if(is_array($val))
                foreach($val as $tag)
                    $a[] = $tag;
            else
                $a[] = $val;
        }
        return $a;
    }

    private static $tagnames = array();

    public function __construct(Database $database) {
        $config = Kohana::$config->load('umusic');

        $this->used_tags = $this::flatten($config->get('tags'));

        foreach($config->get('tags') as $key=>$val) {
            if(is_array($val))
                self::$tagnames[] = $key;
            else
                self::$tagnames[] = $val;
        }

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

        foreach ($tags->execute() as $tag) {
            $result[$tag['usedtags.tag']] = $tag['tid_tag.val'] / 100;
        }

        return $result;
    }

    private static $resultvector = false;

    public function calc_user_vector(Model_User $user) {
        $user_id = $user->rowid;

        // Initialize Result vector for this user
        if(!($resultvector = Umusic_Vectorcalc::$resultvector)) {
            $resultvector = array();
            foreach (self::$tagnames as $tagname) {
                $resultvector[$tagname] = 0;
            }
            Umusic_Vectorcalc::$resultvector = $resultvector;
        }

        // Get user actions
        $actions = Jelly::query('action')->where('user_id', '=', $user_id)->select();
        foreach ($actions as $action) {
            $tags = Jelly::query('tracktag')->where('track_id','=', $action->track_id)->limit(1)->execute();
            $tags = json_decode($tags->tags);

            foreach($tags as $key => $value) {
                if($value != 0) {
                    $resultvector[self::$tagnames[$key]] += $value * $this->ratings[$action->action];
                }
            }
        }

        return $resultvector;
    }

    private static $tags = FALSE;

    public static function simplify_vector(array $array) {
        if(!($tags = Umusic_Vectorcalc::$tags))
            $tags = Umusic_Vectorcalc::$tags = Kohana::$config->load('umusic')->get('tags');

        $result = array();
        foreach ($tags as $key => $tag) {
            if(is_array($tag)) {
                $res = 0;
                foreach($tag as $name)
                    $res += isset($array[$name]) ? $array[$name] : 0;
                $res += isset($array[$key]) ? $array[$key] : 0;
                $result[] = $res;
            } else
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
}