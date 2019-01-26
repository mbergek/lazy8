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

/**
 * This is the model class for table "Template".
 *
 * The followings are the available columns in table 'Template':
 * @property integer $id
 * @property integer $companyId
 * @property string $name
 * @property string $desc
 * @property integer $sortOrder
 * @property integer $allowAccountingView
 * @property integer $allowFreeTextField
 * @property string $freeTextFieldDefault
 * @property integer $allowFilingTextField
 * @property string $filingTextFieldDefault
 * @property integer $forceDateToday
 * @property string $changedBy
 * @property string $dateChanged
 */
class Template extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Template the static model class
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
		return 'Template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('companyId, sortOrder, allowAccountingView, allowFreeTextField, allowFilingTextField, forceDateToday', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('desc, freeTextFieldDefault, filingTextFieldDefault', 'length', 'max'=>255),
			array('dateChanged', 'safe'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'update'),
			array('dateChanged','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),
			array('changedBy','default','value'=>Yii::app()->user->name),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, companyId, name, desc, sortOrder, allowAccountingView, allowFreeTextField, freeTextFieldDefault, allowFilingTextField, filingTextFieldDefault, forceDateToday, changedBy, dateChanged', 'safe', 'on'=>'search'),
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
			'templateRows'=>array(self::HAS_MANY, 'TemplateRow', 'templateId','order'=>'sortOrder'),
			'company'=>array(self::BELONGS_TO, 'Company', 'companyId'),
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
			'name' => Yii::t('lazy8','Name'),
			'desc' => Yii::t('lazy8','Desc'),
			'sortOrder' => Yii::t('lazy8','Sort Order'),
			'allowAccountingView' => Yii::t('lazy8','Allow Accounting View'),
			'allowFreeTextField' => Yii::t('lazy8','Allow Free Text Field'),
			'freeTextFieldDefault' => Yii::t('lazy8','Free Text Field Default'),
			'allowFilingTextField' => Yii::t('lazy8','Allow Filing Text Field'),
			'filingTextFieldDefault' => Yii::t('lazy8','Filing Text Field Default'),
			'forceDateToday' => Yii::t('lazy8','Force Date Today'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('companyId',$this->companyId);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('sortOrder',$this->sortOrder);
		$criteria->compare('allowAccountingView',$this->allowAccountingView);
		$criteria->compare('allowFreeTextField',$this->allowFreeTextField);
		$criteria->compare('freeTextFieldDefault',$this->freeTextFieldDefault,true);
		$criteria->compare('allowFilingTextField',$this->allowFilingTextField);
		$criteria->compare('filingTextFieldDefault',$this->filingTextFieldDefault,true);
		$criteria->compare('forceDateToday',$this->forceDateToday);
		$criteria->compare('changedBy',$this->changedBy,true);
		$criteria->compare('dateChanged',$this->dateChanged,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	private static function FindAccountId($code,$companyId){
		if(strlen($code)==0 || $code==0)
			return 0;
		$accountModel= Account::model()->find(array('condition'=>'code='.$code.' AND companyId='.$companyId));
		if($accountModel!=null)
			return $accountModel->id;
		return 0;
	}
	/**
	 * @return array Errors  Import Template information
	 */
	public static function importTemplates($root,$companyId,&$errors)
	{
		$nodeTemplates = $root->getElementsByTagName('template');
		
		foreach($nodeTemplates as $nodeTemplate){
			if($root->getAttribute('version')>1.00){
				$errors[]='*TemplateVersion*'.Yii::t('lazy8','There maybe problems because this is a file version greater then this programs version');
				$errors[]=Yii::t('lazy8','Select a file and try again');
			}
			
			$modelTemplate=new Template;
			$modelTemplate->companyId=$companyId;
			$modelTemplate->name=$nodeTemplate->getAttribute('name');
			$modelTemplate->desc=$nodeTemplate->getAttribute('desc');
			$modelTemplate->sortOrder=$nodeTemplate->getAttribute('sortorder');
			$modelTemplate->allowAccountingView=$nodeTemplate->getAttribute('allowaccountingview')=='1'?1:0;
			$modelTemplate->allowFreeTextField=$nodeTemplate->getAttribute('allowfreetextfield')=='1'?1:0;
			$modelTemplate->freeTextFieldDefault=$nodeTemplate->getAttribute('freetextfielddefault');
			$modelTemplate->allowFilingTextField=$nodeTemplate->getAttribute('allowfilingtextfield')=='1'?1:0;
			$modelTemplate->filingTextFieldDefault=$nodeTemplate->getAttribute('filingtextfielddefault');
			$modelTemplate->forceDateToday=$nodeTemplate->getAttribute('forcedatetoday');
			if(!$modelTemplate->save()){
				$errors[]=Yii::t('lazy8','Could not create the modelTemplate, bad paramters').';name='.$modelTemplate->name.';'.serialize($modelTemplate->getErrors());
				return $errors;
			}
			$nodesTemplateRows = $nodeTemplate->getElementsByTagName('templaterow');
			
			foreach($nodesTemplateRows as $nodesTemplateRow){
				$modelTemplateRow=new TemplateRow;
				$modelTemplateRow->templateId=$modelTemplate->id;
				$modelTemplateRow->name=$nodesTemplateRow->getAttribute('name');
				$modelTemplateRow->desc=$nodesTemplateRow->getAttribute('desc');
				$modelTemplateRow->sortOrder=$nodesTemplateRow->getAttribute('sortorder');
				$modelTemplateRow->isDebit=$nodesTemplateRow->getAttribute('isdebit')=='1'?1:0;
				$modelTemplateRow->defaultAccountId=Template::FindAccountId($nodesTemplateRow->getAttribute('defaultaccount'),$modelTemplate->companyId);
				$modelTemplateRow->defaultValue=$nodesTemplateRow->getAttribute('defaultvalue');
				$modelTemplateRow->allowMinus=$nodesTemplateRow->getAttribute('allowminus')=='1'?1:0;
				$modelTemplateRow->phpFieldCalc=$nodesTemplateRow->getAttribute('phpfieldcalc');
				$modelTemplateRow->allowChangeValue=$nodesTemplateRow->getAttribute('allowchangevalue')=='1'?1:0;
				$modelTemplateRow->allowRepeatThisRow=$nodesTemplateRow->getAttribute('allowrepeatthisrow')=='1'?1:0;
				$modelTemplateRow->allowCustomer=$nodesTemplateRow->getAttribute('allowcustomer')=='1'?1:0;
				$modelTemplateRow->allowNotes=$nodesTemplateRow->getAttribute('allownotes')=='1'?1:0;
				$modelTemplateRow->isFinalBalance=$nodesTemplateRow->getAttribute('isfinalbalance')=='1'?1:0;
				
				if(!$modelTemplateRow->save()){
					$modelTemplate->delete();
					$errors[]=Yii::t('lazy8','Could not create the TemplateRow, bad paramters').';'.serialize($modelTemplateRow->getErrors());
					return ;
				}
				$nodesTemplateRowAccounts = $nodesTemplateRow->getElementsByTagName('templaterowaccount');
				
				foreach($nodesTemplateRowAccounts as $nodesTemplateRowAccount){
					$modelTemplateRowAccount=new TemplateRowAccount;
					$modelTemplateRowAccount->templateRowId=$modelTemplateRow->id;
					$modelTemplateRowAccount->accountId=Template::FindAccountId($nodesTemplateRowAccount->getAttribute('code'),$modelTemplate->companyId);
					if($modelTemplateRowAccount->accountId!=0){
						if(!$modelTemplateRowAccount->save()){
							$modelTemplate->delete();
							$errors[]=Yii::t('lazy8','Could not create the TemplateRowAccount, bad paramters').';'.serialize($modelTemplateRowAccount->getErrors());
							return ;
						}
					}else{
						$errors[]=Yii::t('lazy8','Could not create the Account, bad account number').';'.$nodesTemplateRowAccount->getAttribute('code');
					}
				}
			}
		}
	}
	/**
	 * @return array Errors Export Account information
	 */
	public static function exportAllTemplates($writer,$companyId)
	{
		$templates= Template::model()->findAll(array('order'=>'sortOrder','condition'=>'companyId='.$companyId));
		foreach($templates as $template){
			Template::exportTemplate($writer,$template->id);
		}
	}
	/**
	 * @return array Errors Export Account information
	 */
	public static function exportTemplate($writer,$id)
	{
		$template= Template::model()->findbyPk($id);
		$writer->startElement('template');
		$writer->writeAttribute('version', '1.00');
		$writer->writeAttribute("name",$template->name);
		$writer->writeAttribute("desc",$template->desc);
		$writer->writeAttribute("sortorder",$template->sortOrder);
		$writer->writeAttribute("allowaccountingview",$template->allowAccountingView);
		$writer->writeAttribute("allowfreetextfield",$template->allowFreeTextField);
		$writer->writeAttribute("freetextfielddefault",$template->freeTextFieldDefault);
		$writer->writeAttribute("allowfilingtextfield",$template->allowFilingTextField);
		$writer->writeAttribute("filingtextfielddefault",$template->filingTextFieldDefault);
		$writer->writeAttribute("forcedatetoday",$template->forceDateToday);
		
		$templateRows= TemplateRow::model()->findAll(array(
			'select'=>'t.id,Account.id as defaultAccountId,t.name as name,t.desc,t.sortOrder,t.isDebit,t.defaultValue,t.allowMinus,t.phpFieldCalc,t.allowChangeValue,t.allowRepeatThisRow,t.allowCustomer,t.allowNotes,t.isFinalBalance',
			'join'=>'LEFT join Account on t.defaultAccountId=Account.id','condition'=>'templateId='.$id,'order'=>'t.sortOrder'));
		foreach($templateRows as $templateRow){
			$writer->startElement('templaterow');
			$writer->writeAttribute("name",$templateRow->name);
			$writer->writeAttribute("desc",$templateRow->desc);
			$writer->writeAttribute("sortorder",$templateRow->sortOrder);
			$writer->writeAttribute("isdebit",$templateRow->isDebit);
			$writer->writeAttribute("defaultaccount",$templateRow->defaultAccountId);
			$writer->writeAttribute("defaultvalue",$templateRow->defaultValue);
			$writer->writeAttribute("allowminus",$templateRow->allowMinus);
			$writer->writeAttribute("phpfieldcalc",$templateRow->phpFieldCalc);
			$writer->writeAttribute("allowchangevalue",$templateRow->allowChangeValue);
			$writer->writeAttribute("allowrepeatthisrow",$templateRow->allowRepeatThisRow);
			$writer->writeAttribute("allowcustomer",$templateRow->allowCustomer);
			$writer->writeAttribute("allownotes",$templateRow->allowNotes);
			$writer->writeAttribute("isfinalbalance",$templateRow->isFinalBalance);

			$accounts=Account::model()->findAll(array('select'=>'t.code as code',
				'join'=>'LEFT JOIN TemplateRowAccount ON t.id=TemplateRowAccount.accountId',
				'order'=>'t.code','condition'=>'templateRowId='.$templateRow->id
				));
			foreach($accounts as $account){
				$writer->startElement('templaterowaccount');
				$writer->writeAttribute("code",$account->code);
				$writer->endElement();
			}
			$writer->endElement();
		}
		$writer->endElement();
	}
	public function delete()
	{
		parent::delete();
		$rowList=TemplateRow::model()->findAll('templateId=:templateId', array(':templateId'=> $this->id));
		if($rowList!=null){
			foreach($rowList as $model){
				$model->delete();
			}
		}
	}
}