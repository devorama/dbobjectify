<?php
/**
* Dbobjectify Config file
* 
* Config file purpose is to configure what CodeIgniter installations must receive the generated classes.
* and how they must be generated
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
defined('BASEPATH') OR exit('No direct script access allowed');

/**Path to the models directory in CI*/
$config['dbobj_model_dir'] = '<path to ci models directory>';

/**Path to the controllers directory in CI*/
$config['dbobj_controller_dir'] = '<path to ci controllers directory>';

/**prefixed used for all DB Object classes created per table*/
$config['dbobj_dbobject_prefix'] = 'DBO_';

/**prefixed used for all Model classes created per table*/
$config['dbobj_model_prefix'] = 'T_';

/**If true then validation rules for all fields in table is created in Model*/
$config['dbobj_create_valiation_rules'] = false;

/**File info related config. Used for each class file created*/
$config['dbobj_author'] = 'Devorama';
$config['dbobj_copyright'] = 'Devorama';
$config['dbobj_license'] = 'MIT';
$config['dbobj_link'] = 'https://github.com/devorama/dbobjectify';
$config['dbobj_date_in_file'] = true;

/**
Linking tables

Linking tables will try and create a class of a combination of models based on master child relationship. 
Child objects will always be created as a array with the key being the database value of the relationship setup.
This automatically eliminates duplicate child entries to the master

The is a 2 level array. The first level will hold the master table and his primary key. The second 
all child tables and there foreign keys and master table related fields

for example:

array(
	'flowers' => array(
					'key' => 'ipkFlowerID',
					'children' => array(
									'flower_color_lnk' => array(
															'child_key' => 'ipkLinkID',
															'child_field' => 'ifkFlowerID',
															'master_field' => 'ipkFlowerID',
															'children' => array(
																			'colors' => array(
																							'child_key' => 'ipkColorID',
																							'child_field' => 'ipkColorID',
																							'master_field' => 'ifkColorID'
																						)
																			)
															),	
									'smells' => array(
													'child_key' => 'ipkSmellID',
													'child_field' => 'ipkSmellID',
													'master_field' => 'ifkSmellID'
													)
	
									)
					)							

	);
*/
$config['dbobj_link_prefix'] = 'Lnk_';
$config['dbobj_link_tables'] = array();
?>