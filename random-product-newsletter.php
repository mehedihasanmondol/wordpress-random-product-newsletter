<?php
/*
 * Plugin Name: Random product Newsletter
 */
require_once __DIR__.'/packages/vendor/autoload.php';
define("NEWS_LETTER_PLUGIN_DIR",__DIR__);
define("NEWS_LETTER_PLUGIN_DIR_URL",plugin_dir_url(__FILE__));

new Init();

global $unsubscriber_table_version;
$unsubscriber_table_version = '1.1';

function unsubscriber_install() {
    global $wpdb;
    global $unsubscriber_table_version;

    $table_name = $wpdb->prefix . (new NewsLetterPluginConfig())->unsubscriber_table_name;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		roll tinytext NOT NULL,
		post_id int NOT NULL,
		user_id int NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    add_option( 'unsubscriber_table_version', $unsubscriber_table_version );
}

register_activation_hook( __FILE__, 'unsubscriber_install' );


//$timezone = wp_timezone();
//echo $timezone->getName();
//
//
//$current_time = (new NewsLetterPluginAssistant())->current_time_stamp();
//echo $current_time."<br>";
//echo (new NewsLetterPluginAssistant())->time_convert_by_zone($current_time,"UTC",wp_timezone()->getName());
