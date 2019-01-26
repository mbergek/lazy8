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
<?php
$companies=CHtml::encodeArray(CHtml::listData(Company::model()->findAll(array('select'=>'id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code')),'id','name'));
$companies[0]=Yii::t('lazy8','-- '.'all'.' --');
?>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'companyId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.companyId'),'onclick'=>'alert(this.title)'));  echo CHtml::activeDropDownList($model,"companyId", $companies);
?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'name',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.name'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'name',array('size'=>35,'maxlength'=>100)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'desc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.desc'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'desc',array('size'=>35,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,"sortOrder",array('size'=>8,'maxlength'=>12)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'cssColorFileName',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.cssColorFileName'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'cssColorFileName',array('size'=>35,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'cssBwFileName',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.cssBwFileName'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($model,'cssBwFileName',array('size'=>35,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'selectSql',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.selectSql'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextArea($model,'selectSql',array('style'=>'width:380px;height:100px')); ?>
</div>
<?php if($update){ ?>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'changedBy',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.changedBy'),'onclick'=>'alert(this.title)'));  echo CHtml::label($model->changedBy,false); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'dateChanged',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateChanged'),'onclick'=>'alert(this.title)'));  echo CHtml::label(User::getDateFormatted($model->dateChanged,$cLoc,$dateformatter),false); ?>
</div>
<?php } ?>
<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Add Parameter'),array('name'=>'AddRow','title'=>Yii::t('lazy8','contexthelp.Add Parameter'))); ?>
</div>

<?php 
$repParams=$model->reportparameters;
//die();
if(isset($repParams)){
if(count($repParams)>0){
?>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'name',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.name'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'desc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.desc'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'alias',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.alias'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'dataType',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dataType'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'phpSecondaryInfo',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.phpSecondaryInfo'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'isDefaultPhp',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDefaultPhp'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'defaultValue',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.defaultValue'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'isDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDate'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'isDecimal',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDecimal'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repParams[0],'actions',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>
  </tr>
  </thead>
  <tbody>
<?php 
	foreach($repParams as $n=>$repParam): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
<td>
<?php echo CHtml::activeTextField($repParam,"[$n]name",array('size'=>8,'maxlength'=>100)); 
CHtml::activeHiddenField($repParam,"[$n]id");
CHtml::activeHiddenField($repParam,"[$n]reportId");?>
</td><td>
<?php echo CHtml::activeTextField($repParam,"[$n]desc",array('size'=>8,'maxlength'=>255)); ?>
</td><td>
<?php echo CHtml::activeTextField($repParam,"[$n]alias",array('size'=>8,'maxlength'=>100)); ?>
</td><td>
<?php echo CHtml::activeTextField($repParam,"[$n]sortOrder",array('size'=>8,'maxlength'=>12)); ?>
</td><td>
<?php echo CHtml::activeDropDownList($repParam,"[$n]dataType",array(
	'FREE_TEXT'=>Yii::t('lazy8','FREE_TEXT'),
	'DROP_DOWN'=>Yii::t('lazy8','DROP_DOWN'),
	'DATE'=>Yii::t('lazy8','DATE'),
	'BOOLEAN'=>Yii::t('lazy8','BOOLEAN'),
	'HIDDEN_SHOW_HEAD'=>Yii::t('lazy8','HIDDEN_SHOW_HEAD'),
	'HIDDEN_NO_SHOW_HEAD'=>Yii::t('lazy8','HIDDEN_NO_SHOW_HEAD'),
	),array('style'=>'width:130px;')); ?>
</td><td>
<?php echo CHtml::activeTextField($repParam,"[$n]phpSecondaryInfo",array('size'=>8)); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repParam,"[$n]isDefaultPhp"); ?>
</td><td>
<?php echo CHtml::activeTextField($repParam,"[$n]defaultValue",array('size'=>8)); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repParam,"[$n]isDate"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repParam,"[$n]isDecimal"); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','delete'),array('name'=>"deleterow[$n]",'title'=>Yii::t('lazy8','contexthelp.delete'))); ?>
</td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>

<?php }} ?>

