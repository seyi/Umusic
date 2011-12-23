<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Umusic External controller
 * 
 * @package    UMusic
 * @category   Controllers
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
class Controller_External extends Controller {
    
    public function action_index() {
        echo "<pre>".Debug::dump($this->request->param())."</pre>";
    }
    
    public function action_artwork() {
        $cache = Cache::instance();
        $artist = $this->request->param('artist');
        $title = $this->request->param('title');
        
        $cached = $cache->get('artwork-'.$artist.'-'.$title);
        
        
        if($cached) {
            $image = $cached['image'];
            $ext = $cached['ext'];
        } else{
            $images = array();

            $request = Request::factory('http://ws.audioscrobbler.com/2.0/')
                    ->query(array(
                        'method'    => 'album.getinfo',
                        'api_key'   => 'cb4d0e82fd5feaceced5dcd045034065',
                        'artist'    => $artist,
                        'album'     => $title,
                    ))
                    ->execute();

            $xml = new DOMDocument();
            $xml->loadXML($request->body());

            $xpath = new DOMXpath($xml);

            $elements = $xpath->query("album/image");
            foreach($elements as $el) {
                $size = $el->attributes->getNamedItem('size')->nodeValue;
                $location = $el->nodeValue;

                $images[$size] = $location;
            }
            
            if(isset($images['extralarge']))
                $location = $images['extralarge'];
            elseif(isset($images['large']))
                $location = $images['large'];
            else
                $location = false;
            
            if($location) {
                $parts = explode('.',$location);
                $ext = end($parts);
                $image = Request::factory($location)->execute();
            } else {
                $image = Image::factory(APPPATH.'data/unknown.png');
                $ext = 'png';
            }
            
            $cache->set('artwork-'.$artist.'-'.$title,array(
                    'ext' => $ext,
                    'image' => $image,
            ));
        }
        
        $this->response->headers('Content-Type',File::mime_by_ext($ext));
        echo $image;
        
    }
    
}