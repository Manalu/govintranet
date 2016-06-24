<?php
/* Template name: How do I? classic page */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

<div class="col-sm-12 white">
	<div class="row">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>
	</div>
	<div class="content-wrapper">
		<h1><?php  the_title(); ?></h1>
		<?php  the_content(); ?>
	</div>					

	<div class="category-search">
		<div class="well well-sm">
			<form class="form-horizontal" role="form" method="get" id="task-alt-search" action="<?php echo site_url('/'); ?>">
				<div class="input-group">
					 <label for="sbc-s" class="sr-only"><?php _e('Search for' , 'govintranet'); ?></label>
					<input type="text" value="" name="s" id="sbc-s" class="multi-cat form-control input-md" placeholder="<?php _e('Search' , 'govintranet'); ?>" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
					 <span class="input-group-btn">
					 <input type="hidden" name="post_types[]" value="task" />
					 <label for="searchbutton2" class="sr-only"><?php _e('Search' , 'govintranet'); ?></label>
			    	 <?php
				    	 $icon_override = get_option('options_search_button_override', false); 
				    	 if ( isset($icon_override) && $icon_override ):
					    	 $override_text = get_option('options_search_button_text', __('Search' , 'govintranet') );
							 ?>
					 		<button class="btn btn-primary" id="searchbutton2" type="submit"><?php echo esc_attr($override_text); ?></button>
						 	<?php 
				    	 else:
					    	 ?>
					 		<button class="btn btn-primary" id="searchbutton2" type="submit"><span class="dashicons dashicons-search"></span><span class="sr-only"><?php _e('Search' , 'govintranet'); ?></span></button>
						 	<?php 
						 endif;
						 ?>
					 </span>
				</div><!-- /input-group -->
			</form>
		</div>
		<script type='text/javascript'>
		    jQuery(document).ready(function(){
				jQuery('#task-alt-search').submit(function(e) {
				    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
				        e.preventDefault();
				        jQuery('#sbc-s').focus();
				    }
				});	
			});	
		
		</script>	

	</div>
</div>


<?php
// Display category blocks

$catcount = 0;
$terms = get_terms('category',array("hide_empty"=>true,"parent"=>0,"orderby"=>"slug"));
	if ($terms) {
  		foreach ((array)$terms as $taxonomy ) {
  		    $themeid = $taxonomy->term_id;
  		    $themeURL= $taxonomy->slug;
   		    if ($themeid == 1) {
	  		    continue;
  		    }
  		    $catcount++;
  		    if ($catcount==4) $catcount=1;
  		    if ($catcount==1) echo "<div class='col-sm-12 white'><br>";
  			echo "
			<div class='col-sm-4 white";
			if ($catcount==3){
				echo ' last';
			} 
			echo "'>
				<div class='category-block'>
					<a class='btn btn-primary t" . $taxonomy->term_id ."' href='".get_term_link($taxonomy->slug, 'category')."'>".$taxonomy->name."</a>
					<p>".$taxonomy->description."</p>
				</div>
			</div>";
			if ( $catcount == 3 ){
				echo '</div>';
			}
		}
		if ($catcount==3) echo "<div class='col-sm-12 white'><br>";
		if ($catcount==2){
			echo "</div>";
			echo "<div class='col-sm-12 white'><br>";
		}
		if ($catcount==1){
			echo "</div></div>";
			echo "<div class='col-sm-12 white'><br>";
		}						
	}  

// Big tag cloud
?>

<div class="col-sm-12">

	<?php 
	$taghtml = "";
	$taghtml = get_transient("ht_how_do_i_tags");
	if ( !$taghtml ):
		$taskcloud = get_option('options_module_tasks_showtags');
		if ( $taskcloud ):
			$taghtml = gi_howto_tag_cloud('task');
		else:
			$taghtml =  my_colorful_tag_cloud('','category','task'); 
		endif;
		set_transient("ht_how_do_i_tags", $taghtml, 60*60);
	endif;
	if ($taghtml):
		echo "<div class='browsetags'><h3>" . __('Search by tag' , 'govintranet') . "</h3>";
		echo $taghtml;
		echo "</div><br><br></div><br>";
	endif;
	?>
</div>
<?php 
if ($catcount == 3) echo "</div>";
if ($catcount == 2) echo "</div></div>";
if ($catcount == 1) echo "</div>";
?>			

<?php endwhile; ?>

<?php get_footer(); ?>