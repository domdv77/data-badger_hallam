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
	
	protected $form_choice = "FORM";
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
			add_filter('gform_pre_render_2', array($this, 'populate_choices'));
			add_filter('gform_pre_validation_2', array($this, 'populate_choices'));
			add_filter('gform_admin_pre_render_2', array($this, 'populate_choices'));
			add_filter( 'gform_pre_submission_filter_2', array($this, 'populate_choices'));			
			add_filter('gform_post_submission_2', array($this, 'save_selection'));
			add_filter('gform_confirmation_2', array($this, 'admin_conf_msg'));
	}

//USER FORM
	public function save_name( $form ){
	/*
	*	create array, store entry id, and timestamp (converted to unix) when form is submitted
		for deleting submissions based on date created
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
		//var_dump($time_ids);//DEBUG
	}
	
	public function conf_msg($confirmation){
		//show personal user message on successful form submission
		$confirmation = 'Thank you ' . $_POST['input_1_3'] . ' for your message<br>';
		$confirmation .= 'submitted on ' . $_POST['input_7'];
		return $confirmation;
	}
	
	
//ADMIN FORM
	function populate_choices($form){
		//get all forms
		$myforms = GFAPI::get_forms();
		//print_r($myforms);//DEBUG
		
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
		//gravity_form( 2, false, false, false, '', false );
		echo '<a href="http://localhost/ddv_test_site/admin/">Link</a>';//change hard coded link
	}
	
	public function save_selection($form){		
	/*
	*	search through dictionary to compare all entries older than user selected option
	*	grab the id of all matching entries
	*	grab form 1 entry id's to delete
	*/
				
		//TEST CODE - NOT FULLY FUNCTIONAL
			$all_selections = [];
			$all_data = [];
			$all_data = GFAPI::get_entries(2);#select form id to get entries from		
			foreach ($all_data as $i){
				$all_selections +=$i;
			}
			//var_dump($all_selections);
				//echo $all_selections['form_id'];
				//var_dump($all_selections['1']);
				$this->form_choice = $all_selections['1'];
				$this->range_choice = $all_selections['4'];
				
			//get the field
			///$field = GFFormsModel::get_field($form, 2);
			//get the html content
			//$content = $field->content;
			//var_dump($content);
	
	}
	
	public function admin_conf_msg($admin_conf){
		$admin_conf = '<h2>Entries deleted successfully</h2>';
		$admin_conf .= '<br><br>You selected form ID: ' . $_POST['input_1'] . '<br>';//to do: Replace post with form fields
		if ($_POST['input_4'] == 'all'){
			$admin_conf .= 'and to delete ' . $_POST['input_4'] . ' entries<br>';
		}else{
			$admin_conf .= 'and to delete all entries older than: ' . $_POST['input_4'];
		}
		return $admin_conf;
	}
	
	
}