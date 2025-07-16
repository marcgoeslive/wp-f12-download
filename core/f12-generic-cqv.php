<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Adding custom query vars to wordpress
 */
function f12d_generic_query_vars_filter($vars){
	$vars[] = 'hash';
	$vars[] = 'f12_force_download';
	return $vars;
}

add_filter('query_vars', 'f12d_generic_query_vars_filter');