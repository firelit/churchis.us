<?php

class Groups extends APIController {
	
	private $session, $user, $okGroups;

	public function __construct() {
		
		parent::__construct();

		$this->session = Firelit\Session::init();
		$this->user = User::find($this->session->userId);

		if ($this->user->role != 'ADMIN')
			$this->okGroups = $this->user->getGroupAccess(true);
		else 
			$this->okGroups = array();

	}

	public function viewAll() {

		$semester = Semester::latestOpen();

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		$sql = "SELECT *, (SELECT COUNT(*) FROM `groups_members` WHERE `groups_members`.`group_id`=`groups`.`id`) AS `count` FROM `groups` WHERE `groups`.`semester_id`=:semester_id ORDER BY CAST(`groups`.`public_id` AS UNSIGNED), `groups`.`public_id`, `groups`.`name` ASC";
		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$groups = array();

		while ($group = $q->getObject('Group')) {

			if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
				continue;

			$array = $group->getArray();
			$array['count'] = $group->count;
			$groups[] = $array;

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
		if (empty($group->public_id)) $group->public_id = null;

		$group->name = trim($request->put['name']);
		$group->description = trim($request->put['description']);
		$group->status = $request->put['status'];

		$data = $group->data;

		$data['leader'] = trim($request->put['leader']);

		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $request->put['email']);
		if ($iv->isValid()) $data['email'] = $iv->getNormalized();

		$iv = new Firelit\InputValidator(Firelit\InputValidator::PHONE, $request->put['phone'], 'US');
		if ($iv->isValid()) $data['phone'] = $iv->getNormalized();
		else $data['phone'] = trim($request->put['phone']);

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
			// Status is set above, let's not change here
			$group->max_members = $max_members;

		}

		$group->save();

		$this->view($id, $group);

	}

	public function addMember($groupId, $memberId) {

		$group = Group::find($groupId);

		if (!$group)
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Access to group forbidden.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'Group is not part of a valid semester.');

		$member = Member::find($memberId);

		if (!$member)
			throw new Firelit\RouteToError(400, 'Invalid member specified.');

		if ($member->semester_id != $group->semester_id) {
			// Member and group semesters do not match
			if (new DateTime($member->created) > new DateTime('-1 minute')) {
				// It was just created, and seems to have been created in the wrong semester
				// Let's just quick fix that
				$member->semester_id = $group->semester_id;
				$member->save();
			}
		}

		$group->addMember($member);

		$this->response->code(204);
		$this->response->cancel();

	}

	public function removeMember($groupId, $memberId) {

		$group = Group::find($groupId);

		if (!$group)
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Access to group forbidden.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'Group is not part of a valid semester.');

		$member = Member::find($memberId);

		if (!$member)
			throw new Firelit\RouteToError(400, 'Invalid member specified.');

		$group->removeMember($member);

		$this->response->code(204);
		$this->response->cancel();

	}

	public function delete($id) {

		$group = Group::find($id);

		if (!$group) 
			throw new Firelit\RouteToError(404, 'Group not found.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Not authorized to delete this group.');

		$request = Firelit\Request::init();

		$group->delete();

		$this->response->code(204);
		$this->response->cancel();

	}

}