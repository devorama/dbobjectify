<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 /*
 @file Dbobjectify.php
@brief Contains the logic to call required functions to generate classes as required 
       and display help as needed
 
Dbobjectify 
Controller class for DB Objectify generator.

Purpose of system is to call the required functions to generate classes as required 
and display help as needed
 

This system is done in dedication to my children. 
To my son I pray and believe that one day you will get well and after all these years 
of suffering you will be able to go and chase your dreams that has been taken from you
with this illness. To my daugther I know you have lived in the shadow of your brothers
illness all this time and want to thank you even when times are hard sometimes you always 
are there for him and love him fully. You will become a wonderfull woman I believe one day.
Love you with my whole heart.
 
@author Devorama
@copyright Copyright (c) 2018  Devorama
@license MIT
@link  https://github.com/devorama/dbobjectify
*/ 

class Dbobjectify extends CI_Controller {
	
	public function index()
	{
		$this->load->view('dbobjectify');
	}
	/**
	 *  @brief will generate object and module for all tables in configured db</p>
	 *  
	 *  @return none
	 *  
	 *  @details will generate object and module for all tables in configured db</p>
	 */
	 public function generateall() {
		 $this->load->model('db_objectify');
		 $this->db_objectify->generate(true);
	}		
	/**
	 *  @brief will generate object and module for specified table in configured db</p>
	 *  
	 *  @return none
	 *  
	 *  @details will generate object and module for specified table in configured db</p>
	 */
	public function generatetable ($table){
		$this->load->model('db_objectify');
		$this->db_objectify->generate($table);
	}
	
	/**
	 *  @brief will generate object only for all tables in configured db</p>
	 *  
	 *  @return none
	 *  
	 *  @details will generate object only for all tables in configured db</p>
	 */
	public function objectsonlyall(){
		 $this->load->model('db_objectify');
		 $this->db_objectify->generate_objects(true);		 
	}
	
	/**
	 *  @brief will generate object only for specified table in configured db</p>
	 *  
	 *  @return none
	 *  
	 *  @details will generate object only for specified table in configured db</p>
	 */
	public function objectsonlytable($table){
		$this->load->model('db_objectify');
		$this->db_objectify->generate_objects($table);
		
	}
	
}
