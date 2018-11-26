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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->user->getState('languagecode'); ?>" lang="<?php echo Yii::app()->user->getState('languagecode'); ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="<?php echo Yii::app()->user->getState('languagecode'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<?php if(strlen(Yii::app()->user->getState('reportCssFile'))>0){  ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl.'/css/'.Yii::app()->user->getState('reportCssFile'); ?>" />
<?php	
		$controller=Yii::app()->getController();
		//print_r($controller->_model);die();
		if($controller->id=="report" && $controller->_model!=null){
			$repRows=$controller->_model->rows;
			if(isset($repRows) && count($repRows)>0){
				echo "<style type=\"text/css\">\n";
				foreach($repRows as $n=>$repRow){ 
					echo "td.col" . $n . ", th.col" . $n ." {\n";
					if($repRow->isSummed){
						$sums[$repRow->fieldName]=0.0;
						$groupSums[$repRow->fieldName]=0.0;
					}
					echo "    width:".$repRow->fieldWidth.";\n";
					if($repRow->isAlignRight)
						echo "    text-align:right;\n";
					echo "}\n";
				}
				echo "</style>\n";
			}
		}
?>	
	
	
	
<?php }  ?>
<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<div id="page">


<?php if( (isset($_GET['r']) && $_GET['r']!='report/report') || ( !isset($_POST['ShowReport'])) || !Yii::app()->user->getState('isReportForPrintout')){ ?>
<div id="header">

<table><tr><td>
	<div id="logo"><a href="<?php echo Yii::app()->request->hostInfo  . Yii::app()->request->scriptUrl; ?>">
<?php echo CHtml::encode(Yii::app()->user->getState('siteHeaderTitle') ); ?>
	</a></div>
</td><td style="text-align:center">
<?php 
$linkhtml="";
$linkendhtml="";
if(!Yii::app()->user->isGuest){ 
	if(Yii::app()->user->getState('allowPeriodSelection')){
		$linkhtml='<a href="' . Yii::app()->request->hostInfo  . Yii::app()->request->scriptUrl . "?r=user/selectcompany&id=".Yii::app()->user->id . '">'; 
		$linkendhtml='</a>'; 
	}
	if(strlen(Yii::app()->user->getState('selectedCompany'))==0){
		echo '<div id="titleperioderror">' .$linkhtml. CHtml::encode(Yii::t("lazy8",'Error: No company is selected for editing')) . $linkendhtml.'</div>' ; 
	}else{
		echo '<div id="titleperiod">'.$linkhtml . CHtml::encode(Yii::t("lazy8",'Now editing Company/period')); 
		?><br /><?php 
		$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode')); 
		$dateformatter=new CDateFormatter($cLoc); 
		echo CHtml::encode(Yii::app()->user->getState('selectedCompany') . ' / ' . 
			User::getDateFormatted(Yii::app()->user->getState('selectedPeriodStart'),$cLoc,$dateformatter) . 
			' - ' .  User::getDateFormatted(Yii::app()->user->getState('selectedPeriodEnd'),$cLoc,$dateformatter) ) . $linkendhtml.'</div>'; 
	}
	?><?php 
}
?>
</td><td style="text-align:right">
<?php if(!Yii::app()->user->isGuest){ ?>
	<div id="logo">
	<?php if(Yii::app()->user->getState('allowSelf') || Yii::app()->user->getState('allowAdmin')){ ?>
	<a href="<?php echo Yii::app()->request->hostInfo  . Yii::app()->request->scriptUrl . "?r=user/update&id=".Yii::app()->user->id; ?>">
	<?php }?>
<?php echo CHtml::encode(Yii::app()->user->getState('displayname')); ?>
	<?php if(Yii::app()->user->getState('allowSelf') || Yii::app()->user->getState('allowAdmin'))echo "</a>";?>
	</div>
<?php } ?>
</td></tr></table>
<?php
$menu=array(        
	"stylesheet"=>"menu_blue.css",
        "menuID"=>"menu",
        "delay"=>3
        );
if(!Yii::app()->user->isGuest){ 
	$menu["menu"]=Yii::app()->mainMenu;




}else{
	$menu["menu"]=array(array(
		"url"=>array("route"=>"/site/login"),
		"label"=>Yii::t("lazy8","Login"),
	));
}
$this->widget('application.extensions.menu.SMenu',$menu);

?>
</div><!-- header -->

<div id="content">
<?php echo $content; ?>

</div><!-- content -->

<div id="footer">
<?php echo Yii::app()->user->getState('siteFooter'); ?>  
</div><!-- footer -->
<?php  }else{ ?>
<div id="content">
<?php echo $content; ?>

</div><!-- content -->
<?php } ?>

</div><!-- page -->
</body>

</html>
