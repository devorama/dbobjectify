<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* @file DBOBase.php
* @brief Base class for generated DB object classes if needed in future for basic general functionality
* 		to help work with object
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
class DBOBase {
	
	    /**
	 *  @brief Constructor for base class
	 *  
	 *  @return None
	 *  
	 *  @details Constructor for base class
	 */
	public function __construct(){
      //Create objects are required
    }	
	
	/**
	 *  @brief Takes array and compare iets keys to object variables, if one found that match the object variable is set to array value
	 *  
	 *  @param [in] $aData Array with key value pair where key equals that of object variable name
	 *  @return None
	 *  
	 *  @details Takes array and compare iets keys to object variables, if one found that match the object variable is set to array value
	 */
	public function load_from_array($aData){
	   if (is_array($aData)) {
			foreach(get_object_vars($this) as $key => $val) {
				if (isset($aData[$key])) {
					$this->{$key} = $aData[$key];
				}	
			}
	   }
	}
}	
?>