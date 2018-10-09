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
	
	//protected $sname;
	//protected $sdate;
	protected $form_choice;
	protected $range_choice;
	
	public static function get_instance(){
		if (self::$_instance == null){
			self::$_instance = new GFDomsAddon();
		}
		return self::$_instance;
	}
	
	public function init(){
			parent::init();
			add_filter('gform_pre_submission_1', array($this, 'save_name'));
			add_filter('gform_confirmation_1', array($this, 'conf_msg'));
		if ( is_admin() ) {
			add_filter('gform_pre_submission_2', array($this, 'save_selection'));
			add_filter('gform_confirmation_2', array($this, 'admin_conf_msg'));
		}
	}
	
	public function save_name( $form ){
		//$this->sname = "";
		//$this->sdate = "";
		$all = [];
		
		$all_entries = GFAPI::get_entries(1);//form id 1
		
		foreach($all_entries as $entry){
			$all += $entry;
			//echo $entry['date_created'] . "###";
	
	/*
	* 	TO DO
	*	create dictionary, store entry id, and timestamp (converted to unix) when form is submitted
	*/
		
		}
		//var_dump($all);
		//var_dump($all_entries);
		//var_dump($all['date_created']);
		//die;		
		//$this->sname = $_POST['input_1_3']; 
		//$this->sname = $all['1.3'];
		//$this->sdate = $_POST['input_7'];
		//$this->sdate = $all['7'];
		//echo strtotime($all['date_created']); //convert timestamp to unix		
				
	}
	
	public function conf_msg($confirmation){
		$confirmation = 'Thank you ' . $_POST['input_1_3'] . ' for your message<br>';
		$confirmation .= 'submitted on ' . $_POST['input_7'];
		//$confirmation = 'Thank you ' . $this->sname . ' for your message<br>';
		//$confirmation .= 'submitted on ' . $this->sdate;
		return $confirmation;
	}
	
	public function plugin_page(){	
		echo '<h1>Admin Manage Entries</h1>';
		gravity_form( 2, false, false, false, '', false );
	}
	
	public function save_selection(){
		$this->form_choice = "";
		$this->range_choice = "";
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
	
	/*
	* TO DO
	*	search through dictionary to compare all entries older than user selected option
	*	grab the id of all matching entries
	*	grab form 1 entry id's to delete
	*/
		
	}
	
	public function admin_conf_msg($admin_conf){
		$admin_conf = '<br><br><h2>Entries deleted succesfully</h2>';
		//$admin_conf .= '<br><br>You selected form ID: ' . $_POST['input_1'] . '<br>';
		$admin_conf .= '<br><br>You selected form ID: ' . $_POST['input_1'] . '<br>';
		if ($_POST['input_4'] == 'all'){
			//$admin_conf .= 'and to delete ' . $this->range_choice . ' entries<br>';
			$admin_conf .= 'and to delete ' . $_POST['input_4'] . ' entries<br>';
		}else{
			//$admin_conf .= 'and to delete all entries older than: ' . $this->range_choice;
			$admin_conf .= 'and to delete all entries older than: ' . $_POST['input_4'];
		}
		return $admin_conf;
	}
	
	
}