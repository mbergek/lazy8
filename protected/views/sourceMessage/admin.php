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
<h2><?php echo Yii::t('lazy8','Managing SourceMessage'); ?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','Create new language'),array('createlang')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Import language'),array('importlang')); ?>]
[<?php echo CHtml::link(Yii::t('lazy8','Create language item'),array('createlangitem')); ?>]
</div>

<div class="yiiForm">


<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($models); ?>
<p>
<div class="simple">
<?php echo CHtml::label(Yii::t('lazy8','Language code'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.Language code'),'onclick'=>'alert(this.title)'));  
//this eval creats the $list array for the list box.
$phpcode=User::optionsUserTemplate();
eval($phpcode['languagecode'][4]);
echo CHtml::dropDownList('langcode', $language,
	CHtml::encodeArray($list),
	array('onchange'=>"this.form.submit()"));
echo CHtml::encode("Version=".$version);
?>
</div>
</p>


<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('category'); ?></th>
    <th><?php echo $sort->link('messagekey'); ?></th>
    <th><?php echo $sort->link('messagestandard'); ?></th>
    <th><?php echo $sort->link('translation'); ?></th>
  </tr>
  </thead>
  <tbody>
<?php foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::encode($model->source->category); ?></td>
    <td><?php echo CHtml::encode($model->source->message); ?></td>
    <td><?php if($model->standard!=null)echo CHtml::encode($model->standard->translation); ?></td>
    <td><?php echo CHtml::activeTextArea($model,"[$model->id]translation",array('style'=>'width:380px;height:50px')); ?></td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>




<p>
<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Save'),array('name'=>'Save','title'=>Yii::t('lazy8','contexthelp.Save')));  echo CHtml::submitButton(Yii::t('lazy8','Delete'),array('name'=>'Delete',
      	  'submit'=>'',
      	  'params'=>array('Delete'=>'delete','id'=>$language),
      	  'confirm'=>Yii::t('lazy8',"Are you sure to delete?") . ' - ' . $language,
      	  'title'=>Yii::t('lazy8','contexthelp.Delete'))); ?>

<?php echo CHtml::submitButton(Yii::t('lazy8','Export'),array('name'=>'Export','title'=>Yii::t('lazy8','contexthelp.Export'))); ?>

</div>
</p>
<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->








