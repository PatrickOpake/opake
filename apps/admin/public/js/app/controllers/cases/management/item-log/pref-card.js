// Patient Preference Card
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CasePrefCardCtrl', [
		'$location',
		'$window',
		'$scope',
		'$http',
		'$filter',
		'View',
		'CardStaff',
		'Cards',
		'CardConst',
		'Tools',
		'BeforeUnload',
		function ($location, $window, $scope, $http, $filter, View, CardStaff, Cards, CardConst, Tools, BeforeUnload) {

			var vm = this;

			vm.action = 'view';
			vm.errors = null;
			vm.cardConst = CardConst;
			vm.caseObj = null;
			vm.isCardPrinting = false;

			var params = $location.search();
			if (angular.isDefined(params.fromCardsQueue) && (params.fromCardsQueue == 1)) {
				vm.fromCardsQueue = true;
			}

			vm.init = function (caseItem) {
				vm.caseObj = caseItem;
				Cards.getCaseCard(caseItem.id).then(function (data) {
					if(data && data.card) {
						vm.card = new CardStaff(data.card);
					} else {
						vm.card = new CardStaff();
					}
					vm.card.case_id = caseItem.id;
					vm.card.user_id = caseItem.users[0].id;
					vm.templates = data.templates;
					if (vm.fromCardsQueue) {
						vm.edit();
					}
				});
			};

			vm.edit = function () {
				vm.originalCard = angular.copy(vm.card);
				vm.action = 'edit';
				BeforeUnload.addForms(vm.originalCard , vm.card, 'pref-card');
			};

			vm.canSave = function () {
				if (angular.equals(vm.card, vm.originalCard)) {
					return false;
				} else {
					var result = true;
					angular.forEach(vm.card.notes, function (note) {
						if (!note.name || !note.text) {
							result = false;
						}
					});

					return result;
				}
			};

			vm.getView = function () {
				var view = 'cases/cm/item_log/pref-cards/' + vm.action + '.html';
				return View.get(view);
			};

			vm.cancel = function () {
				vm.card = vm.originalCard;
				vm.action = 'view';
				vm.errors = null;
				BeforeUnload.clearForms('pref-card');
			};

			vm.save = function (status, isFinish) {
				vm.card.status = status;
				Cards.saveCard(vm.card).then(function (result) {
					vm.errors = null;
					if (result.id) {
						if (isFinish && vm.fromCardsQueue) {
							vm.originalCard.status = status;
							vm.toCardsQueue();
						} else {
							vm.action = 'view';
							vm.init(vm.caseObj);
						}
					} else if (result.errors) {
						vm.errors = result.errors.split(';');
					}
				});
			};

			vm.stagesChanged = function (stagesWithItems, fromView) {
				vm.card.stages = {};
				angular.forEach(stagesWithItems, function (value, key) {
					vm.card.stages[value.stage.id] = {position: key};
				});
				$scope.$broadcast('PrefCard.StagesSortUpdated', vm.card.stages);
				if (fromView) {
					vm.save();
				}
			};

			vm.itemsPositionsChanged = function (stagesWithItems, fromView) {
				var items = [];
				angular.forEach(stagesWithItems, function (stageWithItems) {
					angular.forEach(stageWithItems.items, function (item) {
						item.inventory_id = parseInt(item.inventory.id);
					});

					var itemsInventoryIds = [];
					angular.forEach(stageWithItems.items, function (item, key) {
						item.position = key;
						item.stage_id = stageWithItems.stage.id;
						if ($filter('filter')(itemsInventoryIds, item.inventory_id, true).length) {
							var similarItem = $filter('filter')(items, {inventory_id: item.inventory_id}, true)[0];
							similarItem.default_qty = parseInt(similarItem.default_qty) + parseInt(item.default_qty);
							similarItem.actual_use = parseInt(similarItem.actual_use) + parseInt(item.actual_use);
						} else {
							items.push(item);
							itemsInventoryIds.push(item.inventory_id);
						}
					});
				});
				vm.card.items = items;
				if (fromView) {
					vm.save();
				}
				$scope.$broadcast('PrefCard.CardUpdated', vm.card);
			};

			vm.toCardsQueue = function () {
				$window.location = '/cases/' + $scope.org_id + '/cards/';
			};

			vm.print = function () {
				vm.isCardPrinting = true;
				$http.post('/cards/ajax/' + $scope.org_id + '/exportCaseStaffPrefCard/' + vm.caseObj.id).then(function (result) {
					if (result.data.success) {
						Tools.print(location.protocol + '//' + location.host + result.data.url);
					}
				}).finally(function() {
					vm.isCardPrinting = false;
				});
			};

			vm.selectTemplate = function () {
				vm.card.notes = [];
				vm.card.items = [];
				angular.forEach(vm.card.template.notes, function (item) {
					item.id = null;
					vm.card.notes.push(item);
				});
				angular.forEach(vm.card.template.items, function (item) {
					item.id = null;
					vm.card.items.push(item);
				});
				$scope.$broadcast('PrefCard.CardUpdated', vm.card);

			};

		}]);

})(opakeApp, angular);
