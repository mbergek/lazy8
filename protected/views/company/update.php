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
<h2><?php echo Yii::t('lazy8','Update Company') . ' - ' .$model->name; ?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','New Company'),array('create')); ?>]
<?php if(Yii::app()->user->getState('allowCompanyCreation')||Yii::app()->user->getState('allowCompanyCreation')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Manage Company'),array('admin')); ?>]
<?php }  if(Yii::app()->user->getState('allowImport')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Import'),array('import')); ?>]
<?php }  if(Yii::app()->user->getState('allowExportAll')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Export all companies'),array('exportAll')); ?>]
<?php }  if(Yii::app()->user->getState('allowCompanyExport')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Export this company'),array('export&id='.$model->id)); ?>]
<?php } ?>
</div>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'update'=>true,
	'options'=>$options,
)); ?>