<?php
/*
 Plugin Name: Vebra Properties
 Plugin URI: http://www.ultimateweb.co.uk/vebra_properties
 Description: This plugin will take your VebraAPI feed and create a searchable list of properties in your Wordpress site.
 Version: 1.0
 Author: Ultimateweb Ltd
 Author URI: http://www.ultimateweb.co.uk
 License: GPL2
*/

/*  Copyright 2014  Ian Scotland, Ultimateweb Ltd  (email : info@ultimateweb.co.uk)

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
/* 
error_reporting(E_ALL);
ini_set('display_errors', '1'); 
*/

defined('ABSPATH') or die("No script kiddies please!");
$vp_version = '1.1';

include_once 'includes/vebra_feed.php';
include_once 'includes/vebra_shortcode.php';

register_activation_hook( __FILE__, 'vp_install');
register_deactivation_hook( __FILE__, 'vp_uninstall' );
add_action('plugins_loaded', 'vp_update_check' );
wp_enqueue_script('jquery');
wp_enqueue_style('vebra-properties', plugins_url().'/vebra_properties/includes/css/vp.css' );
wp_enqueue_style('flexslider', plugins_url().'/vebra_properties/includes/css/flexslider.css' );
wp_enqueue_script('vebra-properties', plugins_url().'/vebra_properties/includes/js/vp.js', array(), '1.0.0', true);
wp_enqueue_script('flexslider', plugins_url().'/vebra_properties/includes/js/jquery.flexslider-min.js', array(), '2.0.0', true);
wp_enqueue_script('googlelocation', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places', array(), "1.1.0",false);

/* SETUP THE DATABASE */
function vp_install() {
    global $wpdb;
    global $vp_version;
    $charset_collate = '';
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    if ( ! empty( $wpdb->charset ) ) $charset_collate = " DEFAULT CHARACTER SET {$wpdb->charset}";
    if ( ! empty( $wpdb->collate ) ) $charset_collate .= " COLLATE {$wpdb->collate}";

    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "CREATE TABLE $table_name (
              vebraid int NOT NULL,
              branchid int,
              databaseid int, 
              area varchar(50),
              featured bit,
              uploaded date,
              agentref varchar(50),
              address_name varchar(50),
              address_street varchar(50),
              address_locality varchar(150),
              address_town varchar(50),
              address_county varchar(50),
              address_postcode varchar(9),
              address_custom varchar(100),
              address_display varchar(255),
              price_postfix varchar(3),
              price_qualifier varchar(30),
              price_currency varchar(3),
              price_display bit,
              price numeric(10,2),
              furnished varchar(50),
              let_type varchar(50),
              longitude decimal(10,5),
              latitude decimal(10,5),
              web_status varchar(50),
              property_type varchar(50),
              bedrooms tinyint,
              receptions tinyint,
              bathrooms tinyint,
              excerpt text,
              rm_qualifier varchar(50),
              bullets text,
              UNIQUE KEY id (vebraid)
            ) $charset_collate;";
    dbDelta( $sql );

    $table_name = $wpdb->prefix."vebrafiles";
    $sql = "CREATE TABLE $table_name (
              id int NOT NULL AUTO_INCREMENT,
              vebraid int,
              sortorder tinyint,
              name varchar(50),
              url varchar(500),
              UNIQUE KEY id (id)
            ) $charset_collate;";
    dbDelta( $sql );
    
    $table_name = $wpdb->prefix."vebralog";
    $sql = "CREATE TABLE $table_name (
              id int NOT NULL AUTO_INCREMENT,
              logdate timestamp,
              updated int,
              deleted int,
              totalproperties int,
              UNIQUE KEY id (id)
            ) $charset_collate;";   
    dbDelta( $sql );
   
    $table_name = $wpdb->prefix."vebraparagraphs";
    $sql = "CREATE TABLE $table_name (
              id int NOT NULL AUTO_INCREMENT,
              vebraid int,
              sortorder tinyint,
              filesortorder tinyint,
              name varchar(50),
              dimensions varchar(255),
              description text,
              UNIQUE KEY id (id)
            ) $charset_collate;";   
    dbDelta( $sql );
    
    add_option("vp_version", $vp_version);
    add_option("vp_lastupdated", "");
    add_option("vp_token","");
    add_option("vp_propertycount",0);
    add_action('admin_init', 'vp_admin_init');
    add_action('admin_menu', 'vp_admin_add_page');
    add_action('admin_menu', 'vp_settings_updated');
    
    //add property update schedule
    add_action('vpschedulepopulate','vp_do_populate');
    wp_clear_scheduled_hook('vpschedule');
    wp_schedule_event(time(), 'daily', 'vpschedule');
    add_action('vpschedule','vp_do_schedule');
    
    //add short codes
    add_shortcode("vebra_properties", "vp_list_properties");
    add_shortcode("vebra_details", "vp_property_detail");
    add_shortcode("vebra_quicksearch", "vp_property_quicksearch");
    
}

