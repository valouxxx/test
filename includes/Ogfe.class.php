<?php

	/**
	 * CLASS Ogfe (og-facebook-events)
	 * All the plugin stuffs
	 */
	
	class Ogfe {
		
		public
			// plugin
			$plugin_name		= 'og-facebook-events',
			// Options
			$post_type 			= 'post',
			$post_status 		= 'draft',
			$capability 		= 'manage_options', 	// administrator only
			$fb_events 	= '', 					// list of the event form fb request
			$plugin_post_type 	= '', 					// if create en FB post_type
			$FB, 										// FB class instance
			$options;
		
		/**
		 * FUNCTION __CONSTRUCT
		 * set hooks
		 * @since 1.0
		 */
		function __construct(){
			$this->FB = new Og_facebook();
			// HOOKS
			add_action( 'init', 		array( &$this, 'create_fb_event_post_type'));	// ADD POST TYPE EVENTS
			add_action( 'admin_menu', 	array( &$this, 'add_admin_menu'));				// ADMIN MENU
			add_action( 'admin_init', 	array( &$this, 'ogfe_register_settings' ));		// REGISTER SETTINGS
			//new Admin_notice('ok', 'error');
			$this->get_ogfe_options();
		}
		
		// -------------------------------------------------------------|
		//                    PLUGIN SET-UP                             |
		// -------------------------------------------------------------|
		 
		/**
		 * FUNCTION REGISTER
		 * Used in the "register_activation_hook"
		 * @uses wp_die(), update_option()
		 * @global $wp_version
		 * @since 1.0
		 */
		public function register(){
			global $wp_version;
			if ( version_compare( $wp_version, '3.5', '<' )){
				wp_die( 'This plugin requires WordPress version 3.5 or higher.' );	
			}
			$this->save_options();
		}
		
		/**
		 * FUNCTION ADD ADMIN MENU
		 * @uses add_utility_page()
		 * @since 1.0
		 */
		public function add_admin_menu(){
			add_menu_page( 
				'Facebook Events Plugin By OPENGRAPHY', // page_title
				'Set up Fb events', 					// menu_title
				$this->capability, 						// capability
				'ogfe-settings', 			// menu_slug
				array(&$this, 'settings_page'), 		// settings page ?
				plugins_url( '/images/icon.png', __FILE__ )
				 //position 
			);
			
			add_submenu_page( 'ogfe-settings', 'Events List', 'Events List', $this->capability, 'event-page', array(&$this, 'events_page') );
			//add_submenu_page( 'ogfe-settings', 'Other-settings2', 'Other-settings2', $this->capability, 'other-settings2', '' );
		}
		
		
		/**
		 * FUNCTION GET OGFE OPTION
		 * @since 1.0
		 */
		public function get_ogfe_options(){
			$this->options = get_option('ogfe_options');
		}
		
		
		/**
		 * FUNCTION SAVE OPTIONS
		 * Sets all the plugin options
		 * @since 1.0
		 * @param $option (array) : if empty, defaults options are used
		 */
		public function save_options($options=false){
			// defaults options
			if(!$options){
				
				$ogfe_options = array(
					'wp' => array(
						'post_type' 	=> $this->post_type, 		// wich post_type to use to publish event
						'post_status' 	=> $this->post_status,		// wich post_status to use to publish event
						'capability' 	=> $this->capability		// capability used for plugin usage
					),
					'fb' => array(
						'event_edges' => array(
							'invited' =>array(
								'value'			=>  false, 
								'description' 	=> 'All of the users who have been invited to this event.'
							),
							'attending' => array(
								'value'			=>  false,
								'description' 	=> 'All of the users who are attending this event.'
							),
							'maybe' => array(
								'value'			=>  false, 
								'description' 	=> 'All of the users who have been responded "Maybe" to their invitation to this event.'
							),
							'declined' => array(
								'value'			=>  false, 
								'description' 	=> 'All of the users who declined their invitation to this event.'
							),
							'noreply' => array(
								'value'			=>  false, 
								'description' 	=> 'All of the users who have been not yet responded to their invitation to this event.'
							),
							'feed' => array(
								'value'			=>  false, 
								'description'	=> 'This event’s wall.'
							),
							'picture' => array(
								'value'			=>  false, 
								'description'	=> 'The event’s profile picture.'
							),
							'photos' =>	array(
								'value'			=>	 false, 
								'description' 	=> 'The photos uploaded to an event.'
							),
							'videos' =>	array(
								'value'			=>  false, 
								'description' 	=> 'The videos uploaded to an event.'
							)
						),
						'credentials' => array(
							'app_id' 		=> '',
							'app_secret'	=> '',
							'access_token'	=> ''
						)
					)					
				); //*/
			}else{
				$ogfe_options = $options;
			}
			$this->options = $ogfe_options;
			update_option('ogfe_options', $ogfe_options);
		}
		
		
		/**
		 * FUNCTION CREATE FB EVENT POST TYPE
		 * Called by the add_action 'init'
		 * @uses register_post_type(), post_type_exists()
		 * @since 1.0
		 */
		public function create_fb_event_post_type(){
			
			// just set label here
			$post_type_label 		= 'FB Event';
			$post_type_label_plural = 'FB Events';
			
			$labels = array( // no need to touch
				'name' 				=> __( $post_type_label_plural, 						$this->plugin_name ), 
				'singular_name' 	=> __( $post_type_label_plural, 						$this->plugin_name ), 
				'add_new' 			=> __( 'Add New', 										$this->plugin_name ), 
				'add_new_item' 		=> __( 'Add New '.$post_type_label, 					$this->plugin_name ), 
				'edit_item' 		=> __( 'Edit '.$post_type_label, 						$this->plugin_name ), 
				'new_item' 			=> __( 'New '.$post_type_label, 						$this->plugin_name ), 
				'all_items' 		=> __( 'All '.$post_type_label_plural, 					$this->plugin_name ), 
				'view_item' 		=> __( 'View '.$post_type_label_plural, 				$this->plugin_name ), 
				'search_items' 		=> __( 'Search '.$post_type_label_plural, 				$this->plugin_name ), 
				'not_found' 		=> __( 'No '.$post_type_label_plural.' found', 			$this->plugin_name ), 
				'not_found_in_trash'=> __( 'No '.$post_type_label_plural.' found in Trash', $this->plugin_name ), 
				'menu_name' 		=> __( $post_type_label_plural, 						$this->plugin_name )
			);
			$args = array( 
				'labels' 			=> $labels, 
				'public' 			=> true, 
				'publicly_queryable'=> true, 
				'show_ui' 			=> true,
				'show_in_menu' 		=> true, 
				'query_var' 		=> true, 
				'rewrite' 			=> true, 
				'capability_type' 	=> 'post', 
				'has_archive' 		=> true, 
				'hierarchical' 		=> false, 
				'menu_position' 	=> null, 
				'supports' => array( 
					'title', 
					'editor', 
					'thumbnail', 
					'excerpt' 
				)
			); 
			if(!post_type_exists($this->plugin_post_type)){
				register_post_type( $this->plugin_post_type, $args );
			}else{
				//$err = new WP_Error('post_type', 'Duplicate post_type '.$this->plugin_post_type);
				$mess = new Admin_notice('Duplicate post_type : '.$this->plugin_post_type, 'error');
			}
		}

		
		/**
		 * FUNCTION REGISTER SETTINGS
		 * Hooked in __construct
		 * Fields set in settings_page()
		 * @since 1.0
		 */
		public function ogfe_register_settings() {
			register_setting( 'ogfe-settings-group', 'ogfe_options', array(&$this, 'ogfe_sanitize_options' ));
		}
		
		
		/**
		 * FUNCTION SANITIZE OPTIONS
		 * callback of register_settings()
		 * @since 1.0
		 */
		function ogfe_sanitize_options( $options ) {
			foreach($options as $option){
				$option = (  empty($option) ) ? sanitize_text_field( $option ) : '';
			}
			return $options;
		}
		
		
		// -------------------------------------------------------------|
		//                    EVENTS FUNCTIONS                          |
		// -------------------------------------------------------------|
		
		
		/**
		 * FUNCTION GET EVENTS
		 */
		 public function get_events(){
		 	if(empty($this->fb_events)) $this->fb_events = $this->FB->get_events();
			return $this->fb_events;
		 }
		
		
		/**
		 * FUNCTION IS EVENT EXIST
		 * Checked if event allready exist in wordpress based on event->name VS post_title
		 * @param $event (object) : the object event returned from facebook
		 * @return boolean
		 */
		public function is_event_exist($event){
			global $wpdb;
			$is_exist = $wpdb->get_results('SELECT ID, post_title FROM '.$wpdb->posts.' WHERE post_title="'.$event->name.'"');
			if(!empty($is_exist[0])) return $is_exist[0]->ID;
			else return false;
		}
		
		
		/**
		 * FUNCTION CREATE POST EVENTS
		 * @since 1.0
		 */
		public function create_post_events($events, $post_type=null){
			foreach($events as $event){
				$this->create_post_event($event, $post_type);
			}
		}
		
		
		/**
		 * FUNCTION CREATE POST EVENT
		 * @since 1.0
		 * @todo : stock fb_id en post_meta pour faire la comparaison si exit
		 */
		public function create_post_event($event, $post_type=null){
			//print_r($event);
			//return;
			
			$post = array(
				"post_title" 	=> $event->name,
				"post_name" 	=> sanitize_title($event->name),
				"post_date" 	=> $event->start_time,
				"post_modified" => $event->start_time,
				"post_content"	=> $event->description,
				"post_status"	=> 'publish',//$this->post_status,
				"post_type"		=> $this->post_type
			);
			
			
			
			$post_id = $this->is_event_exist($event);
			
			if(!empty($post_id)){
				$post['ID'] = $post_id;
				$post['edit_date'] = true;
				echo '<br><br>'.$event->name.' allreday exist '.$post_id;
				wp_update_post($post, true);
			}else{
				$post_id = wp_insert_post($post, true);
			}
			
			echo '<br>';
			print_r($event->cover["source"]);
			//$this->og_upload_and_set_images($event->cover["source"], $post_id, $post);
		}
		
		
		
		
		// -------------------------------------------------------------|
		//                   THUMBNAIL FUNCTIONS                        |
		// -------------------------------------------------------------|
		 
		/**
		 * FUNCTION OG UPLOAD AND SET IMAGES
		 * @param $imgLink : absolut path of the image source
		 * @param $post_id : post_parent of image (set 0 if no parent or thumb)
		 * @param $post : to get back the name of post for setting the name of the image and the the description
		 */
		private function og_upload_and_set_images($imgLink, $post_id, $post){
			global $wpdb;
			$file_name_without_ext = $post['post_name'];
			$file_desc = $post['post_title'];
			
			$fileExist = $wpdb->get_results('SELECT ID FROM ma_posts WHERE post_type="attachment" AND post_parent=0 AND post_title="'.$post['post_title'].'"');
			if(empty($fileExist)){
				echo '<br>-> ADD '.$file_name_without_ext.' - '.$file_desc;
				add_image_size( "content_timeline", 270, 381, true );  // type2 detail page
				add_image_size('image-thumb', 300, 230, true);
				set_post_thumbnail_size( 132, 133, true );
				$attach_id = $this->og_media_sideload_image($imgLink, $post_id, $file_name_without_ext, $file_desc); // Permet le transfert vers le upload wordpress - genere le attach_file dans postmeta	
				
				
				if(!empty($attach_id)){
					set_post_thumbnail($post_id, $attach_id);	
					return $attach_id;
				}else{
					echo 'PROBLEME UPLOAD';
				}
			}else{
				return $fileExist[0]->ID;
				echo '<br>-> Allready uploaded';
			}
		}
			
			
		/**
		 * FUNCTION WORDPRESS OG MEDIA SIDELOAD IMAGE
		 * @return $attachment id (vs "media_sideload_image" in wordpress wich doesn't return the attach_id)
		 * generate the image size and db info for a picture
		 */ 
		private function og_media_sideload_image($file, $post_id, $file_name_without_ext, $desc = null) {
			if ( ! empty($file) ) {
				// Download file to temp location
				$tmp = download_url( $file );
				
				
				// Set variables for storage
				// fix file filename for query strings
				preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
				$expl = explode('.',basename($matches[0]));
				$ext = array_pop($expl);
				$file_array['name'] = $file_name_without_ext.'.'.$ext; //basename($matches[0]);
				$file_array['tmp_name'] = $tmp;
				
				
				
				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink($file_array['tmp_name']);
					$file_array['tmp_name'] = '';
					echo "<b>wp_error $tmp</b> : ";
					print_r($tmp);
				}
				// do the validation and storage stuff
				$id = media_handle_sideload( $file_array, $post_id, $desc );
				// If error storing permanently, unlink
				if ( is_wp_error($id) ) {
					@unlink($file_array['tmp_name']);
					echo "<br><b>wp_error $id</b>";
					return $id;
				}
				//die;
			}else{
				echo 'empty file';
			}
			return $id;
		}
				
				
		// -------------------------------------------------------------|
		//                    TEMPLATE LOADING                          |
		// -------------------------------------------------------------|
		 
		/**
		 * FUNCTION SETTINGS PAGE
		 * Load settings_page template
		 * @since 1.0
		 */
		public function settings_page(){
			include plugin_dir_path(__DIR__).'templates/settings_page.php';
		}
		
		/**
		 * FUNCTION EVENTS PAGE
		 * List event objects from fb request
		 * @since 1.0
		 */
		public function events_page(){
			$events = $this->get_events();
			if(!is_array($events)){
				$err = new Admin_notice('No events found', 'error');
			}
			$this->create_post_events($events);
			include plugin_dir_path(__DIR__).'templates/events_page.php';
		}
		
		
		
		

	}