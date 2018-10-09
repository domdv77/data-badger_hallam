<?php

/*
Plugin Name: GF Doms Plugin
Plugin URI:
Description:
Version: 1.0
Author: Domenico De Vivo
Author URI:
*/

define('GF_Doms_VERSION', '0.0.1');

add_action( 'gform_loaded', array( 'GF_Doms_Addon_Boostrap', 'load' ), 5);

class GF_Doms_Addon_Boostrap{
	public static function load(){
		if (! method_exists('GFForms', 'include_addon_framework')){
			return;
		}	
		require_once( 'GFDomsAddon.php');
		
		GFAddOn::register('GFDomsAddon');
	}
		
}

function gf_doms_addon(){
	return GFDomsAddon::get_instance();
}