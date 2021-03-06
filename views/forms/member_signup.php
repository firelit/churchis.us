<?php if (!is_object($this)) die; ?>
<style type="text/css">
#signup-again { margin: 40px 0px; }
#form h3 { margin-top: 0px; margin-bottom: 20px; }
#form legend { color: #666; }
#group-list { max-height: 900px; overflow-y: auto; margin-bottom: 0; }
#group-list label { font-weight: normal; cursor: pointer; }
.group-radio { float: left; }
.group-id { margin-bottom: 10px; margin-top: -5px; }
#group-list .sg-name { font-size: 1.2em; margin-left: 20px; margin-top: 0; color: #333; }
#group-list li { position: relative; padding-bottom: 10px; color: #555; }
#group-list li.group-selected { background-color: #efe; border-color: #ded; }
#group-side-wrap { position: relative; }
#group-side-wrap.scrolled:before {
	content: "";
	position: absolute;
	margin-left: 0;
	top: 0;
	height: 20px;
	width: 100%;
	z-index: 1;
	background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,0))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(255,255,255,0) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(255,255,255,0) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(255,255,255,0) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(255,255,255,0) 100%); /* W3C */
}
#group-side-wrap:after {
	content: "";
	position: absolute;
	margin-left: 0;
	bottom: 0;
	height: 20px;
	width: 100%;
	background: -moz-linear-gradient(top,  rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0)), color-stop(100%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* W3C */
}
#group-side small.help-block { color: #a94442; }
#group-side .form-control-feedback { opacity: 0; }
.group-descrip { margin-top: 0.7em; font-style: italic; }
.group-leader, .group-demographic, .group-meeting { color: #777; }
#group-list li.full:after {
	position: absolute;
	top: 30px;
	left: 70px;
	font-size: 4em;
	color: red;
	font-weight: bold;
	content: "FULL";
	opacity: 0.6;
	transform: rotate(-20deg);
	-webkit-transform: rotate(-20deg); /* Safari */
	-moz-transform: rotate(-20deg); /* Firefox */
	-ms-transform: rotate(-20deg); /* IE */
	-o-transform: rotate(-20deg); /* Opera */
}
.second { display: none; }
::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 8px;
}
::-webkit-scrollbar-thumb {
    border-radius: 8px;
    background-color: rgba(156, 156, 156, .6);
}
</style>
<form method="post" id="form">
	<fieldset>
		<legend>Sign-Up for <?=htmlentities($semester); ?> Semester</legend>

		<?php

		if (!empty($success)) {
			echo '<div class="alert alert-success signup-again-hide">'. $success .'</div>';
			echo '<div class="row signup-again-hide"><div class="col-md-12 text-center"><a href="#" id="signup-again" class="btn btn-primary btn-lg">Sign-Up for a Group</a></div></div>';
		}

		?>

		<div class="row signup-again-show <?=(!empty($success) ? 'hidden' : ''); ?>">
			<div class="col-md-6" id="group-side">
				<h3>1. Pick a group:</h3>
				<div id="group-side-wrap">
					<ul id="group-list" class="list-unstyled">
					<?php

						if (is_array($groups))
						foreach ($groups as $id => $data) {
							?>
							<li class="well <?php if ($data['full']) echo 'full'; ?>"><label>
								<?=(!empty($data['public_id']) ? '<div class="group-id"><span class="label label-default">Group '. $data['public_id'] .'</span></div>' : ''); ?>
								<div>
									<input type="radio" name="group" class="group-radio" value="<?=htmlentities($id); ?>" <?php if ($data['full']) echo 'disabled'; ?>>
									<h3 class="sg-name"><?=htmlentities($data['name']); ?></h3>
								</div>
								<div class="group-leader"><i class="fa fa-fw fa-user"></i> Leader(s): <?=htmlentities($data['leader']); ?></div>
								<div class="group-meeting"><i class="fa fa-fw fa-calendar"></i> <?=htmlentities($data['when']); ?> at <?=htmlentities($data['where']); ?></div>
								<div class="group-demographic"><i class="fa fa-fw fa-check-circle"></i>
									<?=(($data['demographic'] == 'None') ? '' : ucfirst($data['demographic']) .'s '); ?>
									<?=(($data['gender'] == 'None') ? '' : ucfirst($data['gender']) .' group'); ?>
									<?=(($data['gender'] == 'None') && ($data['demographic'] == 'None') ? 'Open group' : ''); ?>
									<?=($data['childcare'] ? ' (childcare provided)' : ''); ?>
								</div>
								<div class="group-descrip">
									<?=htmlentities($data['description']); ?>
								</div>
							</label></li>
							<?php
						}

					?>
					</ul>
				</div>
			</div>
			<div class="col-md-6">
				<h3>2. Enter your info:</h3>
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="first">First Name</label>
					<div class="">
						<input id="first" name="first" type="text" placeholder="First Name" class="form-control input-md" required>
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="last">Last Name</label>
					<div class="">
						<input id="last" name="last" type="text" placeholder="Last Name" class="form-control input-md" required>
					</div>
				</div>

				<div class="form-group">
					<div>
						<div class="checkbox">
							<label for="addsecond">
								<input type="checkbox" name="addsecond" id="addsecond" value="yes"> Also add my spouse, fianc&eacute;, friend, etc.
							</label>
							<p class="help-block text-small"><small>If checked, we'll register a second person to the selected group using the same contact information.</small></p>
						</div>
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group second">
					<label class="control-label" for="first2">Second Member: First Name</label>
					<div class="">
						<input id="first2" name="first2" type="text" placeholder="First Name" class="form-control input-md">
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group second">
					<label class="control-label" for="last2">Second Member: Last Name</label>
					<div class="">
						<input id="last2" name="last2" type="text" placeholder="Last Name" class="form-control input-md">
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="address">Address</label>
					<div class="">
						<input id="address" name="address" type="text" placeholder="Address" class="form-control input-md" required>
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="city">City</label>
					<div class="">
						<input id="city" name="city" type="text" placeholder="City" class="form-control input-md" required>
					</div>
				</div>

				<input type="hidden" name="state" value="MI">

				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="zip">Zip Code</label>
					<div class="">
						<input id="zip" name="zip" type="text" placeholder="Zip Code" class="form-control input-md" required>
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="phone">Phone</label>
					<div class="">
						<input id="phone" name="phone" type="text" placeholder="Phone" class="form-control input-md" required>
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="email">Email</label>
					<div class="">
						<input id="email" name="email" type="text" placeholder="Email" class="form-control input-md" required>
					</div>
				</div>

				<!-- Multiple Checkboxes -->
				<div class="form-group">
					<label class="control-label" for="contact">Preferred Contact Method</label>
					<div class="">
						<div class="checkbox">
							<label for="contact-0">
								<input type="checkbox" name="contact[]" id="contact-0" value="Phone"> Phone
							</label>
						</div>
						<div class="checkbox">
							<label for="contact-1">
								<input type="checkbox" name="contact[]" id="contact-1" value="Email"> Email
							</label>
						</div>
					</div>
				</div>

				<!-- Prepended checkbox -->
				<div class="form-group">
					<label class="control-label" for="childcount">Need Childcare?</label>
					<div class="">
						<div class="input-group">
							<span class="input-group-addon">
								<input type="checkbox" name="childcare" id="childcare" value="Yes">
							</span>
							<input id="childcount" name="childcount" class="form-control" type="text" placeholder="# of Children">
						</div>
						<p class="help-block">If childcare is needed, please enter the number of children</p>
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group ages">
					<label class="control-label" for="ages">Ages of Children</label>
					<div class="">
						<input id="ages" name="ages" type="text" placeholder="Example: 2, 5, 8" class="form-control input-md">
					</div>
				</div>

				<!-- Button -->
				<div class="form-group">
					<label class="control-label" for="submit_button"></label>
					<div class="">
						<button id="submit_button" name="submit_button" class="btn btn-primary">Submit</button>
					</div>
				</div>

			</div>
		</div>
	</fieldset>
