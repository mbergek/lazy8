<?php
/*
*    This is Lazy8Web, a book-keeping ledger program for professionals
*    Copyright (C) 2010  Thomas Dilts                                 
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or   
*    (at your option) any later version.                                 
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of 
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the  
*    GNU General Public License for more details.                   
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="en" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<div id="page">

<div id="header">
<?php $this->pageTitle= 'Configure the database to Lazy8Web'; ?>

<h1><?php echo 'Configure the database to Lazy8Web'; ?></h1>
<?php
if(!is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../assets')){
 ?>
 <p style="color:red">
The directory <?php echo realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../assets');  ?> is
not writeable.  You must change to read-write.
</p>
 <?php
 }
if(!is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config')
	&& !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php') ){
 ?>
 <p style="color:red">
The directory <?php echo realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config');  ?> is
not writeable.  You must change to read-write or copy the file main.php.toinstall to main.php manually and then change main.php manually.
</p>
 <?php
}
 
if(is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config') 
      && !is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php')
      && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php')){
 ?>
 <p style="color:red">
The file <?php echo realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php');  ?> is
not writeable.  You must change to read-write or make changes manually.
</p>
 <?php
 }
 
if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php.toinstall')){
 ?>
 <p style="color:red">
The file <?php echo realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php.toinstall');  ?> does not exist. 
Reinstall the entire Lazy8Web.
</p>
 <?php
 }
 ?>
<p>
You failed to connect to the database.  You can either go to the file "[home]/protected/config/main.php.toinstall"
and change the file manually and rename to main.php or you can try to enter the information here.  We must have write permission to 
that file to succeed.  If you keep comming back here then you must change the file manually.
Don't forget to change that file back to read-only afterwards.
</p>

<div class="yiiForm">
<?php echo CHtml::beginForm(); ?>

<div class="simple">
<?php echo CHtml::label('MySql host(perhaps \'localhost\')','',false);  echo CHtml::textField('databasehost',isset($_POST['databasehost'])?$_POST['databasehost']:"") ?>
</div>
<br/>
<br/>
<div class="simple">
<?php echo CHtml::label('MySql port (standard=3306)','',false);  echo CHtml::textField('databaseport',isset($_POST['databaseport'])?$_POST['databaseport']:"3306") ?>
</div>
<br/>
<br/>
<div class="simple">
<?php echo CHtml::label('Database name(perhaps \'lazy8web\')','',false);  echo CHtml::textField('dbname',isset($_POST['dbname'])?$_POST['dbname']:"") ?>
</div>
<br/>
<br/>
<div class="simple">
<?php echo CHtml::label('database username','',false);  echo CHtml::textField('dbusername',isset($_POST['dbusername'])?$_POST['dbusername']:"") ?>
</div>
<br/>
<br/>
<div class="simple">
<?php echo CHtml::label('database password','',false);  echo CHtml::textField('dbpassword',isset($_POST['dbpassword'])?$_POST['dbpassword']:"") ?>
</div>
<br/>
<br/>
<br/>


<div class="action">
<?php echo CHtml::submitButton('Try to connect',array('title'=>'contexthelp.Login')); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->
</div><!-- header -->
</div><!-- page -->

</body>

</html>