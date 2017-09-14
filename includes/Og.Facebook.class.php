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
	 * Class wich deals with the facebook connection et wich get the data from facebook
	 */
	class Og_facebook{
		
		public 
			$app_id,//= '520862471385905',
			$app_secret,// = 'c05db8e386400b673a32f2cbff7d2fe6',
			$access_token,
			$official_fb_plugin_credentials = false;
		
		
		/**
		 * FUNCTION __CONSTRUCT
		 */
		function __construct(){
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
		 * FUNCTION IS OFFICIAL APP CREDENTIAL EXIST
		 * @return $credentials or false
		 */
		public function is_official_app_credential_exist(){
			$credentials = get_option('facebook_application');
			if( (is_array($credentials) && !empty($credentials)) && (!empty($credentials['app_id']) && !empty($credentials['app_secret']) && !empty($credentials['access_token'])) ){
				$this->official_fb_plugin_credentials = true;	
				return $credentials;
			}else{
				return false;
			}
		}
		
		public function set_credentials($credentials){
				
			// Check Plugin Official facebook
			$official_credentials = $this->is_official_app_credential_exist();
			if($official_credentials){
				echo "official";
				$this->app_id 				= $official_credentials['app_id'];
				$this->app_secret 			= $official_credentials['app_secret'];
				$_SESSION['access_token'] 	= $official_credentials['app_id'].'|'.$official_credentials['app_secret'];
				return true;
			
			// Ogfe plugin crendentials
			}else{
				if(empty($credentials['app_id']) || empty($credentials['app_secret'])) {
					return 0;
				}
				
				$this->app_id 				= $credentials['app_id'];
				$this->app_secret 			= $credentials['app_secret'];
				$_SESSION['access_token'] 	= $this->app_id.'|'.$this->app_secret;
				
				FacebookSession::setDefaultApplication($this->app_id, $this->app_secret);
				$session = new FacebookSession($this->app_id.'|'.$this->app_secret);
				$session = FacebookSession::newAppSession();
				try {
				  $session->validate();
				} catch (FacebookRequestException $ex) {
					//echo $ex->getMessage();
					return $ex->getMessage(); // Session not valid, Graph API returned an exception with the reason.
				} catch (\Exception $ex) {
					//echo $ex->getMessage();
					return $ex->getMessage(); // Graph API returned info, but it may mismatch the current app or have expired.
				}//*/
				return true;
			}
		}
		
		
		/**
		 * FUNCTION CURL GET FILE CONTENT
		 * @param $URL (string) : the fb request url
		 * @return object
		 */
		public function curl_get_contents($url) {
			//$url = trim(preg_replace('/\s\s+/', ' ', $url)); // delete space tab new line
		    $c = curl_init($url);
		    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($c, CURLOPT_URL, $url);
		    $contents = curl_exec($c);
		    $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
		    curl_close($c);
		    if ($contents) {
			    return json_decode($contents);
		    } else {
		    	return FALSE;
			}
		} //http://stackoverflow.com/questions/27056483/how-do-i-auto-post-on-fb-with-php-and-fb-sdk-4-4
	  
		/**
		 * FUNCTION IS PAGE ID VALIDE
		 * @param $id (string)
		 * @return bool
		 * Ask graph api if page_id return an actual fb object
		 */
		public function is_page_id_valide($page_id){
			if(isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])){
				if(!is_object($this->curl_get_contents(FB_API.$page_id.'/?access_token='.$_SESSION['access_token']))){
					return false;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}
	}
	