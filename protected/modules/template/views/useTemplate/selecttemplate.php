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
<h2><?php echo Yii::t('lazy8','Select template'); ?></h2>

<?php if (isset($_GET['saved'])) { ?>
	<div class="infoSummary"><p><?php echo Yii::t('lazy8','Saved the transaction').' ; ' . $_GET['saved']; ?></p></div>	
<?php }  if (isset($_GET['added'])) { ?>
	<div class="infoSummary"><p><?php echo Yii::t('lazy8','Added the transaction').' ; ' . $_GET['added']; ?></p></div>	
<?php }  
	echo CHtml::errorSummary($models); 
	$searchlist=array();
	foreach($models as $n=>$model){
		$searchlist[]=array(
			'id'=>$model->id,
			'value'=>$model->name,
			'label'=>$model->name,
			);
		
	}

	?><div class="yiiForm"><div style="margin-bottom:900px;   text-align:center;"><?php
$form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl('template/useTemplate/selecttemplate'),
        'method'=>'post',
));
?><div class="simple"><?php
echo Yii::t('lazy8','Select a template directly or enter a few letters of the name of the template you want');
?></div><?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'juilist',
    'source'=>$searchlist,
    'value'=>'',
    // additional javascript options( for the autocomplete plugin
    'options'=>array(
        'minLength'=>'0',
        'select' => 'js:function(event, ui){document.getElementById("selectedid").value=ui.item.id;$(this).parents("form").submit(); }'
    ),
));
echo CHtml::hiddenField("selectedid","");
$this->endWidget();
?>
</div></div>
<script type="text/javascript">
/*<![CDATA[*/
window.onload=showwholelist;
function showwholelist(){
jQuery('#juilist').autocomplete( "search" , '' );
document.getElementById("juilist").focus();
}
/*]]>*/
</script>
