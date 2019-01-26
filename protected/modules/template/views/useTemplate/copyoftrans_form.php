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
<?php echo CHtml::activeLabelEx($models[0],'[0]invDate',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.invDate'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]invDate',array('size'=>15)); 
$this->widget('application.extensions.calendar.SCalendar',array(	
			'firstDay'=>'1',
			'language'=>Yii::app()->user->getState('languagecode'),
			'inputField'=>'TempTrans_0_invDate',
			'ifFormat'=>User::getPhPDateFormat(),
			'range'=>"[" . date('Y',strtotime(Yii::app()->user->getState('selectedPeriodStart'))) . "," . date('Y',strtotime(Yii::app()->user->getState('selectedPeriodEnd'))) . "]"
                      )); 
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
<?php echo CHtml::activeLabelEx($models[0],'[0]notesheader',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.notesheader'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]notesheader',array('size'=>35,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($models[0],'[0]fileInfo',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.fileInfo'),'onclick'=>'alert(this.title)'));  echo CHtml::activeTextField($models[0],'[0]fileInfo',array('size'=>35,'maxlength'=>255)); ?>
</div>




<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo CHtml::activeLabelEx($models[0],'accountId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.accountId'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($models[0],'customerId',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.customerId'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($models[0],'notes',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.notes'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($models[0],'debit',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.debit'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($models[0],'credit',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.credit'),'onclick'=>'alert(this.title)')); ?></th>
    <th><?php echo CHtml::activeLabelEx($models[0],'actions',array('class'=>'help','title'=>Yii::t('lazy8','contexthelp.actions'),'onclick'=>'alert(this.title)')); ?></th>

  </tr>
  </thead>
  <tbody>
<?php 
$debit=0.0;
$credit=0.0;

$customers=CHtml::encodeArray(CHtml::listData(Customer::model()->findAll(array('condition'=>'companyId='
	.Yii::app()->user->getState('selectedCompanyId'),'select'=>'id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code')),'id','name'));
$customers[0]='';
$accounts=CHtml::encodeArray(CHtml::listData(Account::model()->findAll(array('condition'=>'companyId='
		.Yii::app()->user->getState('selectedCompanyId'),'select'=>'id, CAST(CONCAT(code,\' \',name) AS CHAR CHARACTER SET utf8) as name','order'=>'code')),'id','name'));
	foreach($models as $n=>$transrow): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
<td>
<?php 
echo CHtml::activeDropDownList($transrow,"[$n]accountId", 
	$accounts,array('style'=>'width:'.(120*Yii::app()->user->getState('TransactionEditWidthMultiplier')).'px'));
?>
</td><td>
<?php 
echo CHtml::activeDropDownList($transrow,"[$n]customerId", 
	$customers,array('style'=>'width:'.(120*Yii::app()->user->getState('TransactionEditWidthMultiplier')).'px'));
?>
</td><td>
<?php echo CHtml::activeTextField($transrow,"[$n]notes",array('size'=>10*Yii::app()->user->getState('TransactionEditWidthMultiplier'),'maxlength'=>255)); 
echo CHtml::activeHiddenField($transrow,"[$n]rownum");?>
</td><td>
<?php echo CHtml::activeTextField($transrow,"[$n]amountdebit",array('size'=>8,'maxlength'=>12,'style'=>'text-align:right;')); ?>
</td><td>
<?php echo CHtml::activeTextField($transrow,"[$n]amountcredit",array('size'=>8,'maxlength'=>12,'style'=>'text-align:right;')); ?>
</td>
    <td>
<?php echo CHtml::submitButton(Yii::t('lazy8','balance'),array('name'=>"balancerow[$n]",'title'=>Yii::t('lazy8','contexthelp.BalanceRow')));
echo CHtml::submitButton(Yii::t('lazy8','Delete'),array('name'=>"accountdeleterow[$n]",'title'=>Yii::t('lazy8','contexthelp.Delete'))); ?>
	</td>
  </tr>
<?php 
$debit+=TransController::parseNumber($transrow->amountdebit,$Locale);
$credit+=TransController::parseNumber($transrow->amountcredit,$Locale);
endforeach; ?>
  <tr class="<?php echo ($n+1)%2?'even':'odd';?>">
<td colspan="3"></td>
    <td><?php echo '<div style=\'text-align:right;\'>'.TransController::formatNumber($debit,$numberFormatter,$numberFormat).'</div>';?></td>
    <td><?php echo '<div style=\'text-align:right;\'>'.TransController::formatNumber($credit,$numberFormatter,$numberFormat).'</div>';?></td>
    <td>
    <?php if(bccomp($debit,$credit,5)!=0)echo CHtml::label(TransController::formatNumber(abs($debit-$credit),$numberFormatter,$numberFormat),false,array("style"=>'color:red'));?>
    </td>
</tr>
<tr class="<?php echo ($n+2)%2?'even':'odd';?>">
<td colspan="6">
<div class="action">
<?php echo CHtml::submitButton(Yii::t('lazy8','Add Row'),array('name'=>'accountAddRow','title'=>Yii::t('lazy8','contexthelp.Add Row'))); ?>
</div>
</td>
</tr>
  </tbody>
</table>

<div class="action">
<table width='100%'><tr><td>

<?php echo CHtml::submitButton($update ? Yii::t('lazy8','Create') : Yii::t('lazy8','Create') ,array('name'=>$update ? 'Save' : 'Add','title'=>$update ? Yii::t('lazy8','contexthelp.Create') : Yii::t('lazy8','contexthelp.Create'))); ?>

</td><td>
<?php if($isMultipleRows || $numTemplateRows==count($models))echo CHtml::submitButton(Yii::t('lazy8','Show template view'),array('name'=>"showtemplate",'title'=>Yii::t('lazy8','contexthelp.showtemplate'))); ?>
</td><td>
<?php echo CHtml::submitButton(Yii::t('lazy8','Update screen'),array('name'=>'accountUpdate','title'=>Yii::t('lazy8','contexthelp.Update screen'))); ?>
</tr></td></table>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->