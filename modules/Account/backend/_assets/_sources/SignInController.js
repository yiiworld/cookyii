"use strict";

angular.module('BackendApp')

  .controller('SignInController', [
    '$scope', '$element', '$http', '$mdToast',
    function ($scope, $modal, $http, $mdToast) {
      var _config = {},
        defaultValues = {
          email: null,
          password: null,
          remember: true
        },
        defaultErrors = {};

      $scope.in_progress = false;

      $scope.init = function (config) {
        _config = angular.extend({}, _config, config);
      };

      resetData();
      resetErrors();

      $scope.submit = function (e) {
        var $form = angular.element(e.target);

        $scope.in_progress = true;
        resetErrors();

        $http({
          method: 'POST',
          url: $form.prop('action'),
          data: jQuery.param({
            _csrf: angular.element('meta[name="csrf-token"]').attr('content'),
            SignInForm: $scope.data
          }),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        })
          .success(function (response) {
            if (true === response.result) {
              location.href = response.redirect;
            } else {
              if (typeof response.errors !== 'undefined') {
                $scope.error = response.errors;
              } else {
                toast($mdToast, 'danger', {
                  message: response.message
                });
              }
            }
          })
          .error(defaultHttpErrorHandler)
          .finally(function () {
            $scope.in_progress = false;
          });

        e.preventDefault();
      };

      function resetData() {
        $scope.data = angular.copy(defaultValues);
      }

      function resetErrors() {
        $scope.error = angular.copy(defaultErrors);
      }
    }
  ]);