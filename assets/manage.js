$(function() {
	// Collapse navbar when menu itme clicked (in mobile)
	$(document).ready(function () {
		$(".navbar-nav li a").click(function(event) {
			$(".navbar-collapse").collapse('hide');
		});
	});

});

function copyToClipboard(text) {
	window.prompt("Copy to clipboard: Ctrl+C or Cmd+C", text);
}

var churchis = angular.module('churchis', [
	'ngRoute',
	'churchisServices',
	'churchisControllers'
]);

churchis.filter("nl2br", function($filter) {
	return function(data) {
		if (!data) return data;
		return data.replace(/\n\r?/g, '<br />');
	};
});

churchis.config(['$routeProvider',
	function($routeProvider) {

		$routeProvider
			.when('/dashboard', {
				templateUrl: '/views/manage/partials/dashboard.html',
				controller: 'DashboardCtl'
			})
			.when('/groups', {
				templateUrl: '/views/manage/partials/group-list.html',
				controller: 'GroupListCtl'
			})
			.when('/groups/:groupId', {
				templateUrl: '/views/manage/partials/group-detail.html',
				controller: 'GroupDetailCtl'
			})
			.when('/members', {
				templateUrl: '/views/manage/partials/member-list.html',
				controller: 'MemberListCtl'
			})
			.when('/members/:memberId', {
				templateUrl: '/views/manage/partials/member-detail.html',
				controller: 'MemberDetailCtl'
			})
			.when('/users', {
				templateUrl: '/views/manage/partials/user-list.html',
				controller: 'UserListCtl'
			})
			.when('/users/:userId', {
				templateUrl: '/views/manage/partials/user-detail.html',
				controller: 'UserDetailCtl'
			})
			.otherwise({
				redirectTo: function() { if (window.is_admin) return '/dashboard'; else return '/groups'; }
			});

	}
]);

angular.module('GroupFilters', []).filter('memberdisplay', function() {
	return function(input) {
		if (!input || (input == 'null')) return 'No maximum';
		return input + ' members';
	};
});

var churchisServices = angular.module('churchisServices', ['ngResource', 'GroupFilters']);

churchisServices.factory('Group', ['$resource',
	function($resource) {

		return $resource('/api/groups/:groupId', {groupId: '@id'}, {
			query: { method: 'GET', isArray: true },
			update: { method: 'PUT' },
			delete: { method: 'DELETE' }
		});

	}
]);

churchisServices.factory('Member', ['$resource',
	function($resource) {

		return $resource('/api/members/:memberId', {memberId: '@id'}, {
			query: { method: 'GET', isArray: true },
			update: { method: 'PUT' },
			save: { method: 'POST' },
			delete: { method: 'DELETE' }
		});

	}
]);

churchisServices.factory('User', ['$resource',
	function($resource) {

		return $resource('/api/users/:userId', {userId: '@id'}, {
			query: { method: 'GET', isArray: true }
		});

	}
]);

var churchisControllers = angular.module('churchisControllers', ['churchisServices']);

churchisControllers.controller('HeaderCtl', ['$scope', '$location', 
	function($scope, $location) {

		$scope.isActive = function(viewLocation) {
			var regex = new RegExp(viewLocation);
			return regex.test( $location.path() );
		}
		
	}
]);

churchisControllers.controller('GroupListCtl', ['$scope', '$location', 'Group', 
	function($scope, $location, Group) {

		$scope.loading = true;

		$scope.groups = Group.query();

		$scope.viewGroup = function(group) {
			$location.path('/groups/' + group.id);
		}

		$scope.statusToLabel = function(stat) {
			if (stat == 'OPEN') return 'label label-success';
			else if (stat == 'FULL') return 'label label-danger';
			else if (stat == 'CLOSED') return 'label label-info';
			else if (stat == 'CANCELED') return 'label label-default';
		}

	}
]);

