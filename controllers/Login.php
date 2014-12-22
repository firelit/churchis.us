<?php

use OAuth\OAuth2\Service\Google;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

class Login extends Firelit\Controller {
	
	protected $session, $req, $resp;
	static protected $lockSeconds = 3600; // 1 Hour

	public function __construct() {
		$this->session = Firelit\Session::init();
		$this->request = Firelit\Request::init();
		$this->response = Firelit\Response::init();
	}

	public function login() {

		if (($this->request->method == 'POST') && isset($this->request->post['go'])) 
			$this->session->loginGo = ($this->secure ? 'https' : 'http') .'://'. $this->request->host . $this->request->post['go'];

		return $this->googleLogin(); // Only one available

	}

	public function googleLogin() {

		$servFactory = new \OAuth\ServiceFactory(); 

		$storage = new Session();

		$credentials = new Credentials(
			$_SERVER['GOOGLE_KEY'],
			$_SERVER['GOOGLE_SEC'],
			'http://churchis.us/login/callback'
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

				$this->response->redirect( $this->session->loginGo );
				exit;
			}

			$u = User::findByEmail($email);

			if (!$u) {
				// TODO: Create an account if it matches a partner email host
				$this->session->loggedIn = false;
				$this->session->loginError = 'There are no accounts with this email address.';

				$this->response->redirect( $this->session->loginGo );
				exit;
			}

			if ($u->service != 'GOOGLE') {
				$this->session->loggedIn = false;
				$this->session->loginError = 'Your account requires local authentication. Please enter your email and password below.';

				$this->response->redirect( $this->session->loginGo );
				exit;
			}

			if (empty($u->service_id)) {
				// First time logging in? Save that UID
				$u->service_id = $result['id'];
				$u->save();

			} elseif ($u->service_id != $result['id']) {
				$this->session->loggedIn = false;
				$this->session->loginError = 'Your Google UID does not match our records. Please try again or contact us.';

				$this->response->redirect( $this->session->loginGo );
				exit;
			}

			$this->executeLogin($u, 'GOOGLE');

		} else {

			if (empty($this->session->loginGo)) $this->session->loginGo = '/';

			$this->response->redirect( $googleService->getAuthorizationUri() );

		}

	}

	public function logout() {

		$this->session->loggedIn = false;
		unset( $this->session->userId );
		
		$this->response->redirect('/');

	}

	public function filterGo($url) {
		if (!preg_match('/^\//', $url)) return '/';
		else return $url;
	}

	public function executeLogin($u, $service) {

		$this->session->loggedIn = true;
		$this->session->userId = $u->id;

		$go = $this->session->loginGo;
		unset($this->session->loginGo);

		$this->response->redirect( $go );

	}

}