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
<div class="yiiForm">

<p>
<?php echo Yii::t('lazy8','Fields with a red star are required') . ' <span class="required">*</span>';?>
</p>

<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model);  if ( (isset($isAccountTypesInCompany) && ! $isAccountTypesInCompany) && ! $update){ 
	//You need several account types for your company. We recommend that you select the standard account types according to your nationality show on the buttons below.  Or you may create manually your own account types 
	
	?>
<div class="action">
<table><tr><td colspan="2">
<?php echo CHtml::encode(Yii::t('lazy8','You.need.several.account.types.for.your.company')); ?>
</td></tr><tr><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','English'),array('title'=>Yii::t('lazy8','contexthelp.EnglishAccountType'),'name'=>'American')); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','Swedish'),array('title'=>Yii::t('lazy8','contexthelp.SwedishAccountType'),'name'=>'Swedish')); ?>
</td><tr></table>	
</div>
	
<?php } ?>

<?php $cLoc=null;$dateformatter=null; ?>


<div class="simple">
<?php echo CHtml::activeLabelEx($model,'code',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.code'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'code'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'name',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.name'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'name',array('size'=>35,'maxlength'=>100)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'sortOrder'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'isInBalance',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isInBalance'),'onclick'=>'alert(this.title)'));  echo CHtml::activeCheckBox($model,'isInBalance'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'changedBy',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.changedBy'),'onclick'=>'alert(this.title)'));  echo CHtml::label($model->changedBy,false); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'dateChanged',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateChanged'),'onclick'=>'alert(this.title)'));  echo CHtml::label(User::getDateFormatted($model->dateChanged,$cLoc,$dateformatter),false); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Create'),array('title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Create'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->