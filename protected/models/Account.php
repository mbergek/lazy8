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


class Account extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Account':
	 * @var integer $id
	 * @var integer $companyId
	 * @var integer $accountTypeId
	 * @var integer $code
	 * @var string $name
	 * @var string $changedBy
	 * @var string $dateChanged
	 */

	 public function toString(){
	 	 return 'id=' . $this->id .';'
	 	 	.'code='.$this->code .  ';' 
	 	 	.'name='.$this->name .  ';' 
	 	 	.'accountType='.$this->accountType->code .  ';' . $this->accountType->name .  ';' . $this->accountTypeId ; 
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
		return 'Account';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('companyId, accountTypeId, code, name', 'required'),
			array('companyId, accountTypeId, code', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('dateChanged', 'safe'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'update'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),
			array('changedBy','default','value'=>Yii::app()->user->name),
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
			'accountType'=>array(self::BELONGS_TO, 'AccountType', 'accountTypeId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'companyId' => Yii::t('lazy8','Company'),
			'accountTypeId' => Yii::t('lazy8','Account Type'),
			'accountType' => Yii::t('lazy8','Account Type'),
			'code' => Yii::t('lazy8','Code'),
			'name' => Yii::t('lazy8','Name'),
			'userId' => Yii::t('lazy8','User'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'actions' => Yii::t('lazy8','Actions'),
		);
	}
	/**
	 * @return array Errors  Import Account information
	 */
	public static function ImportAccounts($fileNameAndPath)
	{
		$errors=array();
		$dom = new domDocument();
		if( ! $dom->load($fileNameAndPath) ){
			$errors[]=Yii::t('lazy8','Upload failed.  This is not a valid file.');
			$errors[]=Yii::t('lazy8','Select a file and try again');
			return $errors;
		}
		
		$root = $dom->documentElement;
		if($root->nodeName!="lazy8webportaccount"){
			$errors[]=Yii::t('lazy8','Upload failed.  This is not a valid file.');
			$errors[]=Yii::t('lazy8','Select a file and try again');
			return $errors;
		}
		if($root->getAttribute('version')>1.00){
			$errors[]=Yii::t('lazy8','There maybe problems because this is a file version greater then this programs version');
			$errors[]=Yii::t('lazy8','Select a file and try again');
		}
		$nodesAccountTypes = $root->getElementsByTagName('accounttype');
		foreach($nodesAccountTypes as $nodeAccountType){
			$modelAccountType=new AccountType;
			$modelAccountType->companyId=Yii::app()->user->getState('selectedCompanyId');
			$modelAccountType->code=$nodeAccountType->getAttribute('code');
			$modelAccountType->name=$nodeAccountType->getAttribute('name');
			$modelAccountType->sortOrder=$nodeAccountType->getAttribute('sortorder');
			$modelAccountType->isInBalance=$nodeAccountType->getAttribute('isinbalance')=='1'?1:0;
			if(!$modelAccountType->save()){
				$errors[]=Yii::t('lazy8','Could not create the AccountType, bad paramters').';name='.$modelAccountType->name.';'.serialize($modelAccountType->getErrors());
				return $errors;
			}
			$nodesAccounts = $nodeAccountType->getElementsByTagName('account');
			foreach($nodesAccounts as $nodeAccount){
				$modelAccount=new Account;
				$modelAccount->companyId=Yii::app()->user->getState('selectedCompanyId');
				$modelAccount->code=$nodeAccount->getAttribute('code');
				$modelAccount->accountTypeId=$modelAccountType->id;
				$modelAccount->name=$nodeAccount->getAttribute('name');
				if(!$modelAccount->save()){
					$modelAccountType->delete();
					$errors[]=Yii::t('lazy8','Could not create the Account, bad paramters').';'.serialize($modelAccount->getErrors());
					return $errors;
				}
			}
		}
		return $errors;
	}
	/**
	 * @return array Errors Export Account information
	 */
	public static function ExportAccounts()
	{
		$comp=Company::model()->findbyPk(Yii::app()->user->getState('selectedCompanyId'));
		// set headers
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Content-Type: text/xml");
		header("Content-Disposition: attachment; filename=\"lazy8web.accounts.". CompanyController::replace_bad_filename_chars($comp->name) . "." . date('Y-m-d_H.i.s') . ".xml\"");
		header("Content-Transfer-Encoding: binary");
		//header("Content-Length: " );

		$writer = new XMLWriter();
		// Output directly to the user
		
		$writer->openURI('php://output');
		$writer->startDocument('1.0','utf-8');
		
		$writer->setIndent(4);
		$writer->startElement('lazy8webportaccount');
		$writer->writeAttribute('version', '1.00');
		$accountTypes= AccountType::model()->findAll(array('condition'=>'companyId='.Yii::app()->user->getState('selectedCompanyId')));
		foreach($accountTypes as $accountType){
			$writer->startElement('accounttype');
			$writer->writeAttribute("code",$accountType->code);
			$writer->writeAttribute("name",$accountType->name);
			$writer->writeAttribute("sortorder",$accountType->sortOrder);
			$writer->writeAttribute("isinbalance",$accountType->isInBalance);
			$accounts= Account::model()->findAll(array('condition'=>'companyId='.Yii::app()->user->getState('selectedCompanyId') . ' AND accountTypeId='. $accountType->id));
			foreach($accounts as $account){
				$allAccounts[$account->id]=$account->code;
				$writer->startElement('account');
				$writer->writeAttribute("code",$account->code);
				$writer->writeAttribute("name",$account->name);
				$writer->endElement();
			}
			$writer->endElement();
		}
		$writer->endElement();
		$writer->endDocument();
		
		$writer->flush();
		return;//we may not send any more to the screen or it will mess up the file we just sent!
	}
	public function delete()
	{
		parent::delete();
		yii::app()->onDeletePost(new Lazy8Event('Account',$this->id));
	}
}