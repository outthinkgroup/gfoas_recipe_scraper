<?php

// add_action('wp', function(){
  $wprm_recipe = json_decode(file_get_contents(RECIPE_SCRAPER_PATH . "/__Mocks__/wprm-recipe-apple-crips.json"));
  $wp_post = json_decode(file_get_contents(RECIPE_SCRAPER_PATH . "/__Mocks__/wp-post.json"));
  $recipe = new WPRM_Recipe($wprm_recipe[0], $wp_post[0]);
  do_action('qm/debug', $recipe->ingredients);
// });
