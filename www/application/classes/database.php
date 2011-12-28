<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * UMusic custom Database connection wrapper/helper.
 *
 * You may get a database instance using `Database::instance()`
 *
 * This class provides connection instance management via Database Drivers, as
 * well as quoting, escaping and other related functions. Querys are done using
 * [Database_Query] and [Database_Query_Builder] objects, which can be easily
 * created using the [DB] helper class.
 *
 * @package    UMusic
 * @category   Core Extensions
 * @author     UMusic Team
 * @copyright  (c) 2011-2012 UMusic Team
 * @license    http://umusic.github.com/license
 */
abstract class Database extends Kohana_Database {

    public static $default = 'umusic';
    protected $_umusic_config;

    /**
     * Get a singleton Database instance. 
     *
     *     // Load the default database
     *     $db = Database::instance();
     * 
     * @return  Database
     */
    public static function instance($name = NULL, array $config = NULL) {
        return parent::instance('umusic');
    }

    /**
     * Stores the database configuration locally and name the instance.
     *
     * [!!] This method cannot be accessed directly, you must use [Database::instance].
     *
     * @return  void
     */
    protected function __construct($name, array $config) {
        parent::__construct($name, $config);
        $this->_umusic_config = Kohana::$config->load('umusic');
    }

    /**
     * Attach a dataset to the umusic database
     * 
     *     // Attach the Lastfm tags dataset
     *     $db->attach('lasfm_tags');
     * 
     *     // Attach the Lastfm tags and similars dataset
     *     $db->attach('lasfm_tags','lastfm_similars');
     * 
     * @param   String the name of datasets to attach
     * @param   ...
     * @return  void
     */
    public function attach($dataset_name) {
        foreach (func_get_args() as $dataset) {
            DB::query(Database::SELECT, DB::expr('ATTACH "' . $this->_umusic_config['dataset']['dir'] . $dataset . '.' . $this->_umusic_config['dataset']['ext'] . '" AS ' . $dataset))->execute();
        }
        return $this;
    }

}

// End Database
