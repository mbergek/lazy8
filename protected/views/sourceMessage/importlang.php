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

<?php if(!$this->hasErrors && isset($_GET['importing'])){ ?>
<h2><?php echo Yii::t('lazy8','Successful import');?></h2>
<?php }else{ ?>
<h2><?php echo Yii::t('lazy8','Import a language')?></h2>


<div class="yiiForm">

<p>
<?php echo Yii::t('lazy8','Fields with a red star are required') . ' <span class="required">*</span>';?>
</p>

<?php echo CHtml::beginForm(CHtml::normalizeUrl(array('sourceMessage/importlang','importing'=>'true')),'post',array('enctype'=>'multipart/form-data'));  echo CHtml::errorSummary($this); ?>

<div class="simple">
<?php echo CHtml::label(Yii::t('lazy8',"Upload a valid lazy8web import file"),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.Upload a valid lazy8web import file'),'onclick'=>'alert(this.title)'));  echo CHtml::fileField('importfile');  echo CHtml::hiddenField('importnow','asdfasdf'); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Import')); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->
<?php } ?>
