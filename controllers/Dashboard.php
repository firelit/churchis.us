<?php

class Dashboard extends APIController {

	private $session, $user, $okGroups;

	public function __construct() {

		parent::__construct();

		$this->session = Firelit\Session::init();
		$this->user = User::find($this->session->userId);

		if ($this->user->role != 'ADMIN') {

			$this->response->respond(array());
			exit;

		}

	}

	public function view() {

		$semester = Semester::getCurrent();

		if (!$semester)
			throw new Firelit\RouteToError(400, 'No open small group semesters found.');

		$sql = "SELECT `members`.`created`, `members`.`child_care`, `members`.`email` FROM `groups_members` INNER JOIN `members` ON `members`.`id`=`groups_members`.`member_id` WHERE `members`.`semester_id`=:semester_id";

		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$data = array();
		$children = 0;
		$emails = array();

		while ($row = $q->getRow()) {

			$created = new DateTime($row['created']);

			$data[ (int) $created->format('Ymd') ]++;

			$children += $row['child_care'];

			if (strlen($row['email']))
				$emails[ $row['email'] ] = true;

		}

		ksort($data);
		$labels = array_keys($data);

		$min = sizeof($labels) ? min($labels) : 0;
		$max = sizeof($labels) ? max($labels) : 0;
		$sum = 0;

		for ($x = $min; $x <= $max; ) {

			if (!isset($data[$x])) $data[$x] = $sum;
			else {
				$sum += $data[$x];
				$data[$x] = $sum;
			}

			$t = strtotime($x) + 86400;
			$x = date('Ymd', $t);

		}

		// Resort and reformat the labels

		ksort($data);
		$labels = array_keys($data);

		foreach ($labels as $i => $val) {

			$labels[$i] = date('n/j/y', strtotime($val));

		}

		// Get group data

		$sql = "SELECT * FROM `groups` WHERE `semester_id`=:semester_id";
		$q = new Firelit\Query($sql, array(':semester_id' => $semester->id));

		$leaderEmails = array();
		$groups = 0;
		$fullGroups = 0;

		while ($group = $q->getObject('Group')) {

			$email = $group->data['email'];

			if (strlen($email))
				$leaderEmails[ $email ] = true;

			if ($group->status == 'FULL')
				$fullGroups++;

			if ($group->status != 'CANCELED')
				$groups++;

		}

		$sql = "SELECT * FROM `semesters` WHERE 1 ORDER BY `start_date` DESC";
		$q = new Firelit\Query($sql);

		$semesters = array();

		while ($aSemester = $q->getObject('Semester')) {

			$semesters[] = array(
				'id' => $aSemester->id,
				'name' => $aSemester->name,
				'selected' => ($aSemester->id == $semester->id)
			);

		}

		$this->response->respond(array(
			'loaded' => true,
			'signups' => array(
				'series' => 'Member Signups',
				'labels' => $labels,
				'data' => array_values($data)
			),
			'counts' => array(
				'members' => $sum,
				'children' => $children,
				'groups' => $groups,
				'fullGroups' => $fullGroups
			),
			'emails' => array(
				'leaders' => array_keys($leaderEmails),
				'members' => array_keys($emails)
			),
			'semesters' => $semesters
		));

	}

	public function semester() {

		$req = Firelit\Request::init();

		$new = intval($req->post['new_semester']);

		$sem = Semester::find($new);

		if ($sem) setCookie('semester', $sem->id);

		$this->response->respond(array('success' => true));

	}

}