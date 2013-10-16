<?php
/*
Plugin Name: Maintenance Mode + Preview Link
Plugin URI: 
Description: Adds a Maintenance Mode Screen for WordPress with an override preview parameter
Version: 1.0
Author: Jonathan Wold & Natalie Ebbens
Author URI: http://jonathanwold.com
License: GPL2
Copyright 2013 Jonathan Wold

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* Maintenance Mode + Preview Link */
include('MaintenanceModeSettingsPage.php');
function jw_maintenance_mode() {
    //if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
    $options = get_option( 'maint_display_text' );
    $message = $options['maint_text'];
    $title = $options['maint_title'] ;
    /*if (!preg_match('/<h2>(.*?)<\/h2>/s', $message, $matches)){*/
        $message = '<h2>' . $title . '<h2>' . $message;
    /*}*/
    if( !empty( $_GET ) ) {
                                        
        // First, grab the current string and parse it out.
        parse_str( $_SERVER['QUERY_STRING'], $query_string );
               
/*
        echo "<pre>";
        print_r('sess:'.$_SESSION);
        print_r('cookie:'.$_COOKIE);
        print_r('post:'.$_POST);
        echo "</pre>";
*/
        if(isset($query_string['nopreview'])){
           setcookie('preview', '', time()-60000, COOKIEPATH, COOKIE_DOMAIN, false);
            wp_die($message,$title); 
        }

        if ( !isset($query_string['preview']) and !isset($_COOKIE['preview'] )  ) {
            wp_die($message,$title); 
        }
        elseif (isset($query_string['preview']) and !isset($_COOKIE['preview'] ) ){
            //set the cookie
            setcookie('preview', 1, time()+60*60*24*365, COOKIEPATH, COOKIE_DOMAIN, false);
        }
        
    }
    elseif (!isset($_COOKIE['preview'] ) ){
            wp_die($message,$title); 
    }
}
add_action('get_header', 'jw_maintenance_mode');
if (function_exists('jw_maintenance_mode') ) {
	function my_admin_notice(){
	$url = admin_url( 'plugins.php?plugin_status=active');
	echo '<div class="updated">
	<p>Maintenance Mode is activated. Please don\'t forget to <a href="' . $url . '">deactivate</a> it as soon as you are done.</p>	
	</div>';
}
add_action('admin_notices', 'my_admin_notice');
}
//set defaults
register_activation_hook(__FILE__, 'add_maint_defaults_fn');
function add_maint_defaults_fn() {
    //set defaults if needed
    $tmp = get_option('maint_display_text');
    if(!is_array($tmp)) {
        $arr = array("maint_text" => "<h2>Temporarily Offline</h2><h3>Hey there! Our site is temporarily offline.</h3>",
                     "maint_title" => "Temporarily Unavailable");
        update_option('maint_display_text', $arr);
    }
}



/* 
== Change Log == 
*/
?>
