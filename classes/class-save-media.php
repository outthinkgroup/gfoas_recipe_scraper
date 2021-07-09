<?php
class Save_Media {
  public $url;
  public $filename;
  public $image_id;

  function __construct($url, $filename){
    $this->url = $url; 
    $this->filename = $filename; 

    $this->image_id = $this->save_to_media_library();

  }
  public function get_image_id(){
    return $this->image_id;
  } 


  public function save_to_media_library(){
    // Gives us access to the download_url() and wp_handle_sideload() functions
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    // URL to the WordPress logo
    $url = $this->url;
    $timeout_seconds = 5;

    // Download file to temp dir
    $temp_file = download_url( $url, $timeout_seconds );
    $wp_filetype = wp_check_filetype(basename($url), null );
    
    if ( !is_wp_error( $temp_file ) ) {
      // Array based on $_FILE as seen in PHP file uploads
      $file = array(
          'name'     => basename($url), // ex: wp-header-logo.png
          'type'     => $wp_filetype['type'],
          'tmp_name' => $temp_file,
          'error'    => 0,
          'size'     => filesize($temp_file),
      );

      $overrides = array(
          'test_form' => false,
          'test_type' => false,
      );
      
      // Move the temporary file into the uploads directory
      $results = wp_handle_sideload( $file, $overrides );
      
      if ( !empty( $results['error'] ) ) {
          
        return $results['error'];

      } else {

        $filename  = $results['file']; // Full path to the file
        $local_url = $results['url'];  // URL to the file in the uploads dir
        $type      = $results['type']; // MIME type of the file
        


        $attachment = array(
          'guid'    => $local_url,
          'post_mime_type' => $wp_filetype['type'],
          'post_title' => $filename,
          'post_content' => '',
          'post_status' => 'inherit'
        );
        
        $attach_id = wp_insert_attachment( $attachment, $local_url );

        $attach_data = wp_generate_attachment_metadata( $attach_id, $local_url );
        
        $post_id = wp_update_attachment_metadata( $attach_id, $attach_data );
        
        return $attach_id;
      }
    } else {
      return "ERROR";
    }
  }
}