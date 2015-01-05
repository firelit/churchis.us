<?php

class EmailMemberSignup extends Email {

	protected $member, $group;

	public function __construct(Member $member, Group $group) {
		
		$this->member = $member;
		$this->group = $group;

	}

	public function toMember() {
		
		$member = $this->member;
		$group = $this->group;

		$this->subject = "Small Group Signup";
		$this->to = $member->email;

		$this->html = "<html><body>";

		$this->html .= "<p>Hi ". htmlentities($member->name) ."!</p>";

		$this->html .= "<p>I'm so glad you've decided to join a small group this semester. If you're receiving this email, it means we've received your registration. The group you chose, <em>". htmlentities($group->name) ."</em>, is being led by ". htmlentities($group->data['leader']) .". We'll also be emailing ". htmlentities($group->data['leader']) ." with your contact information.</p>";

		$this->html .= "<p>If for some reason your group leader doesn't connect with you within a week, please reach out to them directly at <a href=\"mailto:". htmlentities($group->data['email']) ."\">". htmlentities($group->data['email']) ."</a> or give the Frontline office a call (647-1111). Once in a while an email slips through the cracks or a phone number is misentered and it's really important to me that everyone gets connected to their groups.</p>";

		$this->html .= "<p>If you have any questions, you can send an email to my assistant, Holly, by replying to this email.</p>";

		$this->html .= "<p>I'm praying you have an amazing small group experience this semester.</p>";

		$this->html .= "<p>Matthew</p>";
		$this->html .= "<p style=\"color:#888;\">rev. matthew deprez | www.matthewdeprez.com | intergenerational pastor | frontline community church | www.frontlinegr.com | 616.647.1111</p>";

		$this->html .= "</body></html>";

	}

	public function toLeader() {
		
		$member = $this->member;
		$group = $this->group;

		$this->subject = "New Member: ". $member->name;
		$this->to = $group->data['email'];

		$this->html = "<html><body>";

		$this->html .= "<p>Hi ". htmlentities($group->data['leader']) ."!</p>";

		$this->html .= "<p>Below you'll find the contact information for the latest person to signup for your Frontline small group, <em>". htmlentities($group->name) . (!empty($group->public_id) ? " (#". htmlentities($group->public_id) .")" : "") ."</em>.</p>";

		$this->html .= "<dl>";

		$dtS = ' style="clear:left;float:left;text-align:right;width:150px;color:#888;padding-top:7px;padding-right:10px"';
		$ddS = ' style="float:left;margin-left:0;padding-top:7px"';

		$this->html .= "<dt". $dtS .">Name</dt><dd". $ddS ."><strong>". htmlentities($member->name) ."</strong></dd>";
		$this->html .= "<dt". $dtS .">Contact Preference</dt><dd". $ddS .">". htmlentities($member->contact_pref) ."</dd>";
		$this->html .= "<dt". $dtS .">Email</dt><dd". $ddS .">". htmlentities($member->email) ."</dd>";
		$this->html .= "<dt". $dtS .">Phone</dt><dd". $ddS .">". htmlentities($member->phone) ."</dd>";
		$this->html .= "<dt". $dtS .">Childcare Needed</dt><dd". $ddS .">". (empty($member->child_care) ? 'No' : $member->child_care ." children") ."</dd>";

		$this->html .= "</dl>";

		$this->html .= "<div style=\"clear:left;\"></div>";

		$this->html .= "<p><strong>Please reach out to your new member within the next 24 hours.</strong></p>";

		$this->html .= "<p>Thank you!</p>";

		$this->html .= "<p>Matthew</p>";
		$this->html .= "<p style=\"color:#888;\">rev. matthew deprez | www.matthewdeprez.com | intergenerational pastor | frontline community church | www.frontlinegr.com | 616.647.1111</p>";

		$this->html .= "</body></html>";
		
	}

}