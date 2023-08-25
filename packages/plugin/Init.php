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
    function custom_text_editor_meta_box_callback($post) {
        $custom_content = "";
        if ($post){
            // Get the current value of the custom field
            $custom_content = get_post_meta($post->ID, '_custom_content_key', true);

        }

        // Output the HTML for the meta box
        wp_editor($custom_content, 'newsletter_body', array(
            'textarea_name' => 'newsletter_body', // Name of the textarea field
            'media_buttons' => true, // Show media buttons
            'textarea_rows' => 15, // Number of rows in the editor
            'teeny' => true, // Use a minimal editor
        ));
    }

    function add_custom_fields(){
        add_action( 'add_meta_boxes', function (){
            add_meta_box("newsletter_api_key","SendGrid API key",function (){
                echo "<input class='regular-text' style='width:100%' type='text' placeholder='SendGrid API key' value='".$this->send_grid_api_key."'/>";
            },$this->post_type);

            add_meta_box("newsletter_subject","Subject",function (){
                echo "<input class='regular-text' style='width:100%' type='text' placeholder='subject'/>";
            },$this->post_type);

            add_meta_box("newsletter_body_content","Body",function (){
                $this->custom_text_editor_meta_box_callback("");
            },$this->post_type);

            add_meta_box("newsletter_user_categories","Send to",function (){
                $user_categories_html = "";
                // Get the global WP_Roles object
                $roles = wp_roles();

                // Get an array of all the role names
                $role_names = $roles->get_names();

                // Print the role names
                foreach ($role_names as $role_name) {
                    $user_categories_html .= "<div class='inside'>"."<input type='checkbox' name='user_categories' value='".$role_name."'><label>".$role_name."</label></div>";

                }

                echo $user_categories_html;
            },$this->post_type);

            add_meta_box("newsletter_sending_frequency","Frequency of Sending",function (){

                $week_selectize = "<select name='week_day'>";
                foreach ((new NewsLetterPluginAssistant())->days as $index => $day){
                    $week_selectize .= "<option value='".$index."'>".$day."</option>";
                }
                $week_selectize .= "</select>";
                
                $date_selectize = "<select name='month_date'>";
                for ($i=1; $i<=31; $i++){
                    $date_selectize .= "<option value='".$i."'>".$i."</option>";
                }
                $date_selectize .= "</select>";
                
                

                $html = (new NewsLetterPluginAssistant())->frequency_html_generate(array(
                    "week_selectize" => $week_selectize,
                    "date_selectize" => $date_selectize,
                ));

                echo $html;
            },$this->post_type);





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
                "page-attributes"
            ),
        ));

        // Register custom meta field
        register_post_meta('post', 'book_author', array(
            'type' => 'string',
            'description' => 'Book Author',
            'single' => true,
            'show_in_rest' => true,
        ));

        $this->add_custom_fields();



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