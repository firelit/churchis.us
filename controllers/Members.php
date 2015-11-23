<?php

class Members extends APIController {

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

		$semester = Semester::getCurrent();

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		if ($this->user->role != 'ADMIN') {

			if (!sizeof($this->okGroups)) $this->okGroups[] = 0;

			$sql = "SELECT `members`.* FROM `members` INNER JOIN `groups_members` ON `groups_members`.`member_id`=`members`.`id` WHERE `members`.`semester_id`=:semester_id AND `groups_members`.`group_id` IN (". implode(',', $this->okGroups) .") GROUP BY `members`.`id` ORDER BY `name`, `email`, `created` ASC";

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

	public function view($id, $member = false, $checkPermissions = true) {

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

		if ($checkPermissions && ($this->user->role != 'ADMIN') && !sizeof($groupsReturn))
			throw new Firelit\RouteToError(400, 'Access to member forbidden.');

		$return = $member->getArray();
		$return['groups'] = $groupsReturn;

		$this->response->respond($return);
		exit;

	}

	public function create() {

		$request = Firelit\Request::init();

		$name = trim($request->post['name']);

		if (strlen($name) < 2)
			throw new Firelit\RouteToError(400, 'The name must be at least 2 characters long.');

		$iv = new Firelit\InputValidator(Firelit\InputValidator::NAME, $name);
		if ($iv->isValid()) $name = $iv->getNormalized();
		// else just keep the name as submitted

		$email = strtolower(trim($request->post['email']));
		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $email);
		if (!$iv->isValid()) $email = null;
		else $email = $iv->getNormalized();

		$phone = trim($request->post['phone']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::PHONE, $phone, 'US');
		if (empty($phone)) $phone = null;
		elseif ($iv->isValid()) $phone = $iv->getNormalized();
		// else just keep the phone as submitted

		$child_care = intval(trim($request->post['child_care']));
		if ($child_care > 0) $child_ages = trim($request->post['child_ages']);
		else $child_ages = null;

		$semester = Semester::getCurrent();

		if (!empty($email) || !empty($phone)) {
			// If we have a name and an email or phone
			// then check to see if a member already matches
			$sql = "SELECT * FROM `members` WHERE `semester_id`=:semester_id AND `name`=:name AND ";
			if (!empty($email)) $sql .= '`email`=:email AND ';
			if (!empty($phone)) $sql .= '`phone`=:phone AND ';

			$q = new Firelit\Query(substr($sql, 0, -5), array(
				':semester_id' => $semester->id,
				':name' => $name,
				':email' => $email,
				':phone' => $phone
			));

			// A member with the same name and email/phone exists,
			// so just use this member
			if ($member = $q->getObject('Member')) {

				if ($member->child_care != $child_care) {
					$member->child_care = $child_care;
					$member->child_ages = $child_ages;
					$member->save();
				}

				$this->view($member->id, $member, false);
			}

		}

		$member = Member::create(array(
			'semester_id' => $semester->id,
			'name' => $name,
			'email' => $email,
			'phone' => $phone,
			'contact_pref' => 'EITHER',
			'child_care' => $child_care,
			'child_ages' => $child_ages
		));

		$this->view($member->id, $member, false);

	}

	public function edit($id) {

		$member = Member::find($id);

		if (!$member)
			throw new Firelit\RouteToError(404, 'Member not found.');

		if ($this->user->role != 'ADMIN') {

			$groups = $member->getGroups();
			$match = false;

			foreach ($groups as $group) {
				foreach ($this->okGroups as $groupId) {
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
		$iv = new Firelit\InputValidator(Firelit\InputValidator::NAME, $member->name);
		$member->name = $iv->getNormalized();

		$member->email = trim($request->put['email']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $member->email);
		if (!$iv->isValid()) $member->email = null;
		else $member->email = $iv->getNormalized();

		$member->phone = trim($request->put['phone']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::PHONE, $member->phone, 'US');
		if ($iv->isValid()) $member->phone = $iv->getNormalized();
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

	public function delete($id) {

		$member = Member::find($id);

		if (!$member)
			throw new Firelit\RouteToError(404, 'Member not found.');

		if ($this->user->role != 'ADMIN')
			throw new Firelit\RouteToError(400, 'Not authorized to delete this member.');

		$member->delete();

		$this->response->code(204);
		$this->response->cancel();

	}

}