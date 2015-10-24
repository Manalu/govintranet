<?php
/*
Plugin Name: HT Feature blogposts
Plugin URI: http://www.helpfultechnology.com
Description: Display blogposts
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com

#23-oct-2015
Pin posts added

*/

class htFeatureBlogposts extends WP_Widget {
    function htFeatureBlogposts() {
        parent::WP_Widget(false, 'HT Feature blogposts', array('description' => 'Blogpost listing widget'));
		if( function_exists('acf_add_local_field_group') ):

		acf_add_local_field_group(array (
			'key' => 'group_562a555eac132',
			'title' => 'Feature blog widget',
			'fields' => array (
				array (
					'key' => 'field_562a555eb8501',
					'label' => 'Pin posts',
					'name' => 'pin_posts',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array (
						0 => 'news',
						1 => 'blog',
						2 => 'event',
					),
					'taxonomy' => array (
					),
					'filters' => array (
						0 => 'search',
						1 => 'post_type',
					),
					'elements' => '',
					'min' => '',
					'max' => '',
					'return_format' => 'id',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'widget',
						'operator' => '==',
						'value' => 'htfeatureblogposts',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
		
		endif;		
	
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $thumbnails = $instance['thumbnails'];
        $freshness = $instance['freshness'];
        $more = $instance['more'];
        $excerpt = $instance['excerpt'];
		$tdate= getdate();
		$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
		$tday = date( 'd' , strtotime($tdate) );
		$tmonth = date( 'm' , strtotime($tdate) );
		$tyear= date( 'Y' , strtotime($tdate) );
		$tdate=$tyear."-".$tmonth."-".$tday." 00:00";
		$freshness = "-".$freshness." day";
        $tdate = date ( 'F jS, Y', strtotime ( $freshness . $tdate ) );  
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_pin_posts" ;  
		$top_slot = get_option($acf_key); 

		$num_top_slots = count($top_slot);
		$to_fill = $items - $num_top_slots;
		$k = -1;
		$alreadydone = array();
		$titledone = 0;
	
		if ( $num_top_slots > 0 ){ 
			if ( $title ) {
				echo $before_widget; 
				echo $before_title . $title . $after_title;
			}
			echo "<div class='widget-area widget-blogposts'>";
			$titledone = 1;
			foreach ((array)$top_slot as $thisslot){ 
				if (!$thisslot) continue;
				$post = get_post($thisslot); 
				if ($post->post_status != 'publish') continue;
				$k++;
				$alreadydone[] = $post->ID;
				$thistitle = get_the_title($post->ID);
				$edate = $post->post_date;
				$edate = date('j M Y',strtotime($edate));
				$thisURL=get_permalink($post->ID); 
				echo "<div class='media'>";
				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); 
					if (!$image_uri){
						$image_uri = get_avatar($post->post_author,72);
						$image_uri = str_replace("alignleft", "alignleft tinyblogthumb", $image_uri);
						echo "<a class='pull-left' href='".get_permalink($post->ID)."'>{$image_uri}</a>";		
					} else {
						echo "<a class='pull-left' href='".get_permalink($post->ID)."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";					}
				}
				echo "<div class='media-body'><a href='{$thisURL}'>".$thistitle."</a>";
				echo "<br><span class='news_date'>".$edate." by ";
				echo get_the_author();
				echo " <span class='badge'>Featured</span>";
				comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
				echo "</span>";
				echo "</span>";
				if ($excerpt == 'on') the_excerpt();
				echo "</div></div>";
			}		
		};
		
		//fetch fresh blogposts 

		$cquery = array(
		    'post_type' => 'blog',
			'posts_per_page' => $to_fill,
			'post__not_in' => $alreadydone,
			'date_query' => array(
					array(
						'after'     => $tdate,
						'inclusive' => true,
					)
				)
		);

		$news =new WP_Query($cquery);
		if ($news->post_count!=0 && !$titledone ) {
			if ( $title ) {
				echo $before_widget; 
				echo $before_title . $title . $after_title;
			}
			echo "<div class='widget-area widget-blogposts'>";
		}
		$k=0;
		while ($news->have_posts()) {
			$news->the_post();
			$k++;
			if ($k > $to_fill){
				break;
			}
			global $post;//required for access within widget
			$thistitle = get_the_title($post->ID);
			$edate = $post->post_date;
			$edate = date('j M Y',strtotime($edate));
			$thisURL=get_permalink($ID); 
			echo "<div class='media'>";
			if ($thumbnails=='on'){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
				if (!$image_uri){
					$image_uri = get_avatar($post->post_author,72);
					$image_uri = str_replace("alignleft", "alignleft tinyblogthumb", $image_uri);
					echo "<a class='pull-left' href='".get_permalink($post->ID)."'>{$image_uri}</a>";		
				} else {
					echo "<a class='pull-left' href='".get_permalink($post->ID)."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";				}
			}
			echo "<div class='media-body'><a href='{$thisURL}'>".$thistitle."</a>";
			echo "<br><span class='news_date'>".$edate." by ";
			echo get_the_author();
			comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
			echo "</span>";
			echo "</span>";
			if ($excerpt == 'on') the_excerpt();
			echo "</div></div>";
		}
		if ($news->have_posts() && $more){
			$landingpage = get_option('options_module_blog_page'); 
			if ( !$landingpage ):
				$landingpage_link_text = 'blogposts';
				$landingpage = site_url().'/blogposts/';
			else:
				$landingpage_link_text = get_the_title( $landingpage[0] );
				$landingpage = get_permalink( $landingpage[0] );
			endif;
			echo '<hr><p><strong><a title="{$landingpage_link_text}" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
		} 
		if ($news->have_posts()){
			echo '</div>';
			echo $after_widget;
		}
		wp_reset_query();								
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['thumbnails'] = strip_tags($new_instance['thumbnails']);
		$instance['freshness'] = strip_tags($new_instance['freshness']);
		$instance['more'] = strip_tags($new_instance['more']);
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
       return $instance;
    }

    function form($instance) {
		$title = esc_attr($instance['title']);
		$items = esc_attr($instance['items']);
		$thumbnails = esc_attr($instance['thumbnails']);
		$freshness = esc_attr($instance['freshness']);
		$more = esc_attr($instance['more']);
		$more = esc_attr($instance['excerpt']);
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
		<label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
		<label for="<?php echo $this->get_field_id('freshness'); ?>"><?php _e('Freshness (days)'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('freshness'); ?>" name="<?php echo $this->get_field_name('freshness'); ?>" type="text" value="<?php echo $freshness; ?>" /><br><br>
		<input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked((bool) $instance['excerpt'], true ); ?> />
		<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show excerpt'); ?></label> <br><br>
		<input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
		<label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show thumbnails'); ?></label> 
		<p>Displays the featured image if present, otherwise the author avatar.</p>
		<br><br>
		<input id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>" type="checkbox" <?php checked((bool) $instance['more'], true ); ?> />
		<label for="<?php echo $this->get_field_id('more'); ?>"><?php _e('Show link to more'); ?></label> <br><br>
        </p>
        <?php 
    }
}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureBlogposts");'));

?>