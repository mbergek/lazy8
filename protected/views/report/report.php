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
 if($printoutview!=1){ ?>
<h2><?php echo Yii::t('lazy8','Reports'); ?></h2>

<?php } 

$sums=array();
$repRows=$model->rows;
$groupSums=array();
if(isset($repRows) && count($repRows)>0){
	foreach($repRows as $n=>$repRow){ 
		if($repRow->isSummed){
			$sums[$repRow->fieldName]=0.0;
			$groupSums[$repRow->fieldName]=0.0;
		}
	}
}
?>


<div class="report">

<?php 
//*******************************
//create the header
//*******************************
$repParams=$model->reportparameters;
//die();
if(isset($repParams) && count($repParams)>0){
	?><table class="ReportHeader"><?php 
	foreach($repParams as $n=>$repParam){
		if($repParam->dataType!='HIDDEN_NO_SHOW_HEAD'){
			?><tr class="<?php echo $n%2?'even':'odd';?>"><td><?php 
			echo CHtml::encode(Yii::t('lazy8',$repParam->name));
			?></td><td><?php
			$displayNum="";
			if(isset($parameterValues[$n]))$displayNum=$parameterValues[$n];
			if($repParam->isDecimal){
				if(round($displayNum,5)==0.0)
					$displayNum="";
				else
					$displayNum=$numberFormatter->format($numberFormat,$displayNum);
			}
			if($repParam->isDate)$displayNum=$dateFormatter->formatDateTime($displayNum,'short',null);
			echo CHtml::encode($displayNum);
			?></td></tr>
			<?php
		}
	}
	?></table>
	<?php 
}
//get array of all breaks and sums.
$repGroups=$model->groups;
$breaksLastValue=array();
$doGroupSummming=false;
if(isset($repGroups) && count($repGroups)>0){
	foreach($repGroups as $n=>$repGroup){
		$breaksLastValue[$repGroup->breakingField]=-999999999;
		if($repGroup->continueSumsOverGroup)
			$doGroupSummming=true;
	}	
}
function startNewRowTable($repRows){
	?><table class="ReportRows"><?php 
	if(isset($repRows) && count($repRows)>0){
		?><thead><tr><?php 
		foreach($repRows as $n=>$repRow){ 
			echo '<th class="col'.$n.'">'; 
			echo CHtml::encode(Yii::t('lazy8',$repRow->fieldName));
			?></th>
			<?php 
		}
		?></tr></thead>
		<?php
	}
	?><tbody>
	<?php
}
function endRowTable($repRows,&$sums,&$groupSums,$numberFormatter,$numberFormat,$doGroupSummming,$resetSums=true){
	//we must stop the last groups table and do summing.
	if(count($sums)>0){
		?><tr class="ReportSums"><?php 
		$isGroupSumFound=false;
		for($i=0;$i<count($repRows);$i++){
			echo '<td class="col'.$i.'">'; 
			if(isset($sums[$repRows[$i]->fieldName])){
				$displayNum=round($sums[$repRows[$i]->fieldName],5);
				if($repRows[$i]->isDecimal){
					if($displayNum==0.0)
						$displayNum="";
					else
						$displayNum=$numberFormatter->format($numberFormat,$displayNum);
				}
				echo CHtml::encode($displayNum);
				//get group sums
				if(isset($groupSums[$repRows[$i]->fieldName])){
					$isGroupSumFound=true;
					$groupSums[$repRows[$i]->fieldName]+=$sums[$repRows[$i]->fieldName];
				}
				//reset to zero
				$sums[$repRows[$i]->fieldName]=0.0;
			}
			?></td>
			<?php 
		}
		?></tr><?php 
		if($isGroupSumFound && $doGroupSummming){
			//show the group sums
			?><tr class="ReportSumsSums"><?php 
			$isGroupSumFound=false;
			for($i=0;$i<count($repRows);$i++){
				echo '<td class="col'.$i.'">'; 
				if(isset($groupSums[$repRows[$i]->fieldName])){
					$displayNum=round($groupSums[$repRows[$i]->fieldName],5);
					if($repRows[$i]->isDecimal){
						if($displayNum==0.0)
							$displayNum="";
						else
							$displayNum=$numberFormatter->format($numberFormat,$displayNum);
					}
					echo CHtml::encode($displayNum);
					if($resetSums){
						$groupSums[$repRows[$i]->fieldName]=0.0;
					}
				}
				?></td>
				<?php 
			}
			?></tr>
			<?php 
		}
	}
	?></tbody></table>
	<?php 
}
//*******************************
//Loop through all the rows of the report
//*******************************
$rowCounter=0;
foreach($reader as $n=>$row){
	//first time through just check for breaks and do the summing
	$isRowBroken=false;
	if(isset($repGroups) && count($repGroups)>0){
		foreach($repGroups as $nn=>$repGroup){
			if($isRowBroken || $row[$repGroup->breakingField]!=$breaksLastValue[$repGroup->breakingField]){
				if($n!=0 && !$isRowBroken){
					endRowTable($repRows,$sums,$groupSums,$numberFormatter,$numberFormat,$doGroupSummming,$repGroup->continueSumsOverGroup!=1);
					if($repGroup->pageBreak){
						?><p style="page-break-before: always"></p><?php
					}
				}
				$isRowBroken=true;
				$repGroupFields=$repGroup->fields;
				if(isset($repGroupFields) && count($repGroupFields)>0){
					//show the Group Info
					?><table class="ReportGroupTable<?php echo $nn;?>"><?php 
					if($repGroup->showHeader){
						if(isset($repGroupFields) && count($repGroupFields)>0){
							//print out the header for the group
							?><thead><tr class="ReportGroupHeader<?php echo $nn;?>"><?php 
							foreach($repGroupFields as $repGroupField){ 
								?><th><?php
								echo CHtml::encode(Yii::t('lazy8',$repGroupField->fieldName));
								?></th>
								<?php
							}
							?></tr><?php 
							?></thead>
							<?php 
						}
					}
					?><tbody><tr class="ReportGroupBody"><?php 
					//show the fields for the group  
					foreach($repGroupFields as $repGroupField){ 
						?><td><?php
						$display="";
						if(isset($row[$repGroupField->fieldName]))$display=$row[$repGroupField->fieldName];
						if(strlen($repGroupField->fieldCalc)>0){
							eval($repGroupField->fieldCalc);
						}
						if($repGroupField->isDecimal){
							if(round($display,5)==0.0)
								$display="";
							else
								$display=$numberFormatter->format($numberFormat,$display);
						}
						if($repGroupField->isDate)$display=$dateFormatter->formatDateTime($display,'short',null);
							
						echo $display;
						?></td>
						<?php
					}
					?></tr>
					<?php 
					?></tbody></table>
					<?php 
				}
			}
			$breaksLastValue[$repGroup->breakingField]=$row[$repGroup->breakingField];
		}	
	}
	if($n==0 || $isRowBroken){
		startNewRowTable($repRows);
	}
	//now we actually print out the row.
	if(isset($repRows) && count($repRows)>0){
		//first update the sums
		foreach($repRows as $repRow){ 
			if(isset($sums[$repRow->fieldName])){
				$display="";
				if(isset($row[$repRow->fieldName]))$display=$row[$repRow->fieldName];
				if(strlen($repRow->fieldCalc)>0)
					eval($repRow->fieldCalc);
				$sums[$repRow->fieldName]+=$display;
			}
		}
		?><tr class="<?php echo $n%2?'even':'odd';?>"><?php 
		$i=0;
		foreach($repRows as $repRow){ 
			echo '<td class="col'.$i.'">'; 
			$i++;
			if(strlen($repRow->fieldCalc)>0){
				eval($repRow->fieldCalc);
				if($repRow->isDecimal){
					if(round($display,5)==0.0)
						$display="";
					else
						$display=$numberFormatter->format($numberFormat,$display);
				}
				if($repRow->isDate)$display=$dateFormatter->formatDateTime($display,'short',null);
				echo $display;
			}else{
				$displayNum=$row[$repRow->fieldName];
				if($repRow->isDecimal){
					if(round($displayNum,5)==0.0)
						$displayNum="";
					else
						$displayNum=$numberFormatter->format($numberFormat,$displayNum);
				}
				if($repRow->isDate)$displayNum=$dateFormatter->formatDateTime($displayNum,'short',null);
				echo $displayNum;
			}
			?></td>
			<?php 
		}
		?></tr>
		<?php
	}
}
endRowTable($repRows,$sums,$groupSums,$numberFormatter,$numberFormat,$doGroupSummming);
if(count($imageFileNames)>0)
foreach($imageFileNames as $imageFileName)
	echo '<p><img src="'.$imageFileName.'" alt="image" /></p>';

?>

</div><!-- report -->