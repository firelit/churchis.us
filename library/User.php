<?php

class User extends Firelit\DatabaseObject {
	
	protected static $tableName = 'users'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	public static function findByEmail($email) {

		$sql = "SELECT * FROM `users` WHERE `email`=:email LIMIT 1";
		$q = new Firelit\Query($sql, array(':email' => $email));

		return $q->getObject('User');
		
	}
}