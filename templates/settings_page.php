	<div class="wrap"> 
		<h2><?php _e( 'FB Events', $this->plugin_name ) ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'ogfe-settings-group' ); ?>
			
			<!-- PLUGIN SETTIGNS -->
			<table class="form-table">
				<tr valign="top"> 
					<th scope="row">
						<?php _e( 'POST TYPE', $this->plugin_name ) ?>
					</th> 
					<td>
						<input type="text" name="ogfe_options[wp][post_type]" value="<?php echo $this->options['wp']['post_type']; ?>" />
					</td>
				</tr>
				<tr valign="top"> 
					<th scope="row">
						<?php _e( 'POST STATUS', $this->plugin_name ) ?>
					</th> 
					<td>
						<input type="text" name="ogfe_options[wp][post_status]" value="<?php echo $this->options['wp']['post_status']; ?>" />
					</td>
				</tr>
				<tr valign="top"> 
					<th scope="row">
						<?php _e( 'CAPABILITY', $this->plugin_name ) ?>
					</th> 
					<td>
						<input type="text" name="ogfe_options[wp][capability]" value="<?php echo $this->options['wp']['capability']; ?>" />
					</td>
				</tr>
			</table>
			
			<!-- FB CREDENTIALS -->
			<table class="form-table">
				<tr valign="top"> 
					<th scope="row">
						<?php _e( 'APP ID', $this->plugin_name ) ?>
					</th> 
					<td>
						<input type="text" name="ogfe_options[app_id]" value="<?php echo $this->FB->app_id; ?>" />
					</td>
				</tr>
				<tr valign="top"> 
					<th scope="row">
						<?php _e( 'APP SECRET', $this->plugin_name ) ?>
					</th> 
					<td>
						<input type="text" name="ogfe_options[app_secret]" value="<?php echo $this->FB->app_secret; ?>" />
					</td>
				</tr>
			</table>
			
			<!-- EVENTS EDGES -->
			<table class="form-table">
				<?php foreach ($this->options['fb']['event_edges'] as $name=>$edge) : 
					
					?>
				<tr valign="top"> 
					<th scope="row">
						<?php _e(ucfirst($name), $this->plugin_name ) ?>
					</th> 
					<td> 
						<input type="checkbox" name="ogfe_options[fb][event_edges][<?php echo $name; ?>][value]" <?php echo checked( $edge['value'], "on" ); ?> />
						
						<input type="hidden" name="ogfe_options[fb][event_edges][<?php echo $name; ?>][description]" value="<?php echo $edge['description']; ?>" />
						<p class="description"><?php echo $edge['description']; ?></p>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			
			
			
			
			<!-- SUBMIT -->
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'ogfe-plugin' ); ?>" />
			</p>
		</form>
		
		
		
		
	</div>
	<div class="warp">
		<?php 
		$post_types = get_post_types(array('public' => true));
		if(is_array($post_types)){
			foreach($post_types as $post_type){
				if($post_type == 'attachment' || $post_type =='media')
					continue;
				echo '<br>';	
				print_r($post_type);
			}
		}
		
		?>
	</div>
	