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
    public $send_grid_api_key = "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";
    public $send_grid_api_option = "send_grid_api_key";
    public $from_email = "connection.mahadihasan@gmail.com";
    public $from_email_name = "Developer Mehedi hasan";

    public $post_meta_keys = array(
        "api_key" => "newsletter_api_key",
        "subject" => "newsletter_subject",
        "body" => "newsletter_body_content",
        "user_categories" => "newsletter_user_categories",
        "sending_frequency" => "newsletter_sending_frequency",
        "week_day" => "week_day",
        "month_date" => "month_date",
        "cron" => 'cron',
        "cron_time" => 'cron_time',
        "cron_status" => 'cron_status',
    );

    public $post_meta_api_key;
    public $post_meta_body;
    public $post_meta_subject;
    public $post_meta_user_categories;
    public $post_meta_sending_frequency;
    public $post_meta_week_day;
    public $post_meta_month_date;
    public $post_meta_cron;
    public $post_meta_cron_time;
    public $post_meta_cron_status;
    public function __construct()
    {
        $this->post_meta_api_key = $this->post_meta_keys['api_key'];
        $this->post_meta_subject = $this->post_meta_keys['subject'];
        $this->post_meta_body = $this->post_meta_keys['body'];
        $this->post_meta_user_categories = $this->post_meta_keys['user_categories'];
        $this->post_meta_sending_frequency = $this->post_meta_keys['sending_frequency'];
        $this->post_meta_week_day = $this->post_meta_keys['week_day'];
        $this->post_meta_month_date = $this->post_meta_keys['month_date'];
        $this->post_meta_cron = $this->post_meta_keys['cron'];
        $this->post_meta_cron_time = $this->post_meta_keys['cron_time'];
        $this->post_meta_cron_status = $this->post_meta_keys['cron_status'];
    }

}
