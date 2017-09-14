<?php
	/**
	 * CLASS ADMIN NOTICE
	 * Used to display update or erro notice in admin interface
	 */
	class Admin_notice{
		
		public $notice = '';
		public $type	 = '';
		
		/**
		 * FUNCTION __CONSTRUCT
		 * @param $message (string) : notice to display
		 * @param $type (string) : class to add at the div
		 * 		  accepted : 'error', update, 'update-nag'
		 * @link  http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
		 */
		public function __construct($notice, $type){
			$this->notice 	= $notice;
			$this->type 	= $type;
			$this->admin_notice();
			//add_action('admin_notices', array( &$this, 'admin_notice'));
		}
		
		public function admin_notice(){
			 ?>
		    <div class="<?php echo $this->type; ?>">
		        <p>
		        	<?php echo $this->notice; ?>
		        </p>
		    </div>
		    <?php
		}
	}