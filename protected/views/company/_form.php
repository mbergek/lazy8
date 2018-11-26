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
<div class="yiiForm">

<p>
<?php echo Yii::t('lazy8','Fields with a red star are required') . ' <span class="required">*</span>';?>
</p>

<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model); ?>
<?php $cLoc=null;$dateformatter=null; ?>
<div class="simple">
<?php echo CHtml::activeHiddenField($model,'id');  echo CHtml::activeLabelEx($model,'code',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.code'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'code'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'name',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.name'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'name',array('size'=>35,'maxlength'=>100)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'lastAbsTransNum',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.lastAbsTransNum'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'lastAbsTransNum'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'changedBy',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.changedBy'),'onclick'=>'alert(this.title)'));  echo CHtml::label($model->changedBy,false); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'dateChanged',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateChanged'),'onclick'=>'alert(this.title)'));  echo CHtml::label(User::getDateFormatted($model->dateChanged,$cLoc,$dateformatter),false); ?>
</div>


<?php 
if(Yii::app()->user->getState('allowAdmin') ){
$calendar=array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'fodelsedatum',
			'ifFormat'=>User::getPhPDateFormat(),
                      ); 
if(isset($options)){
	$companyoptions=$options;
	$optionTemplate=User::optionsCompanyTemplate();
?>

<br />
<br />

<?php echo CHtml::label(Yii::t('lazy8','Company options'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.Company options'),'onclick'=>'alert(this.title)')); ?> 
<br />


	<table class="dataGrid" width="550">
<?php 
	foreach($companyoptions as $n=>$useroption){
		if($optionTemplate[$useroption->name][3]=='false'){
?>
<tr class="<?php echo $n%2?'even':'odd';?>"><td>
<!-- <div class="simple"> -->
<?php 
		echo CHtml::label(yii::t('lazy8','option.name.' . $useroption->name),'false',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.option.'. $useroption->name),'onclick'=>'alert(this.title)'));
?>
</td><td>
<?php 
		switch($optionTemplate[$useroption->name][0]){
		case 'DROP_DOWN_LIST':
			//this eval creats the $list array for the list box.
			eval($optionTemplate[$useroption->name][4]);
			echo CHtml::dropDownList('option_' . $useroption->name, $useroption->datavalue,$list);
			break;
		case 'STRING':
		case 'INTEGER':
		case 'FLOAT':
			echo CHtml::textField('option_' . $useroption->name,$useroption->datavalue,array('size'=>'10')); 
			break;
		case 'DATE':
			echo CHtml::textField('option_' . $useroption->name,$useroption->datavalue);
			$calendar['inputField']='utbildningsstart';
			$this->widget('application.extensions.calendar.SCalendar',$calendar);
			break;
		case 'BOOLEAN':
			echo CHtml::checkBox('option_' . $useroption->name,$useroption->datavalue=='true');
			break;
		}
?>
</td></tr>
<!-- </div> -->

<?php 
		}
	}
}
}
?>
</table>


<div class="action">
<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Create'),array('title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Create'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->