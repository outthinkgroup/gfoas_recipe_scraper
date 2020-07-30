<?php
if(!class_exists( 'GFOAS_SCRAPE' )){

  class GFOAS_SCRAPE {
    public $error_obj;
    function __construct() {
      add_action("wp_ajax_GFOAS_scrape", array($this, 'logged_in_request'));
      add_action("wp_ajax_nopriv_GFOAS_scrape", array($this, "not_logged_in_request"));
    }

    function logged_in_request(){
       
      $recipe_url = $_POST['recipe'];
      $youtube_url = $_POST['youtube'];
      
      $recipe_post_id = $this->pull_and_save_recipe($recipe_url);
      update_field('youtube_url', $youtube_url, $recipe_post_id);
      
      if(count($this->error_obj)!==0){
        echo json_encode($error_obj);
      }else{
        echo 'success';
      }

      die();
    }
    
    function not_logged_in_request(){
      echo 'PLEASE LOGIN';
      die();
    }


    private function pull_and_save_recipe($url){

      $slug = $this->get_slug($url);

      $recipe = $this->fetch_recipe($slug);
      
      $post_id = $this->insert_recipe($recipe);

      return $post_id;
    }

    private function get_slug($url){
      $exploded_str = explode('/',$url);
      $slug = $exploded_str[3];

      return $slug;
    }

    private function fetch_recipe($slug){
      
      $url = 'https://glutenfreeonashoestring.com/wp-json/wp/v2/posts/?slug='.$slug;

      $raw_body = $this->fetch_data($url);

      $recipe = new Recipe($raw_body);
      return $recipe; //array
    }

    private function insert_recipe($data){

      $args = [
        'post_title' => $data->title,
        'post_type' =>  'recipe',
        'post_status' =>  'publish',
        'post_name' =>  $data->slug,
        'tax_input' =>  [
          'recipe_category' =>  $data->categories,
        ]
      ];

      $post_id = wp_insert_post( $args, true );

      if($post_id !== 0){
        update_field('prep_time', $data->prep_time , $post_id);
        update_field('cook_time', $data->cook_time , $post_id);
        $this->set_featured_image( $data->image , $post_id);
        update_field('yield', $data->yield , $post_id);
        update_field('ingredients', $data->ingredients, $post_id);
        $this->update_repeater_field(['steps', 'step'], $data->steps, $post_id);
        
        return $post_id;

      }else{
        $this->error_obj[] = 'error: could not insert post';
      }

    }


    private function fetch_data($url){
      $json = wp_remote_get($url);
      $json_body = $json['body'];
      $raw_data = json_decode($json_body);
      if($raw_data){
        return $raw_data[0];
      } else {
        $this->error_obj[] = 'error: couldnt fetch post';
      }
    }

    private function update_repeater_field($keys, $values, $post_id){
      $parentKey = $keys[0];
      $childKey =$keys[1];

      foreach($values as $value){
        $row = [
          $childKey => $value
        ];
        add_row($parentKey, $row, $post_id);
      }

    }

    private function set_featured_image($image_id, $post_id){
      // var_dump(wp_get_attachment_image( $image_id, 'thumbnail' ));
      $attachment_id = set_post_thumbnail($post_id, $image_id);
      $success = gettype($attachment_id) === 'integer';
      if(!$success){
        $this->error_obj[] = 'error: couldn\'t set the image as the featured image'; 
      }
    }

  }//end of class

  new GFOAS_SCRAPE(); //*kick it off
}

class Recipe {
  public $title;
  public $slug;
  public $prep_time;
  public $cook_time;
  public $image;
  public $yield;
  public $ingredients;
  public $steps;
  public $categories;

  public $error_obj =[]; 

  function __construct($body){
    $this->set_fields($body);
  }

  private function set_fields($body){
    $acf = $body->acf;
    
    $this->title = $body->title->rendered;
    $this->slug = $body->slug;
    $this->categories = $categories;
    $this->prep_time = $acf->prep_time;
    $this->cook_time = $acf->cook_time;
    // $this->image = $acf->image_upload;
    $image_id = new Save_Media($acf->image_upload, $this->slug);
    $this->image = $image_id->get_image_id();
    $this->yield = $acf->yield;
    $this->ingredients = $acf->ingredient_text;
    $this->steps = $this->clean_up_steps($acf->step);
    $this->categories = $this->set_categories($body->categories);
  }

  private function set_categories($category_ids){
    //get current cats
    $current_categories = get_terms([
      'taxonomy' => 'recipe_category',
      'hide_empty' => false,
    ]);
    $current_category_slugs = [];
    foreach($current_categories as $current_cat){
      $current_category_slugs[] = $current_cat->slug;
    }

    //loop thru cat array arg
    $returned_category_ids = [];
    foreach($category_ids as $cat_id){
      $GFOAS_cat_url = 'https://glutenfreeonashoestring.com/wp-json/wp/v2/categories/'.$cat_id;
      $gfoas_cat = json_decode(wp_remote_get($GFOAS_cat_url)['body']);
      if(!$gfoas_cat){
        $this->error_obj[]='error: couldnt fetch category' . $cat_id;
      }
      //check if in current cats
      $key = array_search($gfoas_cat->slug, $current_category_slugs);

      if( $key !== false ){
        //if true add to return array

        $returned_category_ids[] = $current_categories[$key]->term_id;
      }else{
        // add cat to ours
        
        $new_cat_id = wp_insert_term(
          $gfoas_cat->name,  
          'recipe_category', 
          array(
            'slug'        => $gfoas_cat->slug,
          )
        );

        //add id to return array

        $returned_category_ids[] = $new_cat_id['term_id'];

      }
    }

    return $returned_category_ids;
  }

  private function clean_up_steps($steps){
    $cleaned_steps=[];
    
    foreach($steps as $step){
      $paragraph = $step->add_step;
      $cleaned_steps[] = str_replace(['<p>','</p>'],'',$paragraph);

    }
    return $cleaned_steps;
  }

}