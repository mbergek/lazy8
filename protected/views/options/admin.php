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
<h2><?php echo Yii::t('lazy8','Managing Options'); ?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','Options List'),array('list')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','New Options'),array('create')); ?>]
</div>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('id'); ?></th>
    <th><?php echo $sort->link('optionsTemplateId'); ?></th>
    <th><?php echo $sort->link('datavalue'); ?></th>
    <th><?php echo $sort->link('companyId'); ?></th>
    <th><?php echo $sort->link('userId'); ?></th>
    <th><?php echo $sort->link('userChangedId'); ?></th>
    <th><?php echo $sort->link('dateChanged'); ?></th>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Actions')); ?></th>

  </tr>
  </thead>
  <tbody>
<?php	$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode')); 
	$dateformatter=new CDateFormatter($cLoc);   foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?></td>
    <td><?php echo CHtml::encode($model->optionsTemplateId); ?></td>
    <td><?php echo CHtml::encode($model->datavalue); ?></td>
    <td><?php echo CHtml::encode($model->companyId); ?></td>
    <td><?php echo CHtml::encode($model->userId); ?></td>
    <td><?php echo CHtml::encode($model->userChangedId); ?></td>
    <td><?php echo CHtml::encode(User::getDateFormatted($model->dateChanged,$cLoc,$dateformatter)); ?></td>
    <td>
      <?php echo CHtml::link(Yii::t('lazy8','Update'),array('update','id'=>$model->id)); ?>
      <?php echo CHtml::linkButton(Yii::t('lazy8','Delete'),array(
      	  'submit'=>'',
      	  'params'=>array('command'=>'delete','id'=>$model->id),
      	  'confirm'=>Yii::t('lazy8',"Are you sure to delete?") . ' - ' . $model->id)); ?>
	</td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>