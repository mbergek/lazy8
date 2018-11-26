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

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'template-form',
	'enableAjaxValidation'=>false,
)); ?>

<p>
<?php echo Yii::t('lazy8','Fields with a red star are required') . ' <span class="required">*</span>';?>
</p>

	<?php echo $form->errorSummary($model); 
	$templateRows=$model->templateRows;
	foreach($templateRows as $n=>$repParam) echo $form->errorSummary($repParam);?>


	<?php $cLoc=null;$dateformatter=null; ?>
	<div class="simple">
		<?php echo CHtml::activeLabelEx($model,'name',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.name'),'onclick'=>'alert(this.title)'));  
		echo CHtml::activeTextField($model,'name',array('size'=>35,'maxlength'=>100)); 
		echo $form->error($model,'name'); 
		?>
	</div>
	<div class="simple">
		<?php echo CHtml::activeLabelEx($model,'desc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.desc'),'onclick'=>'alert(this.title)'));  
		echo CHtml::activeTextField($model,'desc',array('size'=>35,'maxlength'=>255)); 
		echo $form->error($model,'desc'); 
		?>
	</div>
	<div class="simple">
		<?php echo CHtml::activeLabelEx($model,'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)'));  
		echo CHtml::activeTextField($model,"sortOrder",array('size'=>8,'maxlength'=>12)); 
		echo $form->error($model,'sortOrder'); 
		?>
	</div>
	
	<div class="simple">
		<?php echo $form->labelEx($model,'allowAccountingView',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowAccountingView'),'onclick'=>'alert(this.title)')); ?>
		<?php echo $form->checkBox($model,'allowAccountingView'); ?>
		<?php echo $form->error($model,'allowAccountingView'); ?>
	</div>

	<div class="simple">
		<?php echo $form->labelEx($model,'allowFreeTextField',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowFreeTextField'),'onclick'=>'alert(this.title)')); ?>
		<?php echo $form->checkBox($model,'allowFreeTextField'); ?>
		<?php echo $form->error($model,'allowFreeTextField'); ?>
	</div>

	<div class="simple">
		<?php echo $form->labelEx($model,'freeTextFieldDefault',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.freeTextFieldDefault'),'onclick'=>'alert(this.title)')); ?>
		<?php echo $form->textField($model,'freeTextFieldDefault',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'freeTextFieldDefault',array('size'=>35,'maxlength'=>100)); ?>
	</div>

	<div class="simple">
		<?php echo $form->labelEx($model,'allowFilingTextField',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowFilingTextField'),'onclick'=>'alert(this.title)')); ?>
		<?php echo $form->checkBox($model,'allowFilingTextField'); ?>
		<?php echo $form->error($model,'allowFilingTextField'); ?>
	</div>

	<div class="simple">
		<?php echo $form->labelEx($model,'filingTextFieldDefault',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.filingTextFieldDefault'),'onclick'=>'alert(this.title)')); ?>
		<?php echo $form->textField($model,'filingTextFieldDefault',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'filingTextFieldDefault'); ?>
	</div>

	<div class="simple">
		<?php echo $form->labelEx($model,'forceDateToday',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.forceDateToday'),'onclick'=>'alert(this.title)')); ?>
		<?php echo $form->checkBox($model,'forceDateToday'); ?>
		<?php echo $form->error($model,'forceDateToday'); ?>
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
	<?php echo CHtml::submitButton(Yii::t('lazy8','Add Row'),array('name'=>'AddRow','title'=>Yii::t('lazy8','contexthelp.Templates.Add Row'))); ?>
	</div>

	<?php 
	
	//die();
	if(isset($templateRows)){
		if(count($templateRows)>0){
		?>
		
		<table class="dataGrid">
		  <thead>
		  <tr>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'name',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.templates.row.name'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'desc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.template.desc'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'sortOrder',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.sortOrder'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'isDebit',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isDebit'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'allowMinus',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowMinus'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'allowChangeValue',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowChangeValue'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'allowRepeatThisRow',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowRepeatThisRow'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'allowCustomer',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowCustomer'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'allowNotes',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.allowNotes'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'isFinalBalance',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.isFinalBalance'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'defaultAccountId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.template.defaultAccountId'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'defaultValue',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.template.defaultValue'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::activeLabelEx($templateRows[0],'phpFieldCalc',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.phpFieldCalc'),'onclick'=>'alert(this.title)')); ?></th>
		    <th><?php echo CHtml::label(Yii::t('lazy8','Actions'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>
		  </tr>
		  </thead>
		  <tbody>
		<?php 
			foreach($templateRows as $n=>$repParam): ?>
		  <tr class="<?php echo $n%2?'even':'odd';?>">
		<td>
		<?php echo CHtml::activeTextField($repParam,"[$n]name",array('size'=>8,'maxlength'=>100)); 
		CHtml::activeHiddenField($repParam,"[$n]id");
		CHtml::activeHiddenField($repParam,"[$n]templateId");?>
		</td><td>
		<?php echo CHtml::activeTextField($repParam,"[$n]desc",array('size'=>8,'maxlength'=>255)); ?>
		</td><td>
		<?php echo CHtml::activeTextField($repParam,"[$n]sortOrder",array('size'=>8,'maxlength'=>12)); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]isDebit"); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]allowMinus"); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]allowChangeValue"); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]allowRepeatThisRow"); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]allowCustomer"); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]allowNotes"); ?>
		</td><td>
		<?php echo CHtml::activeCheckBox($repParam,"[$n]isFinalBalance"); ?>
		</td><td>
			<?php 
			$accounts=CHtml::encodeArray(CHtml::listData(Account::model()->findAll(array('join'=>' LEFT JOIN TemplateRowAccount ON t.id=TemplateRowAccount.accountId','select'=>'t.id as id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code','condition'=>'templateRowId='.$repParam->id)),'id','name'));
			$accounts[0]='-- '.' '.' --';
			echo CHtml::activeDropDownList($repParam,"[$n]defaultAccountId", $accounts,array('title'=>Yii::t('lazy8','contexthelp.template.defaultAccountId')));
			echo CHtml::submitButton(Yii::t('lazy8','edit'),array('name'=>"editrow[$n]",'title'=>Yii::t('lazy8','contexthelp.template.edit'))); 
			?>
		</td><td>
		<?php echo CHtml::activeTextField($repParam,"[$n]defaultValue",array('size'=>8)); ?>
		</td><td>
		<?php echo CHtml::activeTextField($repParam,"[$n]phpFieldCalc",array('size'=>8)); ?>
		</td><td>
		<?php echo CHtml::submitButton(Yii::t('lazy8','Remove Row'),array('name'=>"deleterow[$n]",'title'=>Yii::t('lazy8','contexthelp.Remove Row'))); ?>
		</td>
		  </tr>
		<?php endforeach; ?>
		  </tbody>
		</table>
	
	<?php }} ?>






	<div class="row buttons">
		<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Create'),array('name'=>'save','title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Create'))); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->