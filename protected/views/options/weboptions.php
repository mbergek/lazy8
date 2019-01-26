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

<?php echo CHtml::beginForm(); ?>

<?php 
$calendar=array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'fodelsedatum',
			'ifFormat'=>User::getPhPDateFormat(),
                      ); 
echo CHtml::hiddenField('options_posted','true'); 
if(isset($weboptions)){
	$useroptions=$weboptions;
	$optionTemplate=User::optionsWebTemplate();
?>
	<table class="dataGrid" width="550">
<?php 
	foreach($useroptions as $useroption){
		if(isset($optionTemplate[$useroption->name])){
			if($optionTemplate[$useroption->name][3]=='false'){
?>
<tr><td>
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
<?php echo CHtml::submitButton(Yii::t('lazy8','Save'),array('title'=>Yii::t('lazy8','contexthelp.Save'))); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->