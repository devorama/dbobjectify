<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* @file DBControllerBase.php
* @brief Controller class for DB Objectify Base Controller Class.
* 		The purpose of this class is to help generate html forms and handle posts
* 		from pages back based on Objectify Model and DB Objects. The end goal is to speed up
* 		page generation and let one just focus on the logic required. Input types will try to use latest 
* 		allowed by browser accessing the page.
*  
* 
* This system is done in dedication to my children. 
* To my son I pray and believe that one day you will get well and after all these years 
* of suffering you will be able to go and chase your dreams that has been taken from you
* with this illness. To my daugther I know you have lived in the shadow of your brothers
* illness all this time and want to thank you even when times are hard sometimes you always 
* are there for him and love him fully. You will become a wonderfull woman I believe one day.
* Love you with my whole heart.
*  
* @author Devorama
* @copyright Copyright (c) 2018  Devorama
* @license MIT
* @link  https://github.com/devorama/dbobjectify
* Version : 1.0
*/ 
class DBControllerBase extends CI_Controller {
	   /**
	   @brief The goal is to have a array that will map the db fields used internally
	   to form fields that are used client side for generation and handling posts
	   from clients. If db field is not set the db fieldname itself is used
	   */
	   protected $db_form_field_mappings;
	   
	   /**
	   @brief Each db field will be set to a default type based on its type, but if one wants to overwrite the default
	   one can set the db field equal to a type on requires
	   */
	   protected $db_form_field_types;

	   /**
	   @brief If any extra definitions are required for input type of field one can add them here against the field and a string with all info
	   */
	   protected $db_form_field_extra;
	   
	   /**
	   @brief If set the function will be called on listing type fields like select, lists and radio buttons to provide a array of items 
				and the selected item back function must be defined as follows 
				func($oDBObjects,$targettable,$targetfield,&$selectedkeyArray); 
				and function must return a array with key value pair to populte the select input types
	   */
	   protected $form_listfield_func = null;
	   
   
		/** 
		*  @brief constructor for model call. 
		*  @return None 
		*  @details Constructor for model class.
		*/ 
		 public function __construct(){
			parent::__construct();	
			$this->load->library('user_agent'); //used to discovery what browser agent is being used
			$this->load->helper('form'); //used for creating form input controls
			$this->load->helper('date'); //used with storing values retrieved from input to db
			log_message('debug', 'DBControllerBase Class Initialized');
		 }   
		 
