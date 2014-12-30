<?php

class Groups extends APIController {
	
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

		$sql = "SELECT * FROM `groups` WHERE `semester_id`=:semester_id ORDER BY `public_id`, `name` ASC";
		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$groups = array();

		while ($group = $q->getObject('Group')) {

			if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
				continue;

			$groups[] = $group->getArray();

		}

		$this->response->respond($groups);

	}

	public function view($id, $group = false) {

		if (!$group)
			$group = Group::find($id);

		if (!$group) 
			throw new Firelit\RouteToError(404, 'Group not found.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Not authorized to view this group.');

		$members = $group->getMembers();
		$membersReturn = array();

		foreach ($members as $member) {

			$membersReturn[] = $member->getArray();

		}

		$return = $group->getArray();
		$return['members'] = $membersReturn;

		$this->response->respond($return);

	}

	public function edit($id) {

		$group = Group::find($id);

		if (!$group) 
			throw new Firelit\RouteToError(404, 'Group not found.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Not authorized to edit this group.');

		$request = Firelit\Request::init();

		$group->public_id = trim($request->put['public_id']);
		$group->name = trim($request->put['name']);
		$group->description = trim($request->put['description']);

		$data = $group->data;
		$data['leader'] = trim($request->put['leader']);
		$data['cost'] = ($request->put['cost'] == 'Yes');
		$data['childcare'] = ($request->put['childcare'] == 'Provided');
		$data['demographic'] = trim($request->put['demographic']);
		$data['gender'] = trim($request->put['gender']);
		$data['days'] = $request->put['days'];
		$data['time'] = trim($request->put['time']);
		$data['location'] = trim($request->put['where']);
		$group->data = $data;

		$max_members = intval($request->put['max_members']);
		if ($group->max_members != $max_members) {
			// TODO: Adjust status (Full?)
			$group->max_members = $max_members;
		}

		$group->save();

		$this->view($id, $group);

	}

}