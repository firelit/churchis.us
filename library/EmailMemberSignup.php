<?php

class EmailMemberSignup extends Email {

	public function __construct(Member $member, Group $group) {
		
		$this->subject = "Small Group Signup";
		$this->to = $member->email;

		$this->html = "<html><body>";

		$this->html .= "<p>Hi ". htmlentities($member->name) ."!</p>";

		$this->html .= "<p>I'm so glad you've decided to join a small group this semester. If you're receiving this email, it means we've received your registration. The group you chose, <em>". htmlentities($group->name) ."</em>, is being led by ". htmlentities($group->data['leader']) .". We'll also be emailing ". htmlentities($group->data['leader']) ." with your contact information.</p>";

		$this->html .= "<p>If for some reason your group leader doesn't connect with you within a week, please reach out to them directly at <a href=\"mailto:". htmlentities($group->data['email']) ."\">". htmlentities($group->data['email']) ."</a> or give the Frontline office a call (647-1111). Once in a while an email slips through the cracks or a phone number is misentered and it's really important to me that everyone gets connected to their groups.</p>";

		$this->html .= "<p>I'm praying you have an amazing small group experience this semester.</p>";

		$this->html .= "<p>Matthew</p>";
		$this->html .= "<p style=\"color:#888;\">rev. matthew deprez | www.matthewdeprez.com | intergenerational pastor | frontline community church | www.frontlinegr.com | 616.647.1111</p>";

		$this->html .= "</body></html>";

	}

}