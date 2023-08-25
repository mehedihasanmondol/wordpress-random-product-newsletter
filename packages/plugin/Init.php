<?php
/**
 * Created by PhpStorm.
 * User: mhnoy
 * Date: 8/25/2023
 * Time: 10:35 AM
 */

class Init extends NewsLetterPluginConfig
{
    public function __construct()
    {
        add_action("init",[$this,"create_newsletter_post"]);
        add_action("admin_menu",[$this,"add_setup_menu"]);
        $key = get_option($this->send_grid_api_option);
        $this->send_grid_api_key =  $key ? $key : $this->send_grid_api_key;
    }
    function load_newsletter_script(){
        wp_enqueue_script( 'newsletter-plugin',NEWS_LETTER_PLUGIN_DIR_URL."assets/newsletter-plugin.js",['jquery']);
    }
    function load_jquery_script(){
        wp_enqueue_script( 'jquery-cdn','https://code.jquery.com/jquery-3.7.0.min.js');
    }


    function create_newsletter_post(){
        register_post_type($this->post_type,array(
            "labels" => array(
                "name" => $this->plugin_name
            ),
            "public" => true,
            "supports" => array(
                "title" ,
                "editor",
                "show_ui",
                "page-attributes",
            )
        ));
    }

    function setup_html(){
        $html_form = file_get_contents(NEWS_LETTER_PLUGIN_DIR."/assets/html/setup.html");
        $template_maker = new Mustache_Engine(array(
            'escape' => function($value) {
                return $value;
            }
        ));


        $message = "";

        if (isset($_REQUEST['save'])){
            $update = update_option($this->send_grid_api_option,$_REQUEST['api_key']);
            $this->send_grid_api_key = get_option($this->send_grid_api_option);
            if ($update){
                $message = (new NewsLetterPluginAssistant())->message_html_generate(array(
                    "message" => "Changed has been saved."
                ));
            }
        }



        echo $template_maker->render($html_form,array(
            "api_key" => $this->send_grid_api_key,
            "page_title" => $this->setup_page_title,
            "message" => $message,
        ));

        return "";
    }

    function add_setup_menu(){
        $this->load_jquery_script();
        $this->load_newsletter_script();
        add_menu_page($this->setup_page_title,$this->setup_menu_title,'manage_options',$this->setup_menu_slug,[$this,"setup_html"],'',null);
    }
}