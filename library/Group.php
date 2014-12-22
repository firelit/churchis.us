<?php

class Group extends Firelit\DatabaseObject {
	
	protected static $tableName = 'groups'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array('data'); // Columns that should be automatically JSON-encoded/decoded when using

	public function getMemberCount() {

		$sql = "SELECT COUNT(*) AS `count` FROM `groups_members` WHERE `group_id`=:group_id";
		$q = new Firelit\Query($sql, array(':group_id' => $this->id));

		$count = $q->getRow();
		return $count['count'];

	}

	public function getMembers() {

		$sql = "SELECT `members`.* FROM `members` INNER JOIN `groups_members` ON `members`.`id`=`groups_members`.`member_id` WHERE `groups_members`.`group_id`=:group_id";
		$q = new Firelit\Query($sql, array(':group_id' => $this->id));

		$members = array();

		while ($member = $q->getObject('Member'))
			$members[] = $member;
		
		return $members;

	}

}