<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Add Report Row'),array('name'=>"AddReportRow",'title'=>Yii::t('lazy8','contexthelp.Add Report Row'))); ?>
</div>
<?php 
$repRows=$model->rows;
//die();
if(isset($repRows)){
if(count($repRows)>0){
?>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'fieldName',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fieldName'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'fieldCalc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fieldCalc'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'fieldWidth',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fieldWidth'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'row',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.row'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'isSummed',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isSummed'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'isAlignRight',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isAlignRight'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'isDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDate'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'isDecimal',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDecimal'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repRows[0],'actions',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>
  </tr>
  </thead>
  <tbody>
<?php 
foreach($repRows as $n=>$repRow){ ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
<td>
<?php echo CHtml::activeTextField($repRow,"[$n]sortOrder",array('size'=>8,'maxlength'=>12)); 
CHtml::activeHiddenField($repRow,"id[$n]");
CHtml::activeHiddenField($repRow,"reportId[$n]"); ?>
</td><td>
<?php echo CHtml::activeTextField($repRow,"[$n]fieldName",array('size'=>8,'maxlength'=>100)); ?>
</td><td>
<?php echo CHtml::activeTextField($repRow,"[$n]fieldCalc",array('size'=>8)); ?>
</td><td>
<?php echo CHtml::activeTextField($repRow,"[$n]fieldWidth",array('size'=>8,'maxlength'=>10)); ?>
</td><td>
<?php echo CHtml::activeTextField($repRow,"[$n]row",array('size'=>8,'maxlength'=>12)); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repRow,"[$n]isSummed"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repRow,"[$n]isAlignRight"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repRow,"[$n]isDate"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repRow,"[$n]isDecimal"); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','delete'),array('name'=>"deletereportrow[$n]",'title'=>Yii::t('lazy8','contexthelp.delete'))); ?>
</td>
  </tr>
<?php } ?>
  </tbody>
</table>

<?php }} ?>

<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Add Group'),array('name'=>'AddGroupRow','title'=>Yii::t('lazy8','contexthelp.Add Group'))); ?>
</div>

<?php 
$repGroups=$model->groups;
//die();
if(isset($repGroups)){
if(count($repGroups)>0){
?>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'breakingField',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.breakingField'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'pageBreak',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.pageBreak'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'showGrid',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.showGrid'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'showHeader',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.showHeader'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'continueSumsOverGroup',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.continueSumsOverGroup'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroups[0],'actions',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>
  </tr>
  </thead>
  <tbody>
<?php 
	foreach($repGroups as $n=>$repGroup): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
<td>
<?php echo CHtml::activeTextField($repGroup,"[$n]sortOrder",array('size'=>8,'maxlength'=>100)); 
CHtml::activeHiddenField($repGroup,"[$n]id");
CHtml::activeHiddenField($repGroup,"[$n]reportId");?>
</td><td>
<?php echo CHtml::activeTextField($repGroup,"[$n]breakingField",array('size'=>8,'maxlength'=>100)); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repGroup,"[$n]pageBreak"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repGroup,"[$n]showGrid"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repGroup,"[$n]showHeader"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repGroup,"[$n]continueSumsOverGroup"); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','delete'),array('name'=>"deletegrouprow[$n]",'title'=>Yii::t('lazy8','contexthelp.delete'))); ?>
</td>
  </tr>
  <tr class="<?php echo $n%2?'even':'odd';?>">
  <td colspan="9" style="padding:0  0  20px 100px;">

<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Add Group Field'),array('name'=>"AddGroupFieldRow[$n]",'title'=>Yii::t('lazy8','contexthelp.Add Group Field'))); ?>
</div>
<?php 
$repGroupFields=$repGroup->fields;
//die();
if(isset($repGroupFields)){
if(count($repGroupFields)>0){
?>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'fieldName',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fieldName'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'fieldCalc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fieldCalc'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'fieldWidth',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fieldWidth'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'row',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.row'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'isDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDate'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'isDecimal',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDecimal'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($repGroupFields[0],'actions',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>
  </tr>
  </thead>
  <tbody>
<?php 
foreach($repGroupFields as $m=>$repGroupField){ ?>
  <tr class="<?php echo $m%2?'even':'odd';?>">
<td>
<?php echo CHtml::activeTextField($repGroupField,"[$n][$m]sortOrder",array('size'=>8,'maxlength'=>12)); 
CHtml::activeHiddenField($repGroupField,"[$n][$m]id");
CHtml::hiddenField("ReportGroupFields[$n][reportGroupId]$m");?>
</td><td>
<?php echo CHtml::activeTextField($repGroupField,"[$n][$m]fieldName",array('size'=>8,'maxlength'=>100)); ?>
</td><td>
<?php echo CHtml::activeTextField($repGroupField,"[$n][$m]fieldCalc",array('size'=>8)); ?>
</td><td>
<?php echo CHtml::activeTextField($repGroupField,"[$n][$m]fieldWidth",array('size'=>8,'maxlength'=>10)); ?>
</td><td>
<?php echo CHtml::activeTextField($repGroupField,"[$n][$m]row",array('size'=>8,'maxlength'=>12)); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repGroupField,"[$n][$m]isDate"); ?>
</td><td>
<?php echo CHtml::activeCheckBox($repGroupField,"[$n][$m]isDecimal"); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','delete'),array('name'=>"deletegroupfieldrow[$n][$m]",'title'=>Yii::t('lazy8','contexthelp.delete'))); ?>
</td>
  </tr>
<?php } ?>
  </tbody>
</table>
<?php }} ?>
  
</td></tr>  
  
<?php endforeach; ?>
  </tbody>
</table>

<?php }} ?>
<div class="action">
<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Add') ,array('name'=>$update ? 'Save' : 'Add','title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Add')));  if($update)echo CHtml::submitButton(Yii::t('lazy8','Export') ,array('name'=>'Export','title'=>Yii::t('lazy8','contexthelp.Export'))); ?>
</div>





<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->