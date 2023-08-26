<?php
/**
 * Created by PhpStorm.
 * User: mhnoy
 * Date: 8/25/2023
 * Time: 11:29 AM
 */

class NewsLetterPluginAssistant
{
    public $days = [
        "Sunday",
        "Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"
    ];
    function text_date_time($format="",$date_time=""){
        if (!$date_time){
            $date_time = date("Y-m-d H:i:s");
        }
        if (!$format){
            $format = "j M, Y g:i A";
        }
        return date($format, strtotime($date_time));
    }
    public function message_html_generate($data=array(
        "message" => ""
    )){
        $html_form = file_get_contents(NEWS_LETTER_PLUGIN_DIR."/assets/html/save-message.html");
        $template_maker = new Mustache_Engine(array(
            'escape' => function($value) {
                return $value;
            }
        ));
        return $template_maker->render($html_form,$data);
    }

    public function frequency_html_generate($data=array(
        "days_selectize" => "",
        "week_selectize" => "",
        "date_selectize" => "",
    )){
        $html_form = file_get_contents(NEWS_LETTER_PLUGIN_DIR."/assets/html/frequency.html");
        $template_maker = new Mustache_Engine(array(
            'escape' => function($value) {
                return $value;
            }
        ));
        return $template_maker->render($html_form,$data);
    }

    public function test_mail_checkbox_html_generate($data=array(
//        "test_mode_checked" => "",
//        "line_mode_checked" => "",
    )){
        $html_form = file_get_contents(NEWS_LETTER_PLUGIN_DIR."/assets/html/test-mode.html");
        $template_maker = new Mustache_Engine(array(
            'escape' => function($value) {
                return $value;
            }
        ));
        return $template_maker->render($html_form,$data);
    }


    function save_post_meta($post_id){
        $config_instance = new NewsLetterPluginConfig();
        if (isset($_POST[$config_instance->post_meta_api_key])){
            $cron_time_meta = get_post_meta($post_id,$config_instance->post_meta_cron_time,true);
            if (!$cron_time_meta){
                $this->update_post_meta($post_id,$config_instance->post_meta_cron,0);
                $this->update_post_meta($post_id,$config_instance->post_meta_cron_time,date("Y-m-d H:i:s"));
            }

        }

        foreach ($config_instance->post_meta_keys as $index => $key){
            if (isset($_POST[$key])){
                $value = $_POST[$key];
                if ($key == $config_instance->post_meta_user_categories){
                    $value = join(",",$value);
                }

                $this->update_post_meta($post_id,$key,$value);

            }
        }
    }

    function update_post_meta($post_id,$key,$value){
        update_post_meta($post_id, $key, $value);
    }
    function get_post_data($post_id){
        $meta_keys = (new NewsLetterPluginConfig())->post_meta_keys;
        $data = array(
            "post_date" => "",
            "post_title" => "",
        );

        $get_post = get_post($post_id);
        if ($get_post){
            $data['post_date'] = $get_post->post_date;
            $data['post_title'] = $get_post->post_title;
        }


        foreach ($meta_keys as $index => $key){
            $value = get_post_meta($post_id,$key,true);
            $data[$key] = $value ? $value : "";

            if ($key == (new NewsLetterPluginConfig())->post_meta_api_key){
                $data[$key] = $data[$key] ? $data[$key] : (new NewsLetterPluginConfig())->send_grid_api_key;
            }
            if ($key == (new NewsLetterPluginConfig())->post_meta_week_day){
                $data[$key] = $data[$key] ? $data[$key] : 0;
            }
            if ($key == (new NewsLetterPluginConfig())->post_meta_cron){
                $data[$key] = $data[$key] ? $data[$key] : 0;
            }

            if ($key == (new NewsLetterPluginConfig())->post_meta_test_mode){
                $data[$key] = $data[$key] ? $data[$key] : 'test';
            }




        }


        return $data;
    }

    function get_users_by_roll($role){
        $args = array(
            'role'      => $role, // Retrieve users with the 'customer' role
            'number'    => -1,         // Retrieve all customers (-1)
            'fields'    => array(
                "display_name",
                "user_email",
            ),
        );

        if ($role == "customer"){
            global $wpdb;
            $table_name = $wpdb->prefix . 'wc_customer_lookup'; // Replace with your custom table name

            $query = "SELECT first_name,last_name,email FROM $table_name";
            $results = $wpdb->get_results($query);

            foreach ($results as $index => $result){
                $result->display_name = $result->first_name." ".$result->last_name;
                $result->user_email = $result->email;
            }
            return $results;
        }

        return get_users( $args );
    }

}