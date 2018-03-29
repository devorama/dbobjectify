<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* @file DB_Objectify.php
* @brief Contains the logic to create MY_Model model classes and objects classes for database tables
*  
* DB_Objectify Model class
* 
* Purpose of system is to generate php classes for database tables specified. 
* CodeIgniters configured database is used as source to work from
* Uses MY_Model (https://github.com/lonnieezell/my_model) in Models generated.
* 
* Models and Controllers are generated for the objects as required.
* Linked Models (one master table, multiple children) can also be generated 
* based on config file.
* Sample Controller with display and manipulate functions can also be generated if required.
* Base classes are generated for each Object and Controller to help speed up development of 
* DB driven systems.
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
*/ 
/**
 *  
 *  
 */
class DB_Objectify extends CI_Model {
	
    public function __construct(){
        if (!isset($this->db)){
            $this->load->database();
        }
        log_message('debug', 'DB Objectify Class Initialized');
    }
	
	/**
	 *  @brief Generate only objects for given table list
	 *  
	 *  @param [in] $all if set to true (default) then it generates for all tables. Else it will only generate for table name given in $all
	 *  @return none
	 *  
	 *  @details  Generate only objects for given table list
	 */
	public function generate_objects($all = true){
		/**if (!$this->input->is_cli_request()) { 
		  return 1;
		}*/
		 $tables = null;  
		 if ($all === true){
			 $tables = $this->db->list_tables();  
		 } else {
			 if ($this->db->table_exists($all)) {
			   $tables = array($all);  
			 }
		 }	
		 if (isset($tables)) {
			 $this->config->load('dbobjectify');
			 $outputdir = $this->config->item('dbobj_model_dir');
			 $object_prefix = $this->config->item('dbobj_dbobject_prefix');
			 
			 if (!file_exists($outputdir)) {
				mkdir($outputdir, 0777, true);
			 }
			 if (!file_exists($outputdir.'/dbobjects')) {
				mkdir($outputdir.'/dbobjects', 0777, true);
			 }
			 if (!file_exists($outputdir.'/dbobjects/DBOBase.php')) {
				 copy($outputdir.'/dbosupport/DBOBase.php',$outputdir.'/dbobjects/DBOBase.php');
			 }
			 
			 foreach ( $tables as $table) {
				$tnameuse = ucwords(strtolower($table),'_'); 
				$obectFileName = $object_prefix.$tnameuse ;
				
				$fields = $this->db->field_data($table);
				
				$objectFileContents = $this->generate_object($table,$obectFileName,$fields);
				 
				//save file
	            file_put_contents($outputdir.'/dbobjects/'.$obectFileName.'.php',$objectFileContents);
			 }
		 }	 
	}
	
	/**
	 *  @brief Generate object and model for specific table name or all
	 *  
	 *  @param [in] $all if set to true (default) then it generates for all tables. Else it will only generate for table name given in $all
	 *  @return none
	 *  
	 *  @details Generate object and model for specific table name or all
	 */
	public function generate($all = true){
		 
		 $tables = null;  
		 if ($all === true){
			 $tables = $this->db->list_tables();  
		 } else {
			 if ($this->db->table_exists($all)) {
			   $tables = array($all);  
			 }
		 }
		 
		 if (isset($tables)) {
			 $this->config->load('dbobjectify');
			 $outputdir = $this->config->item('dbobj_model_dir');
			 $controller_outputdir = $this->config->item('dbobj_controller_dir');
			 $object_prefix = $this->config->item('dbobj_dbobject_prefix');
			 $model_prefix = $this->config->item('dbobj_model_prefix');
			 $link_prefix = $this->config->item('dbobj_link_prefix');
			 $link_tables = $this->config->item('dbobj_link_tables');
			 
			 if (!file_exists($outputdir)) {
				mkdir($outputdir, 0777, true);
			 }
			 if (!file_exists($outputdir.'/dbobjects')) {
				mkdir($outputdir.'/dbobjects', 0777, true);
			 }
			 if (!file_exists($controller_outputdir.'/dbcontroller')) {
				mkdir($controller_outputdir.'/dbcontroller', 0777, true);
			 }
			 if (!file_exists($outputdir.'/dbobjects/DBOBase.php')) {
				 copy($outputdir.'/dbosupport/DBOBase.php',$outputdir.'/dbobjects/DBOBase.php');
			 }
			 
			 if (!file_exists($controller_outputdir.'/dbcontroller/DBControllerBase.php')) {
				 copy($outputdir.'/dbosupport/DBControllerBase.php',$controller_outputdir.'/dbcontroller/DBControllerBase.php');
			 }

			 
			 foreach ( $tables as $table) {
				$tnameuse = ucwords(strtolower($table),'_'); 
				$obectFileName = $object_prefix.$tnameuse ;
				$modelFileName = $model_prefix.strtolower($table);
				
				$fields = $this->db->field_data($table);
				
				$objectFileContents = $this->generate_object($table,$obectFileName,$fields);
				$modelFileContents = $this->generate_model($table,$obectFileName,$modelFileName,$fields);
				 
				//save file
	            file_put_contents($outputdir.'/dbobjects/'.$obectFileName.'.php',$objectFileContents);
				file_put_contents($outputdir.$modelFileName.'.php',$modelFileContents);
			 }
			 
			 if ((isset($link_tables)) && (is_array($link_tables))) {
				 foreach ($link_tables as $mastertable => $masterdata){
					 $linkFileName = $link_prefix.strtolower($mastertable);
					 
					 $masterkey = $masterdata['key'];
					 $children = $masterdata['children'];
					 
					 $linkFileContents = $this->generate_link_model($mastertable,$masterkey,$children,$linkFileName);
					 
					 file_put_contents($outputdir.$linkFileName.'.php',$linkFileContents);
				 }
			 }
		 }	 
	}
	
	/**
	 *  @brief Generates a Link model based on configured master child relationship. Generated Models and DB Objects are used in link model
	 *  
	 *  @param [in] $mastertable Master table name to generated link model for
	 *  @param [in] $masterfield Primary key of Master table
	 *  @param [in] $children Array describing the master child relationship
	 *  @param [in] $linkFileName Link modal class/filename to use
	 *  @return String representing the Link model class
	 *  
	 *  @details Generates a Link model based on configured master child relationship. Generated Models and DB Objects are used in link model. This is based on the config value of  dbobj_link_tables. See config file for more info.
	 */
	private function generate_link_model($mastertable,$masterfield,$children,$linkFileName){
		
		$object_prefix = $this->config->item('dbobj_dbobject_prefix');
		$obectFileNames = null;
		$childTables = null;
		foreach ($children as $childtable => $childdata) {
			$tnameuse = ucwords(strtolower($childtable),'_'); 
			$childTables[] = $tnameuse;
			$obectFileName = $object_prefix.$tnameuse ;
			$obectFileNames[] = $obectFileName;
			if ((isset($childdata['children'])) && (is_array($childdata['children']))) {
				foreach ($childdata['children'] as $subchildtable => $subchilddata) {
					$tnameuse = ucwords(strtolower($subchildtable),'_'); 
					$childTables[] = $tnameuse;
					$obectFileName = $object_prefix.$tnameuse ;
					$obectFileNames[] = $obectFileName;
				}
			}
		}
		$linkData = $this->get_link_header($mastertable,$obectFileNames,$childTables,$linkFileName);
		$linkData .= $this->generate_link_opendata_func($mastertable,$masterfield,$children,$linkFileName,$childTables);
		$linkData .= $this->generate_link_find_functions($children);
		
	    $linkData .= "\r\n}\r\n ?>";
		return $linkData;
		
	}
	
	
	/**
	 *  @brief Generate find function for each child for easy retrieval of data based on key configuration
	 *  
	 *  @param [in] $children Array of children that funcion needs to get generated for (if inner children exist they must also generate find functions)
	 *  @return String that represent all the find functions for children
	 *  
	 *  @details Generate find function for each child for easy retrieval of data based on key configuration.
	 */
	private function generate_link_find_functions($children){
		$model_prefix = $this->config->item('dbobj_model_prefix');
		$sData ='';
		foreach ($children as $childtable => $childdata) {
			$childkey = $childdata['child_key'];	
			$childfield = $childdata['child_field'];
			$child_master_field = $childdata['master_field'];
			$modelFileName = strtolower($model_prefix.$childtable);		
			$sVar = 'o'.ucwords(strtolower($childtable),'_');	
			$funcName =  'Find_'.ucwords(strtolower($childtable),'_');	
			$sData .=    "\r\n\t\t/**\r\n".
				"\t\t*  @brief Helps find the values for in object based on master child configuration for $sVar.\r\n".
				"\t\t*  \r\n".
				"\t\t*  @param [in] \$$childfield The value of $childfield inside $sVar\r\n".
				"\t\t*  @return Object or Array of objects that are linked to \$$childfield\r\n".
				"\t\t*  \r\n".
				"\t\t*  @details Helps find the values for in object based on master child configuration for $sVar\r\n".
				"\t\t*/\r\n".
				"\t\t public function $funcName(\$$childfield){\r\n".
				"\t\t\t \$iCount = count(\$this->$sVar);\r\n".
				"\t\t\t \$Ans = null;\r\n".
				"\t\t\t if (count(\$iCount) >= 1) {\r\n".
				"\t\t\t\tforeach(\$this->$sVar as \$listItem){\r\n".
				"\t\t\t\t\t if (\$listItem->$childfield == \$$childfield) {\r\n".
				"\t\t\t\t\t\t \$Ans[] =  \$listItem;\r\n".
				"\t\t\t\t\t }\r\n".
				"\t\t\t\t}\r\n".
				"\t\t\t}	\r\n".
				"\t\t\t return \$Ans;	\r\n".
				"\t\t}";
			if ((isset($childdata['children'])) && (is_array($childdata['children']))) {
					$sData .= $this->generate_link_find_functions($childdata['children']);	 
			}
		}		
		return $sData;

	}
	
	/**
	 *  @brief Generates the child loading logic that is inside generate_link_opendata_func,child can have its own childs etc, thus the logic must be ready to handle that.
	 *  
	 *  @param [in] $children Array of children linked against master table (link info in array)
	 *  @param [in] $model_prefix Prefix read from config file
	 *  @param [in] $targetObject Target object that is serving as master dataset
	 *  @return String reprents the child loading section inside the generate_link_opendata_func function
	 *  
	 *  @details Generates the child loading logic that is inside generate_link_opendata_func,child can have its own childs etc, thus the logic must be ready to handle that.
	 */
	private function generate_lnk_opendata_childloads($children,$model_prefix,$targetObject = '$oMaster'){
		$sData = '';
		foreach ($children as $childtable => $childdata) {
			$childkey = $childdata['child_key'];	
			$childfield = $childdata['child_field'];
			$child_master_field = $childdata['master_field'];
			$modelFileName = strtolower($model_prefix.$childtable);		
			$sVar = 'o'.ucwords(strtolower($childtable),'_');	
			$sData .= "\t\t\t\t\t \$this->load_child_data(\$this->$modelFileName,'$childkey',$targetObject->{$child_master_field},'$childfield',\$this->$sVar);\r\n";
			if ((isset($childdata['children'])) && (is_array($childdata['children']))) {
				$tmptargetObject = "\$tmpC_".$sVar;
				$sData .= "\t\t\t\t\t if (isset(\$this->$sVar)) { \r\n".
					 "\t\t\t\t\t\t foreach(\$this->$sVar as $tmptargetObject) { \r\n";
					$sData .= "\t\t".$this->generate_lnk_opendata_childloads($childdata['children'],$model_prefix,$tmptargetObject);	 
				
				$sData .="\t\t\t\t\t\t } //end for \r\n";
				$sData .="\t\t\t\t\t } //end if \r\n";
					
			}
		}		
		return $sData;
	}
	
	/**
	 *  @brief Generate the open_data function that the link module use to align all data objects against masterkey id
	 *  
	 *  @param [in] $mastertable Table name of the master table
	 *  @param [in] $masterfield Fieldname of the mater table that is primary key
	 *  @param [in] $children Array of children linked against master table (link info in array)
	 *  @param [in] $linkFileName Link class/filename
	 *  @param [in] $childTables Array of all child table names linked to master table
	 *  @return String that represents the open_data function call for link module
	 *  
	 *  @details Generate the open_data function that the link module use to align all data objects against masterkey id
	 */
	private function generate_link_opendata_func($mastertable,$masterfield,$children,$linkFileName,$childTables){
		$model_prefix = $this->config->item('dbobj_model_prefix');
		$modelFileName = strtolower($model_prefix.$mastertable);
		$sVar = 'o'.ucwords(strtolower($mastertable),'_');	
		
		$sData =    "\r\n\t\t/**\r\n".
					"\t\t*  @brief Creates a child array object for the child data based on the linking to the master table.\r\n".
					"\t\t*  \r\n".
					"\t\t*  @param [in] \$oChildModelObj Child Model object to be used for retrieving data\r\n".
					"\t\t*  @param [in] \$childkey Child key to be used to retrieve data from child model\r\n".
					"\t\t*  @param [in] \$masterid Master field data used to help retieve chil records\r\n".
					"\t\t*  @param [in] \$childfield Child field to be used to retrieve data from master model	 \r\n".
					"\t\t*  @param [in] &\$oChildObject The object that holds the child array\r\n".
					"\t\t*  @return none\r\n".
					"\t\t*  \r\n".
					"\t\t*  @details Creates a child array object for the child data based on the linking to the master table. Todo : Allow list of keys to be used.Note currently\r\n".
					"\t\t*/\r\n".
					"\t\t private function load_child_data(\$oChildModelObj,\$childkey,\$masterid,\$childfield,&\$oChildObject){\r\n".
					"\t\t\t \$oTmpL = \$oChildModelObj->select()->where(\$childfield,\$masterid)->find_all();\r\n".
					"\t\t\t if ((isset(\$oTmpL)) && (count(\$oTmpL) >= 1)) {\r\n".
					"\t\t\t\tforeach(\$oTmpL as \$listItem){\r\n".
					"\t\t\t\t\t if (!isset(\$oChildObject[\$listItem->\$childkey])) {\r\n".
					"\t\t\t\t\t\t \$oChildObject[\$listItem->\$childkey] =  \$listItem;\r\n".
					"\t\t\t\t\t }\r\n".
					"\t\t\t\t}\r\n".
					"\t\t\t}	\r\n".
					"\t\t}";
		
		
		$sData .= "\r\n\t\t/**\r\n".
				 "\t\t *  @brief Load master table and all child tables based on ID passed\r\n".
				 "\t\t *  \r\n".
				 "\t\t *  @param [in] \$ID value passed that master table is linked on based on masterkey\r\n".
				 "\t\t *  @return None\r\n".
				 "\t\t *  \r\n".
				 "\t\t *  @details Load master table and all child tables based on ID passed. All objects will be set after load (or cleared if none found). All child tables will be arrays regardless if they are only 1 record or not\r\n".
				 "\t\t */ \r\n".
				"\t\t public function load_data(\$ID){\r\n";
				
		foreach ($childTables as $ctable) {
			$sVar = 'o'.ucwords(strtolower($ctable),'_');	
			$sData .= "\t\t\t \$this->{$sVar} = null; \r\n";
		}			
		$sVar = 'o'.ucwords(strtolower($mastertable),'_');	
        $modelFileName = strtolower($model_prefix.$mastertable);		
		
		$sData .= "\t\t\t \$this->{$sVar} = \$this->{$modelFileName}->select()->where('{$masterfield}',\$ID)->find_all();\r\n".
				 "\t\t\t if (isset(\$this->$sVar)) { \r\n".
				 "\t\t\t\t foreach(\$this->$sVar as \$oMaster) { \r\n";
				 
		$sData .= $this->generate_lnk_opendata_childloads($children,$model_prefix);	 
	
		$sData .="\t\t\t\t } //end for \r\n";
		$sVar = 'o'.ucwords(strtolower($mastertable),'_');	
        $modelFileName = strtolower($model_prefix.$mastertable);		
		
		$sData .="\t\t\t\t if ( (count(\$this->$sVar) == 1)) {\r\n".
				 "\t\t\t\t\t \$this->{$sVar} = \$this->{$sVar}[0];\r\n".
				 "\t\t\t\t }		\r\n";
		foreach ($childTables as $ctable) {
			$sVar = 'o'.ucwords(strtolower($ctable),'_');	
				$sData .="\t\t\t\t if ((isset(\$this->$sVar)) && (count(\$this->$sVar) == 1)) {\r\n".
				 "\t\t\t\t\t \$this->{$sVar} = end(\$this->{$sVar});\r\n".
				 "\t\t\t\t }		\r\n";
		}	
		$sData .= "\t\t\t} \r\n\t\t }";
		return $sData;
		//$sVar = '$o'.ucwords(strtolower($table),'_');
		//$modelFileName = strtolower($model_prefix.$table);
	}
	
	/**
	 *  @brief Generate a Model class for target table
	 *  
	 *  @param [in] $table Table that model class is created for
	 *  @param [in] $obectFileName Classname/filename of db objects class
	 *  @param [in] $modelFileName Classname/filename of model class
	 *  @param [in] $table_fields_data Array that contains meta of all fields for target $table
	 *  @return String representing the whole model file
	 *  
	 *  @details Generate a model class for target table. This will be a whole php document with all required coding in it generated
	 */
	private function generate_model($table,$obectFileName,$modelFileName,$table_fields_data){
		$modelData = $this->get_model_header($table,$obectFileName,$modelFileName);
				foreach ($table_fields_data as $field){
					if ($field->primary_key) {
					  $modelData .= "\t protected \$primary_key	 = '{$field->name}';";
					}

				}
				
				//Body
				$modelData  .= "\r\n\t protected \$date_format  = 'datetime';\r\n".
										"\t protected \$log_user     = FALSE;\r\n\r\n".

										"\t protected \$set_created  = TRUE;\r\n".
										"\t protected \$created_field    = 'created_on';\r\n".
										"\t protected \$created_by_field = 'created_by';\r\n".

										"\t protected \$set_modified     = FALSE;\r\n".
										"\t protected \$modified_field   = 'modified_on';\r\n".
										"\t protected \$modified_by_field = 'modified_by';\r\n".

										"\t protected \$soft_deletes = FALSE;\r\n".
										"\t protected \$deleted_field    = 'deleted';\r\n".
										"\t protected \$deleted_by_field = 'deleted_by';\r\n\r\n".

										"\t protected \$return_type      = 'object';\r\n".
										"\t protected \$custom_return_object      = '$obectFileName';\r\n\r\n".

										"\t protected \$protected_attributes   = array();\r\n\r\n".

										"\t protected \$validation_rules         = array();\r\n".
										"\t protected \$insert_validation_rules  = array();\r\n".
										"\t protected \$skip_validation          = false;\r\n\r\n".

										"\t // Observers / Events\r\n".
										"\t protected \$before_insert    = array();\r\n".
										"\t protected \$after_insert     = array();\r\n".
										"\t protected \$before_update    = array();\r\n".
										"\t protected \$after_update     = array();\r\n".
										"\t protected \$before_find      = array();\r\n".
										"\t protected \$after_find       = array();\r\n".
										"\t protected \$before_delete    = array();\r\n".
										"\t protected \$after_delete     = array();\r\n";
				 //ending	
				 $modelData  .= $this->build_model_constructor($table_fields_data,$modelFileName);
				 $modelData  .= $this->build_validation_rules($table_fields_data);
				 
				 $modelData  .= "} \r\n ?>";		
				 return $modelData;
	}

	/**
	 *  @brief Generate a common field type based on the meta field type given from databse driver object
	 *  
	 *  @param [in] $dbfieldtype Meta field type generated by database driver object
	 *  @return String common db field type
	 *  
	 *  @details Generate a common field type based on the meta field type given from databse driver object
	 */
	private	 function get_general_field_type($dbfieldtype){
		//match all DB types to common types for beter processing when generating form input values and working with replies
		switch ($dbfieldtype) {
			case "text";
			case "char";
			case "varchar";
			case "string" 	: $fieldtype = "string";
							break;
			
			case "tinyint";
			case "integer";
			case "int" 		:  $fieldtype = "int";
							break;
			
			case "decimal";
			case "float";
			case "currency";
			case "real" 	: $fieldtype = "decimal";
							break;
			case "timestamp";
			case "datetime" : $fieldtype = "datetime";
							break;

			case "year" 	: $fieldtype = "year";
							break;
							
			case "date" 	: $fieldtype = "date";
							break;
							
			case "time" 	: $fieldtype = "time";
							break;
							
			case "blob" 	: $fieldtype = "blob";
							break;
							
			case "null" 	; 
			case "unknown" 	:  $fieldtype = "unknown";
							break;
							
			default :
							$fieldtype = "unknown";
							break;
						   
		}		
		return $fieldtype;
	}
	
	/**
	 *  @brief Generates a function that returns a array that defines the fields used in model/object. Fields are mapped to common types for all databases.
	 *  
	 *  @param [in] $table_fields_data Array that contains meta of all fields for target $table
	 *  @return String that represents the whole get_fields_def function
	 *  
	 *  @details Generates a function that returns a array that defines the fields used in model/object. 
				Each field name is the key to the array returned and a object representing the meta is then available for the field.
				Fields are mapped to common types for all databases. This means varchar, string, char are all mapped to string.
	 */
	private function build_field_def_func($table_fields_data,$table){
			$fieldDefFuncData = "\t\t /** \r\n".
				"\t\t *  @brief Generates a array with field name as key and object as value that represents meta. \r\n".
				"\t\t *  @return Array of field defs \r\n".
				"\t\t *  @details Generates a array with field name as key and object as value that represents meta  _field_def class used as meta is StdClass with name,type,max_length and primary_key fields\r\n".
				"\t\t */ \r\n".
				"\t\t public function get_fields_def(){\r\n".
				"\t\t\t \$fldDefArr = null;	\r\n";	 
				$pkField = ""; 
				foreach ($table_fields_data as $field){
					$pk = 'N';
					if ($field->primary_key) {
						$pk = 'Y';  
						$pkField = $field->name;
					}
					$fieldtype = $this->get_general_field_type($field->type);

					$fieldDefFuncData .= "\t\t\t \$fieldObj = new stdClass(); \r\n";
					$fieldDefFuncData .= "\t\t\t \$fieldObj->name = '{$field->name}'; \r\n";
					$fieldDefFuncData .= "\t\t\t \$fieldObj->type = '$fieldtype'; \r\n";
					$fieldDefFuncData .= "\t\t\t \$fieldObj->max_length = '{$field->max_length}'; \r\n";
					$fieldDefFuncData .= "\t\t\t \$fieldObj->primary_key = '$pk'; \r\n";
					$fieldDefFuncData .= "\t\t\t \$fldDefArr['{$field->name}'] = \$fieldObj; \r\n \r\n";
				}
				$fieldDefFuncData .= "\t\t\t return \$fldDefArr ;	\r\n \t\t}\r\n";
				$fieldDefFuncData .= "\t\t /** \r\n".
				"\t\t *  @brief Returns the primary key name for this object. \r\n".
				"\t\t *  @return String that is the primary key for the db objects \r\n".
				"\t\t *  @details Returns the primary key name for this object.\r\n".
				"\t\t */ \r\n".
				"\t\t public function get_primary_key(){\r\n".
				"\t\t\t return '$pkField';	\r\n \t\t } \r\n";	 
				$fieldDefFuncData .= "\t\t /** \r\n".
				"\t\t *  @brief Returns the table name for this object. \r\n".
				"\t\t *  @return String that is the table name for the db objects \r\n".
				"\t\t *  @details Returns the table name for this object.\r\n".
				"\t\t */ \r\n".
				"\t\t public function get_tablename(){\r\n".
				"\t\t\t return '$table';	\r\n \t\t } \r\n";	 				
				return $fieldDefFuncData;
	}
	
	/**
	 *  @brief Generates a function sets the validation rules of the model
	 *  
	 *  @param [in] $table_fields_data Array that contains meta of all fields for target $table
	 *  @return String that represents the whole build_validation_rules function
	 *  
	 *  @details Generates a function sets the validation rules of the model
	 */
	private function build_validation_rules($table_fields_data){
			$fieldValRules = "\t\t /** \r\n".
				"\t\t *  @brief Generates a array used for the validation rules \r\n".
				"\t\t *  @return Array of validatin rules \r\n".
				"\t\t *  @details Generates a array used for the validation rules\r\n".
				"\t\t */ \r\n".
				"\t\t private function build_validation_rules(){\r\n".
				"\t\t\t \$fldValArr = null;	\r\n";	 
				 $this->config->load('dbobjectify');
				 $createvalidationrules = $this->config->item('dbobj_create_valiation_rules');

				if ($createvalidationrules == true) {
					foreach ($table_fields_data as $field){
						$ruleS = array();
						switch ($field->type) {
							case "char";
							case "varchar";
							case "string" : $ruleS[] = 'alpha_numeric_spaces';
											$ruleS[] = 'trim';
											$ruleS[] = 'max_length['.$field->max_length.']';
										   break;
							
							case "tinyint";
							case "int" :  $ruleS[] = 'integer';	
										   break;
							
							case "decimal";
							case "real" : $ruleS[] = 'decimal';	
										   break;
							case "timestamp" : 
										   break;
							case "year" : 
										   break;
							case "date" : 
										   break;
							case "time" : 
										   break;
							case "datetime" : 
										   break;
							case "blob" : 
										   break;
							case "null" : 
										   break;
										   
							case "unknown" : 
										   break;
										   
						}
						if ($field->primary_key) {
							   $ruleS[] = '|required';  
						}
						if (count($ruleS) >= 1) {
							$ruleS = implode('|',$ruleS);
							$camelCase = ucwords($field->name);
							$fieldValRules .= "\t\t\t \$valEntry = null;\r\n";
							$fieldValRules .= "\t\t\t \$valEntry['field'] = '{$field->name}' ; \r\n";
							$fieldValRules .= "\t\t\t \$valEntry['label'] = '$camelCase'; \r\n";
							$fieldValRules .= "\t\t\t \$valEntry['rules'] = '$ruleS' ; \r\n";
							$fieldValRules .= "\t\t\t \$fldValArr[] = \$valEntry; \r\n";
						}
					}
					$fieldValRules .= "\t\t\t return \$fldValArr ;	\r\n \t\t}\r\n";
				} else {
				   $fieldValRules .= "\t\t\t return array() ;	\r\n \t\t}\r\n";
				}					
				$fieldValRules .= "\t\t /** \r\n".
				"\t\t *  @brief Returns the internal validation rules array \r\n".
				"\t\t *  @return Array of validatin rules \r\n".
				"\t\t *  @details Returns the internal validation rules array\r\n".
				"\t\t */ \r\n".
				"\t\t public function get_validation_rules(){\r\n".
				"\t\t\t return  \$this->validation_rules;	\r\n \t\t}\r\n";	 
				return $fieldValRules;
	}
	
	/**
	 *  @brief Generates a constructor for the model class. 
	 *  
	 *  @param [in] $table_fields_data Array that contains meta of all fields for target $table
	 *  @param [in] $modelFileName Name of model begin generated for
	 *  @return String that represents the constructor for the model call
	 *  
	 *  @details Generates a constructor for the model class. 
	 */
	private function build_model_constructor($table_fields_data,$modelFileName){
			$constructorData = "\t\t /** \r\n".
				"\t\t *  @brief constructor for model call. \r\n".
				"\t\t *  @return None \r\n".
				"\t\t *  @details Constructor for model class.\r\n".
				"\t\t */ \r\n".
				"\t\t  public function __construct(&\$write_db=null, &\$read_db=null){\r\n".
				"\t\t\t  parent::__construct(\$write_db,\$read_db);\r\n".
				"\t\t\t  \$this->validation_rules = \$this->build_validation_rules();\r\n".
				"\t\t\t  log_message('debug', '$modelFileName Class Initialized');\r\n".
				"\t\t  }\r\n\r\n";
 				return $constructorData;
	}
		
	/**
	 *  @brief Generate a DB Object class for target table
	 *  
	 *  @param [in] $table Table that objects class is created for
	 *  @param [in] $obectFileName Classname/filename of db objects class
	 *  @param [in] $table_fields_data Array that contains meta of all fields for target $table
	 *  @return String representing the whole DB objects file
	 *  
	 *  @details Generate a DB Object class for target table. This will be a whole php document with all required coding in it generated
	 */
	private function generate_object($table,$obectFileName,$table_fields_data){
		$objectData = $this->get_object_header($table,$obectFileName);
		foreach ($table_fields_data as $field){
			$primarykeys = ''; 
			if ($field->primary_key) {
			  $primarykeys = ' primary key'; 
			}
			$objectData .= "\t public \${$field->name}; \t\t\t /**Type : {$field->type} , Length : {$field->max_length} $primarykeys*/ \r\n";		
		}
		$objectData  .= $this->build_field_def_func($table_fields_data,$table);		
		$objectData .= "} \r\n?>";
		return $objectData;
	}
	
	/**
	 *  @brief Generate the header(start) for the DB object class file
	 *  
	 *  @param [in] $table Table name that the class is created for
	 *  @param [in] $obectFileName DB Objects file/class used in this object
	 *  @return String representing the start of the object class
	 *  
	 *  @details Generate the header(start) for the DB object class file
	 */
	private function get_object_header($table,$obectFileName){
		$dateinfiles = $this->config->item('dbobj_date_in_file');
		$filedate = '';
		if ($dateinfiles == true) {
			$filedate = 'Date : '.date ("Y-m-d");
		}

		return "<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); \r\n ".
				"/**\r\n $obectFileName \r\n".
				"Object class to represent the $table table. \r\n ".
				"$filedate\r\n ".
				"Target table : $table\r\n".
				$this->get_file_copyauth_info().
				"*/ ".
				"\r\n\r\n".
				"include_once 'DBOBase.php'; ".
				"\r\n\r\n".
				"class $obectFileName extends DBOBase { \r\n ";
	}
	/**
	 *  @brief Generate the header(start) for the model class file
	 *  
	 *  @param [in] $table Table name that the class is created for
	 *  @param [in] $obectFileName DB Objects file used in this model
	 *  @param [in] $modelFileName Model class filename /class name used for this table
	 *  @return String representing the start of the model class
	 *  
	 *  @details Generate the header(start) for the model class file
	 */
	private function get_model_header($table,$obectFileName,$modelFileName){
		$dateinfiles = $this->config->item('dbobj_date_in_file');
		$filedate = '';
		if ($dateinfiles == true) {
			$filedate = 'Date : '.date ("Y-m-d");
		}
 		return	"<?php if (!defined('BASEPATH')) exit('No direct script access allowed');\r\n ".
				"/**\r\n $modelFileName \r\n".
				"Model class to represent the $table table. \r\n ".
				"$filedate\r\n ".
				"Target table : $table\r\n".
				$this->get_file_copyauth_info().
				"*/ ".
				"\r\n\r\n".
				"include_once 'MY_Model.php'; ".
				"\r\n".
				"include_once 'dbobjects/$obectFileName.php'; ".
				"\r\n\r\n".
				"class $modelFileName  extends MY_Model { ".
				"\r\n\r\n\t protected \$table_name   = '$table'; \r\n";
	}
	
	/**
	 *  @brief Generates a Link model file header
	 *  
	 *  @param [in] $table Name of master table
	 *  @param [in] $obectFileNames array of object file names being used
	 *  @param [in] $linkFileName Name of link model name being used
	 *  @param [in] $childTables List of child table Names
	 *  @return String representing a link model class header
	 *  
	 *  @details Generates a Link model file header
	 */
	private function get_link_header($table,$obectFileNames,$childTables,$linkFileName){
		$dateinfiles = $this->config->item('dbobj_date_in_file');
		$model_prefix = $this->config->item('dbobj_model_prefix');
		$filedate = '';
		if ($dateinfiles == true) {
			$filedate = 'Date : '.date ("Y-m-d");
		}
 		$sData =	"<?php if (!defined('BASEPATH')) exit('No direct script access allowed');\r\n ".
				"/**\r\n $linkFileName \r\n".
				"Model class to represent the $table table. \r\n ".
				"$filedate\r\n ".
				"Target table : $table\r\n".
				$this->get_file_copyauth_info().
				"*/ ".
				"\r\n\r\n";
		$sVar = '$o'.ucwords(strtolower($table),'_');
		$sData .= "\r\n".
				"class $linkFileName  extends CI_Model { \r\n".
				"\t\t public $sVar; /**Master table*/\r\n".
				"\r\n\r\n";
		foreach ($childTables as $childname){
			 $sVar = '$o'.$childname;
			 $sData .= "\t\t public $sVar;\r\n";
		}			
		$sData .= "\r\n\t\t/** \r\n". 
				 "\t\t*  @brief constructor for model call. \r\n". 
				 "\t\t*  @return None \r\n". 
				 "\t\t*  @details Constructor for model class.\r\n". 
				 "\t\t*/ \r\n". 
				"\t\t public function __construct(){\r\n".
				"\t\t\t if (!isset(\$this->db)){\r\n".
				"\t\t\t\t 	\$this->load->database();\r\n".
				"\t\t\t }\r\n";
				$modelFileName = strtolower($model_prefix.$table);
				$sData .= "\t\t\t \$this->load->model('$modelFileName');\r\n";
				foreach ($childTables as $childname){
					$modelFileName = strtolower($model_prefix.$childname);
					$sData .= "\t\t\t \$this->load->model('$modelFileName');\r\n";
				}
				$sData .= "\t\t\t log_message('debug', '$linkFileName Class Initialized');\r\n".
						  "\t\t }\r\n";
		return $sData;		
	}
	
	/**
	 *  @brief Cretes a string that represents the configured author, license, copyright and link for product 
	 *  
	 *  @return String that represents the configured author, license, copyright and link for product 
	 *  
	 *  @details Cretes a string that represents the configured author, license, copyright and link for product.
	 */
	private function get_file_copyauth_info(){
			 $this->config->load('dbobjectify');
			 $auth = $this->config->item('dbobj_author');
			 $copyright = $this->config->item('dbobj_copyright');
			 $license = $this->config->item('dbobj_license');
			 $link = $this->config->item('dbobj_link');
        return "@author $auth \r\n".
				"@copyright Copyright (c) ".date('Y')." $copyright\r\n".
				"@license $license\r\n".
				"@link  $link\r\n";
				
	}
}