/**
 * Created by りか on 2015/01/26.
 */
/**
 * Registrations.edit Javascript
 *
 * @param {string} Controller name
 */

NetCommonsApp.controller('Registrations.add',
    function($scope, NetCommonsBase) {
      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(registrations, createOption) {
        $scope.registrations = registrations;
        $scope.createOption = createOption;
        $scope.templateFile = '';
        $scope.pastRegistrationSelect = '';
      };
      /**
       * Registration be disable to goto next
       *
       * @return {bool}
       */
      $scope.templateFileSet = function() {
        var el = jQuery('#templateFile');
        $scope.templateFile = el[0].value;
      };
    });

NetCommonsApp.controller('Registrations.setting',
    function($scope, NetCommonsBase, NetCommonsWysiwyg) {

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(frameId, registrations) {
        $scope.frameId = frameId;
        $scope.registrations = registrations;
      };

      /**
       * tinymce
       *
       * @type {object}
       */
      $scope.tinymce = NetCommonsWysiwyg.new();

      /**
       * focus DateTimePicker
       *
       * @return {void}
       */
      $scope.setMinMaxDate = function(ev, min, max) {
        // 自分
        var curEl = ev.currentTarget;
        var elId = curEl.id;

        // minの制限は
        var minDate = $('#publish_start').val();
        // maxの制限は
        var maxDate = $('#publish_end').val();

        if (elId == 'publish_start') {
          $('#publish_start').data('DateTimePicker').maxDate(maxDate);
        } else {
          $('#publish_end').data('DateTimePicker').minDate(minDate);
        }
      };
      /**
       * delete button click
       *
       * @return {void}
       */
      $scope.deleteRegistration = function(e, message) {
        if (confirm(message)) {
          angular.element('#registrationDeleteForm-' +
              $scope.frameId).submit();
          return true;
        }
        e.stopPropagation();
        return false;
      };
      $scope.Date = function(dateStr) {
        if (Date.parse(dateStr)) {
          return new Date(dateStr);
        } else {
          return new Date();
        }
      };
    });
