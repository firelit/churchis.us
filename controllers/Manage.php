<?php

class Manage extends Firelit\Controller {

	public function __construct() { }

	public function viewIndex() {

		$session = Firelit\Session::init();

		if (!$session->loggedIn) {

			$session->loginError = 'Please log in.';

			$resp = Firelit\Response::init();
			$resp->redirect('/');
			return;
		}

		$user = User::find($session->userId);

		$v = new Firelit\View('manage/index');
		$v->render(array(
			'title' => 'CHURCHIS.US',
			'loggedIn' => $session->loggedIn,
			'loggedInAs' => (!empty($user) ? $user->name : null),
			'loginError' => $session->loginError
		));

	}
}