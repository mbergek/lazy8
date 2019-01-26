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


class SiteController extends CController
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image
			// this is used by the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xEBF4FB,
			),
		);
	}
	public static function getLanguages(){
		return array(
		'af'=>'Afrikaans',
		'sq'=>'Albanian',
		'ar'=>'Arabic',
		'be'=>'Belarusian',
		'bg'=>'Bulgarian',
		'ca'=>'Catalan',
		'zh'=>'Chinese',  
		'hr'=>'Croatian',
		'cs'=>'Czech',
		'da'=>'Danish',
		'nl'=>'Dutch',
		'en'=>'English',
		'et'=>'Estonian',
		'tl'=>'Filipino',
		'fi'=>'Finnish',
		'fr'=>'French',
		'gl'=>'Galician',
		'de'=>'German',
		'el'=>'Greek',
		'iw'=>'Hebrew',
		'hi'=>'Hindi',
		'hu'=>'Hungarian',
		'is'=>'Icelandic',
		'id'=>'Indonesian',
		'ga'=>'Irish',
		'it'=>'Italian',
		'ja'=>'Japanese',
		'ko'=>'Korean',
		'lv'=>'Latvian',
		'lt'=>'Lithuanian',
		'mk'=>'Macedonian',
		'ms'=>'Malay',
		'mt'=>'Maltese',
		'no'=>'Norwegian',
		'fa'=>'Persian',
		'pl'=>'Polish',
		'pt'=>'Portuguese',
		'ro'=>'Romanian',
		'ru'=>'Russian',
		'sr'=>'Serbian',
		'sk'=>'Slovak',
		'sl'=>'Slovenian',
		'es'=>'Spanish',
		'sw'=>'Swahili',
		'sv'=>'Swedish',
		'th'=>'Thai',
		'tr'=>'Turkish',
		'uk'=>'Ukrainian',
		'vi'=>'Vietnamese',
		'cy'=>'Welsh',
		'yi'=>'Yiddish');
	}
	
	private static function getBrowserLanguage(){
		//try to find a suitable language
		$languages=SiteController::getLanguages();
		
		//<tr><td style="white-space: nowrap;">Afrikaans<br>Albanian<br>Arabic<br>Belarusian<br>Bulgarian<br>Catalan<br>Chinese<br>Croatian<br>Czech</td><td style="white-space: nowrap;">Danish<br>Dutch<br>English<br>Estonian<br>Filipino<br>Finnish<br>French<br>Galician<br>German</td><td style="white-space: nowrap;">Greek<br>Haitian Creole<br>Hebrew<br>Hindi<br>Hungarian<br>Icelandic<br>Indonesian<br>Irish<br>Italian</td><td style="white-space: nowrap;">Japanese<br>Korean<br>Latvian<br>Lithuanian<br>Macedonian<br>Malay<br>Maltese<br>Norwegian<br>Persian</td><td style="white-space: nowrap;">Polish<br>Portuguese<br>Romanian<br>Russian<br>Serbian<br>Slovak<br>Slovenian<br>Spanish<br>Swahili</td><td style="white-space: nowrap;">Swedish<br>Thai<br>Turkish<br>Ukrainian<br>Vietnamese<br>Welsh<br>Yiddish</td></tr>
		
		$browser_languages = explode(',', getenv('HTTP_ACCEPT_LANGUAGE'));
		for ($i=0, $n=sizeof($browser_languages); $i<$n && !isset($language); $i++) {
			foreach($languages as $key=>$value){
			      if (substr($browser_languages[$i],0,2)==$key){
				      $language=$key;
				      break;
			      }
			}
		}
		
		if(!isset($language))$language="en";
		return $language;
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionBaddatabase()
	{
		//before we allow someone to enter this very critical page, we must first
		//make sure that this is truely the very first attempt to show this database
		//and not somebody trying to hack their way in.
		$connection=null;
		try{
			$connection=yii::app()->getDb();
			//if we come to this point, then somebody is trying to hack their way into the application
			//lets just get out.
			return;
		}catch(Exception $zz){
			//we are okay, this is the first time showing and not some hacker
		}

		// collect user input data
		if(isset($_POST['databasehost']) && strlen($_POST['databasehost'])!=0
			&& isset($_POST['dbname']) && strlen($_POST['dbname'])!=0
			&& isset($_POST['dbusername']) && strlen($_POST['dbusername'])!=0
			&& isset($_POST['dbpassword']) && strlen($_POST['dbpassword'])!=0
			&& isset($_POST['databaseport']) && strlen($_POST['databaseport'])!=0)
		{
			if((is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../config/main.php')
					|| is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../config'))
					&& file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'../config/main.php.toinstall')){
				//create the config file from the config.php.toinstall file
				$filewrite = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'../config/main.php',"w");
				$fileread = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'../config/main.php.toinstall',"r");
				while (!feof($fileread)) {
					$line=fgets($fileread);
					$line=str_replace(array('zzzmysqllocalhost','zzzmysqllazy8web','zzzmysqlusername','zzzmysqlpassword','zzzmysqlport'),
						array($_POST['databasehost'],$_POST['dbname'],$_POST['dbusername'],$_POST['dbpassword'],$_POST['databaseport']),$line);
					fwrite($filewrite,$line);
				}  
				fclose($filewrite);
				fclose($fileread);

				$this->redirect(array('site/index'));
			}		
		}
		// display the bad database form
		$this->layout=false; 
		$this->render('baddatabase');
	}
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'

		//just some tests to see if the database is working
		$connection=null;
		try{
			$connection=yii::app()->getDb();
			//new CDbConnection($dsn,$username,$password);
			// establish connection. You may try...catch possible exceptions
			$connection->active=true;
		}catch(Exception $zz){
			$this->redirect(array('site/baddatabase'));
		}
		$criteria=new CDbCriteria;
		$pages=new CPagination(User::model()->count($criteria));
		$sort=new CSort('User');
		$models=null;
		if(Yii::app()->user->isGuest){
			$language=SiteController::getBrowserLanguage();
			Yii::app()->setLanguage($language);
			Yii::app()->user->setState('languagecode',$language);		
		}else{
			if(Yii::app()->user->getState('allowAdmin')){
				$criteria->addCondition('(dateLastLogin>dateLastLogout OR (NOT ISNULL(dateLastLogin) AND ISNULL(dateLastLogout)))');
				$criteria->addCondition('DATEDIFF(NOW(),dateLastLogin)<1');
				$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
				$pages->applyLimit($criteria);
				$sort->applyOrder($criteria);
		
				$models=User::model()->findAll($criteria);
			}
		}
		$this->layout=null; 
		$this->render('index',array(
					'models'=>$models,
					'pages'=>$pages,
					'sort'=>$sort,
				));
	}


	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if(Yii::app()->user->isGuest){
			$language=SiteController::getBrowserLanguage();
			Yii::app()->setLanguage($language);
			Yii::app()->user->setState('languagecode',$language);		
		}		
		$form=new LoginForm;
		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$form->attributes=$_POST['LoginForm'];
			// validate user input and redirect to previous page if valid
			if($form->validate()){
				User::checkAndFixTable();
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		// display the login form
		$this->render('login',array('form'=>$form));
	}

	/**
	 * Logout the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		User::updateLogOutDatetime();
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}