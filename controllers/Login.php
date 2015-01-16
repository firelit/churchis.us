<?php

use OAuth\OAuth2\Service\Google;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use Firelit\InputValidator as InputValidator;

class Login extends Firelit\Controller {
	
	protected $session, $req, $resp;
	static protected $lockSeconds = 3600; // 1 Hour

	public function __construct() {
		$this->session = Firelit\Session::init();
		$this->request = Firelit\Request::init();
		$this->response = Firelit\Response::init();
	}

	/**
	 *	Login the user
	 */
	public function login() {

		if (($this->request->post['type'] == 'google') || (!empty($this->request->get['code'])))
			return $this->googleLogin(); 

		if ($this->request->post['type'] == 'local')
			return $this->localLogin(); 

		$this->session->loggedIn = false;
		$this->session->loginError = 'Incorrect login type selected.';
		$this->response->redirect( '/' );

	}

	/**
	 *	Login the user with the local credentials.
	 */
	public function localLogin() {

		$email = strtolower(trim($this->request->post['email']));

		$iv = new InputValidator(InputValidator::EMAIL, $email);
		if (!$iv->isValid()) {
			$this->session->loggedIn = false;
			$this->session->loginError = 'Email address is not valid.';

			$this->response->redirect('/');
			exit;
		}

		$password = $this->request->post['password'];

		if (strlen($password) < 2) {
			$this->session->loggedIn = false;
			$this->session->loginError = 'The password field is required.';

			$this->response->redirect('/');
			exit;
		}

		$user = User::findByEmail($email);

		if (!$user) {

			$user = $this->tempCreateUser($email, $password);

			if ($user) {
				$this->executeLogin($user);
				exit;
			};

			sleep(1);

			$this->session->loggedIn = false;
			$this->session->loginError = 'No account found with this email address.';

			$this->response->redirect('/');
			exit;
		}

		if ($user->status != 'ENABLED') {
			$this->session->loggedIn = false;
			$this->session->loginError = 'This account has been disabled.';

			$this->response->redirect('/');
			exit;
		}

		if ($user->service == 'GOOGLE') {
			$this->googleLogin();
			exit;
		}

		if ($user->service != 'LOCAL') {
			$this->session->loggedIn = false;
			$this->session->loginError = 'Login method not available for given email.';

			$this->response->redirect('/');
			exit;
		}

		if (!$user->validatePassword($password)) { 
			sleep(1);
			
			$this->session->loggedIn = false;
			$this->session->loginError = 'The password is incorrect.';

			$this->response->redirect('/');
			exit;
		}

		$this->executeLogin($user);

	}

	/**
	 *	Temporary method creates a user if there is a group leader
	 *	with the same email address. Only creates a user if the password
	 *	matches the universal password and the email is a group leader's
	 *	email address.
	 *
	 *	@param String $email The email address to use for the new user
	 *	@param String $password The password used for the new user
	 *	@return User The user created or false for failure
	 */
	public function tempCreateUser($email, $password) {

		if ($password != $_SERVER['TEMP_PASSWORD']) return false;

		$semester = Semester::latestOpen();

		$sql = "SELECT * FROM `groups` WHERE `semester_id`=:semester_id AND `data` LIKE :email";
		$q = new Firelit\Query($sql, array(
			':semester_id' => $semester->id,
			':email' => '%'. Firelit\Query::escapeLike('"email":"'. $email .'"') .'%'
		));

		if (!$group = $q->getObject('Group')) return false;

		$user = new User(array(
			'name' => $group->data['leader'],
			'email' => $email,
			'status' => 'ENABLED',
			'role' => 'USER',
			'service' => 'LOCAL'
		));

		$user->setPassword($password);
		$user->save();

		$user->grantGroupAccess($group->id);

		while ($group = $q->getObject('Group'))
			$user->grantGroupAccess($group->id);

		return $user;

	}

	/**
	 *	Login the user with Google oAuth
	 */
	public function googleLogin() {

		$servFactory = new \OAuth\ServiceFactory(); 

		$storage = new Session();

		$host = $this->request->host;
		$proto = ($this->request->secure ? 'https://' : 'http://');

		$credentials = new Credentials(
			$_SERVER['GOOGLE_KEY'],
			$_SERVER['GOOGLE_SEC'],
			$proto . $host .'/login/callback'
		);

		$googleService = $servFactory->createService('google', $credentials, $storage, array('email', 'profile'));

		if (!empty($this->request->get['code'])) {

			// This was a callback request from google, get the token
			$googleService->requestAccessToken($this->request->get['code']);

			// Send a request with it
			$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
			
			/*
			array(
				'service' => 'google', 
				'uid' => $result['id'], 
				'name' => $result['name'], 
				'email' => $result['email'],
				'image' => $result['picture']
			);
			*/

			$email = $result['email'];

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$this->session->loggedIn = false;
				$this->session->loginError = 'There are no accounts with this email address.';

				$this->response->redirect('/');
				exit;
			}

			$user = User::findByEmail($email);

			if (!$user) {
				// TODO: Create an account if it matches a partner email host
				$this->session->loggedIn = false;
				$this->session->loginError = 'There are no accounts with this email address.';

				$this->response->redirect('/');
				exit;
			}

			if ($user->service != 'GOOGLE') {
				$this->session->loggedIn = false;
				$this->session->loginError = 'Your account requires local authentication. Please enter your email and password below.';

				$this->response->redirect('/');
				exit;
			}

			if ($user->status != 'ENABLED') {
				$this->session->loggedIn = false;
				$this->session->loginError = 'This account has been disabled.';

				$this->response->redirect('/');
				exit;
			}

			if (empty($user->service_id)) {
				// First time logging in? Save that UID
				$user->service_id = $result['id'];
				$user->save();

			} elseif ($user->service_id != $result['id']) {
				$this->session->loggedIn = false;
				$this->session->loginError = 'Your Google UID does not match our records. Please try again or contact us.';

				$this->response->redirect('/');
				exit;
			}

			$this->executeLogin($user);

		} else {

			$this->response->redirect( $googleService->getAuthorizationUri() );

		}

	}

	/**
	 *	Logout the user and redirect the browser.
	 */
	public function logout() {

		$this->session->loggedIn = false;
		unset( $this->session->userId );
		
		$this->response->redirect('/');

	}

	/**
	 *	Filter the URL passed in as the redirect destination to 
	 *	ensure it doesn't point off-domain.
	 *
	 *	@param String $url The URL to filter
	 *	@return Strin The filtered URL
	 */
	public function filterGo($url) {
		if (!preg_match('/^\//', $url)) return '/';
		else return $url;
	}

	/**
	 *	Take steps required to set user as logged in and redirect the browser.
	 *
	 *	@param User $user The user to login
	 */
	public function executeLogin($user) {

		$this->session->loggedIn = true;
		$this->session->userId = $user->id;

		$this->response->redirect('/manage/');

	}

}