function vp_uninstall() {
    //do some uninstalling
    wp_clear_scheduled_hook('vpschedule');
    remove_action('admin_menu', 'vp_settings_updated');
    remove_action('admin_init', 'vp_admin_init');
    remove_action('admin_menu', 'vp_admin_add_page');
    remove_action('vpschedule','vp_do_schedule');
    remove_action('vpschedulepopulate','vp_do_populate');
    remove_shortcode("vebra_properties");
    remove_shortcode("vebra_details");
    remove_shortcode("vebra_quicksearch");
}

function vp_update_check() {
    global $vp_version;
    if ( get_option( 'vp_version' ) != $vp_version ) vp_install();
}

function vp_do_schedule() {
    if (get_option("vp_lastupdated") != "") {
        vp_populate(false);
    }
}

function vp_do_populate() {
    vp_populate(true);
}

/* SET UP THE SETTINGS PAGE */
function vp_admin_init(){
    register_setting( 'vp_options', 'vp_options', 'vp_options_validate' );
    add_settings_section('vp_main', 'Main Vebra Properties Settings', 'vp_section_text', 'vp_plugin');
    add_settings_field('vp_API_Username', 'API Username', 'vp_setting_username', 'vp_plugin', 'vp_main');
    add_settings_field('vp_API_Password', 'API Password', 'vp_setting_password', 'vp_plugin', 'vp_main');
    add_settings_field('vp_API_FeedID', 'API Data Feed ID', 'vp_setting_feedid', 'vp_plugin', 'vp_main');
    add_settings_field('vp_API_PropertySearchPage', 'Property Search Page', 'vp_setting_search', 'vp_plugin', 'vp_main');
    add_settings_field('vp_API_PropertyPage', 'Property Details Page', 'vp_setting_details', 'vp_plugin', 'vp_main');
}

function vp_admin_add_page() {
    add_options_page('Vebra Properties  Plugin Page', 'Vebra Properties', 'manage_options', 'vebra_properties', 'vp_options_page'); 
}

function vp_options_page() {
    ?>
    <div>
    <h2>Vebra Properties Settings</h2>
    Please complete the details below in order to allow the connection of Vebra Properties plugin to Vebra.
    <form action="options.php" method="post">
    <?php settings_fields('vp_options'); ?>
    <?php do_settings_sections('vp_plugin'); ?>
    There are currently <?php echo get_option("vp_propertycount") ?> properties listed.<br /><br />
    The system will schedule re-population the database when you save changes.<br /><br /> 
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
 
    <?php
}

function vp_section_text() {
    echo '<p>Put your API details below.</p>';
} 

function vp_setting_username() {
    $options = get_option('vp_options');
    echo "<input id='plugin_text_string' name='vp_options[username]' size='40' type='text' value='{$options['username']}' />";
}

function vp_setting_password() {
    $options = get_option('vp_options');
    echo "<input id='plugin_text_string' name='vp_options[password]' size='40' type='text' value='{$options['password']}' />";
}

