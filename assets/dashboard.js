// Chart.defaults.global.responsive = true;

$(function() {

	$('body')
		.on('click', '.dashboard-copy', function() {
			copyToClipboard( $(this).data('emails') );
		})
		.on('change', '#semesterPicker', function() {
			$.ajax('/api/dashboard', {
				type: 'POST',
				data: JSON.stringify({
					'new_semester': $('#semesterPicker').val()
				}),
				success: function() {
					window.location.reload();
				}
			});
		})
		.on('click', '#signup-enable', function() {
			$.ajax('/api/dashboard', {
				type: 'POST',
				data: JSON.stringify({
					'signup': true
				}),
				success: function() {
					window.location.reload();
				}
			});
		})
		.on('click', '#signup-disable', function() {
			$.ajax('/api/dashboard', {
				type: 'POST',
				data: JSON.stringify({
					'signup': false
				}),
				success: function() {
					window.location.reload();
				}
			});
		});

});

churchisControllers.controller('DashboardCtl', ['$scope', '$location', '$http',
	function($scope, $location, $http) {

		$http.get('/api/dashboard')
			.success(function(data, status, headers, config) {

				var ctx = $('#signupChart').get(0).getContext('2d');
				var myLineChart = new Chart(ctx).Line({
					labels: data.signups.labels,
					datasets: [
						{
							label: "Member Sign-Ups",
							fillColor: "rgba(151,187,205,0.2)",
							strokeColor: "rgba(151,187,205,1)",
							pointColor: "rgba(151,187,205,1)",
							pointStrokeColor: "#fff",
							pointHighlightFill: "#fff",
							pointHighlightStroke: "rgba(151,187,205,1)",
							data: data.signups.data
						}
					]
				});

				$('#memberCount').html(data.counts.members);
				$('#childCount').html(data.counts.children);
				$('#groupCount').html(data.counts.groups);
				$('#fullCount').html(data.counts.fullGroups);

				$('#dashboard-leader-copy').data('emails', data.emails.leaders);
				$('#dashboard-member-copy').data('emails', data.emails.members);

				var semPick = $('#semesterPicker').empty();

				for (i in data.semesters) {
					semester = data.semesters[i];
					var opt = $('<option></option>').attr('value', semester.id).html(semester.name).appendTo(semPick);
					if (semester.selected) opt.prop('selected', true);
				}

				$scope.resolved = true;

			})
			.error(function(data, status, headers, config) {

				$('#signupChart').replaceWith('<div class="alert alert-danger">Could not load graph data.</div>');

				$scope.resolved = true;

			});

	}
]);