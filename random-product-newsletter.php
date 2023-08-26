<?php
/*
 * Plugin Name: Random product Newsletter
 */
require_once __DIR__.'/packages/vendor/autoload.php';
define("NEWS_LETTER_PLUGIN_DIR",__DIR__);
define("NEWS_LETTER_PLUGIN_DIR_URL",plugin_dir_url(__FILE__));

new Init();
new DummyUserCategoryRegister();


function custom_every_minute_cronjob() {
    // Your custom code to run on the cron schedule
    (new NewsLetterPluginAssistant())->update_post_meta(2,"cron_hook_register",true);
}
//add_action('custom_cron_hook', 'custom_cron_job');


// Schedule the cron job to run every hour
//wp_schedule_event(time(), 'hourly', 'custom_cron_hook');
//wp_schedule_single_event(time()+1800, 'custom_cron_hook');

//if ( ! wp_next_scheduled( 'custom_cron_hook' ) ) {
//    wp_schedule_event( time(), 'five_seconds', 'custom_cron_hook' );
//}
//

//$args = array(
//    array(0,5,2),
//    array(0,5,2),
//    array(0,5,2),
//);
//
//foreach ($args as $arg){
//    add_action( 'custom_every_minute_event', 'custom_every_minute_cronjob',10,count($arg) );
//    if ( ! wp_next_scheduled( 'custom_every_minute_event',$arg ) ) {
//        wp_schedule_event( time(), 'every15minute', 'custom_every_minute_event', $arg);
//    }
//
//}


//function custom_core_activate(){
//    $args = array(0,5,2,8,7);
//    if ( ! wp_next_scheduled( 'custom_every_minute_event',$args ) ) {
//        wp_schedule_event( time(), 'everyminute', 'custom_every_minute_event', $args);
//    }
//}
//
//
//custom_core_activate();

//register_activation_hook( __FILE__, 'custom_core_activate' );
//
//
//
//
///**
// * Clear cron scedular.
// *
// * @return void
// */
//function custom_deactivation() {
//    wp_clear_scheduled_hook( 'custom_every_minute_event' );
//}
//
//register_deactivation_hook( __FILE__, 'custom_deactivation' );

(new NewsLetterPluginCronJob())->register_cron_jobs();
//$roles = wp_roles();
//// Get an array of all the role names
//$role_names = $roles->get_names();
//
//foreach ($role_names as $role_key => $role_name) {
//    $args = array(
//        'role'      => 'customer', // Retrieve users with the 'customer' role
//        'number'    => -1,         // Retrieve all customers (-1)
//    );
//
//    echo $role_name;
//    $customers = (new NewsLetterPluginAssistant())->get_users_by_roll($role_key);
//
//    print_r($customers);
//}
//




//$email = new \SendGrid\Mail\Mail();
//$config = new NewsLetterPluginConfig();
//try {
//    $email->setFrom($config->from_email, $config->from_email_name);
//    $email->setSubject('test mail');
//    $email->addTo('hasanmahadi889@gmail.com', "Mahadi hasan");
//    $email->addContent(
//        "text/html", "hi how area your"
//    );
//    $sendgrid = new \SendGrid($config->send_grid_api_key);
//    $response = $sendgrid->send($email);
//    print_r(array(
//        "from_email" => $config->from_email,
//        "from_email_name" => $config->from_email_name,
//        "apiKey" => $config->send_grid_api_key,
//    ));
//    print_r($response);
//
////                        return $response->statusCode();
//} catch (Exception $e) {
//   echo "Send grid mail send fail for ". $e->getMessage();
//}
//
//
