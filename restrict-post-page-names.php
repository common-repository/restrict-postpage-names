<?php
/*
Plugin Name: Restrict Post/Page Names
Plugin URI: http://wordpress.org/extend/plugins/restrict-postpage-names/
Description: This plugin is used to restrict post/page names.
Version: 1.1
Author: Rajasekaran M
Author URI: http://wordpress.org/extend/plugins/restrict-postpage-names/
*/

register_activation_hook( __FILE__ , 'rpn_activation' );
register_deactivation_hook( __FILE__ , 'rpn_deactivation' );

/* Add options upon plugin activation */
function rpn_activation() {
	if( !get_option( 'rpn_names' ) )
		add_option( 'rpn_names', '' );
}

/* Delete options upon plugin deactivation */
function rpn_deactivation() {
	delete_option( 'rpn_names' );
}

add_action ( 'admin_menu', 'rpn_menu' );

function rpn_menu() {
	add_options_page( __('Restrict Post/Page Names', 'rpn' ), __('Restrict Post/Page Names', 'rpn' ) , 10, 'restrict-postpage-names', 'restrict_postpage_names');
}

function restrict_postpage_names() {
	if ( isset( $_POST['rpn_names'] ) ) {
		update_option( 'rpn_names' , $_POST['rpn_names'] );
	}
	?>
	<div id="rpn_form" class=wrap>
		<div id="icon-edit" class="icon32"><br></div>		
    	<h2><?php echo __( 'Restrict Post/Page Names', 'rpn' ); ?></h2>
    	<form name="rpn_form" action="<?php _e($_SERVER["REQUEST_URI"]); ?>" method="post" >
    		<h3><?php echo __( 'Restrict Post/Page Names', 'rpn' ); ?></h3>
			<p><label for="rpn_names">Write down your list of post/page names. Separate multiple names with commas(,).</label></p>
			<span>Ex: School, College, Library</span>
			<textarea name="rpn_names" id="rpn_names" class="large-text code" rows="4"><?php echo get_option( 'rpn_names' ); ?></textarea>
			<p class="submit">
				<input name="Submit" class="button-primary" value="<?php _e('Save Changes', 'rpn') ?>" type="submit">
			</p>
		</form>
    </div>
	<?php	
}

add_filter( 'admin_footer', 'rpn_jsHandle' );

function rpn_jsHandle( ) {
	$rpnNames = get_option( 'rpn_names' );
	$nameList = "";
	if( !empty($rpnNames) ) {
		if( strstr($rpnNames , ",") != "" ) {
			$names = explode( "," , get_option( 'rpn_names' ) );
		} else {
			$names = array($rpnNames);	
		}
	} else {
		$names = array();
	}
	if( !empty( $names ) ) {
		foreach( $names as $name ) {
			$name = trim($name);
			if( !empty( $name ) ) {
				$nameList .= '"' . strtolower($name) . '",';
			}
		}
		$nameList = substr($nameList, 0, -1 );
	}
	?>
	<script type="text/javascript">
	<!--
	var names = new Array(<?php echo $nameList; ?>);
	jQuery('#title').keyup(function(){
		jQuery('#rpnErrorDiv').html('');
		var str = jQuery('#title').val();
		str = jQuery.trim(str);
		str = str.toLowerCase();
		if( jQuery.inArray( str, names ) != -1 ) {
			jQuery("#titlediv").prepend("<div id=\"rpnErrorDiv\" style=\"color:red;\">This word \""+str+"\" is restricted. So please enter another one.</div>");
			jQuery('#title').val( str.substring( 0, str.length - 1 ) );
		}
	});
	//-->
	</script>
	<?php 
} 
?>