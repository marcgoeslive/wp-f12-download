<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Create the custom post type for the download section
 */
function f12d_hook_init(){
    register_post_type("f12d_download",array(
        'labels' => array(
            'name' => __('Downloads', "f12-download"),
            'singular_name' => __('Download', "f12-download")
        ),
        'menu_icon' => 'dashicons-download',
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'f12_download'),
        'capability_type' => 'page',
        'supports' => array(
            "title",
	        "revisions"
        )
    ));

    // Generic Links
    register_post_type("f12d_generic",array(
    	'labels' => array(
			'name' => __('GenericLinks',"f12-download"),
		    'singular_name' => __('GenericLink',"f12-download")
	    ),
	    'public' => true,
	    'has_archive' => true,
	    'rewrite' => array('slug' => 'generic-file'),
	    'show_in_menu'=>'edit.php?post_type=f12d_download',
	    'capability_type' => 'page',
	    'supports' => array(
	    	"title",
		    "revisions"
	    )
    ));

}

// Create Hooks
add_action("init","f12d_hook_init");