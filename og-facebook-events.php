<?php

/* 
 *	Plugin Name: Facebook Events
 *	Plugin URI: http://example.com/wordpress-plugins/halloween-plugin 
 *	Description: Import and Synchronise your events from Facebook 
 *	Version: 1.0 BETA 
 *	Author: Opengraphy
 *	Author URI: www.opengraphy.com
 *	License:  Copyright 2015 OPENGRAPHY (email : info@opengraphy.com)
 *
 * 
 *	READ ME
 * 
 *	Welcome on the Opengraphy facebook events plugin!
 *	Abbreviation for og-facebook-event in the whole plugin : "ogfe"
 *	 
 */


	
	
	/**
	 * INCLUDES & LOADING CLASSES
	 **/ 
	include(__DIR__.'/config/config.php');
	include(__DIR__.'/includes/og.library.php');
	og_load_classes(array(
		'Ogfe',
		'Og.Facebook',
		'Og.Admin.notice',
		'Og.Event'
	));
	
  
	/* 
	 * PLUGIN ACTIVATION
	 */
	$ogfe = new Ogfe();
	register_activation_hook( __FILE__, array($ogfe,'register' ));
	
	
	/**************************************************************************************************************/
	
	/*
	 * DEACTIVATION
	 *
	 * *
	register_deactivation_hook( __FILE__, 'ogfe_deactivate' );
	function ogfe_deactivate() {
		 //do something
	} //*/
	
	/*
	 * LOCALISATION - INTERNALISATION // DOC : http://codex.wordpress.org/I18n_for_WordPress_Developers
	 *
	add_action( 'init', 'ogfe_init' );
	function ogfe_init() {
		// load_plugin_textdomain( 'ogfe-plugin', false, plugin_basename( dirname( __FILE__ ) .'/localization' ) );
	}//*/
