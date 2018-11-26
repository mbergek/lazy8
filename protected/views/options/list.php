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
<h2>Options List</h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','New Options'),array('create')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Manage Options'),array('admin')); ?>]
</div>

<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<?php foreach($models as $n=>$model): ?>
<div class="item">
<?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:
<?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('optionsTemplateId')); ?>:
<?php echo CHtml::encode($model->optionsTemplateId); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('datavalue')); ?>:
<?php echo CHtml::encode($model->datavalue); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('companyId')); ?>:
<?php echo CHtml::encode($model->companyId); ?>
<br/>
<?php echo CHtml::encode($model->getAttributelabel(Yii::t("lazy8",'changedBy'))); ?>:
<?php echo CHtml::encode($model->changedBy); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('userChangedId')); ?>:
<?php echo CHtml::encode($model->userChangedId); ?>
<br/>
<?php echo CHtml::encode($model->getAttributelabel(Yii::t("lazy8",'dateChanged'))); ?>:
<?php echo CHtml::encode(User::getDateFormatted($model->dateChanged)); ?>
<br/>

</div>
<?php endforeach; ?>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>