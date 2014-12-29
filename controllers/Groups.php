<?php

class Groups extends APIController {
	
	private $session, $user, $okGroups, $limitSql;

	public function __construct() {
		
		$this->session = Firelit\Session::init();
		$this->user = User::find($this->session->userId);

		if ($this->user->role != 'ADMIN')
			$this->okGroups = $this->user->getGroupIds();
		else 
			$this->okGroups = array();

	}

	public function viewAll() {

		$sql = "SELECT * FROM `semesters` WHERE `status`='OPEN' ORDER BY `start_date` ASC LIMIT 1";
		$q = new Firelit\Query($sql);

		$semester = $q->getObject('Semester');

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		$sql = "SELECT * FROM `groups` WHERE `semester_id`=:semester_id ORDER BY `public_id`, `name` ASC";
		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$groups = array();

		while ($group = $q->getObject('Group')) {

			if (!in_array($group->id, $this->okGroups)) 
				continue;

			$groups[] = array(
				'id' => $group->id,
				'name' => $group->name,
				'status' => $group->status,
				'public_id' => $group->public_id,
				'description' => $group->description,
				'leader' => $group->data['leader'],
				'when' => (is_array($group->data['days']) ? implode(', ', $group->data['days']) : '') .' '. $group->data['time'],
				'where' => (!empty($group->data['location_short']) ? $group->data['location_short'] : $group->data['location']),
				'childcare' => ($group->data['childcare'] ? 'Provided' : 'Not available'),
				'gender' => $group->data['gender'],
				'demographic' => $group->data['demographic'],
				'full' => ($group->status == 'FULL')
			);

		}

		$this->response->respond($groups);

	}

	public function view($id, $group = false) {

		if (!$group)
			$group = Group::find($id);

		if (!$group) 
			throw new Firelit\RouteToError(404, 'Group not found.');

		if (!in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Not authorized to view this group.');

		$members = $group->getMembers();
		$membersReturn = array();

		foreach ($members as $member) {

			$membersReturn[] = array(
				'id' => $member->id,
				'name' => $member->name,
				'email' => $member->email,
				'phone' => $member->phone,
				'address' => $member->address,
				'city' => $member->city,
				'state' => $member->state,
				'zip' => $member->zip,
				'contact_pref' => $member->contact_pref,
				'child_care' => $member->child_care,
				'created' => $member->created
			);

		}

		$this->response->respond(array(
			'id' => $group->id,
			'name' => $group->name,
			'status' => $group->status,
			'public_id' => $group->public_id,
			'description' => $group->description,
			'leader' => $group->data['leader'],
			'days' => (is_array($group->data['days']) ? $group->data['days'] : array()),
			'time' => $group->data['time'],
			'where' => (!empty($group->data['location_short']) ? $group->data['location_short'] : $group->data['location']),
			'childcare' => ($group->data['childcare'] ? 'Provided' : 'Not available'),
			'gender' => $group->data['gender'],
			'demographic' => $group->data['demographic'],
			'cost' => ($group->data['cost'] ? 'Yes' : 'No'),
			'max_members' => $group->max_members,
			'full' => ($group->status == 'FULL'),
			'members' => $membersReturn
		));

	}

	public function edit($id) {

		$group = Group::find($id);

		if (!$group) 
			throw new Firelit\RouteToError(404, 'Group not found.');

		if (!in_array($group->id, $this->okGroups)) 
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