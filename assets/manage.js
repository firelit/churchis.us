var churchis = angular.module('churchis', [
  'ngRoute',
  'churchisServices',
  'churchisControllers'
]);

churchis.config(['$routeProvider',
	function($routeProvider) {

		$routeProvider
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
			.otherwise({
				redirectTo: '/groups'
			});

	}
]);

var churchisServices = angular.module('churchisServices', ['ngResource']);

churchisServices.factory('Group', ['$resource',
	function($resource) {

		return $resource('/api/groups/:groupId', {groupId: '@id'}, {
			query: { method: 'GET', isArray: true },
			update: { method: 'PUT' }
		});

	}
]);

churchisServices.factory('Member', ['$resource',
	function($resource) {

		return $resource('/api/members/:memberId', {memberId: '@id'}, {
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

	}
]);

churchisControllers.controller('GroupDetailCtl', ['$scope', '$routeParams', 'Group',
	function($scope, $routeParams, Group) {

		$scope.group = Group.get({groupId: $routeParams.groupId});

		$scope.updateGroup = function() {
			Group.update($scope.group);
			$scope.editmode = false;
		}

		$scope.cancelEdit = function() {
			$scope.group = Group.get({groupId: $routeParams.groupId});
			$scope.editmode = false;
		}

		$scope.dayToggle = function(day) {
			var idx = $scope.group.days.indexOf(day);

			// is currently selected
			if (idx > -1) $scope.group.days.splice(idx, 1);
			// is newly selected
			else  $scope.group.days.push(day);

		}

		var range = new Array();
		for (var i = 6; i < 20; i++)
			range.push(i);

		$scope.avail_maxsize = range;
		$scope.avail_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

	}
]);

churchisControllers.controller('MemberListCtl', ['$scope', 'Member', 
	function($scope, Member) {

		$scope.members = Member.query();
		
	}
]);

churchisControllers.controller('MemberDetailCtl', ['$scope', '$routeParams', 'Member',
	function($scope, $routeParams, Member) {

		$scope.member = Member.get({memberId: $routeParams.memberId});
		
	}
]);