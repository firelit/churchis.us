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

		$sql = "SELECT `members`.* FROM `members` INNER JOIN `groups_members` ON `members`.`id`=`groups_members`.`member_id` WHERE `groups_members`.`group_id`=:group_id ORDER BY `members`.`name`";
		$q = new Firelit\Query($sql, array(':group_id' => $this->id));

		$members = array();

		while ($member = $q->getObject('Member'))
			$members[] = $member;
		
		return $members;

	}

	public function addMember(Member $member, $leader = false) {
		
		if ($member->semester_id != $this->semester_id) {
			// Member in wrong semster
			// Possible fix: 
			//		Clone the member and assign to this group? 
			//		Problem: Return the new member to caller?
			throw new Exception('Member and group semesters do not match');
		}

		$sql = "REPLACE INTO `groups_members` (`group_id`, `member_id`, `leader`) VALUES (:group_id, :member_id, :leader)";
		$q = new Firelit\Query($sql, array(
			':group_id' => $this->id,
			':member_id' => $member->id,
			':leader' => $leader
		));

		// If adding this member puts us at max members, and the status is OPEN, fix status
		if (($this->status == 'OPEN') && ($this->getMemberCount() >= $this->max_members)) {

			$this->status = 'FULL';
			$this->save();

		}

	}

	public function removeMember(Member $member) {
		
		$sql = "DELETE FROM `groups_members` WHERE `group_id`=:group_id AND `member_id`=:member_id";
		$q = new Firelit\Query($sql, array(
			':group_id' => $this->id,
			':member_id' => $member->id
		));

		// If removing this member drops below max members, and the status is FULL, fix status
		if (($this->status == 'FULL') && (($this->getMemberCount() + 1) == $this->max_members)) {

			$this->status = 'OPEN';
			$this->save();

		}

	}

	public function getArray() {
		
		return array(
			'id' => $this->id,
			'name' => $this->name,
			'status' => $this->status,
			'public_id' => $this->public_id,
			'description' => $this->description,
			'leader' => $this->data['leader'],
			'phone' => $this->data['phone'],
			'email' => $this->data['email'],
			'when' => (is_array($this->data['days']) ? implode(', ', $this->data['days']) : '') .' '. $this->data['time'],
			'days' => (is_array($this->data['days']) ? $this->data['days'] : array()),
			'time' => $this->data['time'],
			'where' => (!empty($this->data['location_short']) ? $this->data['location_short'] : $this->data['location']),
			'childcare' => ($this->data['childcare'] ? 'Provided' : 'Not available'),
			'gender' => $this->data['gender'],
			'demographic' => $this->data['demographic'],
			'cost' => ($this->data['cost'] ? 'Yes' : 'No'),
			'max_members' => $this->max_members,
			'full' => ($this->status == 'FULL')
		);

	}
}