		 /**
		  *  @brief Builds a input controls based on the database objects passed and the array of fields that must be created for objects.
		  *  
		  *  @param [in] $oDBObject The database objects created with a objectifyer model that containts the data. This objects is used to get field names and types.
		  *  @param [in] $aFieldlist Array with list of db fields that is required to be created
		  *  @param [in] $aOptions Array that holds the optional settings that can be used to create the controls
		  *  				create_labels   = If set labels will be created based on fieldname in Camel case all underscores will be removed. Will be created with class formlabel
		  *                 populate_values = If set the values in the DB object will be used with the field creation
		  *  				pk_hidden       = If set will create a hidden field with the primary key retrieved from the object
		  *  				use_tables      = If set table rows are used to create and seperate input fields. row will be of class 'formrow' and col will be of class 'formcol'
		  *					number_cols     = default to 1, else will create div with inner divs for amount cols or table with number of cols as specified
		  *  @return String that represents all the input fielsds requisted
		  *  
		  *  @details Builds a input controls based on the database objects passed and the array of fields that must be created for objects. 
		  */           
		 public function build_input_controls($oDBObject,$aFieldlist,$aOptions){
			   $bCreateLabels = isset($aOptions['create_labels'])?$aOptions['create_labels'] : false;
			   $populateDBvalues = isset($aOptions['populate_values'])?$aOptions['populate_values'] : false;
			   $bCreatPrimaryKeyHidden = isset($aOptions['pk_hidden'])?$aOptions['pk_hidden'] : false;
			   $useTableRows = isset($aOptions['use_tables'])?$aOptions['use_tables'] : false;
			   $numCols = isset($aOptions['number_cols'])?$aOptions['number_cols'] : 1;
			   
			   $dbFieldList = $oDBObject->get_fields_def();
		       $dbPK = $oDBObject->get_primary_key();
			   $dbTableName = $oDBObject->get_tablename();
			   $returnS = '';
			   if (($bCreatPrimaryKeyHidden == true) && ($dbPK !== '')){
					if (isset($this->db_form_field_mappings[$dbPK])) {
						 $fieldname = $this->db_form_field_mappings[$dbPK];
					 } else if (isset($dbFieldList[$dbPK])) {
						  //field dont have a mapping , see if it exist in db object at least
						  $fieldname = strtolower($dbPK);
					 }				   
				   $returnS .= form_hidden($fieldname,$oDBObject->$dbPK);
			   }
			   $counter = 1;
			   $closedRow = false;
			   foreach ($aFieldlist as $field) {
				     $fieldname = null;
					 $fieldmeta = null;
				      //check if field has a mapping.
				     if (isset($this->db_form_field_mappings[$field])) {
						 $fieldname = $this->db_form_field_mappings[$field];
					 } else if (isset($dbFieldList[$field])) {
						  //field dont have a mapping , see if it exist in db object at least
						  $fieldname = strtolower($field);
					 }
					 //if set, then create else ignore
					 if ($fieldname !== null){
						 $fieldFancy = ucwords(strtolower($fieldname),'_'); 
						 $fieldFancy = str_replace('_',' ',$fieldFancy);
						 $fieldmeta = $dbFieldList[$field];
						 if ($useTableRows == true) {
							if ($counter == 1) {
							  $returnS .= '<tr class="formrow">';
							  $closedRow = false;
							}
							$returnS .= '<td class = "formcol">';
						 } else {
							if (($numCols > 1) && ($counter == 1)) {
								$returnS .= '<div class="formrow">';
								$closedRow = false;
							}
							$returnS .= '<div class = "formcol">';
						 }	
						 if ($bCreateLabels) {
							 $returnS .=  form_label($fieldFancy,$fieldname,array('class' => 'formlabel'));
						 }
						 $inpArr = $this->get_input_type_array($fieldmeta);
						 $inpArr['name'] = $fieldname;
						 $inpArr['id'] = $fieldname;
						 if ($populateDBvalues == true) {
							 $inpArr['value'] = $oDBObject->$field; 
						 }
						 $extra = '';
						 if (isset($this->db_form_field_extra[$field])) {
							 $extra = $this->db_form_field_extra[$field];
						 }
						 
						 switch ($inpArr['type']) {
							   case 'textarea' :
											$returnS .=  form_textarea($inpArr,$inpArr['value'],$extra); 
											break;
							   
							   case 'list' :
							   case 'radio' :
							   case 'select' :
											if ((isset($this->form_listfield_func))) {
												$selectID = array();
												$fname = $this->form_listfield_func;
												//$list = $fname($oDBObject,$dbTableName,$field,&$selectID);
												$list = call_user_func_array(array($this,$fname), array($oDBObject,$dbTableName,$field,&$selectID));
												$extra .= ' id = '.$inpArr['id'].' class = '.$inpArr['class'];
												if ($inpArr['type'] == 'select') {
													$returnS .=  form_dropdown($inpArr['name'],$list,$selectID,$extra);
													
												} else if ($inpArr['type'] == 'list') {
													$returnS .=  form_multiselect($inpArr['name'],$list,$selectID,$extra);	
												} else if ($inpArr['type'] == 'radio') {
													foreach ($list as $key => $val) {
														$inpArr['value'] = $key;
														$returnS .= form_radio($inpArr,$key,($key == $selectID[0]),$extra).$val;
													}
												}	
											} else {
												$returnS .=  form_input($inpArr,$inpArr['value'],$extra);
											}
											break;
							   case 'password' :
											$returnS .=  form_password($inpArr,$inpArr['value'],$extra);
											break;
							   case 'checkbox' :
							                 $checked = $inpArr['value'] == '1';
											 if (!$checked) {
												 $checked = $inpArr['value'] == true;
											 }
											$returnS .=  form_checkbox($inpArr,$inpArr['value'],$checked,$extra);
											break;											
								default	:		
										$returnS .=  form_input($inpArr,$inpArr['value'],$extra);
										break;
											
						 }
							 
						   
						 if ($useTableRows == true) {
							$returnS .= '</td>';
							if ($counter >= $numCols) {
								$counter = 0;							  
								 $returnS .= '</tr>';
								 $closedRow = true;
							}
						 } else {
							$returnS .= '</div>';
							if (($numCols > 1) && ($counter >= $numCols)) {
								$counter = 0;							  
								$returnS .= '</div>';
								$closedRow = true;
								
							}	
						 }	
						 $counter++;
 					 }

			   }
			   if ($closedRow == false) {
				if ($useTableRows == true) {
						 $returnS .= '</tr>';
				 } else {
					if (($numCols > 1) ) {
						$returnS .= '</div>';
					}	
				 }						 
			   }			   
			   return $returnS;
			   
		 }
		 
