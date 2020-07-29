<?php
/**
 * Plugin Name:GFOAS Recipe Scraper
 * Plugin URI: 
 * Description: Scrapes recipes from glutenfreeonashoestring.com
 * Version: 1.0
 * Author: Outthinkgroup
 * Author URI: https://outthinkgroup.com/
 */

define('RECIPE_SCRAPER_PATH', plugin_dir_path(__FILE__));
define('RECIPE_SCRAPER_URL', plugin_dir_url(__FILE__));

 add_action('init', function(){
   require RECIPE_SCRAPER_PATH . '/classes/class-recipe-scraper-core.php';
 });