</form>
<script>
$(function() {

	$('body')
		.on('change', '#childcount', function() {

			if ($('#childcount').val() != '')
				$('#childcare').prop('checked', true);

		})
		.on('change', '#childcare', function() {

			if ($(this).closest('.form-group').hasClass('has-error'))
				$('#form').bootstrapValidator('revalidateField', 'childcount');

			if ($(this).is(':checked'))
				$('.form-group.ages').show();
			else
				$('.form-group.ages').hide();

		})
		.on('change', '#addsecond', function() {

			if ($('#addsecond').is(':checked')) {
				$('.second').slideDown();
			} else {
				$('.second').slideUp();
			}

		})
		.on('click', '#signup-again', function() {

			$('.signup-again-show').removeClass('hidden');
			$('.signup-again-hide').addClass('hidden');

		});

	$('#addsecond').trigger('change');
	$('#childcare').trigger('change');

	var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf("android") > -1;

	$('#group-list')
		.scroll(function(ev) {

			if (isAndroid) return; // A bug on android causes div scrolling to freeze

			if (ev.target.scrollTop < 10) {
				$('#group-side-wrap').removeClass('scrolled');
			} else if ((ev.target.scrollTop >= 10) && (ev.target.scrollTop < 100)) {
				$('#group-side-wrap').addClass('scrolled');
			}
		});

	$('#group-list')
		.on('change', 'input', function() {
			$('#group-list input').filter(':checked').closest('li').addClass('group-selected');
			$('#group-list input').filter(':not(:checked)').closest('li').removeClass('group-selected');
		});

	$('#submit_button')
		.click(function() {
			$('.alert-success').remove();
		});

	setTimeout(function() {

		$('.alert-success').slideUp('slow');

	}, 10000);

	$('#form')
		.on('submit', function() {

			$('#validated').remove();

			$('<input type="hidden" name="validated" id="validated">')
				.val( $('#address').val() )
				.appendTo('#form');

		})
		.bootstrapValidator({
			message: 'This value is not valid',
			submitButtons: '#submit_button',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			trigger: 'blur',
			fields: {
				group: {
					trigger: 'change',
					validators: {
						notEmpty: {
							message: 'Please select a group'
						}
					}
				},
				first: {
					validators: {
						notEmpty: {
							message: 'First name is required'
						},
						stringLength: {
							min: 2,
							message: 'The first name must be at least 2 characters long'
						}
					}
				},
				last: {
					validators: {
						notEmpty: {
							message: 'Last name is required'
						},
						stringLength: {
							min: 2,
							message: 'The last name must be at least 2 characters long'
						}
					}
				},
				first2: {
					validators: {
						callback: {
							callback: function(value, validator, $field) {

								if (!$('#addsecond').is(':checked')) return true;

								return {
									valid: (value.length >= 2),
									message: 'The last name must be at least 2 characters long'
								};

							}
						}
					}
				},
				last2: {
					validators: {
						callback: {
							callback: function(value, validator, $field) {

								if (!$('#addsecond').is(':checked')) return true;

								return {
									valid: (value.length >= 2),
									message: 'The last name must be at least 2 characters long'
								};

							}
						}
					}
				},
				address: {
					validators: {
						notEmpty: {
							message: 'Address is required'
						},
						stringLength: {
							min: 2,
							message: 'The address must be at least 2 characters long'
						}
					}
				},
				city: {
					validators: {
						notEmpty: {
							message: 'City is required'
						},
						stringLength: {
							min: 2,
							message: 'The city must be at least 2 characters long'
						}
					}
				},
				zip: {
					validators: {
						notEmpty: {
							message: 'Zip code is required'
						},
						zipCode: {
							country: 'US',
							message: 'This is not a valid zip code'
						}
					}
				},
				phone: {
					validators: {
						notEmpty: {
							message: 'The phone is required and cannot be empty'
						},
						phone: {
							message: 'This is not a valid phone address',
							country: 'US'
						}
					}
				},
				email: {
					validators: {
						notEmpty: {
							message: 'The email is required and cannot be empty'
						},
						emailAddress: {
							message: 'This is not a valid email address'
						}
					}
				},
				'contact[]': {
					trigger: 'change',
					validators: {
						notEmpty: {
							message: 'Please select at least one contact method'
						}
					}
				},
				childcount: {
					validators: {
						callback: {
							message: 'Please indicate how many children',
							callback: function(value, validator, $field) {

								if (!$('#childcare').is(':checked')) return true;

								return (value.length >= 1);

							}
						}
					}
				}
			}
		});

});
</script>