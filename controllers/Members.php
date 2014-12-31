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

	public function view($id, $member = false) {

		if (!$member)
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

	public function edit($id) {

		$member = Member::find($id);

		if (!$member) 
			throw new Firelit\RouteToError(404, 'Member not found.');

		if ($this->user->role != 'ADMIN') {

			$groups = $member->getGroups();
			$match = false;
			
			foreach ($this->okGroups as $groupId) {
				foreach ($groups as $group) {
					if ($groupId == $group->id) {
						$match = true;
						break 2;
					}
				}
			}

			if (!$match)
				throw new Firelit\RouteToError(400, 'Not authorized to edit this member.');

		}

		$request = Firelit\Request::init();

		$member->name = trim($request->put['name']);
		$member->email = trim($request->put['email']);

		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $member->email);
		if (!$iv->isValid()) $member->email = null;

		$member->phone = trim($request->put['phone']);
		if (empty($member->phone)) $member->phone = null;

		$member->address = trim($request->put['address']);
		if (empty($member->address)) $member->address = null;

		$member->city = trim($request->put['city']);
		if (empty($member->city)) $member->city = null;

		$member->state = trim($request->put['state']);
		if (empty($member->state)) $member->state = null;

		$member->zip = trim($request->put['zip']);
		if (empty($member->zip)) $member->zip = null;

		$member->child_care = intval($request->put['child_care']);
		$member->contact_pref = $request->put['contact_pref'];

		$member->save();

		$this->view($id, $member);

	}

	public function create() {

		$request = Firelit\Request::init();

		$group = $request->post['group'];

		if (!preg_match('/^\d+$/', $group))
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		$group = Group::find($group);

		if (!$group)
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->user->getGroups())) 
			throw new Firelit\RouteToError(400, 'Access to group forbidden.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'Group is not part of a valid semester.');

		$name = trim($request->post['name']);

		if (strlen($name) < 2) 
			throw new Firelit\RouteToError(400, 'The name must be at least 2 characters long.');

		$email = strtolower(trim($request->post['email']));

		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $email);
		if (!$iv->isValid()) $email = null;
		
		$phone = trim($request->post['phone']);
		if (empty($phone)) $phone = null;

		$member = Member::create(array(
			'semester_id' => $semester->id,
			'name' => $name,
			'email' => $email,
			'phone' => $phone,
			'contact_pref' => 'EITHER'
		));

		$this->view($member->id, $member);

	}

}