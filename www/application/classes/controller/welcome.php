<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
            $database = Database::instance();
            
            //load Million Song Dataset files
            $database->attach('artist_term','lastfm_similars','lastfm_tags','track_metadata');
            
            $r = $database->query(Database::SELECT, "
                SELECT * FROM (
SELECT artist_id, count(*) AS count FROM artist_term
WHERE artist_term.term = \"jazz\"
OR artist_term.term = \"80s\"
OR artist_term.term = \"90s\"
GROUP BY artist_id
) ORDER BY count DESC
LIMIT 1000");
            
            $this->response->body("<pre>" . Debug::dump($r) . "</pre>");
            
        }

} // End Welcome
