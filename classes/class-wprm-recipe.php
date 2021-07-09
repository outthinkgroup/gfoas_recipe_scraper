<?php

class WPRM_Recipe {
  public $title;
  public $slug;
  public $prep_time;
  public $cook_time;
  public $image;
  public $yield;
  public $ingredients;
  public $steps;
  public $categories;
  public $recipe_notes;

  //used to get the featured image if none is set;
  private $post_content;

  public $error_obj =[]; 

  function __construct($wprm_body, $wp_post_body){
    $this->set_wprm_fields($wprm_body); 
    $this->set_post_fields($wp_post_body); //for things like categories and content
  }

  private function set_post_fields($body) {
    $this->title = $body->title->rendered;
    $this->slug = $body->slug;
    $this->categories = $this->set_categories($body->categories);
  }

  private function set_wprm_fields($body){
    $wprm = $body->recipe;
    $this->prep_time = $wprm->prep_time . " minutes";
    $this->cook_time = $wprm->cook_time . " minutes";
    $this->image = $this->get_featured_image($wprm->image_url);
    $this->yield = "Serves: " . $wprm->servings . "</br> Serving Size:" . $wprm->serving_size;
    $this->ingredients = $this->format_ingredients($wprm->ingredients_flat);
    $this->steps = $this->format_steps($wprm->instructions);
    $this->recipe_notes = $wprm->notes;
  }

  private function get_featured_image($url){
    $image_id = new Save_Media($url, $this->slug);
    return $image_id->get_image_id();
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
  
  /* 
  * Prepare Steps for ACF Repeater.
  */
  private function format_steps($steps ){
    $formatted_steps = [];

    foreach($steps as $step){

      if($step->type == "group") {
        continue; // Skipping the Groups
      }

      if(isset($step->text) && $step->text!=="" ){
        $formatted_steps[] =  $step->text; //Assuming the text automatically has <p> tags
      }
    }
    
    return $formatted_steps;
  }
  
  /* 
  Recursive function to loop ingredients and create a formatted html list.
  */
  private function format_ingredients($ingredients, $depth=0) {
    $ingredients_html = count($ingredients) <= 1 ? "" : "<ul>";

    foreach($ingredients as $ingredient){
      $formatted_ingredient = "";

      //Build the Ingredient with amount and unit and name
      if(isset($ingredient->name) && $ingredient->name!=="" ){
        $ingredient_name = "";
        $heading_depth = $depth + 4;//start at h4 then as we go deeper increase heading_depth
        
        if(isset($ingredient->amount) && $ingredient->amount!=="" ){
          $ingredient_name .= $ingredient->amount; 
          $ingredient_name .= " " . $ingredient->unit; 
        }

        $ingredient_name .= " $ingredient->name";
        $formatted_ingredient .= "<h$heading_depth>$ingredient_name</h$heading_depth>";
      }

      // Notes
      if( isset( $ingredient->notes ) && $ingredients->notes !== "" ){
        $formatted_ingredient .= "<small>$ingredients->notes</small>";
      }

      // Sub ingredients
      if(isset($ingredient->ingredients) && !empty($ingredient->ingredients) && is_array($ingredient->ingredients) && count($ingredient->ingredients) > 0 ) {
        $ingredient_list = $this->format_ingredients($ingredient->ingredients, $depth+1);
        $formatted_ingredient .= $ingredient_list;
      }

      $html_tag = count($ingredients) <= 1 ? "div" : "li";

      $ingredients_html .= "<$html_tag>" . $formatted_ingredient . "</$html_tag>";
    }

    $ingredients_html .= count($ingredients) <= 1 ? "" : "</ul>";
    return $ingredients_html;
  }

  // private function fetch_media_url($id){
  //   $body = $this->fetch_data("https://glutenfreeonashoestring.com/wp-json/wp/v2/media/$id");
  //   return $body->source_url;
  // }

  // private function parse_content_for_image($post_content){
  //   require_once RECIPE_SCRAPER_PATH . '/includes/simple_html_dom.php';
  //   $url = str_get_html($post_content)->find('img', 0)->src;
  //   if($url){
  //     return $url;
  //   }
  //   $this->error_obj[]='error: couldn\'t find an image in content';
  //   return false;
  // }

  public function fetch_data($url){
    $json = wp_remote_get($url);
    $json_body = $json['body'];
    $raw_data = json_decode($json_body);
    if($raw_data){
      return $raw_data[0];
    } else {
      $this->error_obj[] = 'error: couldnt fetch post';
    }
  }

}
