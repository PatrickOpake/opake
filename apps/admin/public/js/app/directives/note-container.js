(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('noteContainer', ['$rootScope', function ($rootScope) {
		return {
			restrict: "AC",
			replace: true,
			bindToController: {
				noteVm: "=ctrl",
				note: "=note"
			},
			controller: function () {
			},
			controllerAs: 'ctrl',
			link: function (scope, elem, attrs, ctrl) {
				elem.hover(function() {
					if (ctrl.note.user_id == $rootScope.loggedUser.id) {
						scope.$apply(function () {
							ctrl.note.show_edit_icons = true;
							ctrl.note.show_flag_icon = true;
							ctrl.note.show_bell_icon = true;
						});
						elem.addClass('hovered');
					}
				}, function() {
					scope.$apply(function () {
						ctrl.note.show_edit_icons = false;
						ctrl.note.show_flag_icon = false;
						ctrl.note.show_bell_icon = false;
					});
					elem.removeClass('hovered');
				});

				scope.$watch(function () {
					if (ctrl.note.edit) {
						elem.find('input').focus();
					}
				});
				
			}
		};
	}]);

})(opakeCore, angular);