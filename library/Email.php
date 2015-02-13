<?php

class Email {
	
	public $subject, $to, $from, $replyTo, $cc, $bcc;
	public $html;

	/**
	 *	Send the email now
	 *	@return Bool success
	 */
	public function send() {

		if (empty($_SERVER['MAILGUN_KEY'])) 
			throw new Exception('No MAILGUN_KEY set.');

		if (empty($_SERVER['MAILGUN_DOM'])) 
			throw new Exception('No MAILGUN_DOM set.');

		if (empty($this->html)) 
			throw new Exception('Nothing to send!');

		if (empty($this->from)) {
			$this->from = $_SERVER['EMAIL_FROM'];
			$this->replyTo = '"'. $_SERVER['OFFICE_NAME'] .'" <'. $_SERVER['OFFICE_EMAIL'] .'>';
		}
		
		$mg = new Mailgun\Mailgun($_SERVER['MAILGUN_KEY']);

		$params = array(
			'from' => $this->from,
			'subject' => $this->subject,
			'html' => $this->html
		);

		if (!empty($this->to))
			$params['to'] = $this->to;

		if (!empty($this->cc))
			$params['cc'] = $this->cc;

		if (!empty($this->bcc))
			$params['bcc'] = $this->bcc;

		if (!empty($this->replyTo))
			$params['h:Reply-To'] = $this->replyTo;

		$result = $mg->sendMessage($_SERVER['MAILGUN_DOM'], $params);

		return ($result->http_response_code == 200);

	}
}