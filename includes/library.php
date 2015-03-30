<?php
	function og_curl_get_file_contents($URL) {
	    $c = curl_init();
	    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($c, CURLOPT_URL, $URL);
	    $contents = curl_exec($c);
	    $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
	    curl_close($c);
	    if ($contents) return $contents;
	    else return FALSE;
	} //http://stackoverflow.com/questions/27056483/how-do-i-auto-post-on-fb-with-php-and-fb-sdk-4-4
	  
	  
	  
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
	
