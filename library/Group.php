<?php

class Group extends Firelit\DatabaseObject {

	protected static $tableName = 'groups'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array('data'); // Columns that should be automatically JSON-encoded/decoded when using

	/**
	 *	Get the number of members in group
	 *	@return Int count
	 */
	public function getMemberCount() {

		$sql = "SELECT COUNT(*) AS `count` FROM `groups_members` WHERE `group_id`=:group_id";
		$q = new Firelit\Query($sql, array(':group_id' => $this->id));

		$count = $q->getRow();
		return $count['count'];

	}

	/**
	 *	Get all members of the group
	 *	@return Firelit\QueryIterator object with iteratable result set
	 */
	public function getMembers() {

		$sql = "SELECT `members`.* FROM `members` INNER JOIN `groups_members` ON `members`.`id`=`groups_members`.`member_id` WHERE `groups_members`.`group_id`=:group_id ORDER BY `members`.`name`";
		$q = new Firelit\Query($sql, array(':group_id' => $this->id));

		return new Firelit\QueryIterator($q, 'Member');

	}

	/**
	 *	Add this member to the group
	 *	@param Member $member
	 *	@param Bool $leader
	 */
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

		// Get the new count
		$count = $this->getMemberCount();

		// If adding this member puts us at max members, and the status is OPEN, fix status
		if (($this->status == 'OPEN') && !is_null($this->max_members) && ($count >= $this->max_members)) {

			$this->status = 'FULL';
			$this->save();

		}

		return $count;

	}

	/**
	 *	Remove this member from the group
	 *	@param Member $member
	 */
	public function removeMember(Member $member) {

		$sql = "DELETE FROM `groups_members` WHERE `group_id`=:group_id AND `member_id`=:member_id";
		$q = new Firelit\Query($sql, array(
			':group_id' => $this->id,
			':member_id' => $member->id
		));

		// If removing this member drops below max members, and the status is FULL, fix status
		if (($this->status == 'FULL') && !is_null($this->max_members) && (($this->getMemberCount() + 1) == $this->max_members)) {

			$this->status = 'OPEN';
			$this->save();

		}

	}

	/**
	 *	Return all recorded meetings for this group
	 *	@return Firelit\QueryIterator object with iteratable result set
	 */
	public function getMeetings() {

		$sql = "SELECT * FROM `meetings` WHERE `group_id`=:group_id ORDER BY `date` ASC";
		$q = new Firelit\Query($sql, array(
			':group_id' => $this->id
		));

		return new Firelit\QueryIterator($q, 'Meeting');

	}

	/**
	 *	Get this object's details in an associative array
	 *	@return Array
	 */
	public function getArray() {

		$start = $this->data['start_date'];
		$end = $this->data['end_date'];
		if (!$start instanceof DateTime) $start = new DateTime($start['date']);
		if (!$end instanceof DateTime) $end = new DateTime($end['date']);

		return array(
			'id' => $this->id,
			'name' => $this->name,
			'author' => !empty($this->data['author']) ? $this->data['author'] : null,
			'status' => $this->status,
			'public_id' => $this->public_id,
			'description' => $this->description,
			'leader' => $this->data['leader'],
			'phone' => $this->data['phone'],
			'email' => $this->data['email'],
			'start_date' => $start->format('n/j/y'),
			'end_date' => $end->format('n/j/y'),
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