<?php

class Members extends APIController {
	
	private $session, $user, $okGroups;

	public function __construct() {
		
		parent::__construct();

		$this->session = Firelit\Session::init();
		$this->user = User::find($this->session->userId);

		if ($this->user->role != 'ADMIN')
			$this->okGroups = $this->user->getGroups();
		else 
			$this->okGroups = array();

		$groupIds = array();

		// Now let's extract group IDs
		foreach ($this->okGroups as $group)
			$groupIds[] = $group->id;

		$this->okGroups = $groupIds;

	}

	public function viewAll() {

		$semester = Semester::latestOpen();

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		if ($this->user->role != 'ADMIN') {

			if (!sizeof($this->okGroups)) $this->okGroups[] = 0;

			$sql = "SELECT `members`.* FROM `members` INNER JOIN `groups_members` ON `groups_members`.`member_id`=`members`.`id` WHERE `members`.`semester_id`=:semester_id AND `groups_members`.`group_id` IN (". implode(',', $this->okGroups) .") ORDER BY `name`, `email`, `created` ASC";
		
		} else {

			$sql = "SELECT * FROM `members` WHERE `semester_id`=:semester_id ORDER BY `name`, `email`, `created` ASC";

		}

		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$members = array();

		while ($member = $q->getObject('Member')) {

			$members[] = $member->getArray();

		}

		$this->response->respond($members);

	}

	public function view($id) {

		$member = Member::find($id);

		if (!$member) 
			throw new Firelit\RouteToError(404, 'Member not found.');

		$groups = $member->getGroups();
		$groupsReturn = array();

		foreach ($groups as $group) {

			if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
				continue;

			$groupsReturn[] = $group->getArray();

		}

		if (($this->user->role != 'ADMIN') && !sizeof($groupsReturn)) 
			throw new Firelit\RouteToError(400, 'Access to member forbidden.');

		$return = $member->getArray();
		$return['groups'] = $groupsReturn;

		$this->response->respond($return);

	}
}