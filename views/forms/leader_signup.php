<?php if (!is_object($this)) die; ?>
<form method="post" id="form">
	<fieldset>

		<!-- Form Name -->
		<legend>Group Leader Sign-Up</legend>

		<?php

		if (!empty($success)) {
			echo '<div class="alert alert-success">'. $success .'</div>';
		}

		?>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="semester">Semester</label>
			<div>
				<select id="semester" name="semester" class="form-control input-md" required>
					<?php

					if (is_array($semesters))
					foreach ($semesters as $id => $semester) {
						?>
						<option value="<?=$id; ?>" data-start="<?=htmlentities($semester['start_date']); ?>" data-end="<?=htmlentities($semester['end_date']); ?>"><?=htmlentities($semester['name']); ?></option>
						<?php
					}

					?>
				</select>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="leader">Leader's First &amp; Last Name(s)</label>
			<div>
				<input id="leader" name="leader" type="text" placeholder="Full Name" class="form-control input-md" required>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="phone">Phone Number</label>
			<div>
				<input id="phone" name="phone" type="text" placeholder="Phone Number" class="form-control input-md" required>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="email">Email</label>
			<div>
				<input id="email" name="email" type="text" placeholder="Email" class="form-control input-md" required>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="smallgroup">Name of Study</label>
			<div>
				<input id="smallgroup" name="smallgroup" type="text" placeholder="Name of Study" class="form-control input-md" required>
				<span class="help-block">The name as you wish it to appear publicly</span>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="author">Book Author (if applicable)</label>
			<div>
				<input id="author" name="author" type="text" placeholder="Author" class="form-control input-md">
				<span class="help-block">The author of the book being studied, if applicable</span>
			</div>
		</div>

		<!-- Textarea -->
		<div class="form-group">
			<label class="control-label" for="description">Topic Description</label>
			<div>
				<textarea class="form-control" id="description" name="description" required></textarea>
				<span class="help-block">Description of study topic in 2-3 sentences</span>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label" for="status">Group Status</label>
			<div>
				<select id="status" name="status" class="form-control">
					<option value="OPEN">OPEN: Accepting new members</option>
					<option value="CLOSED">CLOSED: Not accepting new members</option>
					<option value="FULL">FULL: Reached member limit</option>
				</select>
			</div>
		</div>

		<!-- Multiple Radios -->
		<div class="form-group">
			<label class="control-label" for="cost">Cost Involved?</label>
			<div>
				<div class="radio">
					<label for="cost-0">
						<input type="radio" name="cost" id="cost-0" value="Yes" checked="checked"> Yes (e.g., books)
					</label>
				</div>
				<div class="radio">
					<label for="cost-1">
						<input type="radio" name="cost" id="cost-1" value="No"> No
					</label>
				</div>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="maxsize">Max Group Size</label>
			<div>
				<select id="maxsize" name="maxsize" class="form-control input-md" required>
					<?php

					for ($i = 6; $i <= 20; $i++) {
						echo '<option value="'. $i .'"'. ($i == 12 ? ' selected':'') .'>'. $i .'</option>';
					}

					?>
					<option value="null">No Maximum</option>
				</select>
				<span class="help-block">Preferred maximum number of attendees</span>
			</div>
		</div>

		<!-- Multiple Checkboxes -->
		<div class="form-group">
			<label class="control-label" for="days">Group Meeting Days</label>
			<div>
				<div class="checkbox">
					<label for="days-0">
						<input type="checkbox" name="days[]" id="days-0" value="Sunday"> Sunday
					</label>
				</div>
				<div class="checkbox">
					<label for="days-1">
						<input type="checkbox" name="days[]" id="days-1" value="Monday"> Monday
					</label>
				</div>
				<div class="checkbox">
					<label for="days-2">
						<input type="checkbox" name="days[]" id="days-2" value="Tuesday"> Tuesday
					</label>
				</div>
				<div class="checkbox">
					<label for="days-3">
						<input type="checkbox" name="days[]" id="days-3" value="Wednesday"> Wednesday
					</label>
				</div>
				<div class="checkbox">
					<label for="days-4">
						<input type="checkbox" name="days[]" id="days-4" value="Thursday"> Thursday
					</label>
				</div>
				<div class="checkbox">
					<label for="days-5">
						<input type="checkbox" name="days[]" id="days-5" value="Friday"> Friday
					</label>
				</div>
				<div class="checkbox">
					<label for="days-6">
						<input type="checkbox" name="days[]" id="days-6" value="Saturday"> Saturday
					</label>
				</div>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="time">Meeting Time</label>
			<div>
				<input id="time" name="time" type="text" placeholder="Meeting Time" class="form-control input-md" required>
				<span class="help-block">Start time and stop time (e.g., 7pm - 8pm)</span>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="startdate">Start Date</label>
			<div>
				<input id="startdate" name="startdate" type="text" placeholder="Start Date" class="form-control input-md" required>
				<span class="help-block">Day the small group starts meeting (e.g., <span id="start_date_ex">Jan 19, 2015</span>)</span>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="enddate">End Date</label>
			<div>
				<input id="enddate" name="enddate" type="text" placeholder="End Date" class="form-control input-md" required>
				<span class="help-block">Date the small group stops meeting (e.g., <span id="end_date_ex">March 31, 2015</span>)</span>
			</div>
		</div>

		<!-- Multiple Radios (inline) -->
		<div class="form-group">
			<label class="control-label" for="gender">Group gender preference?</label>
			<div>
				<label class="radio-inline" for="gender-0">
					<input type="radio" name="gender" id="gender-0" value="None" checked="checked"> None
				</label>
				<label class="radio-inline" for="gender-1">
					<input type="radio" name="gender" id="gender-1" value="Male"> Male
				</label>
				<label class="radio-inline" for="gender-2">
					<input type="radio" name="gender" id="gender-2" value="Female"> Female
				</label>
			</div>
		</div>

		<!-- Multiple Radios (inline) -->
		<div class="form-group">
			<label class="control-label" for="demographic">Will your curriculum have a demographic focus?</label>
			<div>
				<label class="radio-inline" for="demographic-0">
					<input type="radio" name="demographic" id="demographic-0" value="None" checked="checked"> None
				</label>
				<label class="radio-inline" for="demographic-1">
					<input type="radio" name="demographic" id="demographic-1" value="Married"> Married
				</label>
				<label class="radio-inline" for="demographic-2">
					<input type="radio" name="demographic" id="demographic-2" value="Parenting"> Parenting
				</label>
				<label class="radio-inline" for="demographic-3">
					<input type="radio" name="demographic" id="demographic-3" value="Single"> Single
				</label>
				<label class="radio-inline" for="demographic-4">
					<input type="radio" name="demographic" id="demographic-4" value="Young Adult"> Young Adult
				</label>
			</div>
		</div>

		<!-- Multiple Radios (inline) -->
		<div class="form-group">
			<label class="control-label" for="childcare">Will child care be needed?</label>
			<div>
				<label class="radio-inline" for="childcare-0">
					<input type="radio" name="childcare" id="childcare-0" value="No" checked="checked"> No
				</label>
				<label class="radio-inline" for="childcare-1">
					<input type="radio" name="childcare" id="childcare-1" value="Yes"> Yes
				</label>
				<span class="help-block">Child care will most likely be provided if you meet at Frontline. If meeting offsite, childcare <em>may</em> be available.</span>
			</div>
		</div>

		<!-- Text input-->
		<div class="form-group">
			<label class="control-label" for="location">Meeting Location</label>
			<div>
				<input id="location" name="location" type="text" placeholder="Meeting Location" class="form-control input-md" required>
				<span class="help-block">Enter "Frontline" or a full address if elsewhere.</span>
			</div>
		</div>

		<!-- Button -->
		<div class="form-group">
			<label class="control-label" for="submit_button"></label>
			<div>
				<button type="submit" id="submit_button" name="submit_button" class="btn btn-primary">Submit</button>
			</div>
		</div>

	</fieldset>
</form>
<script>
$(function() {

	$('#semester').change(function() {

		var $opt = $('#semester option:selected');

		if ($('#startdate').val() == '')
			$('#startdate').val( $opt.attr('data-start') );

		$('#start_date_ex').html( $opt.attr('data-start') );

		if ($('#enddate').val() == '')
			$('#enddate').val( $opt.attr('data-end') );

		$('#end_date_ex').html( $opt.attr('data-end') );

	}).change();

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
			leader: {
				validators: {
					notEmpty: {
						message: 'The leader name is required'
					},
					stringLength: {
						min: 6,
						message: 'The leader name must be more than 6 characters long'
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
			smallgroup: {
				validators: {
					notEmpty: {
						message: 'The small group name is required'
					},
					stringLength: {
						min: 3,
						max: 30,
						message: 'The small group name must be more than 3 characters and less than 30 characters long'
					}
				}
			},
			description: {
				validators: {
					notEmpty: {
						message: 'The topic description is required'
					},
					stringLength: {
						min: 5,
						message: 'The topic description must be more than 5 characters long'
					}
				}
			},
			'days[]': {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Please select at least one meeting day'
					}
				}
			}
		}
	});

});
</script>