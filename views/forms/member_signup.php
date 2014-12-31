<?php if (!is_object($this)) die; ?>
<style type="text/css">
#group-list { max-height: 800px; overflow-y: auto; }
#group-list label { font-weight: normal; }
#group-list .group-radio { float: left; }
#group-list .group-id { margin-bottom: 10px; margin-top: -5px; }
#group-list .sg-name { font-size: 1.2em; margin-left: 20px; margin-top: 0; color: #333; }
#group-list li { position: relative; padding-bottom: 10px; color: #555; }
#group-side small.help-block { color: #a94442; }
#group-side .form-control-feedback { opacity: 0; }
#group-list .group-descrip { margin-top: 0.7em; }
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
</style>
<form method="post" id="form">
	<fieldset>
		<legend>Member Sign-Up for <?=htmlentities($semester); ?></legend>

		<?php

		if (!empty($success)) {
			echo '<div class="alert alert-success">'. $success .'</div>';
		}

		?>

		<div class="row">
			<div class="col-md-6" id="group-side">
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
							<div>Leader(s): <?=htmlentities($data['leader']); ?></div>
							<div><?=htmlentities($data['when']); ?> at <?=htmlentities($data['where']); ?></div>
							<div>
								<?=(($data['demographic'] == 'None') ? '' : ucfirst($data['demographic']) .' '); ?>
								<?=(($data['gender'] == 'None') ? '' : ucfirst($data['gender']) .'\'s group'); ?>
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
			<div class="col-md-6">
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

		});

	$('#form').bootstrapValidator({
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
						message: 'The last name must be at least 2 characters long'
					}
				}
			},
			phone: {
				validators: {
					notEmpty: {
						message: 'The phone is required and cannot be empty'
					},
					phone: {
						message: 'The input is not a valid phone address',
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
						message: 'The input is not a valid email address'
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
						message: 'Test',
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