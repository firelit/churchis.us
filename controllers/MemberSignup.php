<?php

class MemberSignup extends Firelit\Controller {
	
	public function __construct() { }

	public function viewForm() {

		$semester = Semester::latestOpen();

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		$groups = $this->getSmallGroups($semester->id);

		$v = new Firelit\View('forms/member_signup', 'forms/layout');
		$v->render(array(
			'title' => 'Frontline Small Group Signup',
			'semester' => $semester->name,
			'groups' => $groups
		));

	}

	public function submitForm() {

		$request = Firelit\Request::init();

		$group = Group::find($request->post['group']);

		if (!$group)
			throw new Firelit\RouteToError(400, 'No matching small group found.');

		if ($group->status == 'FULL')
			throw new Firelit\RouteToError(400, 'The selected small group is full.');

		if ($group->status != 'OPEN')
			throw new Firelit\RouteToError(400, 'No selected small group is no longer open.');

		$total = $group->getMemberCount();

		if ($total >= $group->max_members)
			throw new Firelit\RouteToError(400, 'Sorry, this group is full! Please click back and pick a different group.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'The small group\'s semester is closed.');

		$first = trim($request->post['first']);
		$last = trim($request->post['last']);
		$name = $first .' '. $last;

		$iv = new Firelit\InputValidator(Firelit\InputValidator::NAME, $name);
		$name = $iv->getNormalized();

		$address = trim($request->post['address']);
		$city = trim($request->post['city']);
		$zip = trim($request->post['zip']);
		$state = trim($request->post['state']);

		$phone = trim($request->post['phone']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::PHONE, $phone, 'US');
		$phone = $iv->getNormalized();

		$email = trim($request->post['email']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $email);
		$email = $iv->getNormalized();

		$smallgroup = trim($request->post['conta']);
		$description = trim($request->post['description']);

		$cost = ($request->post['cost'] == 'Yes');
		$maxsize = intval($request->post['maxsize']);

		$contact = null;

		if (in_array('Phone', $request->post['contact'])) {
			$contact = 'PHONE';
		}

		if (in_array('Email', $request->post['contact'])) {
			if ($contact = 'PHONE') $contact = 'BOTH';
			else $contact = 'EMAIL';
		}

		if ($request->post['childcare'] == 'Yes')
			$childcount = intval($request->post['childcount']);
		else
			$childcount = 0;

		try {

			$member = Member::create(array(
				'semester_id' => $semester->id,
				'name' => $name,
				'email' => $email,
				'phone' => $phone,
				'email' => $email,
				'address' => $address,
				'city' => $city,
				'state' => $state,
				'zip' => $zip,
				'contact_pref' => $contact,
				'child_care' => $childcount
			));

			$member->addToGroup($group->id);

		} catch (Exception $e) {
			throw new Firelit\RouteToError(500, $e->getMessage());
		}

		$groups = $this->getSmallGroups($semester->id);

		$v = new Firelit\View('forms/member_signup', 'forms/layout');
		$v->render(array(
			'title' => 'Frontline Small Group Signup',
			'semester' => $semester->name,
			'groups' => $groups,
			'success' => 'You information has been received. Thank you!'
		));

	}

	public function getSmallGroups($semesterId) {

		$sql = "SELECT * FROM `groups` WHERE `semester_id`=:semester_id AND `status` IN ('OPEN','FULL') ORDER BY `public_id`, `name` ASC";
		$q = new Firelit\Query($sql, array(':semester_id' => $semesterId));

		$groups = array();

		while ($group = $q->getObject('Group')) {

			$groups[$group->id] = array(
				'name' => $group->name,
				'public_id' => $group->public_id,
				'description' => $group->description,
				'leader' => $group->data['leader'],
				'when' => (is_array($group->data['days']) ? implode(', ', $group->data['days']) : '') .' '. $group->data['time'],
				'where' => (!empty($group->data['location_short']) ? $group->data['location_short'] : $group->data['location']),
				'childcare' => $group->data['childcare'],
				'gender' => $group->data['gender'],
				'demographic' => $group->data['demographic'],
				'full' => ($group->status == 'FULL')
			);

		}

		return $groups;

	}

}