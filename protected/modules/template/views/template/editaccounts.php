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

$this->breadcrumbs=array(
	'Templates'=>array('index'),
	$model->name,
);


?>

<h1><?php echo Yii::t('lazy8','Template Row Accounts for'); ?> #<?php echo $model->id . ' - ' .$model->name; ?></h1>

<div class="yiiForm">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'template-row-form',
	'enableAjaxValidation'=>false,
)); 
	$accountsModel=Account::model()->findAll(array('join'=>' LEFT JOIN TemplateRowAccount ON t.id=TemplateRowAccount.accountId','select'=>'t.id as code, TemplateRowAccount.id as id, CAST(CONCAT(t.code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'t.code','condition'=>'templateRowId='.$model->id));
	$allAccountsModel=Account::model()->findAll(array('select'=>'id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code','condition'=>'companyId='.$companyId));
	//get rid of already selected accounts in the from list
	
	$allAccountsModelCopy=$allAccountsModel;
	foreach($accountsModel as $key=>$value){
		foreach($allAccountsModelCopy as $n=>$allAccount){
			if($allAccount->id==$value->code){
				unset($allAccountsModel[$n]);
				break;
			}
			
		}
	}
	$allAccounts=CHtml::encodeArray(CHtml::listData($allAccountsModel,'id','name'));
	$accounts=CHtml::encodeArray(CHtml::listData($accountsModel,'id','name'));
	?>
	<table class="dataGrid" style='margin:20px 20px 20px 20px;'><tr class='even'><td>
		<?php echo CHtml::listBox('accountsfrom',"", $allAccounts,array('style'=>'height:300px;','multiple'=>'true')); ?>
	</td><td>
		<table><tr><td>
		<?php echo CHtml::submitButton('=>',array('name'=>'Add','title'=>Yii::t('lazy8','contexthelp.Accounts.Add Row'))); ?>
		</td></tr><tr><td>
		<?php echo CHtml::submitButton('<=',array('name'=>'Remove','title'=>Yii::t('lazy8','contexthelp.Accounts.Remove Row'))); ?>
		</td></tr></table>
	</td><td>
		<?php echo CHtml::listBox('accountsto',"", $accounts,array('style'=>'height:300px;','multiple'=>'true')); ?>
	</td></tr></table>
	
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('lazy8','Return') ,array('name'=>'Return','title'=> Yii::t('lazy8','contexthelp.Return') )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
