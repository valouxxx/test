<?php

	/**
	 * CLASS Ogfe (og-facebook-events)
	 * All the plugin stuffs
	 */
	
	class Ogfe {
		
		public
			// plugin
			$plugin_name	= 'og-facebook-events',
			// Options
			$post_type 		= 'post',
			$post_status 	= 'draft',
			$capability 	= 'manage_options', // administrator only
		
			$plugin_post_type = 'og-fb-event',
			$FB,
			$options;
		
		/**
		 * FUNCTION __CONSTRUCT
		 * set hooks
		 * @since 1.0
		 */
		function __construct(){
			$this->FB = new Og_facebook();
			// HOOKS
			add_action( 'init', 		array( &$this, 'create_fb_event_post_type'));	// POST TYPE
			add_action( 'admin_menu', 	array( &$this, 'add_admin_menu'));				// ADMIN MENU
			add_action( 'admin_init', 	array( $this, 'ogfe_register_settings' ));			// REGISTER SETTINGS
			//new Admin_notice('ok', 'error');
			$this->get_ogfe_options();
		}
		
		
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
		 * FUNCTION CREATE FB EVENT POT TYPE
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
		
		
		
		/**
		 * FUNCTION EVENTS PAGE
		 * List event objects from fb request
		 * @since 1.0
		 */
		public function events_page(){
			$events = $this->FB->get_events();
			if(!is_array($events)){
				$err = new Admin_notice('No events found', 'error');
			}
			include plugin_dir_path(__DIR__).'templates/events_page.php';
		}
		
		
		/**
		 * FUNCTION SETTINGS PAGE
		 * Load settings_page template
		 * @since 1.0
		 */
		public function settings_page(){
			include plugin_dir_path(__DIR__).'templates/settings_page.php';
		}

	}