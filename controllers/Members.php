<?php

class Members extends APIController {
	
	public function viewAll() {

		$sql = "SELECT * FROM `semesters` WHERE `status`='OPEN' ORDER BY `start_date` ASC LIMIT 1";
		$q = new Firelit\Query($sql);

		$semester = $q->getObject('Semester');

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		$sql = "SELECT * FROM `members` WHERE `semester_id`=:semester_id ORDER BY `name`, `email`, `created` ASC";
		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$members = array();

		while ($member = $q->getObject('Member')) {

			$members[] = array(
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

		$this->response->respond($members);

	}

	public function view($id) {

		$member = Member::find($id);

		if (!$member) 
			throw new Firelit\RouteToError(404, 'Member not found.');

		$groups = $member->getGroups();
		$groupsReturn = array();

		foreach ($groups as $group) {

			$groupsReturn[] = array(
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

		$this->response->respond(array(
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
			'created' => $member->created,
			'groups' => $groupsReturn
		));

	}
}