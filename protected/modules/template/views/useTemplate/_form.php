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

<?php if (isset($_GET['saved'])) { ?>
	<div class="infoSummary"><p><?php echo Yii::t('lazy8','Saved the transaction').' ; ' . $_GET['saved']; ?></p></div>	
<?php }  if (isset($_GET['added'])) { ?>
	<div class="infoSummary"><p><?php echo Yii::t('lazy8','Added the transaction').' ; ' . $_GET['added']; ?></p></div>	
<?php }  
	echo CHtml::errorSummary($models); 
?>
<table><tr><td>
<div class="simple">
<?php echo CHtml::activeLabelEx($models[0],'[0]changedBy',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.changedBy'),'onclick'=>'alert(this.title)'));  echo CHtml::label($models[0]->changedBy,false); ?>
</div>
</td><td>
<div class="simple">
<?php echo CHtml::activeLabelEx($models[0],'[0]dateChanged',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.dateChanged'),'onclick'=>'alert(this.title)'));  echo CHtml::label($models[0]->dateChanged,false); ?>
</div>
</td></tr><tr><td>
<div class="simple">
<?php echo CHtml::activeLabelEx($models[0],'[0]regDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.regDate'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]regDate',array('disabled'=>'true','size'=>15)); ?>
</div>
</td><td>
<div class="simple">
<?php 
if( ! $template->forceDateToday){
	echo CHtml::activeLabelEx($models[0],'[0]invDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.invDate'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]invDate',array('size'=>15)); 
	$this->widget('application.extensions.calendar.SCalendar',array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'TempTrans_0_invDate',
			'ifFormat'=>User::getPhPDateFormat(),
			'range'=>"[" . date('Y',strtotime(Yii::app()->user->getState('selectedPeriodStart'))) . "," . date('Y',strtotime(Yii::app()->user->getState('selectedPeriodEnd'))) . "]"
                      ));
}
else
{
	echo CHtml::activeLabelEx($models[0],'[0]invDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.invDate'),'onclick'=>'alert(this.title)'));  echo CHtml::label($models[0]->invDate,false); 
}
?>
</div>
</td></tr><tr><td>
    <?php if(Yii::app()->user->getState('showPeriodTransactionNumber')){ ?>
<div class="simple">
<?php echo CHtml::activeLabelEx($models[0],'[0]periodNum',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.periodNum'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]periodNum',array('disabled'=>'true','size'=>15)); ?>
</div>
    <?php }else{ ?>
<div class="simple">
<?php echo CHtml::activeLabelEx($models[0],'[0]companyNum',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.companyNum'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]companyNum',array('disabled'=>'true','size'=>15)); ?>
</div>
    <?php } ?>
</td></tr></table>
<div class="simple">
<?php 
if( $template->allowFreeTextField){
	echo CHtml::activeLabelEx($models[0],'[0]notesheader',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.notesheader'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]notesheader',array('size'=>35,'maxlength'=>255)); 
}
?>
</div>
<div class="simple">
<?php 
if( $template->allowFilingTextField){
	echo CHtml::activeLabelEx($models[0],'[0]fileInfo',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fileInfo'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]fileInfo',array('size'=>35,'maxlength'=>255)); 
}
?>
</div>




