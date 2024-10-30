<?php
/*
Plugin Name: MMAILER
Plugin URI: http://ksg.lt
Description: It allows you to send bulk mail messages to multiple commentators addresses at once, bypasing ISP mailing limits...
Version: 1.2
Author: Mantas Usas
Author URI: http://ksg.lt/
*/


add_action( 'admin_menu', 'mmail_plugin_menu' );
add_site_option( 'mmail_last_mail', '0' );
add_site_option( 'mmail_sending_staus', '0' );
$mails_per_hour = 80; /// mails per hour
$mails_per_hour = round(60/$mails_per_hour*60); // calculaitin seconds interwall
add_site_option( 'mmail_between',  $mails_per_hour); /// seting time interwall
add_site_option( 'mmail_title', 'My test email' );
add_site_option( 'mmail_text', 'Just for fun.' );
add_site_option( 'mmail_from_mail', 'my_email@gmail.com' );
add_site_option( 'mmail_from_title', 'My name or website' );
mmailer_send_mail();


function mmail_plugin_menu() {

	
	$page_title = 'MMailer meniu';
	$menu_title = 'MMailer';
	$capability =  'add_users';
	$menu_slug =  'MMailer-options';
	$function =  'mmailer_plugin_main';
	$icon_url =  '';
	$position =  '';
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

	$parent_slug = $menu_slug;
	$page_title = 'Start mail';
	$menu_title = 'Start mail';
	$capability = 'add_users';
	$sub_menu_slug = 'mmailer-start-mail';
	$function = 'mmailer_sart_sending';
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $sub_menu_slug, $function);
	
	$parent_slug = $menu_slug;
	$page_title = 'Stop sending';
	$menu_title = 'Stop sending';
	$capability = 'add_users';
	$sub_menu_slug = 'mmailer-stop-sending';
	$function = 'mmailer_stop_sending';
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $sub_menu_slug, $function);

	$parent_slug = $menu_slug;
	$page_title = 'Reset sending';
	$menu_title = 'Reset sending';
	$capability = 'add_users';
	$sub_menu_slug = 'mmailer-reset-sending';
	$function = 'mmailer_reset_sending';
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $sub_menu_slug, $function);

	$parent_slug = $menu_slug;
	$page_title = 'Options';
	$menu_title = 'Options';
	$capability = 'add_users';
	$sub_menu_slug = 'mmailer-options';
	$function = 'mmailer_plugin_options';
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $sub_menu_slug, $function);
	

}

function mmailer_plugin_main() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '
	<div class="wrap">
	<div class="icon32" id="icon-plugins"><br></div>
	<h2>MMAILER</h2><BR>
	</div>
	Welcome to MMAILER and thankyou for chosing it.<BR>
	I wuold like to hear what you think about plugin and give some review.
	<a href="http://wordpress.org/support/view/plugin-reviews/mmailer?filter=5">Rate and review</a> 
	<ul class="wp-submenu wp-submenu-wrap">
	<li class="wp-first-item current">
	<a class="wp-first-item current" href="admin.php?page=MMailer-options">MMailer</a>
	</li>
	
	<li>
	<a href="admin.php?page=mmailer-start-mail">Start mail</a> - Activates sending function. So every time someone visit your website it will check for time interval and if ok it will send email to one user. 
	</li>
	
	<li>
	<a href="admin.php?page=mmailer-stop-sending">Stop sending</a> - Deactivates sending function. If you activate it again it will continuose sending from last email.
	</li>
	
	<li>
	<a href="admin.php?page=mmailer-reset-sending">Reset sending</a> - Resets all sending information so your emails will be send agian to all users. You have to do this every time you whant to send new email to user.
	</li>
	
	<li>
	<a href="admin.php?page=mmailer-options">Options</a> - Change email title, text, and other options.
	</li>
	</ul>
	
	';
	mmailer_send_mail();
}

