<?php
	
	/**
	 * CLASS OG FACEBOOK
	 * Class wich deals with the facebook connection et wich get the data from facebook
	 */
	class Og_facebook_event extends Og_facebook{
		
		
		/**
		 * FUNCTION GET EVENTS
		 * Get event object list with an fb_id (page)
		 * @since 1.0
		 * @link https://developers.facebook.com/docs/graph-api/reference/v2.2/event/?locale=fr_FR
		 * @param $fb_page_ids (string) : the ids of the page to graph, set in ogfe_options, separate by ";"
		 * @return $list_events (array) : all event objects
		 */
		public function get_events($fb_page_ids){
			echo 'ids : ';
			print_r($fb_page_ids);
			
			$fb_page_ids = str_replace(' ', '', $fb_page_ids);
			$fb_page_ids = explode(',',$fb_page_ids);
			
			
			$events = array();
			
			foreach($fb_page_ids as $fb_page_id){
				echo '<br><br>';
				$fb_page_id = explode('/',$fb_page_id);
				$fb_page_id = array_pop($fb_page_id);
				//echo '<br>page : '.$fb_page_id;
				$events[$fb_page_id] = $this->fb_request($fb_page_id, 'events');
				
				//print_r($events);
				if(isset($events[$fb_page_id]->error)) {
					echo 'Wrong page name : '.$fb_page_id;
					continue;
				}
				
				if(is_array($events[$fb_page_id]->data)){
					foreach($events[$fb_page_id]->data as $event){
						$event_infos = $this->fb_request($event->id, 'event');
						$event_infos = (array) $event_infos;
						$return[$fb_page_id][]= new Event($event_infos); 
					}
				}//*/
				
			}
			return $return;
		}
		
		
		/**
		 * FUNCTION FB REQUEST
		 * Construct the fb request
		 * @since 1.1
		 * @param $id (string) : id of the page / event / (user?)
		 * @param $type (string) : accept "events" -> list of page's events / "event" -> event's infos
		 * @return array
		 */
		public function fb_request($id, $type, $edge=null){
			//$id = trim($id);
			switch ($type) {
				case 'events': // $id : fb_page_id
					//echo '<br> - REQUETE - ';
					$request = FB_API.$id.'/events/?since='.strtotime('-1 month').'&access_token='.$_SESSION['access_token'];
					//print_r($request);
				break;
				case 'event': // $id : fb_event_id
					$request = FB_API.$id.'/?access_token='.$_SESSION['access_token'];
				break;
				case 'cover': // $id : fb_event_id
					$request = FB_API.$id.'/?fields=cover&access_token='.$_SESSION['access_token'];
				break;
				case 'edge': // $id : fb_event_id
					$request = FB_API.$id.'/'.$edge.'?access_token='.$_SESSION['access_token'];
				break;
			}
			return $this->curl_get_contents($request);
		}	
		
	}
	