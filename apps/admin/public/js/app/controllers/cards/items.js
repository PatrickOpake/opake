(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CardItemsCrtl', [
		'$scope',
		'$http',
		'$filter',
		'Source',

		function ($scope, $http, $filter, Source) {

			var vm = angular.isDefined($scope.vm) ? $scope.vm : this;
			var stagesPositions = [];

			vm.items = [];
			vm.notes = [];
			vm.stagesIds = [];
			vm.selectAllNotes = false;

			$scope.$on('PrefCard.StagesSortUpdated', function (e, stagesPosits) {
				stagesPositions = stagesPosits;
			});
			$scope.$on('PrefCard.TemplateUploaded', function (e, card) {
				vm.init(card.items, card.notes, card.stages);
			});
			$scope.$on('PrefCard.CardUpdated', function (e, card) {
				vm.init(card.items, card.notes, card.stages);
			});

			var allPrefCardStages = [];

			vm.init = function (items, notes, stagesPosits) {
				if (items) {
					vm.items = items;
				}
				if (notes) {
					vm.notes = notes;
					vm.checkNotesCheckboxes();
				}
				if (stagesPosits) {
					stagesPositions = stagesPosits;
				}

				Source.getAllPrefCardStages().then(function (result) {
					allPrefCardStages = result;
					groupItemsByStage();
				});
			};

			// Notes

			vm.addNote = function () {
				var newNote = {id: null, name: '', text: '', is_checked: false};
				newNote.edit_mode = true;
				vm.notes.push(newNote);
				vm.checkNotesCheckboxes();
			};

			vm.editNote = function (item) {
				item.edit_mode = true;
				item.original_name = item.name;
				item.original_text = item.text;
			};

			vm.cancelEditNote = function (item) {
				if (item.original_name && item.original_text) {
					item.edit_mode = false;
					item.name = item.original_name;
					item.text = item.original_text;
				} else {
					vm.removeNote(item);
				}
			};

			vm.saveNote = function (item) {
				item.edit_mode = false;
			};

			vm.removeNote = function (item) {
				var idx = vm.notes.indexOf(item);
				if (idx > -1) {
					vm.notes.splice(idx, 1);
				}
				vm.checkNotesCheckboxes();
			};

			vm.addAllNotesToCheck = function () {
				if (vm.selectAllNotes) {
					angular.forEach(vm.notes, function (note) {
						note.is_checked = false;
					});
				} else {
					angular.forEach(vm.notes, function (note) {
						note.is_checked = true;
					});
				}
				vm.selectAllNotes = !vm.selectAllNotes;
			};

			vm.checkNotesCheckboxes = function () {
				if (!vm.notes.length || $filter('filter')(vm.notes, {is_checked: false}).length) {
					vm.selectAllNotes = false;
				} else {
					vm.selectAllNotes = true;
				}
			};

			// Items

			vm.addItem = function () {
				var newItem = {stage_id: null, inventory: null, default_qty: null};
				newItem.edit_mode = true;
				vm.items.push(newItem);
				groupItemsByStage();
			};

			vm.editItem = function (item) {
				item.edit_mode = true;
				item.original_stage = item.stage;
				item.original_inventory = item.inventory;
				item.original_default_qty = item.default_qty;
			};

			vm.cancelEditItem = function (item) {
				if (item.original_inventory) {
					item.edit_mode = false;
					item.stage = item.original_stage;
					item.inventory = item.original_inventory;
					item.default_qty = item.original_default_qty;
				} else {
					vm.removeItem(item);
				}
				groupItemsByStage();
			};

			vm.saveItem = function (item) {
				item.edit_mode = false;
				groupItemsByStage();
			};

			vm.removeItem = function (item) {
				var idx = vm.items.indexOf(item);
				if (idx > -1) {
					vm.items.splice(idx, 1);
					groupItemsByStage();
				}
			};

			vm.itemHasInventory = function (item) {
				return item.inventory;
			};

			vm.getPriceTooltipStr = function (inventory) {
				if (inventory && inventory.full_unit_price) {
					return 'Unit Price: ' + $filter('usd')(inventory.full_unit_price);
				}

				return 'No Unit Price';
			};

			vm.newItem = function(name) {
				return {
					id: null,
					name: name,
					full_name: name,
					org_id: $scope.org_id
				};
			};

			vm.hasItemsInEditMode = function(items) {
				var hasItemsInEditMode = false;
				angular.forEach(items, function (item) {
					if (item.edit_mode == true) {
						hasItemsInEditMode = true;
					}
				});

				return hasItemsInEditMode;
			};

			function groupItemsByStage() {
				var stagesWithItems = [],
					stagesItems = {},
					itemsWithOutStage = [];

				var getPosition = function (stageId) {
					if (angular.isDefined(stagesPositions[stageId])) {
						return stagesPositions[stageId].position;
					}
				};

				angular.forEach(vm.items, function (item) {
					var stages = $filter('filter')(allPrefCardStages, {id: item.stage_id});
					if (item.stage_id && stages.length) {
						if (angular.isUndefined(stagesItems[item.stage_id])) {
							stagesItems[item.stage_id] = [];
						}
						stagesItems[item.stage_id].push(item);
					} else {
						item.stage_id = null;
						itemsWithOutStage.push(item);
					}
				});

				angular.forEach(stagesItems, function (items, stageId) {
					items = $filter('orderBy')(items, 'position');
					var stages = $filter('filter')(allPrefCardStages, {id: stageId});
					if (stages.length) {
						var stage = {
							stage: stages[0],
							items: items,
							position: getPosition(stageId)
						};
						stagesWithItems.push(stage);
					} else {
						itemsWithOutStage = itemsWithOutStage.concat(items);
					}
				});

				if (itemsWithOutStage.length) {
					stagesWithItems.push({
						stage: {id: null, name: ''},
						items: itemsWithOutStage,
						position: getPosition(null)
					});
				}

				vm.stagesWithItems = $filter('orderBy')(stagesWithItems, 'position');

				angular.forEach(vm.stagesWithItems, function (stageWithItems) {
					vm.stagesIds.push(stageWithItems.stage.id);
				});

			};

		}]);

})(opakeApp, angular);
