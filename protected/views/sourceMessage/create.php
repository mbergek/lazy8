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
<h2><?php echo Yii::t('lazy8','Create language item'); ?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','Translate language'),array('admin')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Create new language'),array('createlang')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Import language'),array('importlang')); ?>]
</div>

<div class="yiiForm">

<p>
<?php echo Yii::t('lazy8','Fields with a red star are required') . ' <span class="required">*</span>';?>
</p>

<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model); ?>

<div class="simple">
<?php echo CHtml::activeLabelEx($model,'category',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.category'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'category',array('size'=>35,'maxlength'=>100)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'message',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.message'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'message',array('size'=>35,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::label('englishTrans',false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.englishTrans'),'onclick'=>'alert(this.title)'));  echo CHtml::textArea('englishTrans','',array('size'=>35,'maxlength'=>255,'style'=>'width:380px;height:50px')); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton( Yii::t('lazy8','Create'),array('title'=>Yii::t('lazy8','contexthelp.Create'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->