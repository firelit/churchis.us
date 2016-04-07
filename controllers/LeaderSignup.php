<?php

class LeaderSignup extends Firelit\Controller {

	public function __construct() { }

	public function viewForm() {

		$semesters = $this->getOpenSemesters();

		if (!sizeof($semesters))
			throw new Firelit\RouteToError(400, 'There are no open semesters for signup.');

		$v = new Firelit\View('forms/leader_signup', 'forms/layout');
		$v->render(array(
			'title' => 'Frontline Small Group Leader Signup',
			'semesters' => $semesters
		));

	}

	public function submitForm() {

		$request = Firelit\Request::init();

		$semester = Semester::find($request->post['semester']);

		if (!$semester || ($semester->status != 'OPEN'))
			throw new Firelit\RouteToError(400, 'No matching open small group semesters found.');

		$leader = trim($request->post['leader']);

		$phone = trim($request->post['phone']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::PHONE, $phone, 'US');
		$phone = $iv->getNormalized();

		$email = trim($request->post['email']);
		$iv = new Firelit\InputValidator(Firelit\InputValidator::EMAIL, $email);
		$email = $iv->getNormalized();

		$smallgroup = trim($request->post['smallgroup']);
		$description = trim($request->post['description']);
		$cost = ($request->post['cost'] == 'Yes');
		$author = trim($request->post['author']);

		if ($request->post['maxsize'] == 'null') $maxsize = null;
		else $maxsize = intval($request->post['maxsize']);

		$days = array();
		if (is_array($request->post['days']))
			foreach ($request->post['days'] as $day)
				$days[] = $day;

		$status = $request->post['status'];
		$startdate = new DateTime($request->post['startdate']);
		$enddate = new DateTime($request->post['enddate']);
		$childcare = ($request->post['childcare'] == 'Yes');
		$location = trim($request->post['location']);
		$time = trim($request->post['time']);
		$gender = trim($request->post['gender']);
		$demographic = trim($request->post['demographic']);

		try {

			Group::create(array(
				'semester_id' => $semester->id,
				'name' => $smallgroup,
				'description' => $description,
				'data' => array(
					'leader' => $leader,
					'phone' => $phone,
					'email' => $email,
					'cost' => $cost,
					'days' => $days,
					'author' => $author,
					'start_date' => $startdate,
					'end_date' => $enddate,
					'childcare' => $childcare,
					'location' => $location,
					'time' => $time,
					'gender' => $gender,
					'demographic' => $demographic
				),
				'max_members' => $maxsize,
				'status' => $status
			));

		} catch (Exception $e) {
			throw new Firelit\RouteToError(500, $e->getMessage());
		}

		$semesters = $this->getOpenSemesters();

		$v = new Firelit\View('forms/leader_signup', 'forms/layout');
		$v->render(array(
			'title' => 'Frontline Small Group Leader Signup',
			'success' => 'Your small group information has been submitted. Thank you!',
			'semesters' => $semesters
		));

	}

	public function getOpenSemesters() {

		try {

			$sql = "SELECT * FROM `semesters` WHERE `status`='OPEN' ORDER BY `start_date` ASC";
			$q = new Firelit\Query($sql);

		} catch (Exception $e) {
			throw new Firelit\RouteToError(500, $e->getMessage());
		}

		$semesters = array();

		while ($semester = $q->getObject('Semester')) {

			$start = new DateTime($semester->start_date);
			$end = new DateTime($semester->end_date);

			$semesters[$semester->id] = array(
				'name' => $semester->name,
				'start_date' => $start->format('M j, Y'),
				'end_date' => $end->format('M j, Y')
			);
		}

		return $semesters;

	}

}