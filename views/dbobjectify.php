<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 /*
 @file dbobjectify.php
@brief Containts template for displaying help
 
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
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to DB Objectify</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<h1>Welcome to DB Objectify!</h1>

	<div id="body">
		<p>The purpose of this class is to generate MY_Module and Objects for the specified tables<br/></p>
<br/>
		<p>Note generated files will be placed in the configured [dbobj_output] path in dbobjectify.php in the config directory<br/></p>
		<p>a subdir in the output path will be created called dbobjects that holds the objects for each table<br/></p>
		<p>each table object will have a basic Module that inherits from MY_Module with basic settings<br/></p>
		<br/>
		<p>Usage</p>
		<br/>
		<p>generateall - will generate object and module for all tables in configured db</p>
		<p>generatetable - will generate object and module for specified table in configured db : url param name [table]</p>
		<p>objectsonlyall - will generate object only for all tables in configured db</p>
		<p>objectsonlytable - will generate object only for specified table in configured db : url param name [table]</p>
		
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>