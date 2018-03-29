# DB Objectify
Tool that converts database tables into objects and creates models and controllers to work with data. Based on CodeIgniter and my_model

The purpose of this class is to generate MY_Module and Objects for the specified tables

Note generated files will be placed in the configured [dbobj_output] path in dbobjectify.php in the config directory
a subdir in the output path will be created called dbobjects that holds the objects for each table
each table object will have a basic Module that inherits from MY_Module with basic settings

CodeIgniters configured database is used as source to work from

Uses MY_Model (https://github.com/lonnieezell/my_model) in Models generated. 

Linked Models (one master table, multiple children) can also be generated based on config file.

Sample Controller with display and manipulate functions can also be generated if required.

Base classes are generated for each Object and Controller to help speed up development of DB driven systems.

# Install
Clone / Download to CI directory of choice
Clone / Download MY_Model https://github.com/lonnieezell/my_model and install MY_Model.php in models directory of CI
Configure dbobjectify.php in config directory (makre sure models and controllers directories are set)

# Run
generateall - will generate object and module for all tables in configured db
generatetable - will generate object and module for specified table in configured db : url param name [table]
objectsonlyall - will generate object only for all tables in configured db
objectsonlytable - will generate object only for specified table in configured db : url param name [table]

# Dedication
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
