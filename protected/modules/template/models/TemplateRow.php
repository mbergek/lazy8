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
 * This is the model class for table "TemplateRow".
 *
 * The followings are the available columns in table 'TemplateRow':
 * @property integer $id
 * @property integer $templateId
 * @property integer $sortOrder
 * @property string $name
 * @property string $desc
 * @property integer $isDebit
 * @property integer $defaultAccountId
 * @property double $defaultValue
 * @property integer $allowMinus
 * @property string $phpFieldCalc
 * @property integer $allowChangeValue
 * @property integer $allowRepeatThisRow
 * @property integer $allowCustomer
 * @property integer $allowNotes
 * @property integer $isFinalBalance
 */
class TemplateRow extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TemplateRow the static model class
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
		return 'TemplateRow';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('templateId, sortOrder, isDebit, defaultAccountId, allowMinus, allowChangeValue, allowRepeatThisRow', 'numerical', 'integerOnly'=>true),
			array('defaultValue', 'numerical'),
			array('name', 'length', 'max'=>100),
			array('desc', 'length', 'max'=>255),
			array('phpFieldCalc', 'safe'),
			array('allowCustomer', 'safe'),
			array('allowNotes', 'safe'),
			array('isFinalBalance', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, templateId, sortOrder, name, desc, isDebit, defaultAccountId, defaultValue, allowMinus, phpFieldCalc, allowChangeValue, allowRepeatThisRow,allowCustomer,allowNotes', 'safe', 'on'=>'search'),
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
			'templateRowAccounts'=>array(self::HAS_MANY, 'TemplateRowAccount', 'templateRowId',/*,'order'=>'sortOrder') What will we sort on? Account ID! */)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('lazy8','Id'),
			'templateId' => 'Template',
			'name' => Yii::t('lazy8','Name'),
			'desc' => Yii::t('lazy8','Desc'),
			'sortOrder' => Yii::t('lazy8','Sort Order'),
			'isDebit' => Yii::t('lazy8','Is Debit'),
			'isFinalBalance' => Yii::t('lazy8','Is final balance'),
			'defaultAccountId' => Yii::t('lazy8','Default Account'),
			'defaultValue' => Yii::t('lazy8','Default Value'),
			'allowMinus' => Yii::t('lazy8','Allow Minus'),
			'phpFieldCalc' => Yii::t('lazy8','Php Field Calc'),
			'allowChangeValue' => Yii::t('lazy8','Allow Change Value'),
			'allowRepeatThisRow' => Yii::t('lazy8','Allow Repeat This Row'),
			'allowCustomer' => Yii::t('lazy8','Allow Customer'),
			'allowNotes' => Yii::t('lazy8','Allow Notes'),
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
		$criteria->compare('templateId',$this->templateId);
		$criteria->compare('sortOrder',$this->sortOrder);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('isDebit',$this->isDebit);
		$criteria->compare('defaultAccountId',$this->defaultAccountId);
		$criteria->compare('defaultValue',$this->defaultValue);
		$criteria->compare('allowMinus',$this->allowMinus);
		$criteria->compare('phpFieldCalc',$this->phpFieldCalc,true);
		$criteria->compare('allowChangeValue',$this->allowChangeValue);
		$criteria->compare('allowRepeatThisRow',$this->allowRepeatThisRow);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	public function delete()
	{
		parent::delete();
		$rowList=TemplateRowAccount::model()->findAll('templateRowId=:templateRowId', array(':templateRowId'=> $this->id));
		if($rowList!=null){
			foreach($rowList as $model){
				$model->delete();
			}
		}
	}
}