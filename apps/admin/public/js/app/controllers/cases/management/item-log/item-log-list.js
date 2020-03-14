// Item Log
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseItemLogListCtrl', [
		'$scope',
		'$http',
		'$filter',
		'View',
		'Cards',
		function ($scope, $http, $filter, View, Cards) {

			var vm = this;

			vm.search_params = {};
			vm.search_items = [];

			var caseItem = null,
				card = null;

			vm.init = function (caseObj, cardObj) {
				caseItem = caseObj;
				card = cardObj;
			};

			vm.getItems = function () {
				if (card) {
					return $filter('filter')(card.items, {status: 1});
				}
				return caseItem.inventory_items;
			};

			var saveNewItem = function (item) {
				if (card) {
					item.status = 1;
					card.items.push(item);

					var newCard = !card.id;
					Cards.saveCard(card).then(function () {
						if (newCard) {
							caseItem.cards.location.push(card);
						}
					});
				} else {
					$http.post('/cases/ajax/' + $scope.org_id + '/addInventoryItem/' + caseItem.id, $.param({
						data: JSON.stringify(item)
					})).then(function (result) {
						item.id = result.data.id;
						caseItem.inventory_items.push(item);
					});
				}
			};

			var removeItem = function (item) {
				if (card) {
					item.status = 0;
					Cards.saveCard(card);
				} else {
					$http.get('/cases/ajax/' + $scope.org_id + '/removeInventoryItem/' + item.id).then(function (result) {
						var idx = caseItem.inventory_items.indexOf(item);
						caseItem.inventory_items.splice(idx, 1);
					});
				}
			};

			var getItemMaster = function () {
				return {inventory: {id: '', name: ''}, quantity: null};
			};

			vm.checkItem = function (item, exist) {
				var check = true;
				if (item.inventory_id && !exist) {
					check = !$filter('filter')(vm.getItems(), {inventory_id: item.inventory_id, }).length;
				}
				if (item.id) {
					check = !$filter('filter')(vm.getItems(), {inventory_id: item.id, }).length;
				}
				return check;
			};

			vm.search = function () {
				return $http.get('/cards/ajax/' + $scope.org_id + '/search/', {params: angular.extend(vm.search_params, {l: 5})}).then(function (response) {
					var results = response.data;
					angular.forEach(results, function(item){
						var itemExist = $filter('filter')(vm.getItems(), {inventory_id: item.id});
						if (itemExist.length) {
							itemExist = itemExist[0];
							item.quantity = itemExist.quantity;
						} else {
							item.quantity = 1;
						}
					});
					vm.search_items = results;
				});
			};

			vm.reset = function () {
				angular.forEach(vm.search_params, function (val, key) {
					vm.search_params[key] = "";
				});
				vm.search();
			};

			vm.addItemDialog = function () {
				vm.search();
				$scope.dialog(View.get('cases/cm/item_log/item_add.html'), $scope, {size: 'lg'}).result.then(function () {
					
				});
			};

			vm.addItem = function (item) {
				var newItem = getItemMaster();
				newItem.inventory_id = item.id;
				newItem.inventory = item;
				newItem.quantity = item.quantity;
				saveNewItem(newItem);
			};

			vm.removeItem = function (item) {
				if (confirm('Are you sure?')) {
					removeItem(item);
				}
			};
		}]);

})(opakeApp, angular);
