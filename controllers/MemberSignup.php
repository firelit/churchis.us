<?php

class MemberSignup extends Firelit\Controller {

	public function __construct() { }

	public function viewForm() {

		$semester = Semester::latestOpen();

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		$groups = $this->getSmallGroups($semester->id);


		$request = Firelit\Request::init();

		if (isset($request->get['success']) && ($request->get['success'] > strtotime('-2 minutes')))
			$success = 'You information has been received. You should hear from your group\'s leader soon. Thank you!';
		else
			$success = false;

		$v = new Firelit\View('forms/member_signup', 'forms/layout');
		$v->render(array(
			'title' => 'Frontline Small Group Signup',
			'semester' => $semester->name,
			'groups' => $groups,
			'success' => $success
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

		if (!is_null($group->max_members) && ($total >= $group->max_members))
			throw new Firelit\RouteToError(400, 'Sorry, this group is full! Please click back and pick a different group.');

		$semester = Semester::find($group->semester_id);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'The small group\'s semester is closed.');

		if (empty($request->post['validated']))
			throw new Firelit\RouteToError(400, 'The form was not properly validated (1).');

		if ($request->post['validated'] != $request->post['address'])
			throw new Firelit\RouteToError(400, 'The form was not properly validated (2).');

		$first = trim($request->post['first']);
		$last = trim($request->post['last']);
		$name = $first .' '. $last;

		$iv = new Firelit\InputValidator(Firelit\InputValidator::NAME, $name);
		$name = $iv->getNormalized();

		if ($request->post['addsecond'] == 'yes') {

			$first2 = trim($request->post['first2']);
			$last2 = trim($request->post['last2']);
			$name2 = $first2 .' '. $last2;

			$iv = new Firelit\InputValidator(Firelit\InputValidator::NAME, $name2);
			$name2 = $iv->getNormalized();

		} else $name2 = false;

		$address = trim($request->post['address']);
		$city = trim($request->post['city']);
		$zip = trim($request->post['zip']);
		$state = trim($request->post['state']);

		$phone = trim($request->post['phone']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::PHONE, $phone, 'US');
		if ($iv->isValid()) $phone = $iv->getNormalized();

		$email = trim($request->post['email']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $email);
		if ($iv->isValid()) $email = $iv->getNormalized();
		else $email = null;

		$contact = null;

		if (in_array('Phone', $request->post['contact'])) {
			$contact = 'PHONE';
		}

		if (in_array('Email', $request->post['contact'])) {
			if ($contact == 'PHONE') $contact = 'BOTH';
			else $contact = 'EMAIL';
		}

		if (isset($request->post['childcare']) && ($request->post['childcare'] == 'Yes'))
			$childcount = intval($request->post['childcount']);
		else
			$childcount = 0;

		if (isset($request->post['ages']) && strlen(trim($request->post['ages'])))
			$childages = trim($request->post['ages']);
		else
			$childages = null;

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
				'child_care' => $childcount,
				'child_ages' => $childages
			));

			$newCount = $group->addMember($member);

			if ($name2) {

				$member2 = Member::create(array(
					'semester_id' => $semester->id,
					'name' => $name2,
					'email' => $email,
					'phone' => $phone,
					'email' => $email,
					'address' => $address,
					'city' => $city,
					'state' => $state,
					'zip' => $zip,
					'contact_pref' => $contact,
					'child_care' => 0
				));

				$newCount = $group->addMember($member2);

			} else $member2 = null;


		} catch (Exception $e) {
			throw new Firelit\RouteToError(500, $e->getMessage());
		}

		try {

			$email = new EmailMemberSignup($member, $group, $member2);

			$email->toMember();
			$email->send();

		} catch (Exception $e) { }

		try {

			$email->toLeader();
			$email->send();

		} catch (Exception $e) { }

		if (!is_null($group->max_members) && ($newCount >= $group->max_members)) {

			try {

				$email->groupFull($newCount);
				$email->send();

			} catch (Exception $e) { }

		}

		$response = Firelit\Response::init();
		$response->redirect('/signup/member?success='. time());

	}

	public function getSmallGroups($semesterId) {

		$sql = "SELECT * FROM `groups` WHERE `semester_id`=:semester_id AND `status` IN ('OPEN','FULL') ORDER BY CAST(`public_id` AS UNSIGNED), `public_id`, `name` ASC";
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