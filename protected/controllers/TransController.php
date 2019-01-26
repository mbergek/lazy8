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


class TransController extends CController
{
	

	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='admin';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('create','admin','restore'),
				'expression'=>'Yii::app()->user->getState(\'allowTrans\') && Yii::app()->user->getState(\'selectedCompanyId\') !=0',
			),
			array('allow', 
				'actions'=>array('update'),
				'expression'=>'(Yii::app()->user->getState(\'allowTrans\')||Yii::app()->user->getState(\'allowReports\')) && Yii::app()->user->getState(\'selectedCompanyId\') !=0',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	private function renderLocale($screen,$loadTransValue=null){
		$models=$this->loadTrans($loadTransValue);
		//show the report
		$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
		$numberFormatter=$cLoc->getNumberFormatter();
		$numberFormat=User::getNumberFormat();
		$previous="";
		$next="";
			//show the report
		if($screen=='update'){
			$trans=Trans::model()->findbyPk($_GET['id']);
			$transprevious=Trans::model()->find(
				array('condition'=>'companyId='	.Yii::app()->user->getState('selectedCompanyId') .
				' AND companyNum=' . ($trans->companyNum-1)));
			$transnext=Trans::model()->find(
				array('condition'=>'companyId='	.Yii::app()->user->getState('selectedCompanyId') .
				' AND companyNum=' . ($trans->companyNum+1)));

			if($transprevious!=null)$previous=$transprevious->id;
			if($transnext!=null)$next=$transnext->id;
		}
		$this->render($screen,array('models'=>$models,
				'Locale'=>$cLoc,
				'numberFormatter'=>$numberFormatter,
				'numberFormat'=>$numberFormat,
				'previous'=>$previous,
				'next'=>$next,
				));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionCreate()
	{
		$this->renderLocale('create');
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionRestore()
	{
		$this->renderLocale('create',!isset($_POST['TempTrans']));
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionUpdate()
	{
		if(Yii::app()->user->getState('allowTrans'))
			$this->renderLocale('update');
		else
			$this->renderLocale('view');
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;
		$criteria->addSearchCondition('companyId',Yii::app()->user->getState('selectedCompanyId'));
		$criteria->addSearchCondition('periodId',Yii::app()->user->getState('selectedPeriodId'));

		$pages=new CPagination(Trans::model()->count($criteria));
		$pages->pageSize=Yii::app()->user->getState('NumberRecordsPerPage');
		$pages->applyLimit($criteria);

		$sort=new CSort('Trans');
		$sort->applyOrder($criteria);

		$models=Trans::model()->findAll($criteria);
		
		$trans=TempTrans::model()->findAll(array('condition'=>'userId='.Yii::app()->user->id,'order'=>'rownum'));
		$existsOldTrans=false;
		if($trans!==null){
			$existsOldTrans=count($trans)>1 
				|| (isset($trans[0]) && strlen($trans[0]->notesheader)>0 )
				|| (isset($trans[0]) && strlen($trans[0]->amountdebit)>0 )
				|| (isset($trans[0]) && strlen($trans[0]->amountcredit)>0 )
				|| (isset($trans[0]) && strlen($trans[0]->fileInfo)>0 )
				|| (isset($trans[0]) && strlen($trans[0]->notes)>0 )
				|| (isset($trans[0]) && $trans[0]->customerId!=0);
		}
		$this->render('admin',array(
			'models'=>$models,
			'pages'=>$pages,
			'sort'=>$sort,
			'existsOldTrans'=>$existsOldTrans,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	private function loadTrans($restore=false)
	{
		$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
		$numberFormatter=$cLoc->getNumberFormatter();
		$numberFormat=User::getNumberFormat();
		$dateformatter=new CDateFormatter($cLoc);
		$models=null;
		if($restore){
			$models=$this->getFromTempTrans();
		}elseif(isset($_POST['AddRow'])){
			$models=$this->getFromTempTrans();
			$models[]=$this->getBlankTempTrans(count($models));
		}elseif(isset($_POST['Update'])){
			//same as restore
			$models=$this->getFromTempTrans();
		}elseif(isset($_POST['Save'])){
			if($this->isValidTempTrans(false,$models)){
				if(Yii::app()->user->getState('allowReEditingOfTransactions')){
					$trans=Trans::model()->findbyPk($_GET['id']);
					$modelBeforeChange=$trans->toString();
					//delete the old trans
					$trans->delete();//must delete first to be able to use the transnums
					//copy temp trans to a new Trans/TransRow
					$tempdate=User::parseDate($models[0]->invDate);
					$changedTrans=$this->copyTempTransToNewTrans($trans->periodNum,$trans->companyNum);
					//clear temptrans
					Trans::model()->dbConnection->createCommand("DELETE FROM TempTrans WHERE userId=". Yii::app()->user->id)->execute();
					
					$models=array();
					$models[]=$this->getBlankTempTrans(count($models),$tempdate);
					$models[]=$this->getBlankTempTrans(count($models),$tempdate);
					$models[]=$this->getBlankTempTrans(count($models),$tempdate);
					$models[]=$this->getBlankTempTrans(count($models),$tempdate);
					if(Yii::app()->user->getState('showPeriodTransactionNumber'))
						$transnum=$trans->periodNum;
					else
						$transnum=$trans->companyNum;
					$stringModel=$changedTrans->toString();
					if ($modelBeforeChange!=$stringModel)
						ChangeLog::addLog('UPDATE','Trans','BEFORE<br />' . $modelBeforeChange . '<br />AFTER<br />' . $stringModel);
					$this->redirect(array('create','saved'=>$transnum));
				}else
				{
					$models[0]->addError('[0]amountcredit',Yii::t('lazy8','You are not allowed to change any transactions. See company options.'));
				}
			}else{
				//there are errors. Restore the old
			}
		}elseif(isset($_POST['Add'])){
			if($this->isValidTempTrans(true,$models)){
				$comp=Company::model()->findbyPk(Yii::app()->user->getState('selectedCompanyId'));
				$per=Period::model()->findbyPk(Yii::app()->user->getState('selectedPeriodId'));
				$comp->lastAbsTransNum++;
				$per->lastPeriodTransNum++;
				//save the new transnums
				$comp->save();
				$per->save();
				//copy temp trans to a new Trans/TransRow
				$tempdate=User::parseDate($models[0]->invDate);
				$addedTrans=$this->copyTempTransToNewTrans($per->lastPeriodTransNum,$comp->lastAbsTransNum,true);
				ChangeLog::addLog('ADD','Trans',$addedTrans->toString());
				//clear temptrans
				
				Trans::model()->dbConnection->createCommand("DELETE FROM TempTrans WHERE userId=". Yii::app()->user->id)->execute();
				$models=array();
				$models[]=$this->getBlankTempTrans(count($models),$tempdate);
				$models[]=$this->getBlankTempTrans(count($models),$tempdate);
				$models[]=$this->getBlankTempTrans(count($models),$tempdate);
				$models[]=$this->getBlankTempTrans(count($models),$tempdate);
				if(Yii::app()->user->getState('showPeriodTransactionNumber'))
					$transnum=$per->lastPeriodTransNum;
				else
					$transnum=$per->lastPeriodTransNum;
				$this->redirect(array('create','added'=>$transnum));
			}else{
				//there are errors. Restore the old
			}
		}elseif(isset($_POST['Clear'])){
			Trans::model()->dbConnection->createCommand("DELETE FROM TempTrans WHERE userId=". Yii::app()->user->id)->execute();
			$models=array();
			$models[]=$this->getBlankTempTrans(count($models));
			$models[]=$this->getBlankTempTrans(count($models));
			$models[]=$this->getBlankTempTrans(count($models));
			$models[]=$this->getBlankTempTrans(count($models));
			$this->redirect(array('create'));
		}elseif(isset($_POST['deleterow'])){
			$models=$this->getFromTempTrans();
			if(count($models)>1){
				$deletes=$_POST['deleterow'];
				//there is only one item in this array, but I don't know 
				//any other way to get at it without doing this..
				foreach($deletes as $key=>$transrow){
					$models[$key]->delete();
					unset($models[$key]);
					break;
				}
				//must remove the hole in the array
				$newmodels=array();
				foreach($models as $transrow){
					$newmodels[]=$transrow;
				}
				$models=$newmodels;
			}
		}elseif(isset($_POST['balancerow'])){
			$models=$this->getFromTempTrans();
			if(count($models)>1){
				$balancerow=$_POST['balancerow'];
				//there is only one item in this array, but I don't know 
				//any other way to get at it without doing this..
				foreach($balancerow as $key=>$transrow){
					$balancerow= $key;
					break;
				}
				//Need to get the current balance on all but the $balancerow row
				$credit=0.0;
				$debit=0.0;
				foreach($models as $key=>$model){
					if($key!=$balancerow){
						$amountdebit=$this->parseNumber($model->amountdebit,$cLoc);
						$amountcredit=$this->parseNumber($model->amountcredit,$cLoc);
						$debit+=$amountdebit;
						$credit+=$amountcredit;
					}
				}
				if($debit>$credit){
					$models[$balancerow]->amountcredit=round( $debit-$credit,6);
					$models[$balancerow]->amountdebit='';
				}else{
					$models[$balancerow]->amountdebit=round( $credit-$debit,6);
					$models[$balancerow]->amountcredit='';
				}
				$models[$balancerow]->save();
				$models[$balancerow]->amountdebit=$this->formatNumber($models[$balancerow]->amountdebit,$numberFormatter,$numberFormat);
				$models[$balancerow]->amountcredit=$this->formatNumber($models[$balancerow]->amountcredit,$numberFormatter,$numberFormat);
			}
		}elseif(isset($_GET['id'])){
			//copy from the Trans table to the TempTrans the selected transaction
			$models=array();
			//first clear the TempTrans table.
			Trans::model()->dbConnection->createCommand("DELETE FROM TempTrans WHERE userId=". Yii::app()->user->id)->execute();
			$trans=Trans::model()->findbyPk($_GET['id']);
			// I dont want to just get the relation amounts because I want the amounts to be in the order of
			// account code and that is impossible with the amounts relation
			//$transRows=$trans->amounts;
			$command=Report::model()->dbConnection->createCommand("SELECT TransRow.* FROM TransRow JOIN Account on TransRow.accountId=Account.id WHERE TransRow.transId=".$_GET['id']." ORDER BY Account.code");
			$transRows=$command->query();
			$rownum=0;
			$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
			$numberFormatter=$cLoc->getNumberFormatter();
			$numberFormat=User::getNumberFormat();
			$dateformatter=new CDateFormatter($cLoc);
			foreach($transRows as $transrow){
				$model=new TempTrans;
				$model->rownum=$rownum;
				$rownum++;
				$model->invDate=$trans->invDate;
				$model->regDate=$trans->regDate;
				$model->periodNum=$trans->periodNum;
				$model->companyNum=$trans->companyNum;
				$model->notesheader=$trans->notes;
				$model->fileInfo=$trans->fileInfo;
				$model->changedBy=$trans->changedBy;
				$model->dateChanged=$trans->dateChanged;
				
				$model->accountId=$transrow['accountId'];
				$model->customerId=$transrow['customerId'];
				$model->notes=$transrow['notes'];
				$model->amountdebit=$transrow['amount']>0?$transrow['amount']:'';
				$model->amountcredit=$transrow['amount']<0?-$transrow['amount']:'';
				$model->userId=Yii::app()->user->id;
				$model->save();
				$model->amountdebit=$this->formatNumber($model->amountdebit,$numberFormatter,$numberFormat);
				$model->amountcredit=$this->formatNumber($model->amountcredit,$numberFormatter,$numberFormat);
				$model->invDate=User::getDateFormatted($trans->invDate,$cLoc,$dateformatter);
				$model->regDate=User::getDateFormatted($trans->regDate,$cLoc,$dateformatter);
				$model->dateChanged=User::getDateFormatted($trans->dateChanged,$cLoc,$dateformatter);
				$models[]=$model; 
				
			}
			if($rownum==0)
			{
				$models[]=$this->getBlankTempTrans(0,$trans->invDate);
			}
				
		}else{
			//same thing as Clear
			$models=$this->getFromTempTrans();
			$tempdate=date('Y-m-d H:i:s');
			if($models!=null && count($models)>0)
				$tempdate=User::parseDate($models[0]->invDate);
			//echo $tempdate;die();
			Trans::model()->dbConnection->createCommand("DELETE FROM TempTrans WHERE userId=". Yii::app()->user->id)->execute();
			$models=array();
			$models[]=$this->getBlankTempTrans(count($models),$tempdate);
			$models[]=$this->getBlankTempTrans(count($models),$tempdate);
			$models[]=$this->getBlankTempTrans(count($models),$tempdate);
			$models[]=$this->getBlankTempTrans(count($models),$tempdate);
		}
		return $models;
	}
	private function isValidTempTrans($findNewTransNums,&$models)
	{
		$valid=true;
		$models=$this->getFromTempTrans();
		$comp=Company::model()->findbyPk(Yii::app()->user->getState('selectedCompanyId'));
		$per=Period::model()->findbyPk(Yii::app()->user->getState('selectedPeriodId'));
		if(!isset($comp)){
			$models[0]->addError('[0]invDate',Yii::t('lazy8','Currently selected company is invalid. Select a new company/period and try again.'));
			$valid=false;
		}
		if(!isset($per)){
			$models[0]->addError('[0]invDate',Yii::t('lazy8','Currently selected period is invalid. Select a new company/period and try again.'));
			$valid=false;
		}
		if($findNewTransNums){
			$startTransNum=$comp->lastAbsTransNum;
			//make sure the transnums are valid. Change if not.
			do{
				$startTransNum++;
				$comptest=Trans::model()->find(
					array('condition'=>'companyId='	.Yii::app()->user->getState('selectedCompanyId') .
					' AND companyNum=' . $startTransNum));
			}while($comptest!==null);
			if(($startTransNum-1)!=$comp->lastAbsTransNum){
				//the present number was invalid. Save the new number
				$comp->lastAbsTransNum=$startTransNum-1;
				$comp->save();
			}
			//check the periodnum
			$startTransNum=$per->lastPeriodTransNum;
			//make sure the transnums are valid. Change if not.
			do{
				$startTransNum++;
				$comptest=Trans::model()->find(
					array('condition'=>'companyId='	.Yii::app()->user->getState('selectedCompanyId') .
					' AND periodNum=' . $startTransNum . ' AND periodId='.Yii::app()->user->getState('selectedPeriodId')));
			}while($comptest!==null);
			if(($startTransNum-1)!=$per->lastPeriodTransNum){
				//the present number was invalid. Save the new number
				$per->lastPeriodTransNum=$startTransNum-1;
				$per->save();
			}
		}
		//invoice date must be correct
		$invDate=User::parseDate($models[0]->invDate);
		if(strtotime($invDate)<strtotime($per->dateStart) || strtotime($invDate)>strtotime($per->dateEnd)){
			$models[0]->addError('[0]invDate',Yii::t('lazy8','The invoice date is not within the allowed range')
				.' ; '.$models[0]->invDate.'>='.User::getDateFormatted($per->dateStart). ' AND ' .$models[0]->invDate.'<=' .User::getDateFormatted($per->dateEnd));
			$valid=false;
		}
		$creditsum=0;
		$debitsum=0;
		$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
		foreach($models as $model)
		{
			$amountdebit=$this->parseNumber($model->amountdebit,$cLoc);
			$amountcredit=$this->parseNumber($model->amountcredit,$cLoc);
			$creditsum+=$amountcredit;
			$debitsum+=$amountdebit;
		}
		if(bccomp($creditsum,$debitsum,5)!=0){
			$models[0]->addError('[0]amountcredit',Yii::t('lazy8','The sum of the credits and debits are not equal'));
			$valid=false;
		}
		/*if(!$valid){
			print_r($models[0]->getErrors());
			die();
		}*/

		return $valid;
	}
	private function copyTempTransToNewTrans($periodNum,$companyNum,$isUpdateRegDate=false)
	{
			//everything is ok.  Make the new transaction
		$models=$this->getFromTempTrans();
		$trans=new Trans();
		$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
		$trans->invDate=User::parseDate($models[0]->invDate,$cLoc);
		if($isUpdateRegDate)
			$trans->regDate=date('Y-m-d');
		else
			$trans->regDate=User::parseDate($models[0]->regDate,$cLoc);
		$trans->periodNum=$periodNum;
		$trans->companyNum=$companyNum;
		$trans->notes=$models[0]->notesheader;
		$trans->fileInfo=$models[0]->fileInfo;
		$trans->companyId=Yii::app()->user->getState('selectedCompanyId');
		$trans->periodId=Yii::app()->user->getState('selectedPeriodId');
		
		$trans->save();
		foreach($models as $model){
			$amountdebit=$this->parseNumber($model->amountdebit,$cLoc);
			$amountcredit=$this->parseNumber($model->amountcredit,$cLoc);
			if($amountdebit!=0 || $amountcredit!=0){
				$transrow=new TransRow();
				$transrow->transId=$trans->id;
				$transrow->accountId=$model->accountId;
				$transrow->customerId=$model->customerId;
				$transrow->notes=$model->notes;
				$transrow->amount=$amountdebit>0?$amountdebit:-$amountcredit;
				$transrow->save();
			}
		}
		return $trans;
	}
	private function getFromTempTrans()
	{
		$trans=TempTrans::model()->findAll(array('condition'=>'userId='.Yii::app()->user->id,'order'=>'rownum'));
		if($trans!==null){
			$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
			$numberFormatter=$cLoc->getNumberFormatter();
			$dateformatter=$cLoc->getDateFormatter();
			$numberFormat=User::getNumberFormat();
			$models=array();
			//make sure the data is current
			$comp=Company::model()->findbyPk(Yii::app()->user->getState('selectedCompanyId'));
			$per=Period::model()->findbyPk(Yii::app()->user->getState('selectedPeriodId'));
			foreach($trans as $transrow){
				//see if there is new data to be added..
				if(isset($_POST['TempTrans'][$transrow->rownum]) && $_POST['TempTrans'][$transrow->rownum]){
					//make sure only one of debit or credit are filled
					$olddebit=$this->parseNumber($transrow->amountdebit,$cLoc);
					$transrow->attributes=$_POST['TempTrans'][$transrow->rownum];
					$amountdebit=$this->parseNumber($transrow->amountdebit,$cLoc);
					$amountcredit=$this->parseNumber($transrow->amountcredit,$cLoc);
					$transrow->invDate=User::parseDate($transrow->invDate,$cLoc);
					if($amountdebit!=0 and $amountcredit!=0){
						//keep only the newly added amount
						if($olddebit!='')
							$transrow->amountdebit='';
						else
							$transrow->amountcredit='';
					}
				}
				$amountdebit=$this->parseNumber($transrow->amountdebit,$cLoc);
				$amountcredit=$this->parseNumber($transrow->amountcredit,$cLoc);
				//amount must be positive at this stage
				if($amountdebit<0){
					$amountdebit=-$amountdebit;
				}
				if($amountcredit<0){
					$amountcredit=-$amountcredit;
				}
				//this gets rid of extra zeros
				if(strlen($amountcredit)>0)
					$amountcredit+=0.0;
				if(strlen($amountdebit)>0)
					$amountdebit+=0.0;
				if(!isset($_GET['id'])){
					$transrow->regDate=date('Y-m-d');
					$transrow->periodNum=$per->lastPeriodTransNum+1;
					$transrow->companyNum=$comp->lastAbsTransNum+1;
				}
				$transrow->amountdebit=$amountdebit;
				$transrow->amountcredit=$amountcredit;
				$transrow->save();
				$transrow->amountdebit=$this->formatNumber($transrow->amountdebit,$numberFormatter,$numberFormat);
				$transrow->amountcredit=$this->formatNumber($transrow->amountcredit,$numberFormatter,$numberFormat);
				$transrow->invDate=User::getDateFormatted($transrow->invDate,$cLoc,$dateformatter);
				$transrow->regDate=User::getDateFormatted($transrow->regDate,$cLoc,$dateformatter);
				$transrow->dateChanged=User::getDateFormatted($transrow->dateChanged,$cLoc,$dateformatter);
			}
			//return what was found in the temptrans table.
			$models=$trans;
		}else{
			//we really should never come here, but just in case...
			$models=$this->getBlankTempTrans(0);
		}
		return $models;
	}
	private function getBlankTempTrans($rownum,$invDate=null)
	{
		$comp=Company::model()->findbyPk(Yii::app()->user->getState('selectedCompanyId'));
		$per=Period::model()->findbyPk(Yii::app()->user->getState('selectedPeriodId'));
		$model=new TempTrans();
		$model->rownum=$rownum;
		$model->regDate=date('Y-m-d');
		if($invDate==null)$invDate=$model->regDate;
		
		$model->invDate=$invDate;
		
		$model->changedBy=Yii::app()->user->getState('displayname');
		$model->dateChanged=$model->regDate;
		$model->periodNum=$per->lastPeriodTransNum+1;
		$model->companyNum=$comp->lastAbsTransNum+1;
		
		$model->notesheader='';
		$model->fileInfo='';
		
		$model->accountId=0;
		$model->customerId=0;
		$model->notes='';
		$model->amountdebit="";
		$model->amountcredit="";
		$model->userId=Yii::app()->user->id;
		$model->save();
		$cLoc=CLocale::getInstance(Yii::app()->user->getState('languagecode'));
		$model->invDate=User::getDateFormatted($model->invDate,$cLoc);
		$model->regDate=User::getDateFormatted($model->regDate,$cLoc);
		$model->dateChanged=$model->regDate;
		return $model; 
	}
	/**
	 * Executes any command triggered on the admin page.
	 */
	protected function processAdminCommand()
	{
		if(isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete')
		{
			$trans=Trans::model()->findbyPk($_POST['id']);
			ChangeLog::addLog('DELETE','Trans',$trans->toString());			
			$trans->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}
	public static function parseNumber($num,$Locale)
	{
		$result=preg_replace('/[^0-9a-zA-Z' . $Locale->getNumberSymbol('decimal') . '\-−]/','',$num);
		//if(strlen($result)>4){echo $result. ";".$Locale->getNumberSymbol('decimal').";".str_replace($Locale->getNumberSymbol('decimal'),'.',$result);die();}
		return str_replace($Locale->getNumberSymbol('decimal'),'.',$result);
	}
	public static function formatNumber($num,$numberFormatter,$numberFormat){
		$display=$num;
		if(round($display,5)==0.0)
			$display="";
		else
			$display=$numberFormatter->format($numberFormat,$display);
		return $display;
	}
}