churchisControllers.controller('GroupDetailCtl', ['$scope', '$routeParams', '$http', '$location', 'Group', 'Member', 
	function($scope, $routeParams, $http, $location, Group, Member) {

		$scope.group = Group.get({groupId: $routeParams.groupId});

		$scope.updateGroup = function() {
			Group.update($scope.group);
			$scope.edit_mode = false;
		}

		$scope.cancelEdit = function() {
			$scope.group = Group.get({groupId: $routeParams.groupId});
			$scope.edit_mode = false;
		}

		$scope.dayToggle = function(day) {
			var idx = $scope.group.days.indexOf(day);

			// is currently selected
			if (idx > -1) $scope.group.days.splice(idx, 1);
			// is newly selected
			else  $scope.group.days.push(day);

		}

		$scope.deleteGroup = function() {
			var conf = confirm('Delete group? This cannot be undone.');
			if (!conf) return;
			
			$scope.group.$delete();
			$location.path('/groups');
		}

		$scope.copyEmails = function() {
			var emails = '';

			for (x in $scope.group.members) {
				if ($scope.group.members[x].email) 
					emails = emails +', '+ $scope.group.members[x].email;
			}

			if (emails.length < 3) {
				alert('No email addresses to copy :(');
				return;
			}

			emails = emails.substr(2);

			copyToClipboard(emails);
		}

		var range = new Array();
		for (var i = 6; i <= 20; i++)
			range.push({
				value: i,
				name: i + ' members'
			});

		range.push({
			value: 'null',
			name: 'No maximum'
		});

		$scope.is_admin = window.is_admin;
		$scope.avail_maxsize = range;
		$scope.avail_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		$scope.avail_status = [
			{value: 'OPEN', name: 'OPEN: Accepting new members'},
			{value: 'FULL', name: 'FULL: Reached member limit'},
			{value: 'CLOSED', name: 'CLOSED: Not accepting new members'},
			{value: 'CANCELED', name: 'CANCELED: Group has been canceled'}
		];

		$scope.statusToLabel = function(stat) {
			if (stat == 'OPEN') return 'label label-success';
			else if (stat == 'FULL') return 'label label-danger';
			else if (stat == 'CLOSED') return 'label label-info';
			else if (stat == 'CANCELED') return 'label label-default';
		}

		$scope.createMember = function() {
			var member = new Member();

			member.name = $("#member-name").val();
			member.email = $("#member-email").val();
			member.phone = $("#member-phone").val();
			member.group = $routeParams.groupId;

			member.$save(function() {

				$http
					.post('/api/groups/'+ $routeParams.groupId +'/members/'+ member.id)
					.success(function() {

						$("#member-name").val('');
						$("#member-email").val('');
						$("#member-phone").val('');

						$scope.group = Group.get({groupId: $routeParams.groupId});
						$scope.member_mode = false;

					})
					.error(function() {
						alert('An error occured');
					});

			});
		}

		$scope.removeMember = function(memberId) {
			var conf = confirm('Remove this member from the group?');
			if (!conf) return;

			$http
				.delete('/api/groups/'+ $routeParams.groupId +'/members/'+ memberId)
				.success(function() {

					$('#member-'+ memberId).fadeOut(function() {
						
						$scope.group = Group.get({groupId: $routeParams.groupId});

					});

				})
				.error(function() {
					alert('An error occured');
				});

			return false;

		}

		$scope.add_meeting = false;
		$scope.date = new Date();

		$scope.createMeeting = function() {

			var meeting = {};

			meeting.date = $("#meeting-date").val();
			meeting.attendance = $("#meeting-attend").val();
			meeting.group_size = $("#meeting-size").val();
			meeting.group = $routeParams.groupId;

			$http
				.post('/api/groups/'+ $routeParams.groupId +'/meetings', meeting)
				.success(function() {

					$("#meeting-date").val('');
					$("#meeting-attend").val('');
					$("#meeting-size").val('');

					$scope.group = Group.get({groupId: $routeParams.groupId});
					$scope.add_meeting = false;

				})
				.error(function(data) {
					alert('An error occured: '+ data.errors[0].message);
				});

		}

		$scope.editMeeting = function(meetingId) {

			$http
				.edit('/api/groups/'+ $routeParams.groupId +'/meetings/'+ meetingId)
				.success(function() {

					$scope.group = Group.get({groupId: $routeParams.groupId});

				})
				.error(function() {
					alert('An error occured');
				});

		}

		$scope.removeMeeting = function(meetingId) {
			var conf = confirm('Delete this meeting?');
			if (!conf) return;

			$http
				.delete('/api/groups/'+ $routeParams.groupId +'/meetings/'+ meetingId)
				.success(function() {

					$('#meeting-'+ meetingId).fadeOut(function() {
						
						$scope.group = Group.get({groupId: $routeParams.groupId});

					});

				})
				.error(function() {
					alert('An error occured: '+ data.errors[0].message);
				});

			return false;

		}

	}
]);

churchisControllers.controller('MemberListCtl', ['$scope', '$location', 'Member', 
	function($scope, $location, Member) {

		$scope.members = Member.query();
		
		$scope.viewMember = function(member) {
			$location.path('/members/' + member.id);
		}

	}
]);

churchisControllers.controller('MemberDetailCtl', ['$scope', '$routeParams', '$location', 'Member',
	function($scope, $routeParams, $location, Member) {

		$scope.member = Member.get({memberId: $routeParams.memberId});
		
		$scope.cancelEdit = function() {
			$scope.member = Member.get({memberId: $routeParams.memberId});
			$scope.edit_mode = false;
		}

		$scope.updateMember = function() {
			Member.update($scope.member);
			$scope.edit_mode = false;
		}

		$scope.deleteMember = function() {
			var conf = confirm('Delete member? This cannot be undone.');
			if (!conf) return;

			$scope.member.$delete();
			$location.path('/members');
		}

		$scope.is_admin = window.is_admin;
		$scope.avail_states = ['MI'];

	}
]);

churchisControllers.controller('UserListCtl', ['$scope', '$location', 'User', 
	function($scope, $location, User) {

		$scope.users = User.query();
		
		$scope.viewUser = function(user) {
			$location.path('/users/' + user.id);
		}

	}
]);

churchisControllers.controller('UserDetailCtl', ['$scope', '$routeParams', 'User',
	function($scope, $routeParams, User) {

		$scope.user = User.get({userId: $routeParams.userId});

	}
]);
