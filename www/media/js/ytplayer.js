/* PHP
public function action_youtube_playlist() {
        try {
            
            require_once 'C:\Users\Bojana\Projects\ZendGdata-1.11.11\ZendGdata-1.11.11\library\Zend\Loader.php'; // the Zend dir must be in your include_path
            Zend_Loader::loadClass('Zend_Gdata_YouTube');
            Zend_Loader::loadClass('Zend_Gdata_YouTube');
            $yt = new Zend_Gdata_YouTube();
            
            $query = $yt->newVideoQuery();
            $query->setOrderBy('viewCount');
            $query->setMaxResults(1);
            $query->setCategory('Music/music');
            $query->setVideoQuery('Coldplay+Clocks');
            
            $video_id = $yt->getVideoEntry($query)->getVideoId(); 

        } catch (Exception $e) {
                $this->respond('Failed', 1, array('error_message' => $e->getTraceAsString()));
        }
    }*/
	
$(document).ready(function() {
    var params = { allowScriptAccess: "always" };
    var atts = { id: "myytplayer" };
    swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&version=3&playerapiid=ytplayer",
                       "myytplayer", "1", "1", "8", null, null, params, atts);
});

function onYouTubePlayerReady(playerId) {
    console.log(playerId);
    ytplayer = document.getElementById("myytplayer");
    ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
    ytplayer.addEventListener("onError", "onPlayerError");
    
    var playlist = ["y0LO6v43YCo", "WQtGqmi2O2U"]
    ytplayer.cuePlaylist(playlist);
}

function onytplayerStateChange(newState) {
    console.log("Player's new state: " + newState);
}
 
function onPlayerError(error) {
    console.log("Error occured: " + error);
}