		 /**
		  *  @brief Get a array with input control type values
		  *  
		  *  @param [in] $fieldmeta The DB field meta data that is returned from the DB Object
		  *  @return Array that containts the input type fields info
		  *  
		  *  @details Get a array with input control type values
		  */
		 private function get_input_type_array($fieldmeta){
			 $rVal =  array(
					'type'  => 'text', //default type
					'name'  => '', //need to set
					'id'    => '', //need to set
					'value' => '', //need to set
					'class' => '' //need to set
			 );
			 
			 $rVal['class'] = $this->get_input_classname(strtolower($fieldmeta->type));
			 if (isset($this->db_form_field_types[$fieldmeta->name])) {
				  $rVal['type'] = $this->setval_if_html5_browser($this->db_form_field_types[$fieldmeta->name],"text");
			 } else {
				 //if no preset type set
				switch ($fieldmeta->type) {
					case "string" : $rVal['type'] = "text";
								   break;
					
					case "int" :  $rVal['type'] = $this->setval_if_html5_browser("number","text");
								   break;
					
					case "decimal" : $rVal['type'] = $this->setval_if_html5_browser("number","text");
								   break;
					case "datetime" : $rVal['type'] = $this->setval_if_html5_browser("datetime","text");
								   break;
					case "year" : $rVal['type'] = "text";
								   break;
					case "date" : $rVal['type'] = $this->setval_if_html5_browser("date","text");
								   break;
					case "time" : $rVal['type'] = $this->setval_if_html5_browser("time","text");
								   break;
					case "blob" : $rVal['type'] = "text";
								   break;
					case "null" : 
								   break;
								   
					case "unknown" : 
								   break;
								   
				}	
			 }		
			 return $rVal;	
		 }
		 
		 /**
		  *  @brief Generates a class name based on field meta type. To allow css etc to work with field as required
		  *  
		  *  @param [in] $fieldtype Field type as recieved from field meta in DB object
		  *  @return String representing the class type (can be blank)
		  *  
		  *  @details Generates a class name based on field meta type. To allow css etc to work with field as required
		  */
		 private function get_input_classname($fieldtype){
			 switch ($fieldtype) {
					case "string" : return "string";
								   break;
					
					case "int" :  return "integer";
								   break;
					
					case "decimal" : return "decimal";
								   break;
					case "datetime" : return "datetime";
								   break;
					case "year" : return "year";
								   break;
					case "date" : return "date";
								   break;
					case "time" : return "time";
								   break;
					case "blob" : return "blob";
								   break;
					case "null" : return "";
								   break;
								   
					case "unknown" : return "";
								   break;
								   
				}
		 }
		 
		 /**
		  *  @brief Based on in browser can handle the required input type the wanted field will be returned else the default would be returned
		  *  
		  *  @param [in] $wantedval Input type required for control
		  *  @param [in] $defaultifnot Default if browser cant support the input type
		  *  @return String that browser can handle
		  *  
		  *  @details Based on in browser can handle the required input type the wanted field will be returned else the default would be returned
		  */
		 private function setval_if_html5_browser($wantedval,$defaultifnot){
			 $rVal = $wantedval;
			 if (($this->agent->is_browser()) || ($this->agent->is_mobile()) ){
				 $agent = strtolower($this->agent->browser());
				 $version = $this->agent->version();
				 if ($wantedval == "number") {
					 switch ($agent) {
						 case "ie": if ($version <= 9) {
										$rVal = $defaultifnot;
									}
									break;
					 }
				 }	 
				 if ($wantedval == "date") {
					 switch ($agent) {
						 case "ie": if ($version <= 11) {
										$rVal = $defaultifnot;
									}
									break;
					 }
				 }	 
				 if ($wantedval == "datetime") {
					 if ($agent !== 'safari') {
						 $rVal = $defaultifnot;
					 }
				 }	 
				 if ($wantedval == "time") {
					 switch ($agent) {
						 case "ie": if ($version <= 12) {
										$rVal = $defaultifnot;
									}
									break;
						 case "firefox": 
									$rVal = $defaultifnot;
									break;
									
					 }
				 }					 
			 } else {
				 $rVal = $defaultifnot;
			 }	
			 
			  return $rVal ;			 
		 }
} 