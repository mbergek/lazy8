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
<h2><?php echo Yii::t('lazy8','Update Transaction') . ' - ' .$models[0]->periodNum . ' ; ' . $models[0]->companyNum . ' ; ' . $models[0]->invDate;?></h2>

<div class="actionBar">
[<?php echo CHtml::link(Yii::t('lazy8','Select template'),array('selecttemplate')); ?>]
[<?php echo CHtml::label(Yii::t('lazy8','How to write this quickly'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.How to write this quickly'),'onclick'=>'alert(this.title)'));?>]
<?php if($previous!=null)echo '[' . CHtml::link(Yii::t('lazy8','&lt; Previous'),array('update','id'=>$previous)) . ']'; ?>
<?php if($next!=null)echo '[' . CHtml::link(Yii::t('lazy8','Next &gt;'),array('update','id'=>$next)) . ']'; ?>
</div>

<?php echo $this->renderPartial('copyoftrans_form', array(
	'models'=>$models,
	'Locale'=>$Locale,
	'numberFormatter'=>$numberFormatter,
	'numberFormat'=>$numberFormat,
	'update'=>true,
	'isMultipleRows'=>$isMultipleRows,
	'numTemplateRows'=>count($template->templateRows),
)); ?>