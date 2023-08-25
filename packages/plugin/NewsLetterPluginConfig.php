<?php
/**
 * Created by PhpStorm.
 * User: mhnoy
 * Date: 8/25/2023
 * Time: 10:50 AM
 */

class NewsLetterPluginConfig
{
    public $plugin_name = "Random product newsletter";
    public $plugin_short_name = "Newsletter";
    public $post_type = "product-newsletter";
    public $setup_menu_slug = "newsletter_settings";
    public $label = "Product newsletter";
    public $setup_page_title = "Random product newsletter settings";
    public $setup_menu_title = "Newsletter";
    public $send_grid_api_key = "send_grid_api_key_Newsletter";
    public $send_grid_api_option = "send_grid_api_key";

    public $post_meta_keys = array(
        "api_key" => "newsletter_api_key",
        "subject" => "newsletter_subject",
        "body" => "newsletter_body_content",
        "user_categories" => "newsletter_user_categories",
        "sending_frequency" => "newsletter_sending_frequency",
        "week_day" => "week_day",
        "month_date" => "month_date",
    );

    public $post_meta_api_key;
    public $post_meta_body;
    public $post_meta_subject;
    public $post_meta_user_categories;
    public $post_meta_sending_frequency;
    public $post_meta_week_day;
    public $post_meta_month_date;
    public function __construct()
    {
        $this->post_meta_api_key = $this->post_meta_keys['api_key'];
        $this->post_meta_subject = $this->post_meta_keys['subject'];
        $this->post_meta_body = $this->post_meta_keys['body'];
        $this->post_meta_user_categories = $this->post_meta_keys['user_categories'];
        $this->post_meta_sending_frequency = $this->post_meta_keys['sending_frequency'];
        $this->post_meta_week_day = $this->post_meta_keys['week_day'];
        $this->post_meta_month_date = $this->post_meta_keys['month_date'];
    }

}
