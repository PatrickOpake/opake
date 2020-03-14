// Card save
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CardCrtl', [
		'$scope',
		'$http',
		'$window',
		'$filter',
		'BeforeUnload',
		'CardConst',
		'Cards',
		'View',
		'CardStaff',
		'Tools',
		function ($scope, $http, $window, $filter, BeforeUnload, CardConst, Cards, View, CardStaff, Tools) {

			$scope.card_const = CardConst;

			var vm = this;

			vm.isShowForm = false;
			vm.isCardPrinting = false;
			vm.errors = null;

			vm.init = function (cardId, user) {
				BeforeUnload.reset(true);
				if (cardId) {
					Cards.getCard(cardId).then(function (data) {
						vm.card = new CardStaff(data);
					});
				} else {
					vm.card = new CardStaff();
					vm.card.user = user;
					vm.isShowForm = true;
				}
			};

			vm.edit = function () {
				vm.originalCard = angular.copy(vm.card);
				vm.isShowForm = true;
			};

			vm.cancel = function () {
				if (vm.card.id) {
					vm.card = vm.originalCard;
					vm.isShowForm = false;
					vm.errors = null;
				} else {
					history.back();
				}
			};

			vm.canSave = function () {
				var result = true;
				angular.forEach(vm.card.notes, function (note) {
					if (!note.name || !note.text) {
						result = false;
					}
				});

				return result;
			};
			
			vm.save = function () {
				var isCreation = !vm.card.id;
				Cards.savePrefCard(vm.card, function (result) {
					vm.errors = null;
					if (result.data.id) {
						if (isCreation) {
							BeforeUnload.reset(true);
							$window.location = '/cards/' + $scope.org_id + '/view/' + result.data.id;
						} else {
							vm.isShowForm = false;
							vm.init(result.data.id);
						}
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
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

			vm.print = function () {
				vm.isCardPrinting = true;
				$http.post('/cards/ajax/' + $scope.org_id + '/exportStaffPrefCard/', $.param({cards: [vm.card.id]})).then(function (result) {
					if (result.data.success) {
						Tools.print(location.protocol + '//' + location.host + result.data.url);
					}
				}).finally(function() {
					vm.isCardPrinting = false;
				});
			};

			vm.getView = function () {
				var view = 'cards/staff/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.itemHasInventory = function (item) {
				if (item.inventory && (item.inventory != 'null')) {
					return true;
				}

				return false;
			};

			vm.uploadTemplate = function (files) {

				var fd = new FormData();
				angular.forEach(files, function (file) {
					fd.append('file', file);
				});

				$http.post('/cards/ajax/' + $scope.org_id + '/uploadTemplate/', fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				}).then(function (result) {
					vm.errors = null;
					if (result.data.success) {
						vm.card.name = result.data.name;
						vm.card.case_types = result.data.case_types;
						vm.card.notes.length = 0;
						angular.forEach(result.data.notes, function (note) {
							vm.card.notes.push(note);
						});
						vm.card.items.length = 0;
						angular.forEach(result.data.items, function (item) {
							if (item.stage_id) {
								item.stage_id = item.stage_id.toString();
							}
							vm.card.items.push(item);
						});
						$scope.$broadcast('PrefCard.TemplateUploaded', vm.card);
					} else {
						vm.errors = result.data.errors;
					}
				});
			};
		}]);

})(opakeApp, angular);
