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
<h2><?php echo Yii::t('lazy8','Managing Trans'); ?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','New Trans'),array('create')); ?>]
<?php if($existsOldTrans)echo '['.CHtml::link(Yii::t('lazy8','Back to old transaction'),array('restore')).']';  if(Yii::app()->user->getState('allowPeriodSelection')){ ?>
[<?php echo CHtml::link(Yii::t('lazy8','Select company/period'),array('user/selectcompany','id'=>Yii::app()->user->id)); ?>]
<?php } ?>
</div>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('regDate'); ?></th>
    <th><?php echo $sort->link('invDate'); ?></th>
    <?php if(Yii::app()->user->getState('showPeriodTransactionNumber')){ ?>
    	    <th><?php echo $sort->link('periodNum'); ?></th>
    <?php }else{ ?>
    	    <th><?php echo $sort->link('companyNum'); ?></th>
    <?php } ?>
    <th><?php echo $sort->link('notes'); ?></th>
    <th><?php echo $sort->link('fileInfo'); ?></th>
    <th><?php echo $sort->link('changedBy'); ?></th>
    <th><?php echo $sort->link('dateChanged'); ?></th>
    <th><?php echo CHtml::encode(Yii::t('lazy8','Actions')); ?></th>

  </tr>
  </thead>
  <tbody>
<?php	$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode')); 
	$dateformatter=new CDateFormatter($cLoc);   foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::encode(User::getDateFormatted($model->regDate,$cLoc,$dateformatter)); ?></td>
    <td><?php echo CHtml::encode(User::getDateFormatted($model->invDate,$cLoc,$dateformatter)); ?></td>
    <?php if(Yii::app()->user->getState('showPeriodTransactionNumber')){ ?>
    	    <td><?php echo CHtml::encode($model->periodNum); ?></td>
    <?php }else{ ?>
    	    <td><?php echo CHtml::encode($model->companyNum); ?></td>
    <?php } ?>
    <td><?php echo CHtml::encode($model->notes); ?></td>
    <td><?php echo CHtml::encode($model->fileInfo); ?></td>
    <td><?php echo CHtml::encode($model->changedBy); ?></td>
    <td><?php echo CHtml::encode(User::getDateFormatted($model->dateChanged,$cLoc,$dateformatter)); ?></td>
    <td>
    <?php if(Yii::app()->user->getState('allowReEditingOfTransactions')){ ?>
      <?php echo CHtml::link(Yii::t('lazy8','Update'),array('update','id'=>$model->id)); ?>
      <?php echo CHtml::linkButton(Yii::t('lazy8','Delete'),array(
      	  'submit'=>'',
      	  'params'=>array('command'=>'delete','id'=>$model->id),
      	  'confirm'=>Yii::t('lazy8',"Are you sure to delete?") . ' - ' . $model->periodNum)); ?>
	</td>
    <?php }else{ ?>
      <?php echo CHtml::link(Yii::t('lazy8','View'),array('update','id'=>$model->id)); ?>
    <?php } ?>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>