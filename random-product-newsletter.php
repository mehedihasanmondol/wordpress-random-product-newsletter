<?php
/*
 * Plugin Name: Random product Newsletter
 */
require_once __DIR__.'/packages/vendor/autoload.php';
define("NEWS_LETTER_PLUGIN_DIR",__DIR__);
define("NEWS_LETTER_PLUGIN_DIR_URL",plugin_dir_url(__FILE__));

new Init();
new DummyUserCategoryRegister();







