<?php
	
	  
	/**
	 * FUNCTION OG LOAD CLASSES
	 * 
	 */
	function og_load_classes($classes){
		foreach($classes as $classe){
			if(!class_exists($classe)){
				require_once( plugin_dir_path( __FILE__ ).$classe.'.class.php');
			}else{
				print_r($classe.' name allready exist');
			}
		}
	}
	
	/**
	 * FUNCTION OG SELECTED
	 * Enable to print "selected" in html option > selected form
	 */
	 function og_selected($value, $option_value){
		if($value == $option_value) return "selected";
	 }
	