<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::label(Yii::t('lazy8','Description'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.templates.row.name'),'onclick'=>'alert(this.title)')); ?></th>
    <?php if($isChooseAccount){ ?>
    <th><?php echo CHtml::activeLabelEx($models[0],'accountId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.accountId'),'onclick'=>'alert(this.title)')); ?></th>
    <?php } ?>
    <?php if($isChooseCustomer){ ?>
    	    <th><?php echo CHtml::activeLabelEx($models[0],'customerId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.customerId'),'onclick'=>'alert(this.title)')); ?></th>
    <?php } ?>
    <?php if($isChooseNotes){ ?>
    <th><?php echo CHtml::activeLabelEx($models[0],'notes',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.notes'),'onclick'=>'alert(this.title)')); ?></th>
    <?php } ?>
    <th><?php echo CHtml::label(Yii::t('lazy8','Amount'),false,array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.amount'),'onclick'=>'alert(this.title)')); ?></th>
    <?php if($isMultipleRows){ ?>
    <th><?php echo CHtml::activeLabelEx($models[0],'actions',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>
    <?php } ?>
  </tr>
  </thead>
  <tbody>
<?php 
$debit=0.0;
$credit=0.0;
if($isChooseCustomer) {
$customers=CHtml::encodeArray(CHtml::listData(Customer::model()->findAll(array('condition'=>'companyId='
	.Yii::app()->user->getState('selectedCompanyId'),'select'=>'id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code')),'id','name'));
$customers[0]='';
}
$templaterows=$template->templateRows;
	foreach($models as $n=>$transrow): 
		$templateRowNum=$n;
		if($n>=count($templaterows))$templateRowNum=$multipleRowNumber;
	
	?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
<td>
	<?php echo CHtml::label($templaterows[$templateRowNum]->name,false,array('class'=>'help','title'=>$templaterows[$templateRowNum]->desc,'onclick'=>'alert(this.title)')); ?>
</td><td>
<?php 
$accounts=CHtml::encodeArray(CHtml::listData(Account::model()->findAll(array('join'=>' LEFT JOIN TemplateRowAccount ON t.id=TemplateRowAccount.accountId','select'=>'t.id as id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code','condition'=>'templateRowId='.$templaterows[$templateRowNum]->id)),'id','name'));
if($templaterows[$templateRowNum]->defaultAccountId==0 && count($accounts)>1)$accounts[0]='';
if(count($accounts)>1){
	echo CHtml::activeDropDownList($transrow,"[$n]accountId", 
		$accounts,array('style'=>'width:'.(250*Yii::app()->user->getState('TransactionEditWidthMultiplier')).'px')); ?>
<?php } 
elseif(count($accounts)==1){
	foreach($accounts as $key=>$value){
		$transrow->accountId=$key;
		echo CHtml::activeHiddenField($transrow,"[$n]accountId");
	}
}
if($isChooseAccount)
	echo '</td><td>';	
	
?>
<?php if($isChooseCustomer && $templaterows[$templateRowNum]->allowCustomer){
	echo CHtml::activeDropDownList($transrow,"[$n]customerId", 
		$customers,array('style'=>'width:'.(120*Yii::app()->user->getState('TransactionEditWidthMultiplier')).'px'));
?>
<?php } else echo CHtml::activeHiddenField($transrow,"[$n]customerId"); ?>
<?php if($isChooseCustomer)
	echo '</td><td>'; ?>
<?php if($isChooseNotes && $templaterows[$templateRowNum]->allowNotes) {
	echo CHtml::activeTextField($transrow,"[$n]notes",array('size'=>10*Yii::app()->user->getState('TransactionEditWidthMultiplier'),'maxlength'=>255)); ?>
<?php } else echo CHtml::activeHiddenField($transrow,"[$n]notes"); ?>
<?php if($isChooseNotes)
	echo '</td><td>'; ?>
<?php   
	if($templaterows[$templateRowNum]->allowMinus){
		//need to make readjustments in the case this has been already adjusted for negative numbers
		$amountdebit=TransController::parseNumber($transrow->amountdebit,$Locale);
		$amountcredit=TransController::parseNumber($transrow->amountcredit,$Locale);
		if($templaterows[$templateRowNum]->isDebit){
			if(bccomp($amountdebit,0,5)==0)
				$transrow->amountdebit=TransController::formatNumber(-$amountcredit,$numberFormatter,$numberFormat);
		}else{
			if(bccomp($amountcredit,0,5)==0)
				$transrow->amountcredit=TransController::formatNumber(-$amountdebit,$numberFormatter,$numberFormat);
		}
	}
	$isDisabledText=array('size'=>8,'maxlength'=>14,'style'=>'text-align:right;');
	if(!$templaterows[$templateRowNum]->allowChangeValue)
		$isDisabledText=array('size'=>8,'maxlength'=>14,'style'=>'text-align:right;','disabled'=>'disabled');
	if($templaterows[$templateRowNum]->isDebit && !$templaterows[$templateRowNum]->isFinalBalance || (strlen($transrow->amountcredit)==0 && strlen($transrow->amountdebit)>0)){
		echo CHtml::activeTextField($transrow,"[$n]amountdebit",$isDisabledText); 
	}elseif(!$templaterows[$templateRowNum]->isFinalBalance) {
		echo CHtml::activeTextField($transrow,"[$n]amountcredit",$isDisabledText); 
	}else{
		$displayText=$transrow->amountcredit;
		if(strlen($displayText)==0)$displayText=$transrow->amountdebit;
		echo CHtml::textField("[$n]balance",$displayText,array('size'=>8,'maxlength'=>14,'style'=>'text-align:right;','disabled'=>'disabled')); 
	}
if($isMultipleRows){ ?>
	</td><td>
<?php
if($templaterows[$templateRowNum]->allowRepeatThisRow  && $n<count($templaterows))
	echo CHtml::submitButton(Yii::t('lazy8','Add Row'),array('name'=>'AddRow','title'=>Yii::t('lazy8','contexthelp.templates.transaction.Add Row')));
if($n>=count($templaterows))
	echo CHtml::submitButton(Yii::t('lazy8','Remove Row'),array('name'=>"deleterow[$n]",'title'=>Yii::t('lazy8','contexthelp.Remove Row'))); ?>
<?php } 
echo CHtml::activeHiddenField($transrow,"[$n]rownum"); ?>
</td> </tr>
<?php 
$debit+=TransController::parseNumber($transrow->amountdebit,$Locale);
$credit+=TransController::parseNumber($transrow->amountcredit,$Locale);
endforeach; ?>
  </tbody>
</table>

<div class="action">
<table width='100%'><tr><td>
<?php if(Yii::app()->user->getState('allowReEditingOfTransactions') || !$update){  
	echo CHtml::submitButton($update ? Yii::t('lazy8','Save') : Yii::t('lazy8','Create') ,array('name'=>$update ? 'Save' : 'Add','title'=>$update ? Yii::t('lazy8','contexthelp.Save') : Yii::t('lazy8','contexthelp.Create'))); ?>
<?php } ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','Show accounting view'),array('name'=>"showaccounting",'title'=>Yii::t('lazy8','contexthelp.showaccounting'))); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','Update screen'),array('name'=>"Update",'title'=>Yii::t('lazy8','contexthelp.Update screen'))); ?>
</tr></td></table>
</div>





<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->