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
 $this->pageTitle=Yii::app()->name; 
if(is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php')){
 ?>
 <p style="color:red">
The file <?php echo realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../config/main.php');  ?> is
writeable.  This should be changed to read-only..
</p>
 <?php
 }
 ?>
<h1>
	<?php echo Yii::t("lazy8",'Welcome to Lazy8Web!'); ?>
</h1>
<?php foreach(User::$Version as $version): ?>
<p>Version <?php echo $version ?></p>
<?php endforeach; ?>
<p>
<?php 
if(!Yii::app()->user->isGuest){ 
 echo Yii::t("lazy8",'welcome.message.first.page');  
}else{ 
 echo Yii::t("lazy8",'welcome.message.login');  
} 
if($models!=null){
?>
<br/><br/>
<?php echo Yii::t('lazy8','Users who could possibly be using this application now'); ?>
<br/>
<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('username'); ?></th>
    <th><?php echo $sort->link('displayname'); ?></th>
    <th><?php echo $sort->link('dateLastLogin'); ?></th>
  </tr>
  </thead>
  <tbody>
<?php	foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::encode($model->username); ?></td>
    <td><?php echo CHtml::encode($model->displayname); ?></td>
    <td><?php echo CHtml::encode($model->dateLastLogin); ?></td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>




<?php 
}
?>



</p>
