<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic Conversion controller
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_Conversion extends Controller {

    private $database;
    private $used_tags;
    private $rating;

    public function before() {
        parent::before();
        if (PHP_SAPI !== 'cli')
            exit('Execute this from command line.');
        
        $config = Kohana::$config->load('umusic');
        $this->used_tags = $config->get('tags');
        $this->rating = $config->get('ratings');
    }

    public function action_users() {
        $this->database = Database::instance();
        $this->database->attach('artist_term', 'lastfm_similars', 'lastfm_tags', 'track_metadata');

        $users = Jelly::query('user')->select();
        foreach ($users as $user) {
            echo "Starting with " . $user->username . ".\n";
            // initialize result array
            $resultvector = array();
            foreach ($this->used_tags as $tagname) {
                $resultvector[$tagname] = 0;
            }

            // fetch users
            $user_id = $user->rowid;
            $actions = Jelly::query('action')->where('user_id', '=', $user_id)->select();

            // Delete all old tags            
            $oldtags = Jelly::query('usertag')->where('user_id', '=', $user_id)->select();
            foreach ($oldtags as $tag)
                $tag->delete();

            echo "  Calculating action vectors... \n";
            // calculate rating vector for each action
            foreach ($actions as $action) {
                $result = DB::select('tags.tag', 'tid_tag.val')
                                ->from('tags')
                                ->join('tid_tag')->on('tags.rowid', '=', 'tid_tag.tag')
                                ->join('tids')->on('tid_tag.tid', '=', 'tids.rowid')
                                ->where('tids.tid', '=', $action->track_id)->execute();

                // the resulting vector of this action
                foreach ($result as $tag) {
                    $tagname = $tag['tag'];
                    $value = $tag['val'];

                    if (isset($resultvector[$tagname]))
                        $resultvector[$tagname] += $value;
                }
            }

            echo "  normalizing and saving... \n";
            // normalize the result vector
            $sum = array_sum($resultvector);
            foreach ($this->used_tags as $key => $tagname) {
                $value = $resultvector[$tagname] / $sum;
                Jelly::factory('usertag')
                        ->set(array(
                            'user_id' => $user_id,
                            'tag' => $key,
                            'rating' => $value
                        ))->save();
            }
            echo "Finished with " . $user->username . "\n\n";
        }
    }

}
