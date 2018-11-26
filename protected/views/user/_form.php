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

<div class="simple">
<?php echo CHtml::activeLabelEx($model,'username',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.username'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'username',array('size'=>35,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'password',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.password'),'onclick'=>'alert(this.title)'));  echo CHtml::activePasswordField($model,'password',array('size'=>35,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'confirmPassword',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.confirmPassword'),'onclick'=>'alert(this.title)'));  echo CHtml::activePasswordField($model,'confirmPassword',array('size'=>35,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'displayname',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.displayname'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'displayname',array('size'=>35,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'mobil',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.mobil'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'mobil',array('size'=>35,'maxlength'=>50)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'email',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.email'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'email',array('size'=>35,'maxlength'=>100)); ?>
</div>
<?php if(count($allCompanies)>0) { ?>
<div class="simple">
<?php echo CHtml::label(Yii::t('lazy8','Select the companies this user may work with'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.Select the companies this user may work with'),'onclick'=>'alert(this.title)')); echo CHtml::listBox('companies', $selectedCompanies,CHtml::listData($allCompanies,'id','name'),array('multiple'=>'yes')); ?>
</div>
<div class="simple">
<?php echo CHtml::label(Yii::t('lazy8','Select a template for the options if desired'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.Select a template for the options if desired'),'onclick'=>'alert(this.title)')) 
.CHtml::submitButton( Yii::t('lazy8','Administrator'),array('name'=>'Admin','title'=>Yii::t('lazy8','contexthelp.Administrator')))
.CHtml::submitButton( Yii::t('lazy8','Editing User'),array('name'=>'Editor','title'=>Yii::t('lazy8','contexthelp.Editing User')))
.CHtml::submitButton( Yii::t('lazy8','Reports only User'),array('name'=>'Viewer','title'=>Yii::t('lazy8','contexthelp.Reports only User'))); ?>
</div>

<?php } ?>
<?php 
$calendar=array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'fodelsedatum',
			'ifFormat'=>User::getPhPDateFormat(),
                      ); 
if(isset($model->useroptions)){
	$useroptions=$model->useroptions;
	$optionTemplate=User::optionsUserTemplate();
?>

<div class="clear" style="clear: both;"></div> 
	<table class="dataGrid" width="550">
<?php 
	foreach($useroptions as $n=>$useroption){
		if(isset($optionTemplate[$useroption->name])){
			if($optionTemplate[$useroption->name][3]=='false' && ($optionTemplate[$useroption->name][5]=='false' || Yii::app()->user->getState('allowAdmin'))){
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



<?php

if(isset($weboptions) && isset($companies)){

?>
<div class="simple">
<br />
<?php echo CHtml::label(Yii::t('lazy8','Company options'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.Company options'),'onclick'=>'alert(this.title)'));  
      echo CHtml::hiddenField('companyForOptionsBeforeChange',$companyForOptions);  
      echo CHtml::dropDownList('companyForOptions',$companyForOptions,$companies,array('onchange'=>'form.submit();')); ?>
<br />
<br />
</div>
	
	
	
<?php 	
	$useroptions=$weboptions;
	$optionTemplate=User::optionsCompanyUserTemplate();
?>
	<table class="dataGrid" width="550">
<?php 
	foreach($useroptions as $n=>$useroption){
		if(isset($optionTemplate[$useroption->name])){
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
?>
</table>







<div class="action">
<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Create'),array('name'=>'save','title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Create'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->