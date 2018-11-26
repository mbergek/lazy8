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

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','New Template'),array('create')); ?>]
<?php if(true){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Manage Templates'),array('admin')); ?>]
<?php }  if(Yii::app()->user->getState('allowExportAll')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Export all'),array('exportall')); ?>]
<?php } ?>
</div>


<?php if(!$this->hasErrors && isset($_GET['importing'])){ ?>
<h2><?php echo Yii::t('lazy8','Successful import');?></h2>
<?php }else{ ?>
<h2><?php echo Yii::t('lazy8','Import a template')?></h2>


<div class="yiiForm">

<p>
<?php echo Yii::t('lazy8','Fields with a red star are required') . ' <span class="required">*</span>';?>
</p>

<?php echo CHtml::beginForm(CHtml::normalizeUrl(array('import','importing'=>'true')),'post',array('enctype'=>'multipart/form-data'));  echo CHtml::errorSummary($this); ?>

<div class="simple">
<?php echo CHtml::label(Yii::t('lazy8',"Upload a valid lazy8web import file"),false);  echo CHtml::fileField('importfile');  echo CHtml::hiddenField('importnow','asdfasdf'); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Import'),array('title'=>Yii::t('lazy8','contexthelp.Import'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->
<?php } ?>
