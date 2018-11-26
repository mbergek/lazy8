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

require_once('config/lang/eng.php');
require_once('tcpdf.php');

// extend TCPF with custom functions
class lazy8tcpdf extends TCPDF {
	private	$fontSize=8;
	private $rawWidths=array();
	private $normalizedWidths=array();
	private $rowAlign=array();
	private $rowText=array();
	private $rowBorder=array();
	private $rowFill=array();
	private $groupWidths=array();
	private $rowWidths=null;
	private $pageWidth=0;
	private $isBlackWhite=0;
	public function lazy8tcpdf($model,$reader,$numberFormatter,$dateFormatter,
		$numberFormat,$printoutview,$parameterValues,$imageFileNames,
		$isBlackWhite,$isPortrait)
	{
		parent::__construct($isPortrait?'P':'L', PDF_UNIT, Yii::app()->user->getState('PdfPageFormat'), true, 'UTF-8', false);
		$repParams=$model->reportparameters;
		//get the page header texts
		$this->isBlackWhite=$isBlackWhite;
		$header1="";
		$header2="";
		$title="";
		if(isset($repParams) && count($repParams)>0){
			foreach($repParams as $n=>$repParam){
				if($repParam->dataType!='HIDDEN_NO_SHOW_HEAD'){
					$displayNum=$parameterValues[$n];
					if($repParam->isDecimal){
						if(round($displayNum,5)==0.0)
							$displayNum="";
						else
							$displayNum=$numberFormatter->format($numberFormat,$displayNum);
					}
					if($repParam->isDate)$displayNum=$dateFormatter->formatDateTime($displayNum,'short',null);
					if(strlen($header1)==0)
					{
						$header1= Yii::t('lazy8',$repParam->name) . ": " . $displayNum;
						$title=$displayNum;
					}
					elseif(strlen($header2)==0)
					{
						$header2= Yii::t('lazy8',$repParam->name) . ": " . $displayNum;
						$title.= "_".$displayNum;
						break;
					}
				}
			}
		}
		// set document information
		$this->SetCreator(PDF_CREATOR." Lazy8Web");
		$this->SetAuthor(Yii::app()->user->getState('displayname'));
		$this->SetTitle("lazy8webReport.". $title . "." . date('Y-m-d_H.i.s'));
		$this->SetSubject($title);
		$this->SetKeywords('TCPDF, PDF, Lazy8Web');
		$this->fontSize=Yii::app()->user->getState('PdfFontSize');

		// set default header data
		$this->SetHeaderData(null, null, $header1, $header2);
		
		// set header and footer fonts
		$this->setHeaderFont(Array(Yii::app()->user->getState('PdfFont'), '', $this->fontSize));
		$this->setFooterFont(Array(Yii::app()->user->getState('PdfFont'), '', $this->fontSize));
		
		// set default monospaced font
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$this->SetMargins(PDF_MARGIN_LEFT, $this->fontSize*1.25 + 3 , PDF_MARGIN_RIGHT);
		$this->SetHeaderMargin(3);
		$this->SetFooterMargin(10);
		
		//set auto page breaks
		$this->SetAutoPageBreak(TRUE, 10);
		
		//set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		//$this->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		$this->SetFont(Yii::app()->user->getState('PdfFont'), '', $this->fontSize);
		
		// add a page
		$this->AddPage();
		if($isPortrait)
			$this->pageWidth=$this->fwPt/$this->k-$this->rMargin-$this->lMargin;
		else
			$this->pageWidth=$this->fhPt/$this->k-$this->rMargin-$this->lMargin;
			
		// ---------------------------------------------------------
		$this->LoadData($model,$reader,$numberFormatter,$dateFormatter,
			$numberFormat,$printoutview,$parameterValues,$imageFileNames,
			$isBlackWhite);
		//Close and output PDF document
		$this->Output("lazy8webReport.". $title . "." . date('Y-m-d_H.i.s').".pdf", 'I');
	}
	public function resetWidths()
	{
		$this->rawWidths=array();
		$this->normalizedWidths=array();
	}
	public function addWidth($w,$isAbsolute=false)
	{
		$this->rawWidths[]=floatval($w);
		$this->normalizedWidths=array();
		if($isAbsolute)
		{
			foreach($rawWidths as $w)
				$this->normalizedWidths[]=$w;
		}
		else
		{
			$totalWidth=0;
			foreach($this->rawWidths as $w)
				$totalWidth+=$w;
			if($totalWidth!=0)
				foreach($this->rawWidths as $w)
					$this->normalizedWidths[]=$this->pageWidth*$w/$totalWidth;
		}
		
			
	}
	public function addCell($txt, $border, $align,$fill)
	{
		$this->rowAlign[]=$align;
		$this->rowText[]=$txt;
		$this->rowBorder[]=$border;		
		$this->rowFill[]=$fill;		
	}
	public function writeRow($widths,$repRows=null)
	{
		$maxHeight=0;
		foreach($this->rowText as $n=>$txt)
		{
			$tstHeight=0;
			if(isset($widths[$n]) && strlen($widths[$n])>0 && $widths[$n]>0 && strlen($txt)>0)$tstHeight=$this->getStringHeight($widths[$n],$txt);
			if($tstHeight>$maxHeight)
				$maxHeight=$tstHeight;
		}
		if($this->checkPageBreak($maxHeight+0.2) )
		{
			if($repRows!=null)
			{
				$this->SetFont('', 'B');
				$mheight=0;
				foreach($repRows as $n=>$repRow){ 
					$tstHeight=0;
					$txt=Yii::t('lazy8',$repRow->fieldName);
					if(isset($widths[$n]) && strlen($widths[$n])>0 && $widths[$n]>0 && strlen($txt)>0)$tstHeight=$this->getStringHeight($widths[$n],$txt);
					if($tstHeight>$mheight)
						$mheight=$tstHeight;
				}
				
				$y_start = $this->GetY();
				$x_start = $this->GetX();
				$x_cellstart=$x_start;
				if(!$this->isBlackWhite)
					$this->SetFillColor(211, 223, 238);
				foreach($repRows as $n=>$repRow){ 
					$this->MultiCell($widths[$n], $mheight+0.2, Yii::t('lazy8',$repRow->fieldName), 1, $repRow->isAlignRight?'R':'L',1,1,$x_cellstart,$y_start);
					$x_cellstart+=$widths[$n];
				}
				if(!$this->isBlackWhite)
					$this->SetFillColor(230, 242, 255);
				$this->SetXY($x_start,$this->GetY());
				$this->SetFont('');
			}
		}
		
		$y_start = $this->GetY();
		$x_start = $this->GetX();
		$x_cellstart=$x_start;
		
		foreach($this->rowText as $n=>$txt)
		{
			$width=0;
			if(isset($widths[$n]))$width=$widths[$n];
			$this->MultiCell($width, $maxHeight+0.2, $txt, $this->rowBorder[$n],  $this->rowAlign[$n],$this->rowFill[$n],1,$x_cellstart,$y_start);
			$x_cellstart+=$width;
		}
		$this->rowAlign=array();
		$this->rowText=array();
		$this->rowBorder=array();
		$this->rowFill=array();
		$this->SetXY($x_start,$this->GetY());
	}

