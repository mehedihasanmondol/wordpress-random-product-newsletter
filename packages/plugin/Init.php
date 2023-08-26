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
        parent::__construct();

        add_action("init",[$this,"create_newsletter_post"]);
        add_action("admin_menu",[$this,"add_setup_menu"]);
        add_filter( 'cron_schedules', [(new NewsLetterPluginCronJob()),"add_schedule_intervals"] );
//        new WPEditorToolConfig();



    }
    function load_newsletter_script(){
        wp_enqueue_script( 'newsletter-plugin',NEWS_LETTER_PLUGIN_DIR_URL."assets/newsletter-plugin.js",['jquery']);
    }
    function load_jquery_script(){
        wp_enqueue_script( 'jquery-cdn','https://code.jquery.com/jquery-3.7.0.min.js');
    }


    function custom_text_editor_meta_box_callback($field_name="",$content="") {
        // Output the HTML for the meta box
        new WPEditorToolConfig();
        $settings = array(
            'tinymce' => array(
                'toolbar1' => 'bold,italic,strikethrough,link,unlink,block quote,horizontal line,bullet list,numbered list,align left,align center,align right,undo,redo,fullscreen toggle,toolbar toggle,custom_button', // Add your custom button here
                'plugins' => 'custom_tinymce_plugin', // Add your custom TinyMCE plugin name here
            ),
        );

        $settings = array_merge($settings,array(
            'textarea_name' => $field_name, // Name of the textarea field
            'media_buttons' => true, // Show media buttons
            'textarea_rows' => 15, // Number of rows in the editor
//            'teeny' => true, // Use a minimal editor
        ));

//        wp_editor($content, 'newsletter_body', array(
//            'textarea_name' => $field_name, // Name of the textarea field
//            'media_buttons' => true, // Show media buttons
//            'textarea_rows' => 15, // Number of rows in the editor
//            'teeny' => true, // Use a minimal editor
//        ));

        wp_editor($content, 'newsletter_body', $settings);

    }






    function add_custom_fields(){

        add_action( 'add_meta_boxes', function (){
            $meta_keys = (new NewsLetterPluginConfig())->post_meta_keys;

            $post_id = $_GET['post'] ?? 0;
            $post_data = (new NewsLetterPluginAssistant())->get_post_data($post_id);


            add_meta_box($meta_keys['api_key'],"SendGrid API key",function () use ($meta_keys,$post_data){
                echo "<input class='regular-text' value='".$post_data[$meta_keys['api_key']]."' name='".$meta_keys['api_key']."' style='width:100%' type='text' placeholder='SendGrid API key'/>";
            },$this->post_type);

            add_meta_box($meta_keys['subject'],"Subject",function () use ($meta_keys,$post_data){
                echo "<input class='regular-text' value='".$post_data[$meta_keys['subject']]."' name='".$meta_keys['subject']."' style='width:100%' type='text' placeholder='subject'/>";
            },$this->post_type);

            add_meta_box($meta_keys['body'],"Body",function () use ($meta_keys,$post_data){
                $this->custom_text_editor_meta_box_callback($meta_keys['body'],$post_data[$meta_keys['body']]);
            },$this->post_type);

            add_meta_box($meta_keys['user_categories'],"Send to",function () use ($meta_keys,$post_data){
                $user_categories_html = "";
                // Get the global WP_Roles object
                $roles = wp_roles();
                // Get an array of all the role names
                $role_names = $roles->get_names();

                foreach ($role_names as $role_key => $role_name) {
                    $checked = "";
                    if (in_array($role_key,explode(",",$post_data[$meta_keys['user_categories']]))){
                        $checked = "checked";
                    }
                    $user_categories_html .= "<div class='inside'>"."<input $checked type='checkbox' name='".$meta_keys['user_categories']."[]' value='".$role_key."'><label>".$role_name."</label></div>";

                }

                echo $user_categories_html;
            },$this->post_type);

            add_meta_box($meta_keys['sending_frequency'],"Frequency of Sending",function () use ($meta_keys,$post_data){

                $value = $post_data[$meta_keys['sending_frequency']];

                $checked_data = array(
                    "one_time_checked" => "",
                    "weekly_checked" => "",
                    "monthly_checked" => "",
                );
                $week_selectize = "<select name='week_day'>";

                foreach ((new NewsLetterPluginAssistant())->days as $index => $day){
                    $checked = "";
                    if ($post_data[$meta_keys['week_day']] == $index){
                        $checked = "selected";
                    }
                    $week_selectize .= "<option $checked value='".$index."'>".$day."</option>";
                }
                $week_selectize .= "</select>";
                
                $date_selectize = "<select name='month_date'>";
                for ($i=1; $i<=31; $i++){
                    $checked = "";
                    if ($post_data[$meta_keys['month_date']] == $i){
                        $checked = "selected";
                    }
                    $date_selectize .= "<option $checked value='".$i."'>".$i."</option>";
                }
                $date_selectize .= "</select>";

                $checked_data[$value."_checked"] = "checked";
                if (!$value){
                    $checked_data['one_time_checked'] = "checked";
                }
                

                $html = (new NewsLetterPluginAssistant())->frequency_html_generate(array_merge(array(
                    "week_selectize" => $week_selectize,
                    "date_selectize" => $date_selectize,
                ),$checked_data));

                echo $html;
            },$this->post_type);


            /// test mode checkbox
            add_meta_box($meta_keys['test_mode'],"Test mode / Live mode",function () use ($meta_keys,$post_data){

                $value = $post_data[$meta_keys['test_mode']];

                $checked_data = array(
                    "test_mode_checked" => $value == 'test' ? "checked" : "",
                    "live_mode_checked" => $value == 'live' ? "checked" : "",
                );

                $html = (new NewsLetterPluginAssistant())->test_mail_checkbox_html_generate($checked_data);

                echo $html;
            },$this->post_type,'side');


            add_meta_box($meta_keys['test_email_1'],"Test email 1",function () use ($meta_keys,$post_data){
                echo "<input class='regular-text' value='".$post_data[$meta_keys['test_email_1']]."' name='".$meta_keys['test_email_1']."' style='width:100%' type='text' placeholder='Test email 1'/>";
            },$this->post_type,'side');
            
            add_meta_box($meta_keys['test_email_2'],"Test email 2",function () use ($meta_keys,$post_data){
                echo "<input class='regular-text' value='".$post_data[$meta_keys['test_email_2']]."' name='".$meta_keys['test_email_2']."' style='width:100%' type='text' placeholder='Test email 2'/>";
            },$this->post_type,'side');
            





        } );
    }

    function create_newsletter_post(){
        register_post_type($this->post_type,array(
            "labels" => array(
                "name" => $this->plugin_name,
                "singular_name" => $this->plugin_name,
                "add_new" => "Add ".$this->plugin_short_name,
                "add_new_item" => "Add new ".$this->plugin_name,
                "edit_item" => "Edit ".$this->plugin_name,
                "new_item" => "New ".$this->plugin_name,
                "view_item" => "View ".$this->plugin_name,
                "view_items" => "View ".$this->plugin_name."s",
                "search_items" => "Search ".$this->plugin_name,
                "all_items" => "All ".$this->plugin_short_name,
            ),
            "public" => true,
            "supports" => array(
                "title" ,
//                "editor",
                "show_ui",
//                "page-attributes"
            ),
        ));


        $this->add_custom_fields();

        /// save post when submit
        add_action('edit_post', [(new NewsLetterPluginAssistant()),'save_post_meta']);


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
            $update = update_option($this->send_grid_api_from_email,$_REQUEST['from_email']);
            $update = update_option($this->send_grid_api_from_email_name,$_REQUEST['from_email_name']);
            $this->send_grid_api_key = get_option($this->send_grid_api_option);
            $this->send_grid_api_from_email = get_option($this->send_grid_api_from_email);
            $this->send_grid_api_from_email_name = get_option($this->send_grid_api_from_email_name);
            if ($update){
                $message = (new NewsLetterPluginAssistant())->message_html_generate(array(
                    "message" => "Changed has been saved."
                ));
            }
        }

        $send_grid_api_message = get_option($this->send_grid_api_message);


        echo $template_maker->render($html_form,array(
            "api_key" => $this->send_grid_api_key,
            "from_email_name" => $this->send_grid_api_from_email_name,
            "from_email" => $this->send_grid_api_from_email,
            "page_title" => $this->setup_page_title,
            "message" => $message,
            "send_grid_api_message" => $send_grid_api_message ? $send_grid_api_message : "",
        ));

        return "";
    }

    function add_setup_menu(){
        $this->load_jquery_script();
        $this->load_newsletter_script();
        add_menu_page($this->setup_page_title,$this->setup_menu_title,'manage_options',$this->setup_menu_slug,[$this,"setup_html"],'',null);
    }
}