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


class User extends CActiveRecord
{
	public static $Version=array("Lazy8Web 02.03 2011-02-27");
	
	private $_confirmPassword;
	public function getConfirmPassword(){
		return $this->_confirmPassword;
	}
	public function setConfirmPassword($pass){
		$this->_confirmPassword=$pass;
	}
	
	//these are forced to be added here to fix a crash in the case that
	//these do not exist in the database table User
	private $_dateLastLogin;
	public function getDateLastLogin(){
		return $this->_dateLastLogin;
	}
	public function setDateLastLogin($pass){
		$this->_dateLastLogin=$pass;
	}
	private $_dateLastLogout;
	public function getDateLastLogout(){
		return $this->_dateLastLogout;
	}
	public function setDateLastLogout($pass){
		$this->_dateLastLogout=$pass;
	}

	/**
	 * The followings are the available columns in table 'User':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $salt
	 * @var string $displayname
	 * @var string $mobil
	 * @var string $email
	 * @var integer $selectedCompanyId
	 * @var integer $selectedPeriodId
	 * @var string $changedBy
	 * @var string $dateChanged
	 * @var string $dateLastLogin
	 * @var string $dateLastLogout
	 */

	 public function toString(){
	 	 $returnString= 'id=' . $this->id .';'
	 	 	.'username='.$this->username .  ';' 
	 	 	.'displayname='.$this->displayname .  ';' 
	 	 	.'mobil='.$this->mobil .  ';' 
	 	 	.'email='.$this->email .  ';' 
	 	 	.'selectedCompany='.$this->selectedCompanyId .  ';' 
	 	 	.'selectedPeriodId='.$this->selectedPeriodId .  ';' ;
	 	 $returnString.=$this->optionsArrayToString($this->optionsUserTemplate());
	 	 $returnString.=$this->optionsArrayToString($this->optionsCompanyUserTemplate());
		return $returnString;
	 }
	 public static function optionsArrayToString($options){
		$returnString='';
		foreach($options as $key=>$option){
			$returnString.='<br />'. $key . '=' . Yii::app()->user->getState($option[0]);
		}
		return $returnString;
	 }
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'User';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, displayname, password', 'required'),
			array('username, password, displayname', 'length', 'max'=>128),
			array('mobil', 'length', 'max'=>50),
			array('email', 'length', 'max'=>100),
			array('dateLastLogin', 'safe'),
			array('dateLastLogout', 'safe'),
			array('dateChanged', 'safe'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'update'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),
			array('changedBy','default','value'=>Yii::app()->user->name),
                        array('confirmPassword', 'compare', 'compareAttribute'=>'password'),
		);
	}
	public function safeAttributes(){
		return array(
		    'confirmPassword','password',
		);
	}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'selectedCompany'=>array(self::BELONGS_TO, 'Company', 'selectedCompanyId'),
			'selectedPeriod'=>array(self::BELONGS_TO, 'Period', 'selectedPeriodId'),
			'useroptions'=>array(self::HAS_MANY, 'Options', 'userId',
				'condition'=>'companyId=0','order'=>'id'),
			'companyoptions'=>array(self::HAS_MANY, 'Options', 'userId',
				'condition'=>'companyId=selectedCompanyId AND companyId<>0','order'=>'id'),
			'companies'=>array(self::MANY_MANY, 'Company', 'CompanyUser(userId, companyId)',
				'together'=>true,
				'joinType'=>'INNER JOIN'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'username' => Yii::t('lazy8','Username'),
			'password' => Yii::t('lazy8','Password'),
			'confirmPassword' => Yii::t('lazy8','Confirm password'),
			'displayname' => Yii::t('lazy8','Displayname'),
			'mobil' => Yii::t('lazy8','Mobil'),
			'email' => Yii::t('lazy8','Email'),
			'userId' => Yii::t('lazy8','User'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'actions' => Yii::t('lazy8','Actions'),
			'dateLastLogin' => Yii::t('lazy8','Date last logged in'),
			'dateLastLogout' => Yii::t('lazy8','Date last logged out'),
		);
	}
	
	/**
	 * set all the states for the CWebUser
	 */
	public function setStates($force=false)
	{
		$webapp=Yii::app()->user;
		if($webapp->id==$this->id || $force){
			$webapp->setState('displayname', $this->displayname);
			$webapp->setState('selectedCompany', '');
			$webapp->setState('selectedPeriod', '');
			if(isset($this->selectedCompanyId)){
				$comp=Company::model()->findbyPk($this->selectedCompanyId);
				if(isset($comp)){
					$webapp->setState('selectedCompany', $comp->name);
				}else{
					$this->selectedCompanyId=0;
					$this->selectedPeriodId=0;
					$this->confirmPassword=$this->password;
					$this->save();
				}
			}
			if(isset($this->selectedPeriodId)){
				$per=Period::model()->findbyPk($this->selectedPeriodId);
				if(isset($per)){
					$webapp->setState('selectedPeriodStart', $per->dateStart);
					$webapp->setState('selectedPeriodEnd', $per->dateEnd);
				}else{
					//$this->selectedCompanyId=0;
					$this->selectedPeriodId=0;
					$this->confirmPassword=$this->password;
					$this->save();
				}
			}
			$webapp->setState('selectedCompanyId', $this->selectedCompanyId);
			$webapp->setState('selectedPeriodId', $this->selectedPeriodId);
			$allowCompanySelect=false;
			if(isset($this->companies))if(count($this->companies)>1)$allowCompanySelect=true;
			$webapp->setState('allowCompanySelect',$allowCompanySelect);
		}
		$this->setOptionStatesAndControlTable($force,false,$webapp,$this->optionsWebTemplate(),0,0);
		$this->setOptionStatesAndControlTable($force,false,$webapp,$this->optionsUserTemplate(),0,$this->id);
		$this->setOptionStatesAndControlTable($force,!isset($this->selectedCompanyId)||$this->selectedCompanyId==0,$webapp,$this->optionsCompanyUserTemplate(),$this->selectedCompanyId,$this->id);
		$this->setOptionStatesAndControlTable($force,!isset($this->selectedCompanyId)||$this->selectedCompanyId==0,$webapp,$this->optionsCompanyTemplate(),$this->selectedCompanyId,0);
		//echo Yii::app()->user->getState('languagecode');die();
		Yii::app()->setLanguage(Yii::app()->user->getState('languagecode'));
	}
	
	/**
	 * Converts the option to the correct format
	 *  
	 */
	private static function convertOptionToObject($textValue,$option)
	{
		switch($option[0]){
		case 'DROP_DOWN_LIST':
		case 'STRING':
			return $textValue;
			break;
		case 'INTEGER':
			return(int)$textValue;
			break;
		case 'FLOAT':
			return $textValue + 0.0;
			break;
		case 'DATE':
			return strtotime($textValue);
			break;
		case 'BOOLEAN':
			return  ($textValue=='true');
			break;
		}
	}
	
	/**
	 * Set the states and initialize the options table if necessary
	 *  
	 */
	public static function setOptionStatesAndControlTable($force,$skipDatabaseCheck,$webapp,$options,$compId,$userId)
	{
		if( ! $skipDatabaseCheck){
			foreach($options as $key=>$option){$foundOption=null;
				$foundOption=Options::model()->find('name=:name AND userId=:id AND companyId=:compid',
					array(':name'=>$key,':id'=>$userId,':compid'=>$compId));
				if($foundOption!==null){
					if($webapp->id==$userId||$force)
						$webapp->setState($key,User::convertOptionToObject($foundOption->datavalue,$option));
				}else{
					$createOption=new Options();
					$createOption->name=$key;
					$createOption->userId=$userId;
					$createOption->companyId=$compId;
					$createOption->datavalue=$option[1];
					$createOption->save();
					if($webapp->id==$userId||$force)
						$webapp->setState($key,User::convertOptionToObject($option[1],$option));
				}
				
			}
		}else{
			if($webapp->id==$userId||$force){
				//just set all company permissions to no
				foreach($options as $key=>$option){
					$webapp->setState($key, User::convertOptionToObject($option[2],$option));
				}
			}
		}
	}

	public static function updateOptionTemplate($options,$id,$companyId,$updateAdminOptions=true)
	{
		$webapp=Yii::app()->user;
		foreach($options as $key=>$option){
			if(($updateAdminOptions || $option[5]=='false') && $option[3]=='false'){
				$foundOption=Options::model()->find('name=:name AND userId=:id AND companyId=:compid',
					array(':name'=>$key,':id'=>$id,':compid'=>$companyId));
				if($foundOption===null){
					$createOption=new Options();
					$createOption->name=$key;
					$createOption->userId=$id;
					$createOption->companyId=$companyId;
					$foundOption=$createOption;
				}
				switch($option[0]){
				case 'STRING':
				case 'DROP_DOWN_LIST':
				case 'INTEGER':
				case 'FLOAT':
				case 'DATE':
					$foundOption->datavalue=$_POST['option_' . $foundOption->name];
					break;
				case 'BOOLEAN':
					$foundOption->datavalue=isset($_POST['option_' . $foundOption->name]) && $_POST['option_' . $foundOption->name]==1?'true':'false';
					break;
				}
				$foundOption->save();
				if($id==$webapp->id || $id==0)
					$webapp->setState($key, User::convertOptionToObject($foundOption->datavalue,$option));
			}
		}
	}

	/**
	 * @return array options template
	 *  'STRING','INTEGER','FLOAT','DATE','BOOLEAN'
	 */
	public static function optionsUserTemplate()
	{
		//arrayname=>StringType,DefaultValue,DefaultValueNoLogin,IsHidden,dropdownlist,IsAdminOnly,admin,editing user, nonediting user
		return array(
			'allowPeriodSelection'=>array('BOOLEAN','false','false','false','','true','true','true','true'),
			'allowSelf'=>array('BOOLEAN','false','false','false','','true','true','true','true'),
			'allowAdmin'=>array('BOOLEAN','false','false','false','','true','true','false','false'),
			'allowExportAll'=>array('BOOLEAN','false','false','false','','true','true','false','false'),
			'allowImport'=>array('BOOLEAN','false','false','false','','true','true','false','false'),
			'allowChangeLog'=>array('BOOLEAN','false','false','false','','true','true','false','false'),
			'allowCompanyCreation'=>array('BOOLEAN','false','false','false','','true','true','false','false'),
			'languagecode'=>array('DROP_DOWN_LIST','en','en','false','$ar=array();$msg=Message::model()->findAll(array(\'select\'=>\'distinct language\'));foreach($msg as $a){$ar[]=array(\'id\'=>$a[\'language\'],\'name\'=>yii::t(\'lazy8\',\'languagename.iso636.\'.$a[\'language\']));}$list=CHtml::encodeArray(CHtml::listData($ar,\'id\',\'name\'));','false','false','false','false'),
			'NumberRecordsPerPage'=>array('INTEGER','20','20','false','','false','false','false','false'),
			'TransactionEditWidthMultiplier'=>array('FLOAT','1.0','1.0','false','','false','false','false','false'),
			'NonStandardNumberDecimalFormat'=>array('STRING','','','false','','false','false','false','false'),
			'NonStandardDateFormat'=>array('STRING','','','false','','false','false','false','false'),
			'PdfPageFormat'=>array('DROP_DOWN_LIST','A4','A4','false','$list=array(\'A4\'=>\'A4\',\'LETTER\'=>\'LETTER\',\'A3\'=>\'A3\',\'A5\'=>\'A5\',\'B4\'=>\'B4\',\'B5\'=>\'B5\',\'B6\'=>\'B6\',\'C4\'=>\'C4\',\'C5\'=>\'C5\',\'E4\'=>\'E4\',\'E5\'=>\'E5\',\'G4\'=>\'G4\',\'G5\'=>\'G5\',\'P3\'=>\'P3\',\'P4\'=>\'P4\',\'LEGAL\'=>\'LEGAL\',\'GLETTER\'=>\'GLETTER\',\'JLEGAL\'=>\'JLEGAL\',\'QUARTO\'=>\'QUARTO\',\'FOLIO\'=>\'FOLIO\',\'EXECUTIVE\'=>\'EXECUTIVE\',\'MEMO\'=>\'MEMO\',\'FOOLSCAP\'=>\'FOOLSCAP\');','false','false','false','false'),
			'PdfFont'=>array('DROP_DOWN_LIST','helvetica','helvetica','false','$list=array(\'courier\'=>\'courier\',\'dejavusanscondensed\'=>\'dejavusanscondensed\',\'dejavusansmono\'=>\'dejavusansmono\',\'dejavusans\'=>\'dejavusans\',\'dejavuserifcondensed\'=>\'dejavuserifcondensed\',\'dejavuserif\'=>\'dejavuserif\',\'freemono\'=>\'freemono\',\'freesans\'=>\'freesans\',\'freeserif\'=>\'freeserif\',\'helvetica\'=>\'helvetica\',\'times\'=>\'times\');','false','false','false','false'),
			'PdfFontSize'=>array('DROP_DOWN_LIST','8','8','false','$list=array(6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14);','false','false','false','false'),
			'lastPrintedReportId'=>array('INTEGER','0','0','true','','false','false','false','false'),
			'isReportBlackAndWhite'=>array('BOOLEAN','false','true','true','','false','false','false','false'),
			'isReportForPrintout'=>array('BOOLEAN','false','true','true','','false','false','false','false'),
			);
	}
	/**
	 * @return array options template
	 *  'STRING','INTEGER','FLOAT','DATE','BOOLEAN'
	 */
	public static function optionsWebTemplate()
	{
		$footer="Copyright &copy; 2009 by <a href=\"http://lazy8.nu\">Thomas Dilts</a><br/>
All Rights Reserved.<br/>
Licensed under <a href=\"http://gnu.org/licenses\">GNU General Public License.</a><br/>
Powered by <a href=\"http://yiiframework.com/\">Yii Framework</a>";
		//arrayname=>StringType,DefaultValue,DefaultValueNoLogin,IsHidden,dropdownlist
		return array(
			'siteHeaderTitle'=>array('STRING','Lazy8Web','Lazy8Web','false'),
			'siteFooter'=>array('STRING',$footer,$footer,'false'),
			);
	}
	/**
	 * @return array options template
	 *  'STRING','INTEGER','FLOAT','DATE','BOOLEAN'
	 */
	public static function optionsCompanyUserTemplate()
	{
		//arrayname=>StringType,DefaultValue,DefaultValueNoLogin,IsHidden,admin,editing user, nonediting user
		return array(
			'allowReports'=>array('BOOLEAN','true','false','false','true','true','true'),
			'allowTrans'=>array('BOOLEAN','true','false','false','true','true','false'),
			'allowAccount'=>array('BOOLEAN','true','false','false','true','true','false'),
			'allowAccountTypes'=>array('BOOLEAN','true','false','false','true','true','false'),
			'allowPeriod'=>array('BOOLEAN','true','false','false','true','true','false'),
			'allowCustomer'=>array('BOOLEAN','true','false','false','true','true','false'),
			'allowCompany'=>array('BOOLEAN','true','false','false','true','false','false'),
			'allowCompanyExport'=>array('BOOLEAN','true','false','false','true','false','false'),
			'allowReEditingOfTransactions'=>array('BOOLEAN','false','false','false','false','false','false'),
			);
	}
	/**
	 * @return array options template
	 *  'STRING','INTEGER','FLOAT','DATE','BOOLEAN'
	 */
	public static function optionsCompanyTemplate()
	{
		//arrayname=>StringType,DefaultValue,DefaultValueNoLogin,IsHidden,admin,editing user, nonediting user
		return array(
			'showPeriodTransactionNumber'=>array('BOOLEAN','false','false','false'),
			'RemoveEmptyAmountsInReports'=>array('BOOLEAN','true','true','false','false','true','true','false'),
			);
	}
	public function delete()
	{
		parent::delete();
		$this->onDeletePost(new Lazy8Event('User',$this->id));
		$this->dbConnection->createCommand("DELETE FROM CompanyUser WHERE userId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM Options WHERE userId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM ReportUserLastUsedParams WHERE userId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM TempTrans WHERE userId={$this->id}")->execute();
	}
	private static function leading_zeros($value, $places){
	    $leading = "";
	    if(is_numeric($value)){
		for($x = 1; $x <= $places; $x++){
		    $ceiling = pow(10, $x);
		    if($value < $ceiling){
			$zeros = $places - $x;
			for($y = 1; $y <= $zeros; $y++){
			    $leading .= "0";
			}
		    $x = $places + 1;
		    }
		}
		$output = $leading . $value;
	    }
	    else{
		$output = $value;
	    }
	    return $output;
	}	
	public static function parseDate($toParse,$locale=null){
		//this will parse a date.  Simple thing. But it takes
		//a lot of code to do it since:
		//the following 2 rows can't be used because it is only in php 5.3.  Too new!	
		/*
		$dt = date_create_from_format( User::getPhPDateFormatNoPercent($locale),$toParse );
		return $dt->format('Y-m-d') ;
		*/
		//and the following 2 rows wont work because it wont work in windows.
		/*
		$dateArray=strptime($toParse,User::getPhPDateFormat($locale));
		return ($dateArray['tm_year']+1900) . "-" . User::leading_zeros($dateArray['tm_mon']+1,2) . "-" . User::leading_zeros($dateArray['tm_mday'],2);
		*/
		
		//so here is my solution
		$format=User::getPhPDateFormatNoPercent($locale);
		//parse out the given date into a number array
		$intArray=array();
		$testInt='';
		$foundStart=false;
		$splitStringArray=str_split($toParse);
		foreach($splitStringArray as $testChar){
			if(is_numeric($testChar)){
				$testInt.= $testChar;
				$foundStart=true;
			}else{
				if($foundStart){
					$intArray[]=$testInt+0;//force conversion to number.Drops leading zeros
					$testInt='';
					$foundStart=false;
				}
			}
		}
		$intArray[]=$testInt+0;//force conversion to number.Drops leading zeros
		
		//read the format and put into an array
		$NumberOrder=array();
		$posYear=strpos($format,'y');
		if($posYear===false)
			$NumberOrder['Y']=strpos($format,'Y');
		else
			$NumberOrder['y']=$posYear;
		$NumberOrder['m']=strpos($format,'m');
		$NumberOrder['d']=strpos($format,'d');
		//merge the format and the number array
		asort($NumberOrder);
		$i=0;
		$resultArray=array();
		foreach ($NumberOrder as $key => $val) {
			if($key=='y')
				$resultArray['Y']=(date('Y') - (date('Y') % 100))+$intArray[$i];
			elseif($key=='Y')
				$resultArray['Y']=$intArray[$i];
			elseif($key=='m')
				$resultArray['m']=$intArray[$i];
			elseif($key=='d')
				$resultArray['d']=$intArray[$i];
			$i++;
		}	
		//return mysql formatted time , that is, Y-m-d
		return User::leading_zeros($resultArray['Y'],4) . '-' .  User::leading_zeros($resultArray['m'],2) . '-' .  User::leading_zeros($resultArray['d'],2) ;
	}
	public static function getPhPDateFormatNoPercent($locale=null){
		//convert the format to PHP date format
		return str_replace(array('MM','M','dd','d','%%d','yyyy','yy'),array('m','m','d','d','d','Y','y',),User::getDateFormat($locale));
	}
	public static function getPhPDateFormat($locale=null){
		//convert the format to PHP date format
		return str_replace(array('MM','M','dd','d','%%d','yyyy','yy'),array('%m','%m','%d','%d','%d','%Y','%y',),User::getDateFormat($locale));
	}
	public static function getDateFormatted($dateToFormat,$locale=null,$formatter=null){
		if(strlen($dateToFormat)==0)return "";
		if($locale==null)
			$locale=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
		if($locale==null)
			$locale=CLocale::getInstance();
		if ($formatter==null)
			$formatter=new CDateFormatter($locale);
		return $formatter->format(User::getDateFormat($locale),$dateToFormat);
	}
	public static function getDateFormat($locale=null){
		$dateFormat=trim(Yii::app()->user->getState('NonStandardDateFormat'));
		if(strlen($dateFormat)==0){
			if($locale==null)
				$locale=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
			if($locale==null)
				$locale=CLocale::getInstance();
			return $locale->getDateFormat('short');
		}
		return $dateFormat;
	}
	public static function getNumberFormat(){
		$numberFormat=trim(Yii::app()->user->getState('NonStandardNumberDecimalFormat'));
		if(strlen($numberFormat)>0){
			return $numberFormat;
		}else{
			$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
			if($cLoc==null)
				$cLoc=CLocale::getInstance();
			return $cLoc->getDecimalFormat();
		}
	}
	public static function updateLogInDatetime(){
		$usersModel=User::model()->findbyPk(Yii::app()->user->id);
		$usersModel->confirmPassword=$usersModel->password;
		$usersModel->dateLastLogin=date('Y-m-d H:i:s');
		$usersModel->save();
	}
	public function updateThisLogInDatetime(){
		$this->confirmPassword=$this->password;
		$this->dateLastLogin=date('Y-m-d H:i:s');
		$this->save();
	}
	public static function updateLogOutDatetime(){
		$usersModel=User::model()->findbyPk(Yii::app()->user->id);
		//there are problems here that it crashes if logging out after the 
		//SESSION is dead.  This "if" should fix it.
		if(isset($usersModel->id) && $usersModel->id!=null && $usersModel->id!=0){
			$usersModel->confirmPassword=$usersModel->password;
			$usersModel->dateLastLogout=date('Y-m-d H:i:s');
			$usersModel->save();
		}
	}
	public static function checkAndFixTable(){
		//this table has changed form.  Make sure it has the correct fields
		$connection=yii::app()->getDb();
		try{
			$command=$connection->createCommand('select dateLastLogin from User LIMIT 0,1');
			$reader=$command->query();
		}catch(Exception $zz){
			$sql = "ALTER TABLE `User` ADD `dateLastLogin` DATETIME NULL, ADD `dateLastLogout` DATETIME NULL";
			$command=$connection->createCommand($sql);
			$reader=$command->execute();
		}
		
	}
}