function mmailer_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '
	<div class="wrap">
	<div class="icon32" id="icon-plugins"><br></div>
	<h2>MMAILER</h2><BR>
	</div>
	';
	if ($_POST['Save']=="Save") {
		update_site_option( 'mmail_title', $_POST['mmail_title']);
		update_site_option( 'mmail_text', $_POST['mmail_text']);
		update_site_option( 'mmail_between', $_POST['mmail_between']);
		update_site_option( 'mmail_from_mail', $_POST['mmail_from_mail']);
		update_site_option( 'mmail_from_title', $_POST['mmail_from_title']);
		Echo '<div class="updated below-h2" id="message" style="margin-left: 0px; width: 79%;"><p>Email settings updated.</p></div>';
	}
	
	
	$txt = "";
	$mmail_last_mail = get_site_option( 'mmail_last_mail');
	$mmail_sending_staus = get_site_option( 'mmail_sending_staus');
	// $mails_per_hour = 80; /// mails per hour
	// $mails_per_hour = round(60/$mails_per_hour*60); // calculaitin seconds interwall
	$mmail_between = get_site_option( 'mmail_between'); /// seting time interwall
	$mmail_title = get_site_option( 'mmail_title');
	$mmail_text = get_site_option( 'mmail_text');
	$mmail_from_mail = get_site_option( 'mmail_from_mail');
	$mmail_from_title = get_site_option( 'mmail_from_title');

	$mmail_last_mail = get_site_option( 'mmail_last_mail' );
	$now = date("Y-m-d H:i:s");
	$mmail_mail_diff = mmail_plugin_time_dif($mmail_last_mail, $now);
	$mailing_report=mmailer_list_mail();

	if ($mmail_sending_staus == 1) { $status = "Enabled";} else { $status = "Disabled";}
	
	$txt.="<strong>Email send by: </strong><input type='text' autocomplete='off' id='mmail_from_title' value='".$mmail_from_title."' size='30' name='mmail_from_title' style='width: 250px;'><br>";
	$txt.="<strong>Email send from email: </strong><input type='text' autocomplete='off' id='mmail_from_mail' value='".$mmail_from_mail."' size='30' name='mmail_from_mail' style='width: 250px;'><br>";
	$txt.="<strong>Sending staus: </strong>".$status."<br>";
	$txt.="<strong>Email send interval: </strong><input type='text' autocomplete='off' id='mmail_between' value='".$mmail_between."' size='30' name='mmail_between' style='width: 35px;'> sec.<br>";
	$txt.="<strong>Last mail send date: </strong>".$mmail_last_mail."<br>";
	$txt.="<strong>Last mail send: </strong>".$mmail_mail_diff." seconds before now.<br>";
	//$txt.="<strong>Email title: </strong>".$mmail_title."<br>";
	//$txt.="<strong>Email body: </strong>".$mmail_text."<br>";
	$txt.=$mailing_report['send_stat'];
	$txt2.='<button id="clickme">Detailed send info</button>';
	$txt2.='<div id="detailed" style="display:none">';
	$txt2.=$mailing_report['send_log'];
	$txt2.='</div>
	<script>
	$j = jQuery.noConflict();
	$j( "#clickme" ).click(function() {
		$j( "#detailed" ).slideToggle( "slow" );
	});
	</script>
	';
	$settings = array( 'wpautop' => false );
	$editor_id = 'mmail_text';
	echo '<div style="clear:both"></div>
	<form method="post" action="">
		<div id="titlediv">
			<div id="titlewrap">
				<input type="text" autocomplete="off" id="title" value="'.$mmail_title.'" size="30" name="mmail_title" style="width: 80%;">
			</div>
		</div>';
		wp_editor( $mmail_text, $editor_id, $settings );
		echo '<input type="submit" accesskey="p" value="Save" class="button button-primary button-large" id="Save" name="Save"><div style="clear:both"></div>';
	echo $txt.'<br>';
	echo '<input type="submit" accesskey="p" value="Save" class="button button-primary button-large" id="Save" name="Save">';
	echo '</form><BR>';
	echo $txt2;
}

