<?php
define('CLASS_DIR', RECIPE_SCRAPER_PATH . '/classes' );

if(!class_exists( 'Recipe_Scraper_Core' )){
  class Recipe_Scraper_Core {

    function __construct(){

      //include files
      include_once CLASS_DIR . '/class-recipe-post-type.php';
      include_once CLASS_DIR . '/class-gfoas-scraper.php';
      include_once CLASS_DIR . '/class-save-media.php';
      include_once CLASS_DIR . '/class-recipe-scraper-ui.php';


      //functions
      $this->add_recipe_post_type();
      $this->add_scraper_ui_page();

      //TEST
      //include_once RECIPE_SCRAPER_PATH . '/__TEST__/index.php';
    }

    function add_recipe_post_type(){
      Recipe_Post_Type::register();
    }

    function add_scraper_ui_page(){
      new Recipe_Scraper_UI();
    }

  }
  new Recipe_Scraper_Core();

}
