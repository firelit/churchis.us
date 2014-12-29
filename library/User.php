<?php

class User extends Firelit\DatabaseObject {
	
	protected static $tableName = 'users'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	public function validatePassword($password) {

		$hash = static::hashPassword($password);

		return ($this->password === $hash);

	}

	public function setPassword($password) {

		$this->service = 'LOCAL';
		$this->password = static::hashPassword($password);

	}

	public static function hashPassword($password) {

		if (strlen($password) == 0) return null;

		$hash = $password;

		for ($i = 0; $i < 25; $i++) {
			$hash = sha1('sha256', $_SERVER['PASS_HASH_SALT'] . $hash);
		}

		return $hash;

	}

	public function getGroupIds() {

		$sql = "SELECT `group_id` FROM `users_groups` WHERE `user_id`=:user_id ORDER BY `group_id`";
		$q = new Firelit\Query($sql, array(':user_id' => $this->id));

		$groups = array();

		while ($row = $q->getRow())
			$groups[] = $row['group_id'];

		return $groups;

	}

	public static function findByEmail($email) {

		$sql = "SELECT * FROM `users` WHERE `email`=:email LIMIT 1";
		$q = new Firelit\Query($sql, array(':email' => $email));

		return $q->getObject('User');
		
	}

}