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
<?php $cLoc=null;$dateformatter=null; ?>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'dateStart',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateStart'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'dateStart'); 
$this->widget('application.extensions.calendar.SCalendar',array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'Period_dateStart',
			'ifFormat'=>User::getPhPDateFormat(),
                      )); 
?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'dateEnd',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateEnd'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'dateEnd'); 
$this->widget('application.extensions.calendar.SCalendar',array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'Period_dateEnd',
			'ifFormat'=>User::getPhPDateFormat(),
                      )); 
?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'lastPeriodTransNum',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.lastPeriodTransNum'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'lastPeriodTransNum'); ?>
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