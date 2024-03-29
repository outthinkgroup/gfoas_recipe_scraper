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
    $this->yield = "Serves: " . $wprm->servings;
    $this->ingredients = $this->format_ingredients($wprm->ingredients_flat);
    $this->steps = $this->format_steps($wprm->instructions_flat);
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

      if(property_exists($step,'text') && $step->text!=="" ){
        $formatted_steps[] =  $step->text; //Assuming the text automatically has <p> tags
      }
    }

    return $formatted_steps;
  }

  /*
  Function to loop ingredients and create a formatted html list.
  */
  private function format_ingredients($ingredients, $depth=0) {
    $ingredients_html = "";

    foreach($ingredients as $index=>$ingredient){
      $formatted_ingredient = "";
      $ingredient_type = "ingredient";
      // if Ingredient is type group it is a heading
      if(property_exists($ingredient,"type") && $ingredient->type == "group") {
        $ingredient_type = "group";
        $formatted_ingredient = "<h4>" . $ingredient->name . "</h4> ";
      } else {
        // if this is the first ingredient, add the <ul>
        if($index == 0) {
          $ingredients_html .= "<ul>";
        }
        $formatted_ingredient .= "<li>";
        // else its an ingredient
        if(property_exists($ingredient,"name") && $ingredient->name!=="" ){
          $ingredient_name = "";

          if(property_exists($ingredient,"amount") && $ingredient->amount!=="" ){
            $ingredient_amount = "";
            $ingredient_amount .= $ingredient->amount;
            $ingredient_amount .= " " . $ingredient->unit;

            if (property_exists( $ingredient, "converted" ) ) {
              $ingredient_amount .= " (" . $ingredient->converted->{"2"}->amount . " " . $ingredient->converted->{"2"}->unit . ")";
            }


            $ingredient_name .= $ingredient_amount;
          }

          $ingredient_name .= " $ingredient->name";
        }

        // Notes
        if( property_exists( $ingredient,"notes" ) && $ingredient->notes !== "" ){
          $ingredient_name .= "<br/><small style=\"font-size:.8em; font-style:italic;\">$ingredient->notes</small>";
        }

        $formatted_ingredient .= $ingredient_name."</li>";
      }

      $ingredients_html .= $formatted_ingredient;

      // check to see whats next to close html tags if needed
      $next_ingredient = $ingredients[$index+1];
      if( ( property_exists($next_ingredient,"type") && $next_ingredient->type == "group" ) || $next_ingredient == null) {
        $ingredients_html .= "</ul>";
      }
      if($ingredient_type === "group") {
        $ingredients_html .= "<ul>";
      }
    }

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
