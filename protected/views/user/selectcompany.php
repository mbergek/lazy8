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
<h2><?php echo Yii::t('lazy8','Select company/period') . ' - ' .$usersModel->displayname; ?></h2>

<div class="actionBar">
<?php if(Yii::app()->user->getState('allowAdmin')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','New User'),array('create')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Manage User'),array('admin')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Add/remove Companies to/from user'),array('addcompanies','id'=>$id)); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','User'),array('update','id'=>$id)); ?>]
<?php } ?>
</div>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Code')); ?></th>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Name')); ?></th>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Period')); ?></th>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Actions')); ?></th>

  </tr>
  </thead>
  <tbody>
<?php foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::encode($model->code); ?></td>
    <td><?php echo CHtml::encode($model->name); ?></td><td></td><td>
    </td></tr>
<?php 
if(isset($model->periods)){
	$periods=$model->periods;
	foreach($periods as $period): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td></td><td></td><td><?php echo CHtml::encode($period->dateStart. ' - ' . $period->dateEnd); ?></td>
    <td>
<?php 
if($usersModel->selectedCompanyId==$model->id && $usersModel->selectedPeriodId==$period->id){
      echo CHtml::label(Yii::t('lazy8','Selected'),'false'); 
}else{
      echo CHtml::link(Yii::t('lazy8','Select'),array('selectcompany','id'=>$id,'periodId'=>$period->id,'companyId'=>$model->id,'select'=>'true')); 
}
?>
	</td>
  </tr>
<?php endforeach;}  endforeach; ?>
  </tbody>
</table>
<br/>