function vp_setting_feedid() {
    $options = get_option('vp_options');
    echo "<input id='plugin_text_string' name='vp_options[feedid]' size='40' type='text' value='{$options['feedid']}' />";
}

function vp_setting_search() {
    $options = get_option('vp_options');
    wp_dropdown_pages(array('selected' => $options['searchpageid'], 'name' => 'vp_options[searchpageid]'));
}

function vp_setting_details() {
    $options = get_option('vp_options');
    wp_dropdown_pages(array('selected' => $options['pageid'], 'name' => 'vp_options[pageid]'));
}

function vp_options_validate($input) {
    return $input;
}

function vp_settings_updated() {
    if(isset($_GET['settings-updated']) && $_GET['settings-updated'])
    {       
        if ($_GET['page']=="vebra_properties") {
            if (get_option('vp_options')) {
                $options = get_option('vp_options');
                if (array_key_exists('username',$options) && array_key_exists('password',$options) && array_key_exists('feedid',$options)) {
                    if ($options["username"]!="" && $options["password"]!="" && $options["feedid"]!="") {
			            //shedule population task
			            wp_schedule_single_event(time(), "vpschedulepopulate");
                    }
                }
            }
        }
    }
}

function vp_list_properties($atts) {
    global $vp_searchvars;
    $options = get_option('vp_options');

    //process plugin
    $vp_searchvars = shortcode_atts(array(
        'branchid' => '',
        'area' => '',
        'featured' => '',
        'bedrooms' => '0',
        'minprice' => '',
        'maxprice' => '',
        'type' => '',
        'location' => '',
        'radius' => '3',
        'pagesize' => '6',
        'page' => '1',
        'orderby' => 'price desc',
        'view' => 'list'
    ), $atts );
    
    //update my settings from the post
    foreach ($_REQUEST as $key => $value) {
        $vp_key = str_replace("vp_","", $key);
        if (array_key_exists($vp_key,$vp_searchvars)) {
            if (is_array($_REQUEST[$key]))
                $vp_searchvars[$vp_key]=implode(",",$_REQUEST[$key]);
            else
                $vp_searchvars[$vp_key]=$_REQUEST[$key];
        }
    }
    
    $plugindir = dirname( __FILE__ );
    if (file_exists(TEMPLATEPATH . '/vp_list.php')) {
        $template = TEMPLATEPATH . '/' . $templatefilename;
    } else {
        $template = $plugindir . '/includes/templates/vp_list.php';
    }
    include_once($template);   
}

function vp_property_detail($atts) {
    global $vp_detailvars;
    //process plugin
    $vp_detailvars = shortcode_atts(array(
        'vebraid' => '',
        'agentref' => ''
    ), $atts );
    
    $plugindir = dirname( __FILE__ );
    if (file_exists(TEMPLATEPATH . '/vp_detail.php')) {
        $template = TEMPLATEPATH . '/' . $templatefilename;
    } else {
        $template = $plugindir . '/includes/templates/vp_detail.php';
    }
    include_once($template);   
}

function vp_property_quicksearch($atts) {
    global $vp_qsearchvars;

    //process plugin
    $vp_qsearchvars = shortcode_atts(array(
        'branchid' => '',
        'area' => 'To Buy',
        'location' => ''
    ), $atts );
    
    //update my settings from the post
    foreach ($_REQUEST as $key => $value) {
        $vp_key = str_replace("vp_","", $key);
        if (array_key_exists($vp_key,$vp_qsearchvars)) {
            if (is_array($_REQUEST[$key]))
                $vp_qsearchvars[$vp_key]=implode(",",$_REQUEST[$key]);
            else
                $vp_qsearchvars[$vp_key]=$_REQUEST[$key];
        }
    }
    
    $plugindir = dirname( __FILE__ );
    if (file_exists(TEMPLATEPATH . '/vp_quicksearch.php')) {
        $template = TEMPLATEPATH . '/' . $templatefilename;
    } else {
        $template = $plugindir . '/includes/templates/vp_quicksearch.php';
    }
    include_once($template);   
}

?>