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

<?php echo CHtml::errorSummary($model); ?>

<div class="simple">
<?php echo CHtml::activeLabelEx($model,'optionsTemplateId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.optionsTemplateId'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'optionsTemplateId',array('size'=>35,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'datavalue',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.datavalue'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'datavalue',array('size'=>35,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'companyId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.companyId'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'companyId'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'changedBy',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.changedBy'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'changedBy'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'userChangedId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.userChangedId'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'userChangedId'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'dateChanged',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateChanged'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'dateChanged'); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Create'),array('title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Create'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->