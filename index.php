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


// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
if(file_exists(dirname(__FILE__).'/protected/config/main.php'))
    $config=dirname(__FILE__).'/protected/config/main.php';
else
    $config=dirname(__FILE__).'/protected/config/main.php.toinstall';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once(dirname(__FILE__).'/protected/components/CWebLazy8Application.php');
$app = new CWebLazy8Application($config);
$app->run();
//Yii::createWebApplication('CWebLazy8Application',$config)->run();

