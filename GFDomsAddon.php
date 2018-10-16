<?php

GFForms::include_addon_framework();

class GFDomsAddOn extends GFAddOn{	
	
	protected $_version = GF_Doms_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'domaddon';
	protected $_path = 'domplugin/domplugin.php';
	protected $_full_path = __FILE__;
	protected $_title = 'GF Doms Plugin';
	protected $_short_title = 'Doms Plugin'; /*title used in form > settings > subs delete tab*/

	private static $_instance = null;
	
	protected $form_choices = "FORM";
	protected $range_choice = "";
	
	public static function get_instance(){
		if (self::$_instance == null){
			self::$_instance = new GFDomsAddon();
		}
		return self::$_instance;
	}
	
	public function init(){
			parent::init();
			add_filter('gform_post_submission_1', array($this, 'save_name'));
			add_filter('gform_confirmation_1', array($this, 'conf_msg'));
		if ( is_admin() ) {
			//add_filter('gform_pre_render_2', 'populate_form');
			add_filter('gform_post_submission_2', array($this, 'save_selection'));
			add_filter('gform_confirmation_2', array($this, 'admin_conf_msg'));
		}
	}

//USER FORM
	public function save_name( $form ){
	/*
	*	create array, store entry id, and timestamp (converted to unix) when form is submitted
	*/
		$all = [];		
		$all_entries = GFAPI::get_entries(1);//form id 1
		$time_ids = [];
		
		//get date created of each submission and convert to unix format
		foreach($all_entries as $entry){
			$all += $entry;
			$rest = $entry['date_created'];
			$y = strtotime($rest);
			
			//populate array with id's associated with each submission time
			array_push($time_ids, $rest, $entry['id']);
		}	
		var_dump($time_ids);
	}
	
	public function conf_msg($confirmation){
		$confirmation = 'Thank you ' . $_POST['input_1_3'] . ' for your message<br>';
		$confirmation .= 'submitted on ' . $_POST['input_7'];
		return $confirmation;
	}
	
	
//ADMIN FORM
/*	function populate_form($form){
		//get all forms
		$forms = GFAPI::get_forms();
		
		//make a list of forms and remove the admin form
		$all_forms = [];
		foreach ($forms as $form){
			array_push($all_forms, $form['title']);
		}
		$key = array_search('Admin_Form', $all_forms);
		unset($all_forms[$key]);
		var_dump($all_forms);
		
		//pass list data to drop down option in the form

	}
*/
	
	
	public function plugin_page(){	
		echo '<h1>Admin Manage Entries</h1>';
		gravity_form( 2, false, false, false, '', false );
	}
	
	public function save_selection(){
		
	/*
	*	search through dictionary to compare all entries older than user selected option
	*	grab the id of all matching entries
	*	grab form 1 entry id's to delete
	*/
		
	}
	
	public function admin_conf_msg($admin_conf){
		$admin_conf = '<br><br><h2>Entries deleted succesfully</h2>';
		$admin_conf .= '<br><br>You selected form ID: ' . $_POST['input_1'] . '<br>';
		if ($_POST['input_4'] == 'all'){
			$admin_conf .= 'and to delete ' . $_POST['input_4'] . ' entries<br>';
		}else{
			$admin_conf .= 'and to delete all entries older than: ' . $_POST['input_4'];
		}
		return $admin_conf;
	}
	
	
}