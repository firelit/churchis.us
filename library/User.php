<?php

class User extends Firelit\DatabaseObject {
	
	protected static $tableName = 'users'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	/**
	 *	Check the password to see if it is valid.
	 *	
	 *	@param String $password The password to validate.
	 *	@return Bool True if the password matches.
	 */
	public function validatePassword($password) {

		$hash = static::hashPassword($password);

		return ($this->password === $hash);

	}

	/**
	 *	Set the user's password.
	 *
	 *	@param String $password The new password to use.
	 */
	public function setPassword($password) {

		$this->service = 'LOCAL';
		$this->password = static::hashPassword($password);

	}

	/**
	 *	Hashes the password for storage.
	 *
	 *	@param String $password The password to hash.
	 *	@return String The hashed password.
	 */
	public static function hashPassword($password) {

		if (strlen($password) == 0) return null;

		if (empty($_SERVER['PASS_HASH_SALT']))
			throw new Exception('No password salt defined');

		$hash = $password;

		for ($i = 0; $i < 25; $i++) {
			$hash = hash('sha256', $_SERVER['PASS_HASH_SALT'] . $hash, false);
		}

		return $hash;

	}

	/**
	 *	Grant this user access to a group.
	 *
	 *	@param Int $groupId The group to grant this user access to.
	 */
	public function grantGroupAccess($groupId) {

		$sql = "INSERT IGNORE INTO `users_groups` (`user_id`, `group_id`) VALUES (:user_id, :group_id)";
		$q = new Firelit\Query($sql, array(
			':user_id' => $this->id,
			':group_id' => $groupId
		));

	}

	/**
	 *	Revoke this user access to a group.
	 *
	 *	@param Int $groupId The group to revoke access from.
	 */
	public function revokeGroupAccess($groupId) {

		$sql = "DELETE FROM `users_groups` WHERE `user_id`=:user_id AND `group_id`=:group_id";
		$q = new Firelit\Query($sql, array(
			':user_id' => $this->id,
			':group_id' => $groupId
		));

	}

	/**
	 *	Get the groups this user has access to.
	 *
	 *	@param Bool $idsOnly True to return only the group IDs, else the group objects
	 *	@return Array An array of Group objects that this user has access to.
	 */
	public function getGroupAccess($idsOnly = false) {

		$sql = "SELECT `groups`.* FROM `groups` INNER JOIN `users_groups` ON `groups`.`id`=`users_groups`.`group_id` WHERE `users_groups`.`user_id`=:user_id";
		$q = new Firelit\Query($sql, array(':user_id' => $this->id));

		$groups = array();

		while ($group = $q->getObject('Group'))
			if ($idsOnly) $groups[] = $group->id;
			else $groups[] = $group;
		
		return $groups;

	}

	/**
	 *	Find a user by email address.
	 *
	 *	@param String $email The email address to look for.
	 *	@return User The User object for the user that matches (or false)
	 */
	public static function findByEmail($email) {

		$sql = "SELECT * FROM `users` WHERE `email`=:email LIMIT 1";
		$q = new Firelit\Query($sql, array(':email' => $email));

		return $q->getObject('User');
		
	}

	/**
	 *	Return an array of this user's data.
	 *
	 *	@return Array An associative array of user data.
	 */
	public function getArray() {

		return array(
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'status' => $this->status,
			'role' => $this->role,
			'service' => $this->service,
			'created' => $this->created
		);
		
	}

}