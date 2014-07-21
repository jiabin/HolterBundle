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

HolterApp.filter('orderObjectBy', function(){
 return function(input, attribute) {
    if (!angular.isObject(input)) return input;

    console.debug(input);
    var array = [];
    for(var objectKey in input) {
        array.push(input[objectKey]);
    }

    array.sort(function(a, b){
        a = parseInt(a[attribute]);
        b = parseInt(b[attribute]);
        return a - b;
    });
    return array;
 }
});