function mmailer_stop_sending() {
global $wpdb;
update_site_option( 'mmail_sending_staus', '0' );
}

function mmailer_sart_sending() {
global $wpdb;
$sql="DELETE FROM `wp_usermeta` WHERE `meta_key` LIKE 'email_recieved'";
$db = $wpdb->get_results($sql);
update_site_option( 'mmail_sending_staus', '1' );
}

function mmailer_reset_sending() {
global $wpdb;
$sql="DELETE FROM `wp_usermeta` WHERE `meta_key` LIKE 'email_recieved'";
$db = $wpdb->get_results($sql);
}


function mmailer_list_mail () {
	global $wpdb;
	$count = 0;
	$send = 0;
	$not_send = 0;
	$txt['send_log'] = "";
	$sql="select * from wp_users";
	$db = $wpdb->get_results($sql);
	foreach ($db as $db) {
		$email_send = get_user_meta( $db->ID, 'email_recieved', true ); 
		if ($email_send != '') { 
			$txt['send_log'].="<strong>Email send: </strong>".$db->user_email." (".$email_send.")<br>";
			$send++;
		} else {
			$txt['send_log'].="<strong>Email not send: </strong>".$db->user_email."".$to."<br>";
			$not_send++;
		}
	  $count++;
	}
	$txt['send_stat']="
	<strong>Alredy send emails: </strong>".$send."<br>
	<strong>Left to send emails:</strong>".$not_send."<br>
	";
	return $txt;
}





function mmailer_send_mail () {
	global $wpdb;
	$count = 0;
	$limit = 1;
	$sql="select * from wp_users";
	$db = $wpdb->get_results($sql);
	foreach ($db as $db) {
	  //if ($count < $limit ) {
		if (mmailer_mail ($db->ID, $db->user_email) == true) {break;}
	  //}
	  $count++;
	}
}

function mmailer_mail ($user_id, $to) {
	// message
	$message = '
	<html>
	<head>
	  <title>'.get_site_option( 'mmail_title' ).'</title>
	</head>
	<body>
	  '.get_site_option( 'mmail_text' ).'
	</body>
	</html>
	';

	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'To: <'.$to.'>' . "\r\n";
	$headers .= 'From: '.get_site_option( 'mmail_from_title' ).' <'.get_site_option( 'mmail_from_mail' ).'>' . "\r\n";
	$email_send = get_user_meta( $user_id, 'email_recieved', true ); 
	$last_mail = get_site_option( 'mmail_last_mail' );
	$now = date("Y-m-d H:i:s");
	$dif = mmail_plugin_time_dif($last_mail, $now);
	if ($last_mail == 0 ) { update_site_option( 'mmail_last_mail', date("Y-m-d H:i:s")); }
	//echo "---> ".$now." - ".$last_mail." = ". $dif ." <----";
	if ($email_send == '' AND $dif > get_site_option('mmail_between') and  get_site_option('mmail_sending_staus') == 1 ) {
		// Mail it
		mail($to, get_site_option( 'mmail_title' ), $message, $headers);
		add_user_meta( $user_id, 'email_recieved', $now);
		update_site_option( 'mmail_last_mail', date("Y-m-d H:i:s"));
		return true;
		//echo "Email send: ".$to."<br>";
	} else {
		if ($email_send != '') { 
			//echo "<strong>Email send: </strong>".$to." (".$email_send.")<br>";
		} else {
			//echo "<strong>Email not send: </strong>".$to."<br>";
		}
	}

}

function mmail_plugin_time_dif($time1, $time2) {
	$time1 = strtotime($time1);
	$time2 = strtotime($time2);
	$diff = $time2 - $time1;
	return $diff;
}
?>