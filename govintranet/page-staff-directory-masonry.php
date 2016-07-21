<?php
/* Template name: Staff directory flexible */

wp_enqueue_script( 'masonry',1 );
					
get_header(); ?>

<?php 



$fulldetails=get_option('options_full_detail_staff_cards'); // 1 = show
$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$showgrade = get_option('options_show_grade_on_staff_cards'); // 1 = show 
$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
$sort = '';
if ( isset( $_GET["sort"] ) ) $sort = $_GET["sort"]; 
if (!$sort) $sort = "first";
$requestshow = "A";
if ( isset( $_REQUEST['show'] ) ) $requestshow = $_REQUEST['show'];
$grade = '';
if ( isset( $_GET["grade"] ) ) $grade = $_GET["grade"];  

if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<a class="sr-only sr-only-focusable" href="#gridcontainer"><?php echo _x('Skip to staff','Screen reader text','govintranet'); ?></a>
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-12">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) bcn_display(); ?>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="col-sm-4"><!-- blank --></div>
			<div class="col-sm-12 well well-sm">
			<div id="staff-search">
				<div class="col-sm-8">
					<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php if ( function_exists('relevanssi_do_query') ) { echo site_url('/'); } else { echo site_url( '/search-staff/' ); } ?>">
						<div class="input-group">
							<label for="s2" class="sr-only"><?php _e('Search staff' , 'govintranet' ); ?></label>
					    	<input type="text" class="form-control pull-left" placeholder="<?php _e('Name, job title, skills, team, number...' , 'govintranet' ); ?>" name="<?php if ( function_exists('relevanssi_do_query') ) { echo "s"; } else { echo "q"; } ?>" id="s2">
					    	<input type="hidden" name="include" value="user">
					    	<input type="hidden" name="post_types[]" value="team">
							<span class="input-group-btn">
							<label for="searchbutton2" class="sr-only"><?php _e('Search','govintranet'); ?></label>
							<button class="btn btn-primary" type="submit" id="searchbutton2"><i class="dashicons dashicons-search"></i></button>
							 </span>
						</div><!-- /input-group -->
					</form>
				</div>
				<div class="col-sm-4">
					<?php
					$teams = get_posts('post_type=team&posts_per_page=-1&post_parent=0&orderby=title&order=ASC');
					if ($teams) {
						$otherteams='';
				  		foreach ((array)$teams as $team ) {
				  			$otherteams.= " <li><a href='".get_permalink($team->ID)."'>".govintranetpress_custom_title($team->post_title)."</a></li>";
				  		}  
				  		$teamdrop = get_option('options_team_dropdown_name');
				  		if ($teamdrop=='') $teamdrop = __("Browse teams","govintranet");
				  		echo '
						<div class="dropdown">
						  <button class="btn btn-info pull-right dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						    ' . $teamdrop . '
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">' . $otherteams . '
						  </ul>
						</div>					
						';
					}
					?>
				</div>
			</div>
		</div>
		</div><!--end-->	
	<script type='text/javascript'>
	    jQuery(document).ready(function(){
			jQuery('#searchform2').submit(function(e) {
			    if (jQuery.trim(jQuery("#s2").val()) === "") {
			        e.preventDefault();
			        jQuery('#s2').focus();
			    }
			});	
		});	
	</script>
		
		<div class="col-lg-4 col-md-4 col-sm-4">
			<!-- intentionally left blank -->
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12">
	<?php 
		global $wpdb;
		if ($sort == 'last' || $sort == 'first'){
			if ($sort == 'first') :
				$q = "select distinct left(meta_value,1) as letter from $wpdb->usermeta where meta_key = 'first_name' group by meta_value;";
				$liveletters = $wpdb->get_results($q,ARRAY_A);
			endif;
			if ($sort == 'last') :
				$q = "select distinct left(meta_value,1) as letter from $wpdb->usermeta where meta_key = 'last_name' group by meta_value;";
				$liveletters = $wpdb->get_results($q,ARRAY_A);
			endif;
			$live = array();
			foreach ($liveletters as $ll){
				$live[] = $ll['letter'];
			}
			$letters = range('A','Z');
			$activeletter = $requestshow;
			foreach($letters as $l) {
				if ($l == $activeletter) {
					$letterlink[$l] = "<li  class='{$l} active'><a href='?grade=".$grade."&amp;show=".$l."&amp;sort={$sort}'>".$l."</a></li>";
				} else {
					if (in_array($l, $live)){
						$letterlink[$l] = "<li  class='{$l}'><a href='?grade=".$grade."&amp;show=".$l."&amp;sort={$sort}'>".$l."</a></li>";
					} else {
							$letterlink[$l] = "<li  class='{$l} disabled'><a href='?grade=".$grade."&amp;show=".$l."&amp;sort={$sort}'>".$l."</a></li>";
					}
				}						
			}
				
			?>	

			<div class="col-lg-12 col-md-12 col-sm-12">
				<ul id='atozlist' class="pagination">
					<?php
					if ($sort == 'last'){
						$q = "select user_id, meta_value as name from $wpdb->usermeta where meta_key = 'last_name' and ucase(left(meta_value,1)) = '".strtoupper($requestshow)."' order by meta_value asc";
					} elseif ($sort == "first"){
						$q = "select user_id, meta_value as name from $wpdb->usermeta where meta_key = 'first_name' and ucase(left(meta_value,1)) = '".strtoupper($requestshow)."' order by meta_value asc";
					}					
					$userq = $wpdb->get_results($q,ARRAY_A);
					$html="<div class='row'>";
					foreach ((array)$userq as $u){ 
						$usergrade = get_user_meta($u['user_id'],'user_grade',true); 
						$gradecode = '';
						if ( $usergrade ) $gradecode = get_option('grade_'.$usergrade.'_grade_code', '');
						$title = $u['name'];
						$userid = $u['user_id'];
						$thisletter = strtoupper(substr($title,0,1));	
						$user_info = get_userdata($userid);
						if ( isset( $hasentries[$thisletter] ) ):
							$hasentries[$thisletter] = $hasentries[$thisletter] + 1;
						else: 
							$hasentries[$thisletter] = 1;
						endif;
						if (!$requestshow || (strtoupper($thisletter) == strtoupper($requestshow) ) ) {
							if ($sort == 'last'){
								$displayname = get_user_meta($userid ,'last_name',true ).", ".get_user_meta($userid ,'first_name',true );
							} else {
								$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );
							} 
							if ( ( ( isset( $usergrade['slug'] ) && $usergrade['slug'] == $grade) && ($grade ) ) || (!$grade)  ) {
								$gradedisplay='';
								if ($gradecode && $showgrade){
									$gradedisplay = "<span class='badge pull-right'>".$gradecode."</span>";
								}
								$avstyle="";
								if ( $directorystyle==1 ) $avstyle = " img-circle ";
								$avatarhtml = get_avatar($userid,66);
								$avatarhtml = str_replace(" photo", " photo alignleft ".$avstyle, $avatarhtml);
								if ($fulldetails){
									$html .= "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12 pgrid-item'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";
								// display team name(s)
								$poduser = get_userdata($userid);
								$team = get_user_meta($userid ,'user_team',true );
								if ($team) {				
									foreach ((array)$team as $t ) { 
							  		    $theme = get_post($t);
										$html.= "<a href='".get_permalink($theme->ID)."'>".govintranetpress_custom_title($theme->post_title)."</a><br>";
							  		}
								}  
								
								if ( get_user_meta($userid ,'user_job_title',true )) : 
									$meta = get_user_meta($userid ,'user_job_title',true );
									$html .= '<span class="small">'.$meta."</span><br>";
								endif; 
	
							
								if ( get_user_meta($userid ,'user_telephone',true )) $html.= '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
								if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $html.= '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span><br>";
								$html .= '<span class="small"><a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></span></p></div></div></div>';
								$counter++;
							} else {
								$avstyle="";
								if ( $directorystyle==1 ) $avstyle = " img-circle ";
								$avatarhtml = get_avatar($userid,66);
								$avatarhtml = str_replace(" photo", " photo alignleft ".$avstyle, $avatarhtml);
								$html .= "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12 pgrid-item'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong>".$gradedisplay."<br>";
								// display team name(s)
								$team = get_user_meta($userid,'user_team',true);
								if ($team){
									foreach ((array)$team as $t ) { 
							  		    $theme = get_post($t);
										$html.= govintranetpress_custom_title($theme->post_title)."<br>";
						  			}
						  		}
								if ( get_user_meta($userid ,'user_job_title',true )) {
									$meta = get_user_meta($userid ,'user_job_title',true );
									$html .= '<span class="small">'.$meta."</span><br>";
								}
								if ( get_user_meta($userid ,'user_telephone',true )) $html.= '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
								if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $html.= '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
								$html .= "</div></div></a></div></div>";
							}																							
						}
					}
				}

				echo @implode("",$letterlink); 
				?>
				
			</ul>
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div id="sortfilter">
			<div class="col-lg-4 col-md-5 col-sm-6">
				<strong><?php _e('Sort by' , 'govintranet'); ?>:&nbsp;</strong>
				<?php if ($sort=="first") : ?>
						<button type="button" class="btn btn-primary"><?php _e('First name' , 'govintranet'); ?></button>
				<?php else : ?>
						<a class='btn btn-default' href="<?php the_permalink(); ?>?sort=first&amp;show=<?php echo $requestshow ?>"><?php _e('First name' , 'govintranet'); ?></a>
				<?php endif; ?>

				<?php if ($sort=="last") : ?>
					<button type="button" class="btn btn-primary"><?php _e('Last name' , 'govintranet'); ?></button>
				<?php else : ?>
					<a class='btn btn-default' href="<?php the_permalink(); ?>?sort=last&amp;show=<?php echo $requestshow ?>"><?php _e('Last name' , 'govintranet' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>	
	<div class="col-lg-12 col-md-12 col-sm-12">
  		<?php 
	  	$output='<div id="gridcontainer"><div class="grid-sizer"></div>'.$html."</div>";
		echo $output;
		?>
	</div>

<?php
}
?>

<script lang="text/javascript">
jQuery(document).ready(function($){
var container = jQuery('#gridcontainer');
container.imagesLoaded(function(){
container.masonry({
		itemSelector: '.pgrid-item',
		gutter: 0,
		isAnimated: true
});
});
});
</script>

	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<?php the_content(); ?>
		</div>
	</div>

</div>

<?php endwhile; ?>

<?php get_footer(); ?>