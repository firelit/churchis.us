<?php

class Users extends APIController {
	
	private $session, $user;

	public function __construct() {
		
		parent::__construct();

		$this->session = Firelit\Session::init();
		$this->user = User::find($this->session->userId);

	}

	public function viewAll() {

		if ($this->user->role != 'ADMIN') {

			$this->response->respond(array($this->user->getArray()));
			exit;

		}


		$sql = "SELECT * FROM `users` WHERE 1 ORDER BY `name`, `email`, `created` ASC";
		$q = new Firelit\Query($sql);

		$users = array();

		while ($user = $q->getObject('User')) {

			$users[] = $user->getArray();

		}

		$this->response->respond($users);

	}

	public function view($id) {

		$user = User::find($id);

		if (!$user) 
			throw new Firelit\RouteToError(404, 'User not found.');

		if (($this->user->role != 'ADMIN') && ($user->id != $this->user->id))
			throw new Firelit\RouteToError(400, 'Access to user forbidden.');

		$groups = $user->getGroups();
		$groupsReturn = array();

		foreach ($groups as $group) {

			$groupsReturn[] = $group->getArray();

		}

		$return = $user->getArray();
		$return['groups'] = $groupsReturn;

		$this->response->respond($return);

	}
}