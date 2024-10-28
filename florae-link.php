<?php
/**
 * @package Florae
 */
/*
Plugin Name: Add Florae link
Plugin URI: https://florae.it/
Description: In your botanical blog put the scientific name in title and you will find the link to the Italian florae in your articles
Version: 1.0
Author: caiofior
Author URI: http://caio.florae.it/
License: GPLv2 or later
Text Domain: florae
*/
add_action( 'the_post', 'add_post_link', 10, 1);
 
function add_post_link($post_to_add_florae_link ) {
    if ($post_to_add_florae_link->post_type != 'post') {
        return;
    }
    if ($post_to_add_florae_link->post_title == '' ) {
        return;
    }
    $url_florae_search = 'https://www.florae.it/xhr.php?task=taxa&action=search&name='.urlencode($post_to_add_florae_link->post_title);
    $response_florae_search = wp_remote_retrieve_body(wp_remote_get( $url_florae_search ));
    if ($response_florae_search == '' || $response_florae_search == false) {
       return;
    } else {
       $decoded_response_florae_search = json_decode($response_florae_search);
       if (json_last_error() != 0) {
          return;
       }
    }
    $link_to_florae = '<a rel="linktoflorae" href="'.$decoded_response_florae_search.'">Flora d\'Italia.</a>';
    preg_match("'<a rel=\"linktoflorae\"(.*?)</p>'si", $post_to_add_florae_link->post_content, $match);
    if($match) {
       preg_replace("'<a rel=\"linktoflorae\"(.*?)</p>'si", $link_to_florae, $post_to_add_florae_link->post_content);
    } else {
      $post_to_add_florae_link->post_content = '<p>'.$link_to_florae.'</p>'.$post_to_add_florae_link->post_content;
    }
}