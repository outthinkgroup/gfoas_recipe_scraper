<?php
class Recipe_Post_Type {

  public static function register(){
    $labels = [
      'name'                  => _x( 'Recipes', 'Post type general name', 'textdomain' ),
      'singular_name'         => _x( 'Recipe', 'Post type singular name', 'textdomain' ),
      'menu_name'             => _x( 'Recipes', 'Admin Menu text', 'textdomain' ),
      'name_admin_bar'        => _x( 'Recipe', 'Add New on Toolbar', 'textdomain' ),
    ];

    $args = [
      'labels'             => $labels,
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => 'recipe' ),
      'capability_type'    => 'post',
      'rewrite' => array( 'slug' => 'projects' ),
      'has_archive' => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
    ];

    register_post_type( 'recipe', $args );
    flush_rewrite_rules();

    register_taxonomy('recipe_category', 'recipe', [
      'labels'  =>  [
        'name'                  => _x( 'Recipe Categories', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Recipe Category', 'Post type singular name', 'textdomain' ),
      ],
      'hierarchical'  =>  true

    ]);
  }
  
}