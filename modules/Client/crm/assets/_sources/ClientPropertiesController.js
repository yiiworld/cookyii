"use strict";

angular.module('CrmApp')

  .controller('ClientPropertiesController', [
    '$scope', '$window', '$location', '$http', '$timeout', 'ToastrScope', '$mdDialog',
    function ($scope, $window, $location, $http, $timeout, ToastrScope, $mdDialog) {

      var query = $location.search(),
        toastr = ToastrScope($scope);

      $scope.limit = 5;
      $scope.detailedList = false;

      $scope.isNewProperty = false;
      $scope.editedProperty = null;

      $scope.$on('clientDataReloaded', function (e, client) {
        if (query.prop === '__new') {
          $scope.create();
        } else {
          angular.forEach(client.properties, function (item) {
            if (item.key === query.prop) {
              $scope.edit(item);
            }
          });
        }
      });

      $scope.toggleAllProperties = function () {
        $scope.detailedList = !$scope.detailedList;
      };

      $scope.cancel = function () {
        $scope.isNewProperty = false;
        $scope.editedProperty = null;

        $location.search('prop', null);
      };

      $scope.create = function () {
        $scope.isNewProperty = true;
        $scope.editedProperty = {};

        $location.search('prop', '__new');
      };

      $scope.edit = function (property) {
        $scope.isNewProperty = false;
        $scope.editedProperty = property;

        $timeout(function () {
          // need for adjust angular elastic
          angular.element($window)
            .trigger('resize');
        }, 100);

        $location.search('prop', $scope.editedProperty.key);
      };

      $scope.save = function (property, createNew) {
        createNew = typeof createNew === 'boolean'
          ? createNew
          : false;

        $http({
          method: 'POST',
          url: '/client/rest/property',
          data: {
            key: query.prop,
            client_id: $scope.$parent.getClientId(),
            property: $scope.editedProperty
          }
        })
          .success(function (response) {
            if (response.result === false) {
              if (typeof response.errors !== 'undefined') {
                angular.forEach(response.errors, function (error) {
                  toastr.error(error);
                });
              } else {
                toastr.error(response.message);
              }
            } else {
              if (!createNew) {
                $location.search('prop', $scope.editedProperty.key);
              }

              $scope.$emit('reloadClientData');

              toastr.success(response.message);
            }
          })
          .error(function (response, status) {
            toastr.error(response.message);
          });
      };

      $scope.remove = function (property, e) {
        var confirm = $mdDialog.confirm()
          .parent(angular.element(document.body))
          .title('Would you like to delete this property?')
          .ok('Please do it!')
          .cancel('Cancel')
          .targetEvent(e);

        $mdDialog.show(confirm).then(function () {
          $http({
            method: 'DELETE',
            url: '/client/rest/property',
            params: {
              key: query.prop,
              client_id: $scope.$parent.getClientId()
            }
          })
            .success(function (response) {
              if (response.result === false) {
                toastr.error(response.message);
              } else {
                $scope.$emit('reloadClientData');

                $location.search('prop', null);

                toastr.success(response.message);

                $scope.editedProperty = null;
              }
            })
            .error(function (response, status) {
              toastr.error(response.message);
            });
        });
      };
    }
  ]);