// app.js
app.controller('LoginController', ['$scope', '$http', '$window', function($scope, $http, $window) {
    $scope.email = '';
    $scope.password = '';
    $scope.message = '';

    $scope.login = function($event) {
        $event.preventDefault();
        $http.post('/auth/login', { email: $scope.email, password: $scope.password })
            .then(function(response) {
                if (response.data.success) {
                    $window.location.href = '/welcome.php';
                } else {
                    $scope.message = response.data.message;
                }
            })
            .catch(function(error) {
                console.error(error);
                $scope.message = 'An error occurred. Please try again.';
            });
    };
}]);