<?php

class GroupMeetings extends APIController {

	private $session, $user, $okGroups, $request;

	public function __construct() {
		
		parent::__construct();

		$this->request = Firelit\Request::init();

		$this->session = Firelit\Session::init();
		$this->user = User::find($this->session->userId);

		if ($this->user->role != 'ADMIN')
			$this->okGroups = $this->user->getGroupAccess(true);
		else 
			$this->okGroups = array();

	}

	public function viewAll($groupId) {

		$group = Group::find($groupId);

		if (!$group)
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Access to group forbidden.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'Group is not part of a valid semester.');

		$meetings = array();

		$meetsQuery = $group->getMeetings();

		foreach ($meetsQuery as $i => $meeting) {
			$meetings[] = $meeting->getArray();
		}

		$this->response->respond($meetings);

	}

	public function addMeeting($groupId) {

		$group = Group::find($groupId);

		if (!$group)
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Access to group forbidden.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'Group is not part of a valid semester.');

		$date = new DateTime($this->request->post['date']);

		$start = new DateTime($semester->start_date);
		$start->sub(new DateInterval('P2W'));

		if ($date < $start) 
			throw new Firelit\RouteToError(400, 'Meeting date is more than 2 weeks before the semseter started.');

		$end = new DateTime($semester->end_date);
		$end->add(new DateInterval('P2W'));

		if ($date > $end) 
			throw new Firelit\RouteToError(400, 'Meeting date is more than 2 weeks after the semseter ended.');

		$count = intval($this->request->post['attendance']);
		$size = intval($this->request->post['group_size']);

		if ($count < 1) 
			throw new Firelit\RouteToError(400, 'There was no meeting if the attendance is less than 1.');

		if ($size < 1) 
			throw new Firelit\RouteToError(400, 'There was no meeting if the group size is less than 1.');

		Meeting::create(array(
			'group_id' => $group->id,
			'date' => $date,
			'attendance' => $count,
			'group_size' => $size
		));

		$this->response->code(204);
		$this->response->cancel();

	}

	public function removeMeeting($groupId, $meetingId) {

		$group = Group::find($groupId);

		if (!$group)
			throw new Firelit\RouteToError(400, 'Invalid group specified.');

		if (($this->user->role != 'ADMIN') && !in_array($group->id, $this->okGroups)) 
			throw new Firelit\RouteToError(400, 'Access to group forbidden.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'Group is not part of a valid semester.');

		$meet = Meeting::find($meetingId);

		if (!$meet)
			throw new Firelit\RouteToError(400, 'Invalid meeting specified.');

		$meet->delete();

		$this->response->code(204);
		$this->response->cancel();

	}

}
