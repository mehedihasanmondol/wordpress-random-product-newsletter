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

}