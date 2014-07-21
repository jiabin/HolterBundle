// Application
var HolterApp = angular.module('HolterApp', []);

HolterApp.controller('HolterCtrl', function($scope, $http) {
  // Data loader
  $scope.loadData = function () {
    // Status
    $http.get('/api/status.json').error(function() {
      alert('Houston, we have a problem');
      location.reload();
    }).then(function(res) {
      // Put in groups
      var groups = {};
      for (i in res.data.checks) {
        var check = res.data.checks[i];
        var group = check.display_group;
        if (group == undefined || groups == null) {
          group = 'default';
        }
        if (groups[group] == undefined) {
          groups[group] = {
            name: group,
            checks: []
          };
        }
        groups[group].checks.push(check);
      }
      $scope.groups = groups;
      $scope.status = res.data;
      // Change favicon
      changeFavicon(res.data.status_name);
    });
    $scope.updatedAt = new Date();
  };

  // Initial load
  $scope.loadData();

  // Data refresh interval
  setInterval(function() {
    $scope.loadData();
  }, 30000);
});

angular.module('HolterApp').filter('fromNow', function() {
  return function(date) {
    return moment(date).fromNow();
  }
});