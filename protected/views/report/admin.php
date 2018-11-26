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
<h2><?php echo Yii::t('lazy8','Managing Report'); ?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','New Report'),array('create')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Export all'),array('exportall')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Import'),array('import')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Re-install standard reports'),array('reload'),array(
      	  'onclick'=>'return confirm(\'' . Yii::t('lazy8',"Are you sure? This will delete all current standard reports.") . '\')')); ?>]
</div>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('companyId'); ?></th>
    <th><?php echo $sort->link('name'); ?></th>
    <th><?php echo $sort->link('desc'); ?></th>
    <th><?php echo $sort->link('sortOrder'); ?></th>
    <th><?php echo $sort->link('cssColorFileName'); ?></th>
    <th><?php echo $sort->link('cssBwFileName'); ?></th>
    <th><?php echo $sort->link('changedBy'); ?></th>
    <th><?php echo $sort->link('dateChanged'); ?></th>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Actions')); ?></th>

  </tr>
  </thead>
  <tbody>
<?php	$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode')); 
	$dateformatter=new CDateFormatter($cLoc);   foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php 
    if(isset($model->company)){
    	    echo CHtml::encode($model->company->code . ' ' . $model->company->name);
    }else{
    	    echo CHtml::encode(Yii::t('lazy8','-- '.'all'.' --'));
    }
 ?></td>
    <td><?php echo CHtml::encode($model->name); ?></td>
    <td><?php echo CHtml::encode($model->desc); ?></td>
    <td><?php echo CHtml::encode($model->sortOrder); ?></td>
    <td><?php echo CHtml::encode($model->cssColorFileName); ?></td>
    <td><?php echo CHtml::encode($model->cssBwFileName); ?></td>
    <td><?php echo CHtml::encode($model->changedBy); ?></td>
    <td><?php echo CHtml::encode(User::getDateFormatted($model->dateChanged,$cLoc,$dateformatter)); ?></td>
    <td>
      <?php echo CHtml::link(Yii::t('lazy8','Update'),array('update','id'=>$model->id)); ?>
      <?php echo CHtml::linkButton(Yii::t('lazy8','Delete'),array(
      	  'submit'=>'',
      	  'params'=>array('command'=>'delete','id'=>$model->id),
      	  'confirm'=>Yii::t('lazy8',"Are you sure to delete?") . ' - ' . $model->name)); ?>
	</td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>