<h1>Events page</h1>

<?php 
	if(!is_array($pages_events)){
		return;
	}
?>
	
	

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox" />
			</th>
			<th scope='col'>
				Cover
			</th>
			<th scope='col' id='title' class='manage-column column-title sortable desc'  style="">
				<a href="http://www.valentinrocher.com/events-wp/wp-admin/edit.php?orderby=title&#038;order=asc">
					<span>Title</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th>Status</th>
			<th scope='col' id='author' class='manage-column column-author'  style="">
				Date
			</th>
			<th scope='col' id='categories' class='manage-column column-categories'  style="">
				Owner
			</th>
			<th scope='col' id='tags' class='manage-column column-tags'  style="">
				Location
			</th>
		</tr>
	</thead>
	<tbody id="the-list">
		<?php foreach($pages_events as $page_name=>$events) : ?>
			<tr>
				<td colspan="7" style="font-weight: bold; text-align: center; background-color: rgb(250,250,250); border-bottom: 1px solid rgb(240,240,240);">
					<?php echo ucfirst($page_name); ?>
				</td>
			</tr>
			<?php foreach($events as $event) : 
				
				
				
				?>
			<tr id="post-1" class="type-post">
				
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1">Select</label>
						<input id="cb-select-1" type="checkbox" name="post[]" value="1">
						<div class="locked-indicator"></div>
					</th>
					<td>
						<?php if(empty($event->cover)){ _e('No cover found on facebook', $this->plugin_name); } ?>
						<img src="<?php echo $event->cover['source']; ?>"  height="60"/>
					</td>
					<td>
						<a href="https://www.facebook.com/events/<?php echo $event->id; ?>" target="blank"><?php echo $event->name; ?></a>
					</td>
					<td>
						<?php 
							if(empty($this->is_event_exist($event))) echo '<b><span style="color:#5CC841">NEW !</span</b>';
							else echo "Allready created in WP";
						 ?>
					</td>
					<td>
						<?php echo substr($event->start_time,0,10); ?>
					</td>
					<td>
						<?php echo $event->owner->name; ?>
					</td>
					<td>
						<?php echo $event->location; ?>
					</td>
				</tr>	
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>		
</table>
