<?php

class Member extends Firelit\DatabaseObject {
	
	protected static $tableName = 'members'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array(); // Columns that should be automatically JSON-encoded/decoded when using

	public function getGroups() {

		$sql = "SELECT `groups`.* FROM `groups` INNER JOIN `groups_members` ON `groups`.`id`=`groups_members`.`group_id` WHERE `groups_members`.`member_id`=:members_id";
		$q = new Firelit\Query($sql, array(':members_id' => $this->id));

		$groups = array();

		while ($group = $q->getObject('Group'))
			$groups[] = $group;
		
		return $groups;

	}

}