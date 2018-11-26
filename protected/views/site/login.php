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
 $this->pageTitle=Yii::app()->name . ' - Login'; ?>

<h1><?php echo Yii::t('lazy8','Login'); ?></h1>

<div class="yiiForm">
<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($form); ?>

<div class="simple">
<?php echo CHtml::activeLabel($form,'username',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.username'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($form,'username') ?>
</div>

<div class="simple">
<?php echo CHtml::activeLabel($form,'password',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.password'),'onclick'=>'alert(this.title)'));  echo CHtml::activePasswordField($form,'password') ?>

</div>

<div class="action">
<?php echo CHtml::activeCheckBox($form,'rememberMe',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.rememberMe')));  echo CHtml::activeLabel($form,'rememberMe'); ?>
<br/>
<?php echo CHtml::submitButton(Yii::t('lazy8','Login'),array('title'=>Yii::t('lazy8','contexthelp.Login'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->