<?php
class Recipe_Scraper_UI {

  function __construct(){
    add_action('admin_enqueue_scripts', array($this, 'enqueue_all_admin_scripts'));
    add_action('admin_menu', array($this, 'register_page'));
  }

  public function register_page(){
    $slug = add_menu_page( 
      'Pull Recipes From Gluten Free On A Shoestring', 
      'Pull Recipes', 
      'manage_options',
      'scrape-recipes-admin', 
      'Recipe_Scraper_UI::markup',
      '',
      25
    );
  }

  public function enqueue_all_admin_scripts(){
    wp_enqueue_script('main-script', RECIPE_SCRAPER_URL . 'dist/main.js', array(), '1.00', true);
    wp_enqueue_style('main-styles', RECIPE_SCRAPER_URL . 'dist/main.css', '1.00' , 'all');
    wp_localize_script( 
      'main-script', 
      'WP', 
      [
        'ajax'  =>  admin_url( 'admin-ajax.php' ),
      ]
    );
  }

  static function markup(){
    include RECIPE_SCRAPER_PATH . '/ui/admin-page.php';
  }
}
