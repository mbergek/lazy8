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


class Report extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Report':
	 * @var integer $id
	 * @var integer $companyId
	 * @var string $name
	 * @var string $desc
	 * @var integer $sortOrder
	 * @var string $cssColorFileName
	 * @var string $cssBwFileName
	 * @var string $selectSql
	 * @var string $changedBy
	 * @var string $dateChanged
	 */

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
		return 'Report';
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
			array('companyId,sortOrder', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('desc, cssColorFileName, cssBwFileName', 'length', 'max'=>255),
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
			'reportparameters'=>array(self::HAS_MANY, 'ReportParameters', 'reportId','order'=>'sortOrder'),
			'company'=>array(self::BELONGS_TO, 'Company', 'companyId'),
			'groups'=>array(self::HAS_MANY, 'ReportGroups', 'reportId','order'=>'sortOrder'),
			'rows'=>array(self::HAS_MANY, 'ReportRows', 'reportId','order'=>'sortOrder'),
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
			'cssColorFileName' => Yii::t('lazy8','Css Color File Name'),
			'cssBwFileName' => Yii::t('lazy8','Css B/W File Name'),
			'selectSql' => Yii::t('lazy8','Sql Select Statement'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'actions' => Yii::t('lazy8','Actions'),
			'sortOrder' => Yii::t('lazy8','Sorting Order'),
		);
	}
	public function delete()
	{
		parent::delete();
		$this->dbConnection->createCommand("DELETE FROM ReportUserLastUsedParams WHERE reportId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM ReportParameters WHERE reportId={$this->id}")->execute();
		$this->dbConnection->createCommand("DELETE FROM ReportRows WHERE reportId={$this->id}")->execute();
		$groups=$this->groups;
		if(isset($groups) && count($groups)>0){
			foreach($groups as $group){
				$group->delete();
			}
		}
	}
}