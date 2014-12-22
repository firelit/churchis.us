<?php

class Index extends Firelit\Controller {

	public function __construct() { }

	public function viewIndex() {

		$session = Firelit\Session::init();

		if ($session->loggedIn)
			$user = User::find($session->userId);

		$v = new Firelit\View('index');
		$v->render(array(
			'loggedIn' => $session->loggedIn,
			'loggedInAs' => (!empty($user) ? $user->name : null),
			'loginError' => $session->loginError
		));

		unset($session->loginError);

	}

}