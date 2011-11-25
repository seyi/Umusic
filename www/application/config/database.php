<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'umusic' => array(
	    'type'       => 'pdo',
	    'connection' => array(
	        'dsn'        => 'sqlite:/umusic_data/umusic.sqlite',
	        'persistent' => FALSE,
	    ),
	    'table_prefix' => '',
	    'charset'      => NULL, /* IMPORTANT- charset 'utf8' breaks sqlite(?) */ 
	    'caching'      => FALSE,
	    'profiling'    => TRUE,
	),
);