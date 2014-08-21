<?php
/*
Profile Nudge Update 
*/


require_once('../../../wp-blog-header.php');

// We need to verify the nonce.
$nonce = $_REQUEST['_wpnonce'];
$userid = $_POST['userid'];
if (check_ajax_referer('update-user-'.$userid, 'nonce', false ) ) {
    // This nonce is not valid.
    die( 'Security check - there is something wrong' ); 
} else {
    // The nonce was valid.
    // Do stuff here.
    
    if ($_POST['type']=='add-grade'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$usergrade = $_POST['grade']; 
		if ($usergrade==0){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
	    add_user_meta($current_userid,'user_grade',$usergrade); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}

    if ($_POST['type']=='add-team'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$team = $_POST['team']; 
		if ($team==0){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
	    add_user_meta($current_userid,'user_team',$team); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}


    if ($_POST['type']=='add-skills'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$skills = $_POST['key_skills']; 
		if ($skills==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
		$skills = sanitize_text_field($skills);
	    add_user_meta($current_userid,'user_key_skills',$skills); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}


    if ($_POST['type']=='add-job-title'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$jobtitle = $_POST['job_title']; 
		if ($jobtitle==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
		$jobtitle = sanitize_text_field($jobtitle);
	    add_user_meta($current_userid,'user_job_title',$jobtitle); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}
	

    if ($_POST['type']=='add-bio'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$bio = $_POST['bio']; 
		if ($bio==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
		$bio = sanitize_text_field($bio);
	    update_user_meta($current_userid,'description',$bio); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}
	
	
    if ($_POST['type']=='add-phone'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$phone = $_POST['phone']; 
		if ($phone==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
		$phone = sanitize_text_field($phone);
	    add_user_meta($current_userid,'user_telephone',$phone); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}

    if ($_POST['type']=='add-mobile'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$phone = $_POST['mobile']; 
		if ($phone==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( 'Security check - can\'t check your identity.' ); 	
		}
		$phone = sanitize_text_field($phone);
	    add_user_meta($current_userid,'user_mobile',$phone); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}
	
		
	$referer = $_SERVER['HTTP_REFERER'];
	wp_redirect($referer);

	
}



?>