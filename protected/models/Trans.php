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


class Trans extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Trans':
	 * @var integer $id
	 * @var integer $companyId
	 * @var string $regDate
	 * @var string $invDate
	 * @var integer $periodId
	 * @var integer $periodNum
	 * @var integer $companyNum
	 * @var string $notes
	 * @var string $fileInfo
	 * @var string $changedBy
	 * @var string $dateChanged
	 */

	 public function toString(){
	 	 $returnString= 'id=' . $this->id .';'
	 	 	.'regDate='.$this->regDate .  ';' 
	 	 	.'invDate='.$this->invDate .  ';' 
	 	 	.'periodId='.$this->periodId .  ';' 
	 	 	.'periodNum='.$this->periodNum .  ';' 
	 	 	.'companyNum='.$this->companyNum .  ';' 
	 	 	.'notes='.$this->notes .  ';' 
	 	 	.'fileInfo='.$this->fileInfo .  ';' ;
		$transRows=$this->amounts;
		if(isset($transRows) && count($transRows)>0){
			foreach($transRows as $transrow){
				$returnString.='<br />'.$transrow->toString();
			}
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
		return 'Trans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('companyId, periodId, periodNum, companyNum', 'required'),
			array('companyId, periodId, periodNum, companyNum', 'numerical', 'integerOnly'=>true),
			array('notes, fileInfo', 'length', 'max'=>255),
			array('regDate, invDate, dateChanged', 'safe'),
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
			'amounts'=>array(self::HAS_MANY, 'TransRow', 'transId')
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
			'regDate' => Yii::t('lazy8','Reg Date'),
			'invDate' => Yii::t('lazy8','Inv Date'),
			'periodNum' => Yii::t('lazy8','Trans Num'),
			'companyNum' => Yii::t('lazy8','Trans Num'),
			'notes' => Yii::t('lazy8','Notes'),
			'fileInfo' => Yii::t('lazy8','File Info'),
			'userId' => Yii::t('lazy8','User'),
			'dateChanged' => Yii::t('lazy8','Date Changed'),
			'changedBy' => Yii::t('lazy8','Changed by'),
			'actions' => Yii::t('lazy8','Actions'),
		);
	}
	public function delete()
	{
		//TransRow::model()->deleteAll('transId=:transId', array(':transId'=> $this->id));
		parent::delete();
		$this->dbConnection->createCommand("DELETE FROM TransRow WHERE transId={$this->id}")->execute();
	}
}