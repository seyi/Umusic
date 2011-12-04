<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Kostache extension
 * 
 * 
 * @package    UMusic
 * @category   Base
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
abstract class Kostache extends Kohana_Kostache {

    /**
     * Loads the template and partial paths.
     *
     * @param   string  template path
     * @param   array   partial paths
     * @return  void
     * @uses    Kostache::template
     * @uses    Kostache::partial
     */
    public function __construct($template = NULL, array $partials = NULL) {
        parent::__construct($template, $partials);

        $this->set('base', URL::base(null, true));
    }

}
