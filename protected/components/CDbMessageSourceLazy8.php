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


class CDbMessageSourceLazy8 extends CDbMessageSource
{
	const CACHE_KEY_PREFIX='Yii.CDbMessageSource.';
	/**
	 * @var string the ID of the database connection application component. Defaults to 'db'.
	 */
	public $connectionID='db';
	/**
	 * @var string the name of the source message table. Defaults to 'SourceMessage'.
	 */
	public $sourceMessageTable='SourceMessage';
	/**
	 * @var string the name of the translated message table. Defaults to 'Message'.
	 */
	public $translatedMessageTable='Message';
	/**
	 * @var integer the time in seconds that the messages can remain valid in cache.
	 * Defaults to 0, meaning the caching is disabled.
	 */
	public $cachingDuration=0;
	/**
	 * @var string the ID of the cache application component that is used to cache the messages.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 * Set this property to false if you want to disable caching the messages.
	 * @since 1.0.10
	 */
	public $cacheID='cache';

	private $_db;
	private $_messages=array();
	/**
	 * Translates the specified message.
	 * If the message is not found, an {@link onMissingTranslation}
	 * event will be raised.
	 * @param string the category that the message belongs to
	 * @param string the message to be translated
	 * @param string the target language
	 * @return string the translated message
	 */
	protected function translateMessage($category,$message,$language)
	{
		$language=Yii::app()->user->getState('languagecode');
		//echo $key;die();
		$key=$language.'.'.$category;
		if(!isset($this->_messages[$key]))
			$this->_messages[$key]=$this->loadMessages($category,$language);
		if(isset($this->_messages[$key][$message]) && $this->_messages[$key][$message]!=='')
			return $this->_messages[$key][$message];
		else
			return $message;
	}
/*	protected function translateMessage( $category,  $message,  $language)
	{
		$language=Yii::app()->user->getState('languagecode');
		$trans=parent::translateMessage( $category,  $message,  $language);
		
		return $trans;
		//echo $.';'.  $message . ';'.  $language ;
		//die();

		$criteria=new CDbCriteria;
		$criteria->addSearchCondition('category',$category);
		$criteria->addSearchCondition('message',$message);
		$findtest=SourceMessage::model()->find($criteria);
		if($findtest===null){
			$maxId=1;
			$maxIdSearch=SourceMessage::model()->find(
				array('select'=>'MAX(id) as id,category,message'));
			if($maxIdSearch!==null)
				$maxId=	$maxIdSearch->id + 1;
			$findtest=new SourceMessage();
			$findtest->category=$category;
			$findtest->message=$message;
			$findtest->id=$maxId;
			$findtest->save();
		}
		return $trans;
	}
*/
	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by preprocessing
	 * the user request data.
	 */
	public function init()
	{
		parent::init();
		if(($this->_db=Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection)
			$this->_db->setActive(true);
		else
			throw new CException(Yii::t('yii','CDbMessageSource.connectionID is invalid. Please make sure "{id}" refers to a valid database application component.',
				array('{id}'=>$this->connectionID)));
	}

	/**
	 * Loads the message translation for the specified language and category.
	 * @param string the message category
	 * @param string the target language
	 * @return array the loaded messages
	 */
	protected function loadMessages($category,$language)
	{
		if($this->cachingDuration>0 && $this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
		{
			$key=self::CACHE_KEY_PREFIX.'.messages.'.$category.'.'.$language;
			if(($data=$cache->get($key))!==false)
				return unserialize($data);
		}

		$sql=<<<EOD
SELECT t1.message AS message, t2.translation AS translation
FROM {$this->sourceMessageTable} t1, {$this->translatedMessageTable} t2
WHERE t1.id=t2.id AND t1.category=:category AND t2.language=:language
EOD;
		$command=$this->_db->createCommand($sql);
		$command->bindValue(':category',$category);
		$command->bindValue(':language',$language);
		$rows=$command->queryAll();
		$messages=array();
		foreach($rows as $row)
			$messages[$row['message']]=$row['translation'];

		if(isset($cache))
			$cache->set($key,serialize($messages),$this->cachingDuration);

		return $messages;
	}
}