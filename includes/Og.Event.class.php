<?php

 class Event{
 	
	
	
	public 
		$id,
		$cover,
		$description,
		$end_time,
		$is_date_only,
		$location,
		$name,
		$owner,
		$parent_group,
		$privacy,
		$start_time,
		$ticket_uri,
		$timezone,
		$updated_time,
		$venue;
		//$infos;
		
	/**
	 * FUNCTION __CONSTRUCT
	 */
	public function __construct($event_infos){
		global $ogfe;
		foreach($event_infos as $k => $v){
			$this->{$k} = $v;	
		}
		$this->set_cover($ogfe);
		$this->set_edges($ogfe);
	}
	
	
	/**
	 * FUNCTION SET COVER
	 * @since 1.0
	 * @param $ogfe (object) : instance of Ogfe.class
	 */
	private function set_cover($ogfe){
		$cover = (array) $ogfe->FB->fb_request($this->id, 'cover');
		$this->cover = (array) $cover['cover'];
	}
	
	/**
	 * FUNCTION SET_EDGES
	 * @since 1.0
	 * @param $ogfe (object) : instance of Ogfe.class
	 */
	private function set_edges($ogfe){
		//$edges = $ogfe->options['fb']['event_edges'];
		//print_r($edges);
	
		foreach($ogfe->options['fb']['event_edges'] as $name => $edge){
			if($edge['value']=="on") {
				//echo '<br><br>'.$name;
				//$var = $ogfe->FB->fb_request($this->id, 'edge', $name);
				print_r($var);
				//$this->infos[$edge['name']] = $ogfe->FB->fb_request($this->id, 'event',$edge['name']);
			}
		}//*/
		
		//print_r($edges);
		/*
			attending 	- User objects 		/ any token 	/ + fied rsvp
			declined 	- User objects 		/ any token 	/ + fied rsvp
			invited		- User objects 		/ any token 	/ + fied rsvp
			maybe		- User objects 		/ any token 	/ + fied rsvp
			noreply		- User objects 		/ any token 	/ + fied rsvp
			
		 	feed 		- array of posts	/ user token 	/ publish capabilty
			picture		- json or redirect	/ user token	/ url, is_silouhette
			photos		- array of Photos	/ user token	/ can publis with publish_action
			videos		- array of Videos	/ user token	/ can publis with publish_action
		*/
	}
		
		
		
	
 }
 
 /** 
	 * FIELDS
	 * @link https://developers.facebook.com/docs/graph-api/reference/v2.2/event
	 *
		id 				- numeric string
		cover 			- CoverPhoto
		description 	- string
		end_time 		- datetime
		is_date_only 	- bool
		location 		- string
		name 			- string
		owner 			- Objet : User|Page|Group
		parent_group 	- Group
		privacy 		- string
		start_time 		- datetime
		ticket_uri 		- string
	*/
