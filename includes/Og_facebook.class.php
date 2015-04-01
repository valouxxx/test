<?php
	session_start();
	 
	/**
	 * FACEBOOK SDK v4
	 */
	require_once (__DIR__.'/facebook-php-sdk-v4-4.0-dev/autoload.php'); 
	use Facebook\FacebookSession;
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\FacebookRequest;
	use Facebook\FacebookResponse;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookAuthorizationException;
	use Facebook\GraphObject;
	use Facebook\GraphSessionInfo;
	use Facebook\GraphUser;
	use Facebook\FacebookHttpable;
	use Facebook\FacebookCurlHttpClient;
	use Facebook\FacebookCurl;
	
	
	
	/**
	 * CLASS OG FACEBOOK
	 */
	class Og_facebook{
		
		public 
			$app_id = '520862471385905',
			$app_secret = 'c05db8e386400b673a32f2cbff7d2fe6',
			$access_token;
		
		
		/**
		 * FUNCTION __CONSTRUCT
		 */
		function __construct(){
			
			//$this->is_app_credential_exist();
			$this->set_credentials();
			$this->fb_session();
			
		}
		
		
		/**
		 * FUNCTION FB SESSION
		 * @since 1.0
		 */
		public function fb_session(){
			// APP SESSION & ACCESS TOKEN (without user connect)	
			$session = new FacebookSession($this->app_id.'|'.$this->app_secret);
			$_SESSION['access_token'] = $this->app_id.'|'.$this->app_secret;
			/**	 USER CONNECT VERSION
			 * @todo : ALL !!!
			$helper = new FacebookRedirectLoginHelper( 'http://events-wp.valentinrocher.com/', $this->app_ID, $this->app_secret);
			try
			{
			  // In case it comes from a redirect login helper
			  $session = $helper->getSessionFromRedirect();
			}
			catch( FacebookRequestException $ex )
			{
			  // When Facebook returns an error
			  $mess = new Admin_notice($ex, 'error');
			  echo $ex;
			}
			catch( Exception $ex )
			{
			  // When validation fails or other local issues
			  $mess = new Admin_notice($ex, 'error');
			}
			//*/
		}
		
		
		
		/**
		 * 
		 */
		public function is_app_credential_exist(){
			$credentials = get_option('facebook_application');
			//print_r($credentials);
		}
		
		public function set_credentials(){
			// Check Plugin Official facebook
			$credentials = get_option('facebook_application');
			if(is_array($credentials) && !empty($credentials)){
				if(!empty($credentials['app_id']) && !empty($credentials['app_secret']) && !empty($credentials['access_token'])){
					$this->app_id 				= $credentials['app_id'];
					$this->app_secret 			= $credentials['app_secret'];
					$this->access_token 		= $credentials['access_token'];
					$_SESSION['access_token'] 	= $this->access_token;
					
					//echo 'FB plugin OK';
					
					return true;
				}
			}//*/
			
			/**
			 * @todo : check credentials
			 */
			$credentials = get_option('ogfe_facebook_credential');
				
			
			//print_r($_SESSION['access_token']);
		}
		
		/**
		 * FUNCTION GET EVENTS
		 * Get event object list with an fb_id (page)
		 * @since 1.0
		 * @link https://developers.facebook.com/docs/graph-api/reference/v2.2/event/?locale=fr_FR
		 * @param $page_id (string) : the id of the page to graph
		 * @return $list_events (array) : all events object
		 */
		public function get_events($page_id = FB_PAGE_TEST){
			$events = $this->fb_request($page_id, 'events');
			//print_r($events);
			if(is_array($events->data)){
				$list_events = array();
				foreach($events->data as $event){
					$event_infos = $this->fb_request($event->id, 'event');
					$event_infos = (array) $event_infos;
					$list_events[]= new Event($event_infos);
				}
				return $list_events;
			}else{
				//$err = new Admin_notice('No Events found', 'error');
			}
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
			switch ($type) {
				case 'events': // $id : fb_page_id
					$request = FB_API.$id.'/events/?since='.strtotime('-1 month').'&access_token='.$_SESSION['access_token'];
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
			return json_decode(og_curl_get_file_contents($request));
		}	
		
	}
	