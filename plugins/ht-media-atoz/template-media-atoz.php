<?php
/* Template name: Document finder A to Z */

get_header();

$gisheight = get_option('options_widget_border_height');
if (!$gisheight) $gisheight = 7;
$gis = "options_header_background";
$gishex = get_theme_mod('header_background', '#0b2d49'); if ( substr($gishex, 0 , 1 ) != "#") $gishex="#".$gishex;
if ( $gishex == "#") $gishex = "#0b2d49";
$custom_css.= ".custom-background  { background-color: ".$gishex.";	}";
$headtext = get_theme_mod('header_textcolor', '#ffffff'); if ( substr($headtext, 0 , 1 ) != "#") $headtext="#".$headtext;
if ( $headtext == "#") $headtext = "#ffffff";

// set bar colour
// if using automatic complementary colour then convert header color
// otherwise use specified colour

if (get_option('options_complementary_colour')):
	$giscc = get_option('options_complementary_colour');
else:
	 $giscc = $gishex; 
endif;
$custom_css = "
.document-finder-filter { border-top: {$gisheight}px solid {$gishex}; }
.document-finder-filter-box { background: #eee; padding: 0 0 5px 0; margin-bottom: 17px; }
.document-finder-filter-box h3.widget-title,
.matoz-results h3.widget-title { background: {$gishex}; color: {$headtext}; padding-top: 10px; }
.matoz-results h3.widget-title small { color: {$headtext}; }
.pager li a:hover { background: {$giscc}; color: white; }
.pager li.active a { background: {$gishex}; color: {$headtext}; }
";
wp_register_style( 'ht-media-atoz', plugin_dir_url("/") ."ht-media-atoz/css/ht-media-atoz.css" );
wp_enqueue_style('ht-media-atoz');
wp_add_inline_style('ht-media-atoz' , $custom_css);	
wp_register_script( 'ht-media-atoz-js', plugin_dir_url("/") ."ht-media-atoz/js/ht_matoz.js" );
wp_enqueue_script('ht-media-atoz-js');



if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div class="col-lg-12 white ">
	<div class="row">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>
	</div>

	<h1><?php the_title(); ?></h1>
	<?php the_content(); 
		
	//get filter options
	$doctyp = 'any';
	$cat_slug = 'any';
	$matoz = 'any';
	
	if ( isset( $_GET['doctyp'] ) ) $doctyp = $_GET['doctyp'] ? $_GET['doctyp'] : 'any' ;
	if ( isset( $_GET['cat'] ) ) $cat_slug = $_GET['cat'] ? $_GET['cat'] : 'any' ;
	if ( isset( $_GET['matoz'] ) ) $matoz = $_GET['matoz'] ? $_GET['matoz'] : 'any' ;
	
	if ($cat_slug != "any") {
		$catterm = get_category_by_slug($cat_slug);
		$catname = $catterm->name;
		$catid = $catterm->term_id;
	} else {
		$catname = __("All","govintranet") . " <span class='caret'></span>";
	}	
	
	if ($doctyp != "any") {
		$dtterm = get_term_by('slug', $doctyp, 'document-type'); 
		$dtname = $dtterm->name;
		$dtid = $dtterm->term_id;
	} else {
		$dtname = __("All","govintranet") . " <span class='caret'></span>";
	}	

	$search = $_GET['q'];
	
	$is_filtered = true;
	if ( $cat_slug == "any" && $doctyp == "any" && $matoz == "any" && $search == "" ) $is_filtered = false;
	
	// get all document types for the left hand menu
	$args = array(
	    'orderby'       => 'name', 
	    'order'         => 'ASC',
	    'hide_empty'    => false,
	    );

	$subcat = get_terms( 'document-type', $args );
	$cathead = '';
	?>

	<div class="row">

		<div class="col-md-5">
			<div class="document-finder-filter-box doc_search">
				<h3 class="widget-title"><?php _e('Search','govintranet'); ?></h3>
				<div class="col-sm-12">
					<form class="form-horizontal" role="form" id="docsearchform" name="docsearchform" method="get">
		  
					  	<div class="input-group">
						 <label for="sdoc" class="sr-only"><?php _e('Search','govintranet'); ?></label>
				    	 <input type="text" class="form-control" name="q" id="q" value="<?php echo esc_attr($search);?>">
						 <span class="input-group-btn">
						<label for="docsearchbutton" class="sr-only"><?php _e('Search','govintranet'); ?></label>	 <div class="matoz">
				    	 <?php
					    	 $icon_override = get_option('options_search_button_override', false); 
					    	 if ( isset($icon_override) && $icon_override ):
						    	 $override_text = esc_attr(get_option('options_search_button_text', __('Search', 'govintranet') ));
								 ?>
						 		<button class="btn btn-primary" id="docsearchbutton" type="submit"><?php echo $override_text; ?></button>
							 	<?php 
					    	 else:
						    	 ?>
						 		<button class="btn btn-primary" id="docsearchbutton" type="submit"><span class="dashicons dashicons-search"></span><span class="sr-only"><?php _e('Search','govintranet'); ?></span></button>
							 	<?php 
							 endif;
							 ?></div>
					 	</span>
						</div><!-- /input-group -->
						<input type="hidden" name="doctyp" value="<?php echo $doctyp; ?>" />
						<input type="hidden" name="cat" value="<?php echo $cat_slug; ?>" />
						<input type="hidden" name="matoz" value="<?php echo $matoz; ?>" />
					</form>
			  	</div>
				<div class="clearfix"></div>
			</div>
			<div class="document-finder-filter-box doc_atoz">
				<h3 class="widget-title"><?php _e('A to Z','govintranet'); ?></h3>
				<div id="document_atoz">
					<div class="matoz">
						<ul id='matozbutton' class="pager">
						<?php 
						//fill the default a to z array
						$letters = range('a','z');
						$letterlink=array();
						$hasentries = array();
						
						foreach($letters as $l) { 
							$letterlink[$l] = "<li class='disabled'><a href='#'>".strtoupper($l)."</a></li>";
						}				
			
						$terms = get_terms('media-a-to-z',array("hide_empty"=>false));
						if ($terms) {
							foreach ((array)$terms as $taxonomy ) {
								$letterlink[$taxonomy->slug] = "<li";
								if (strtolower($matoz)==strtolower($taxonomy->slug)) $letterlink[$taxonomy->slug] .=  " class='active'";
								$letterlink[$taxonomy->slug] .=  "><a href='".get_permalink(get_the_id())."?doctyp={$doctyp}&cat={$cat_slug}&matoz=".$taxonomy->slug."&q={$search}'>".strtoupper($taxonomy->name)."</a></li>";
							}
						}
						$active = "";
						if ( $matoz == "any" ) $active = " class='active'";

						echo "<li".$active."><a href='".get_permalink(get_the_id())."?doctyp={$doctyp}&cat={$cat_slug}&matoz=any&q={$search}'>All</a></li>";
						echo @implode("",$letterlink); 
						?>
						</ul>
					</div>
				</div>
			</div>

	 		<div class="document-finder-filter-box doc_types">
				<h3 class="widget-title"><?php _e('Document type','govintranet'); ?></h3>
				<ul class='pager matoz' role="menu">
				<?php
				$active = "";
				if ( !$doctyp || $doctyp == "any" ) $active = " class='active'";
				if ($cat_slug){    
					echo "<li".$active."><a class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp=&cat={$cat_slug}&matoz=".$matoz."&q={$search}'>" . __('All' , 'govintranet') . "</a></li>";
				} else {
			       echo "<li".$active."><a  class='doctypespinner".$active."' href='".get_permalink(get_the_id())."?doctyp=&matoz=".$matoz."&q={$search}'>" . __('All' , 'govintranet') . "</a></li>";
				}
				foreach ($subcat as $sc) { 
					if ($doctyp == $sc->slug) {
				       echo "<li class='active'><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp={$sc->slug}&cat={$cat_slug}&matoz=".$matoz."&q={$search}'>";
				       echo "{$sc->name}</a></li>";
				    } else {
					    if ($cat_slug){   
					       echo "<li><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp={$sc->slug}&cat={$cat_slug}&matoz=".$matoz."&q={$search}'>";
					       echo "{$sc->name}</a></li>";
					    } else {
					       echo "<li><a  class='doctypespinner' href='".get_permalink(get_the_id())."?doctyp={$sc->slug}&matoz=".$matoz."&q={$search}'>{$sc->name}</a></li>";
						}
					}
				}
				?>
				</ul>
			</div>
			<?php
			$taxonomies[]='category';
			$post_type[]='attachment';
			$post_cat = get_terms_by_media_type( $taxonomies, $post_type);
			if ($post_cat){ ?>
			<div class="document-finder-filter-box doc_cats">
				<h3 class="widget-title"><?php _e('Category','govintranet'); ?></h3>
				
				<ul class='nav nav-pills nav-stacked matoz white' role="menu">
				<?php
				$active = "";
				if ( !$cat_slug || $cat_slug == "any" ) $active = " class='active'";
				if ($doctyp !='any' ) {
					echo "<li".$active."><a class='doccatspinner' href='".get_permalink(get_the_id())."?doctyp=".$doctyp."&matoz=".$matoz."&q={$search}'>" . __('All','govintranet') . "</a></li>";
				} else {
					echo "<li".$active."><a class='doccatspinner' href='".get_permalink(get_the_id())."?matoz=".$matoz."&q={$search}'>" . __('All','govintranet') . "</a></li>";
				}
				foreach($post_cat as $cat){ 
					if ($cat->term_id > 1 && $cat->name){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						if ($doctyp) { // show chosen doc type as selected
							if ($cat->slug == $cat_slug) {
								echo "<li class='active'><a class='doccatspinner' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&doctyp=".$doctyp."&matoz=".$matoz."&q={$search}'><span class='brd" . $cat->term_id . "'></span>&nbsp;";
								echo $newname."</a></li>";
							} else {
								echo "<li><a class='doccatspinner' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&doctyp=".$doctyp."&matoz=".$matoz."&q={$search}'><span class='brd" . $cat->term_id . "'></span>&nbsp;";
								echo $newname."</a></li>";
							}
						} else {
							echo "<li><a class='doccatspinner' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&matoz=".$matoz."&q={$search}'><span class='brd" . $cat->term_id . "'></span>&nbsp;";
							echo $newname."</a></li>";
						}
					}
				}
				?>
				</ul>
			</div><?php
			}
			?> 			
		</div>
		<div class="col-md-7">
			<div class='matoz-results'>
				<?php
				
				/* BUILD THE QUERY BASED ON FILTERS */
				
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
				if ( $cat_slug != 'any' ) {
					if ( $doctyp != 'any' ){	// cat and doc type
				
						//tax queries need to be meta queries for document_type
						
						$docfinder = array(
						'post_type'=>'attachment',
						'orderby'=>'title',
						'order'=>'ASC',
					    'posts_per_page' => -1,
						'post_status'=>'inherit',
					    'tax_query'=>array(
					    array(  
					    'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => $cat_slug)),
						'meta_query'=>array(
					    array(  
					    'key' => 'document_type',
					    'compare'=>"LIKE",
						'value' => '"' .$dtid.'"' )),
						);
		
					} else {
						
						// single cat
						
						$inlist=array();
						foreach ( $subcat as $term ) {
							$inlist[] = $term->term_id; 
						}
						$docfinder = array(
							'post_type'=>'attachment',
							'orderby'=>'title',
							'order'=>'ASC',
							'posts_per_page' => -1,
							'post_status'=>'inherit',
							'tax_query'=>array(
							array(  
							'taxonomy' => 'category',
							'field' => 'slug',
							'terms' => $cat_slug,
							)),
							'meta_query'=>array(
						    array(  
						    'key' => 'document_type',
							'value' => '',
							'compare' => '!=',
							)),
							);
					}
		
				} else {
					
					if ( $doctyp != 'any' ){	// single doc type
						$docfinder = array(
						'post_type'=>'attachment',
						'orderby'=>'title',
						'order'=>'ASC',
					    'posts_per_page' => -1,
						'post_status'=>'inherit',
						'post_mime_type' => array( 'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/x-icon', 'application' ),
						'meta_query'=>array(
					    array(  
					    'key' => 'document_type',
					    'compare'=>"LIKE",
						'value' => '"' .$dtid.'"' ))
						);	
					} else {
		
						// no filter
						
						$inlist=array(); 
					    foreach ( $subcat as $term ) {
					       $inlist[] = $term->term_id; 
						}
						$docfinder = array(
							'post_type'=>'attachment',
							'orderby'=>'title',
							'order'=>'ASC',
					        'posts_per_page' => -1,
							'post_status'=>'inherit',
							'meta_query'=>array(
						    array(  
						    'key' => 'document_type',
							'value' => '',
							'compare' => '!=',
							)),
						);	
						
					}
					
				}
		
				if ( $matoz != "any" ){
					if ( $docfinder['tax_query'] ){
						$docfinder['tax_query'] = array("relation"=>"AND", $docfinder['tax_query'][0], array(
								'taxonomy' => 'media-a-to-z',
								'field' => 'slug',
								'terms' => $matoz,
							));
					} else {
						$docfinder['tax_query'] = array(array(
								'taxonomy' => 'media-a-to-z',
								'field' => 'slug',
								'terms' => $matoz,
							));
					}
				}

				if ( $search ){
					$docfinder['s'] = $search;
				}		
				
				$docs = get_posts($docfinder);
				
				$postsarray = array();
				
				if ( isset($docs) ) foreach($docs as $doc){ 
					if (!is_object($doc)):
						$postsarray[]=$doc['ID'];
					else:
						$postsarray[]=$doc->ID;
					endif;
				};
		
				if (count($docs) == 0 ) {
					$postsarray[]='';
				}
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$counter = 0;	
				if ( $cat_slug != "any" || $doctyp != "any" || $matoz != "any" ):
					$max_posts = -1;
					$paged = 1;
				else:
					$max_posts = 25;
				endif;
				$docs = new wp_query(array('orderby'=>'title','order'=>'ASC','post_status'=>'inherit','posts_per_page'=>$max_posts,'paged'=>$paged,'post_type'=>'attachment','post__in'=>$postsarray));
				if ($docs->found_posts == 0 ) {
					?>
					<h3 class="widget-title">
						<a class="btn btn-primary btn-sm pull-right matoz" href="<?php echo get_permalink();?>"><?php _e('Reset filters','govintranet'); ?></a>
						<?php _e('No results' , 'govintranet'); ?>
					</h3>
					<?php
				
				} else {
					?>
					<h3 class="widget-title">

						<?php
						if ( $is_filtered  ):
							printf( esc_html( _n( '%d result', '%d results', $docs->found_posts, 'govintranet'  ) ), $docs->found_posts );
							?>
							<a class="btn btn-primary btn-sm pull-right matoz" href="<?php echo get_permalink();?>"><?php _e('Reset filters','govintranet'); ?></a>
						<?php						
						else:
							_e('All results','govintranet');
						endif;
						?>
						</h3>
					<?php
				}
				if ( $cat_slug!="any" ):
					echo "<a class='btn btn-primary btn-sm pull-right matoz' href='".get_permalink(get_the_id())."?doctyp={$doctyp}&cat=&matoz={$matoz}'>".strtoupper($catname)." <span class='badge small'>X</span></a>";
				endif;
				if ( $doctyp!="any" ):
					echo "<a class='btn btn-primary btn-sm pull-right matoz' href='".get_permalink(get_the_id())."?doctyp=&cat={$cat_slug}&matoz={$matoz}'>".strtoupper($dtname)." <span class='badge small'>X</span></a>";
				endif;
				if ( $matoz!="any" ):
					echo "<a class='btn btn-primary btn-sm pull-right matoz' href='".get_permalink(get_the_id())."?doctyp={$doctyp}&cat={$cat_slug}'>".strtoupper($matoz)." <span class='badge small'>X</span></a>";
				endif;
				?>						
				<div class="clearfix"></div>
				<?php if ($docs->found_posts == 0 ) echo '<p id="docresults">' . __('Nothing to show' , 'govintranet') . ".</p>"; ?>
				<div id="docspinner" class="col-sm-12 hidden">
					<img src="<?php echo includes_url('/images/spinner-2x.gif'); ?>" alt="<?php esc_attr_e('Please wait' , 'govintranet') ;?>" />
				</div>
				<?php
				echo '<div id="docresults"><ul class="docmenu">';
			
				if ( $docs->have_posts() ) while ( $docs->have_posts() ) : $docs->the_post(); 
					echo '<li class="docresult"><a href="'.wp_get_attachment_url().'">';
					echo esc_html($post->post_title);
					echo '</a></li>';
				endwhile;
			
				echo '</ul></div>';
			
				wp_reset_query();
		
				?>
				<?php if ( $docs->max_num_pages > 1 ) : ?>
					<?php if (function_exists(wp_pagenavi)) : ?>
						<?php wp_pagenavi(array('query' => $docs)); ?>
					<?php else : ?>
						<?php next_posts_link(__('&larr; Older items','govintranet'), $docs->max_num_pages); ?>
						<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $docs->max_num_pages); ?>						
					<?php endif; ?>
				<?php endif; ?>
		
			</div>
		</div>
	</div>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>