<?php
	 
	 
	 
	 
	 
	 
	 
?>
	<!--
	<form method="post" action="options.php">
		<?php settings_fields( 'ogfe-settings-group-test' ); ?>
		<input type="text" name="ogfe_options_test[text1]" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'ogfe-plugin' ); ?>" />
			</p>
		
	</form>
	-->
	
	<div class="wrap"> 
		
		<h2><?php _e( 'FB Events', $this->plugin_name ) ?></h2>
		
		<form method="post" action="options.php" id="ogfe_form">
			<?php settings_fields( 'ogfe-settings-group' ); ?>
			
			<!-- FB CREDENTIALS -->
			<div class="ogf-form-block">
				<div class="ogfe-form-block-header">
					FB CREDENTIALS
					<?php if($this->FB->official_fb_plugin_credentials){ ?>
							<p class="description">
								Your credentials are allready set in the facebook plugin
							</p>
					<?php } ?>
				</div>
				<div class="ogfe-form-block-content">
					<table class="form-table ogfe-table">
						<tr valign="top"> 
							<th scope="row" class="ofge-table-th">
								<?php _e( 'App Id', $this->plugin_name ) ?>
							</th> 
							<td>
								<?php if($this->FB->official_fb_plugin_credentials){ ?>
									<p><?php echo $this->FB->app_id; ?></p>
								<?php }else{ ?>
									<input type="text" name="ogfe_options[credentials][app_id]" value="<?php echo $this->FB->app_id; ?>" />
									<p class="description">
										An application identifier associates your site, its pages, and visitor actions with a registered Facebook application.
									</p>
								<?php } ?>
							</td>
						</tr>
						<tr valign="top"> 
							<th scope="row" class="ofge-table-th">
								<?php _e( 'App Secret', $this->plugin_name ) ?>
							</th> 
							<td>
								<?php if($this->FB->official_fb_plugin_credentials){ ?>
									<p><?php echo $this->FB->app_secret; ?></p>
								<?php }else{ ?>
									<input type="text" name="ogfe_options[credentials][app_secret]" value="<?php echo $this->FB->app_secret; ?>" />
									<p class="description">
										An application secret is a secret shared between Facebook and your application, similar to a password.
									</p>
								<?php } ?>
							</td>
						</tr>
						<?php
						 if(!empty($this->FB_session)) { ?>
						<tr>
							<th class="ofge-table-th">
								<?php _e( 'Status', $this->plugin_name ) ?>
							</th>
							<td>
								<?php 
								
								if(!empty($this->FB_session) && $this->FB_session != 1){ ?>
										<div class="fb-session-notice fb-session-error">
											Invalid APP ID and/or APP SECRET given ! 
											<?php //print_r($this->FB_session); ?>
										</div>
								<?php }elseif($this->FB_session == 1){ ?>
										<div class="fb-session-notice fb-session-success">
											Your crendetials are validated
										</div>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
				<div style="clear:both"></div>
			</div>
			
			<div <?php if(!$this->FB_session || $this->FB_session != 1) { ?> style="display:none" <?php } ?> >
			
				<!-- FB PAGE IDS -->
				<div class="ogf-form-block">
					<div class="ogfe-form-block-header">
						<?php _e('FB PAGE & PROFIL IDs', $this->plugin_name); ?>
					</div>
					<div class="ogfe-form-block-content">
						<table class="form-table">
							<tr valign="top">
								<th scope="row" style="font-weight: normal">
									<textarea name="ogfe_options[fb][fb_page_ids]" rows="1" cols="40"><?php echo $this->options['fb']['fb_page_ids']; ?></textarea>
									<?php 
									$pages_id = explode(',', $this->options['fb']['fb_page_ids']);
									foreach($pages_id as $id){
										if(!$this->FB->is_page_id_valide($id)){ ?>
											<div class="fb-session-notice fb-session-error">
												Be careful, <b>"<?php echo trim($id); ?>"</b> is not a valide facebook page 
											</div>
										<?php }
									}
									?>
									<p class="description">
										<?php _e('Type your facebook page/profile ids or name or url, separate with ",". Ex: "123456789, 123456789", "my_page_name, my_other_page_name", "https://www.facebook.com/my_page_name, https://www.facebook.com/my_other_page_name"', $this->plugin_name); ?>
									</p>
								</th>
								<td>
									
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both"></div>
				</div>
			
				<!-- OPTIONS -->
				<div class="ogf-form-block">
					<div class="ogfe-form-block-header">
						<?php _e('OPTIONS', $this->plugin_name ); ?>
					</div>
					<div class="ogfe-form-block-content">
						
						<table class="form-table">
							<tr valign="top"> 
								<th scope="row" class="ofge-table-th">
									<?php _e( 'Post Type', $this->plugin_name ); ?>
									
								</th> 
								<td>
									<select name="ogfe_options[wp][post_type]">
										<?php 
										if(is_array($this->post_types)){
											foreach($this->post_types as $post_type){
												if($post_type == 'attachment' || $post_type =='media')
													continue;
												echo '<option value="'.$post_type.'" '.og_selected($post_type, $this->options['wp']['post_type']).'>';	
												echo $post_type;
												echo '</option>';
											}
										}
										?>
									</select>
									<p class="description">
										Choose the default POST_TYPE to create the imported events
									</p>
								</td>
							</tr>
							<tr valign="top"> 
								<th scope="row" class="ofge-table-th">
									<?php _e( 'Post Status', $this->plugin_name ); ?>
									
								</th> 
								<td>
									<select name="ogfe_options[wp][post_status]">
										<?php $post_status = array('publish', 'pending', 'draft', 'future', 'private');
										foreach($post_status as $status){
											echo '<option value="'.$status.'" '.og_selected($status, $this->options['wp']['post_status']).'>';
											echo $status;
											echo '</option>';
										}
										?>
									</select>
									<p class="description">
										Choose the default POST_STATUS to create the imported events
									</p>
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both"></div>
				</div>
			
				<!-- EVENTS EDGES -->
				<div class="ogf-form-block">
					<div class="ogfe-form-block-header">
						<?php _e('OPTIONAL INFOS ABOUT YOUR EVENTS' , $this->plugin_name ); ?>
					</div>
					<div class="ogfe-form-block-content">
						
						<table class="form-table">
							<?php 
							foreach ($this->options['fb']['event_edges'] as $name=>$edge) : ?>
							<tr valign="top"> 
								<th scope="row" class="ofge-table-th">
									<?php _e(ucfirst($name), $this->plugin_name ) ?>
								</th> 
								<td> 
									<fieldset>
										<label for="ogfe_options-fb-event-edges-<?php echo $name; ?>">
											<input type="checkbox" name="ogfe_options[fb][event_edges][<?php echo $name; ?>][value]" id="ogfe_options-fb-event-edges-<?php echo $name; ?>"<?php echo checked( $edge['value'], "on" ); ?> />
											<?php echo $edge['description']; ?>
										</label>
										<input type="hidden" name="ogfe_options[fb][event_edges][<?php echo $name; ?>][description]" value="<?php echo $edge['description']; ?>" />
									</fieldset>
								</td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
					<div style="clear:both"></div>
				</div>
				
			</div>
			
			<!-- SUBMIT -->
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'ogfe-plugin' ); ?>" />
			</p>
		</form>
		
	</div>
