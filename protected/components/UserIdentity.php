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


/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	private $_id;
	private $_salt;
	public function getId(){
		return $this->_id;
	}
	 

	public function authenticate()
	{
		$user=User::model()->find('username=:name',array(':name'=>$this->username));
		if($user===null)
		    $this->errorCode=self::ERROR_USERNAME_INVALID;
		//the password can also be completely unencrypted in the case where the administrator forgets his password.
		//Just poke a short text (or blank) into the database field password.
		else if(hash('sha1',$this->password . $user->salt)!==$user->password && ($this->password!==$user->password || strlen($user->password)>30))
		    $this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
		{
		    $this->username=$user->username;
		    $this->_id=$user->id;
		    $this->_salt=$user->salt;
		    $user->setStates(true);
		    $this->errorCode=self::ERROR_NONE;
		    $user->updateThisLogInDatetime();
		}
		return !$this->errorCode;
	}
}