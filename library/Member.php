<?php

class Member extends Firelit\DatabaseObject {
	
	protected static $tableName = 'members'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array(); // Columns that should be automatically JSON-encoded/decoded when using

	/**
	 *	Return all groups this member is a part of
	 *	@return Firelit\QueryIterator object with iteratable result set
	 */
	public function getGroups() {

		$sql = "SELECT `groups`.* FROM `groups` INNER JOIN `groups_members` ON `groups`.`id`=`groups_members`.`group_id` WHERE `groups_members`.`member_id`=:member_id";
		$q = new Firelit\Query($sql, array(':member_id' => $this->id));

		return new Firelit\QueryIterator($q, 'Group');

	}

	/**
	 *	Get this object's details in an associative array
	 *	@return Array
	 */
	public function getArray() {

		return array(
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'phone' => $this->phone,
			'address' => $this->address,
			'city' => $this->city,
			'state' => $this->state,
			'zip' => $this->zip,
			'contact_pref' => $this->contact_pref,
			'child_care' => $this->child_care,
			'created' => $this->created
		);
		
	}

}