<?php
/**
 * Created by PhpStorm.
 * User: mhnoy
 * Date: 8/26/2023
 * Time: 5:43 AM
 */

class NewsLetterPluginCronJob
{
    function set_cron_job($post_id){
//        $post_data = (new NewsLetterPluginAssistant())->get_post_data($post_id);

//        (new NewsLetterPluginAssistant())->update_post_meta($post_id,"cron",1);
        (new NewsLetterPluginAssistant())->update_post_meta($post_id,(new NewsLetterPluginConfig())->post_meta_cron_time,date("Y-m-d H:i:s"));

    }

    function register_cron_jobs(){
        $config = new NewsLetterPluginConfig();
        $posts = get_posts(array(
            "post_type" => $config->post_type
        ));

        $assistant = new NewsLetterPluginAssistant();

        foreach ($posts as $post_object){
            $post = $assistant->get_post_data($post_object->ID);
            $post_id = $post_object->ID;


            if ($post[$config->post_meta_sending_frequency] == "one_time" and !$post[$config->post_meta_cron]){
                $hook_name = "one_time_newsletter_cron_job_of_".$post_id;
                $args = array($post_id);
                add_action($hook_name,[$this,"set_cron_job"],10,$args);
                if ( ! wp_next_scheduled( $hook_name ,$args) ) {
                    $time = $assistant->text_date_time("Y-m-d",$post[$config->post_meta_cron_time])." 22:59:01";
                    wp_schedule_single_event(strtotime($time), $hook_name,$args);
                }
            }

            else if ($post[$config->post_meta_sending_frequency] == "weekly" and !$post[$config->post_meta_cron]){
                $hook_name = "weekly_newsletter_cron_job_of_".$post_id;
                $args = array($post_id);
                add_action($hook_name,[$this,"set_cron_job"],10,$args);
                if ( ! wp_next_scheduled( $hook_name ,$args) ) {
                    $time = strtotime('next '.$assistant->days[$post[$config->post_meta_week_day]].' 01:00:00',strtotime($post[$config->post_meta_cron_time]));
                    wp_schedule_single_event($time, $hook_name,$args);
                }
            }
            else if ($post[$config->post_meta_sending_frequency] == "monthly" and !$post[$config->post_meta_cron]){
                $hook_name = "monthly_newsletter_cron_job_of_".$post_id;
                $args = array($post_id);
                add_action($hook_name,[$this,"set_cron_job"],10,$args);
                if ( ! wp_next_scheduled( $hook_name ,$args) ) {
                    $time = strtotime($assistant->text_date_time("Y-m-",$post[$config->post_meta_cron_time]).$post[$config->post_meta_month_date]);
                    wp_schedule_single_event($time, $hook_name,$args);
                }
            }



        }
    }

    function add_schedule_intervals( $schedules ) {
        // add a 'everyminute' schedule to the existing set
        $schedules['every15seconds'] = array(
            'interval' => 15,
            'display'  => __( 'Every 15 seconds'),
        );
        $schedules['monthly'] = array(
            'interval' => 2635200,
            'display'  => __( 'Every month'),
        );

        return $schedules;
    }
}