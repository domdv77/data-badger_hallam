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
	
	//protected $form_choice = "FORM";
	//protected $range_choice = "";
	
	public static function get_instance(){
		if (self::$_instance == null){
			self::$_instance = new GFDomsAddon();
		}
		return self::$_instance;
	}
	
	public function init(){
			parent::init();
			add_filter('gform_confirmation_1', array($this, 'conf_msg'));
			add_filter('gform_pre_render_2', array($this, 'populate_choices'));
			add_filter('gform_pre_validation_2', array($this, 'populate_choices'));
			add_filter('gform_admin_pre_render_2', array($this, 'populate_choices'));
			add_filter( 'gform_pre_submission_filter_2', array($this, 'populate_choices'));			
			add_filter('gform_post_submission_2', array($this, 'save_selection'));
			add_filter('gform_confirmation_2', array($this, 'admin_conf_msg'));
	}

//USER FORM	
	public function conf_msg($confirmation){
		//show personal user message on successful form submission
		$confirmation = 'Thank you ' . $_POST['input_1_3'] . ' for your message<br>';
		$confirmation .= 'submitted on ' . $_POST['input_7'];
		return $confirmation;
	}
	
	
//ADMIN FORM
	function populate_choices($form){
		//pre-populate admin form field 1 (dropdown) with all installed form
		//to allow admin to select which form to process
		
		//get all forms
		$myforms = GFAPI::get_forms();
		
		//pass list data to drop down option in the form		
		$choices = array();
			
		foreach($myforms as $formt){
			if ($formt['title'] != 'Admin_Form'){
				$choices[] = array('value' =>$formt['title'], 'text' =>$formt['title']);
			}
		}
			$field['choices'] = $choices;
			//print_r($choices);//DEBUG
			
		foreach( $form['fields'] as &$field ) {
			if ( $field->id == 1 ) {
				$field->choices = $choices;
			}
		}		
		return $form;
	}


	
	public function plugin_page(){	
		echo '<h1>Admin Manage Entries</h1>';
		//gravity_form( 2, false, false, false, '', false );//embedded dashboard form removed 
		echo '<a href="http://localhost/ddv_test_site/admin/">Link</a>';//change hard coded link
	}
	
	
	
	public function save_selection($form){		
	/*
	*	search through dictionary to compare all entries older than user selected option
	*	grab the id of all matching entries
	*	grab user-form entry id's to delete
	*/
	
		//get all forms
		$myforms = GFAPI::get_forms();
		//print_r($myforms);//DEBUG
		
		foreach ($myforms as $data){
			if($data['title'] == "FORM"){
				$xformid = $data['id'];
			}
			elseif($data['title'] == "Admin_Form"){
				$admformid = $data['id'];
			}
		}
		//echo $xformid;//DEBUG
		
		$all_data = [];
		$all_data = GFAPI::get_entries($xformid);#select form to process via form_id and get entries
		//var_dump($all_data);//DEBUG
		$time_ids = [];
		
		//get date created of each submission and convert to unix format
		foreach($all_data as $entry){
			$rest = $entry['date_created'];
			$y = strtotime($rest);//unix time
			
			//populate array with id's associated with each submission time
			array_push($time_ids, $y, $entry['id']);
		}	
		//var_dump($time_ids);//DEBUG
		
		
		//get admin selected options
		
		$adm_data = [];
		$adm_data = GFAPI::get_form($admformid);
		//print_r($adm_data);
		$adm_entry = GFAPI::get_entry(4);
		//print_r( $adm_entry);
		
		//foreach($adm_data as $adm_entry){
		//	$sel_time = $adm_entry[4];
		//	echo $sel_time;
		//}
		
		//search user form for entries in search time period
		//1 DAY
		$start_date = date( '2018-10-01', strtotime('-30 days') );//Y-m-d TEST
		$end_date = date( '2018-10-12', time() );//Y-m-d TEST
		
		$search_criteria['start_date'] = $start_date;
		$search_criteria['end_date'] = $end_date;
		
		$search_criteria = array();
		$sorting = array();
		$paging = array( 'offset' => 0, 'page_size' => 2 );
		$entries = GFAPI::get_entries( $xformid, $search_criteria, $sorting, $paging );
		
		//print_r($entries);//DEBUG
		//return $xformid;
	}
	
	public function admin_conf_msg($admin_conf){
		$admin_conf = '<h2>Entries deleted successfully</h2>';
		$admin_conf .= '<br><br>You selected form ID: ' . $_POST['input_1'] . '<br>';//to do: Replace post with form fields
		//$admin_conf .= '<br><br>You selected form ID: ' . $xformid . '<br>';
		if ($_POST['input_4'] == 'all'){
			$admin_conf .= 'and to delete ' . $_POST['input_4'] . ' entries<br>';
		}else{
			$admin_conf .= 'and to delete all entries older than: ' . $_POST['input_4'];
		}
		return $admin_conf;
	}
	
	
}