	// Load table data from file
	public function LoadData($model,$reader,$numberFormatter,$dateFormatter,
			$numberFormat,$printoutview,$parameterValues,$imageFileNames,
			$isBlackWhite) {

		$sums=array();
		$repRows=$model->rows;
		$groupSums=array();
		$repRowColumnWidths="";

		//*******************************
		//create the header
		//*******************************
		$repParams=$model->reportparameters;
		//thin lines
		$this->SetLineStyle(array('width' => 0.1));
		if($isBlackWhite)
		{
			$this->SetTextColor(0);
			$this->SetFillColor(255, 255, 255);
			$this->SetDrawColor(0, 0, 0);
		}
		else
		{
			$this->SetTextColor(68, 68, 68);
			$this->SetFillColor(230, 242, 255);
			$this->SetDrawColor(79, 129, 189);
		}
		$this->SetFont('');
		if(isset($repParams) && count($repParams)>0){
			$fill=0;
			foreach($repParams as $n=>$repParam){
				if($repParam->dataType!='HIDDEN_NO_SHOW_HEAD'){
					$displayNum="";
					if(isset($parameterValues[$n]))$displayNum=$parameterValues[$n];
					if($repParam->isDecimal){
						if(round($displayNum,5)==0.0)
							$displayNum="";
						else
							$displayNum=$numberFormatter->format($numberFormat,$displayNum);
					}
					if($repParam->isDate)$displayNum=$dateFormatter->formatDateTime($displayNum,'short',null);
					$this->addCell(Yii::t('lazy8',$repParam->name), 1, 'L', $fill);
					$this->addCell($displayNum, 1, 'L', $fill);
					$this->writeRow(array(80,80));
					$fill=!$fill ;
					
				}
			}
			$this->Ln();
			
		}
		//get array of all breaks and sums. Get also field widths
		$repGroups=$model->groups;
		$breaksLastValue=array();
		$doGroupSummming=false;
		if(isset($repGroups) && count($repGroups)>0){
			foreach($repGroups as $n=>$repGroup){
				
				$breaksLastValue[$repGroup->breakingField]=-999999999;
				if($repGroup->continueSumsOverGroup)
					$doGroupSummming=true;
				//get field widths
				$repGroupFields=$repGroup->fields;
				if(isset($repGroupFields) && count($repGroupFields)>0){
					foreach($repGroupFields as $repGroupField){ 
						$this->addWidth($repGroupField->fieldWidth);
					}
					$this->groupWidths[$n]=$this->normalizedWidths;
					$this->resetWidths();
				}
			}	
		}
		//set summed rows to zero, get also field widths
		foreach($repRows as $n=>$repRow){ 
			if($repRow->isSummed){
				$sums[$repRow->fieldName]=0.0;
				$groupSums[$repRow->fieldName]=0.0;
			}
			$this->addWidth($repRow->fieldWidth);
		}		
		$this->rowWidths=$this->normalizedWidths;
		//$this->resetWidths();		
		//*******************************
		//Loop through all the rows of the report
		//*******************************
		$rowCounter=0;
		$fill=0;
		foreach($reader as $n=>$row){
			//first time through just check for breaks and do the summing
			$isRowBroken=false;
			if(isset($repGroups) && count($repGroups)>0){
				foreach($repGroups as $nn=>$repGroup){
					if($isRowBroken || $row[$repGroup->breakingField]!=$breaksLastValue[$repGroup->breakingField]){
						if($n!=0 && !$isRowBroken){
							$this->endRowTable($repRows,$sums,$groupSums,$numberFormatter,$numberFormat,$doGroupSummming,$repGroup->continueSumsOverGroup!=1);
							if($repGroup->pageBreak){
								$this->AddPage();
							}
						}
						$isRowBroken=true;
						$repGroupFields=$repGroup->fields;
						if(isset($repGroupFields) && count($repGroupFields)>0){
							//show the Group Info
							$this->Ln();
							if($repGroup->showHeader){
								if(isset($repGroupFields) && count($repGroupFields)>0){
									//print out the header for the group
									foreach($repGroupFields as $repGroupField){ 
										$this->addCell(Yii::t('lazy8',$repGroupField->fieldName), 0,'L', 0);
									}
									$this->writeRow($this->groupWidths[$nn]);
								}
							}
							//show the fields for the group  
							$fill=0;
							$this->SetFont('', '',  $this->fontSize*1.5);
							foreach($repGroupFields as $repGroupField){ 
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
								$this->addCell($this->removeLinks($display), 0, 'L', 0);								
							}
							$this->writeRow($this->groupWidths[$nn]);
							$this->SetFont('', '', $this->fontSize);
						}
					}
					$breaksLastValue[$repGroup->breakingField]=$row[$repGroup->breakingField];
				}	
			}
			if($n==0 || $isRowBroken){
				$this->startNewRowTable($repRows);
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
				
				$i=0;
				foreach($repRows as $repRow){ 
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
						$this->addCell($this->removeLinks($display), 1, $repRow->isAlignRight?'R':'L', $fill);
					}else{
						$displayNum=$row[$repRow->fieldName];
						if($repRow->isDecimal){
							if(round($displayNum,5)==0.0)
								$displayNum="";
							else
								$displayNum=$numberFormatter->format($numberFormat,$displayNum);
						}
						if($repRow->isDate)$displayNum=$dateFormatter->formatDateTime($displayNum,'short',null);
						$this->addCell($this->removeLinks($displayNum), 1, $repRow->isAlignRight?'R':'L', $fill);
					}
				}
				$fill=!$fill;
				$this->writeRow($this->rowWidths,$repRows);
			}
		}
		$this->endRowTable($repRows,$sums,$groupSums,$numberFormatter,$numberFormat,$doGroupSummming);
		if(count($imageFileNames)>0)
			foreach($imageFileNames as $imageFileName)
			{
				$imageFolder  = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR;
				$this->Image($imageFolder.$imageFileName);
				$this->Ln();
				$imsize = @getimagesize($imageFolder.$imageFileName);
				list($pixw, $pixh) = $imsize;
				$h = $this->pixelsToUnits($pixh);
				$this->SetY($this->GetY()+$h);
			}
	}
	function removeLinks($fieldText)
	{
		$retVal=str_replace("−","-",$fieldText);
		if(strpos($retVal,"<a")===false)
			return $retVal;
		else
		{
			$retVal=preg_replace("/<a.*?>/","",$retVal);
			$retVal=preg_replace("/<\/a>/","",$retVal);
			return $retVal;
		}
	}
	function startNewRowTable($repRows){
		//header
		if(isset($repRows) && count($repRows)>0){
			$this->repRowsHeader=$repRows;
			$this->SetFont('', 'B');
			if(!$this->isBlackWhite)
				$this->SetFillColor(211, 223, 238);
			foreach($repRows as $n=>$repRow){ 
				$this->addCell(Yii::t('lazy8',$repRow->fieldName), 1,$repRow->isAlignRight?'R':'L', 1);
			}
			$this->writeRow($this->rowWidths);
			if(!$this->isBlackWhite)
				$this->SetFillColor(230, 242, 255);
			$this->SetFont('');
			
		}
	}
	function endRowTable($repRows,&$sums,&$groupSums,$numberFormatter,$numberFormat,$doGroupSummming,$resetSums=true){
		//we must stop the last groups table and do summing.
		$this->repRowsHeader=null;
		if(count($sums)>0){
			$isGroupSumFound=false;
			for($i=0;$i<count($repRows);$i++){
				if(isset($sums[$repRows[$i]->fieldName])){
					$displayNum=round($sums[$repRows[$i]->fieldName],5);
					if($repRows[$i]->isDecimal){
						if($displayNum==0.0)
							$displayNum="";
						else
							$displayNum=$numberFormatter->format($numberFormat,$displayNum);
					}
					$this->addCell($this->removeLinks($displayNum), 1, $repRows[$i]->isAlignRight?'R':'L', 0);
					//get group sums
					if(isset($groupSums[$repRows[$i]->fieldName])){
						$isGroupSumFound=true;
						$groupSums[$repRows[$i]->fieldName]+=$sums[$repRows[$i]->fieldName];
					}
					//reset to zero
					$sums[$repRows[$i]->fieldName]=0.0;
				}
				else
				{
					//empty cell
					$this->addCell(" ", 1, 'L', 0);
				}
			}
			$this->writeRow($this->rowWidths);
			if($isGroupSumFound && $doGroupSummming){
				//show the group sums
				$isGroupSumFound=false;
				for($i=0;$i<count($repRows);$i++){
					if(isset($groupSums[$repRows[$i]->fieldName])){
						$displayNum=round($groupSums[$repRows[$i]->fieldName],5);
						if($repRows[$i]->isDecimal){
							if($displayNum==0.0)
								$displayNum="";
							else
								$displayNum=$numberFormatter->format($numberFormat,$displayNum);
						}
						$this->addCell($this->removeLinks($displayNum), 1, $repRows[$i]->isAlignRight?'R':'L', 0);
						if($resetSums){
							$groupSums[$repRows[$i]->fieldName]=0.0;
						}
					}
					else
					{
						//empty cell
						$this->addCell(" ", 1, 'L', 0);
					}
				}
				$this->writeRow($this->rowWidths);
			}
		}
	}
}



//============================================================+
// END OF FILE                                                
//============================================================+
