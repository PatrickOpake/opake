(function (opakeCore, angular) {
	'use strict';

	opakeCore.filter('oiSelectCloseIcon', ['$sce', function ($sce) {
			return function (label) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return $sce.trustAsHtml(label + closeIcon);
			};
		}]);

	opakeCore.directive('opkSelect', ['$compile', '$timeout', function ($compile, $timeout) {
			return {
				restrict: "E",
				replace: true,
				scope: true,
				compile: function(elem, attrs) {
					var select = angular.element('<oi-select ng-model="' + attrs.ngModel + '"></oi-select>');

					var options = '';
					if (attrs.keyValueOptions) {
						options = 'item.key as item.value for item in opkSelectKvArr';
					} else {
						options = attrs.options;
					}

					select.attr('oi-options', options);

					if (angular.isDefined(attrs.ngDisabled)) {
						select.attr('ng-disabled', attrs.ngDisabled);
					}

					if (angular.isDefined(attrs.multiple)) {
						select.attr('multiple', '');
						elem.addClass('multiple');
					}

					if (angular.isDefined(attrs.multipleLimit)) {
						select.attr('multiple-limit', attrs.multipleLimit);
					}

					if (angular.isDefined(attrs.placeholder)) {
						select.attr('placeholder', attrs.placeholder);
					} else {
						select.attr('placeholder', "Type or select");
					}

					if (angular.isDefined(attrs.ngChange)) {
						select.attr('ng-change', attrs.ngChange);
					}

					select.attr('oi-select-options', 'selectOptions');

					elem.addClass('opk-select');
					elem.append(select);


					return {
						pre:  function (scope, elem, attrs) {
							var selectOptions = {
								debounce: 500
							};

							if (attrs.keyValueOptions) {

								var intKeys = angular.isDefined(attrs.intKeys);

								scope.opkSelectKvArr = [];

								var obj = scope.$parent.$eval(attrs.keyValueOptions);
								var fillKvArr = function(obj) {
									var arr = [];
									for (var prop in obj) {
										arr.push({
											key: intKeys ? parseInt(prop, 10) : prop,
											value: obj[prop]
										});
									}
									scope.opkSelectKvArr = arr;
								};

								if (angular.isObject(obj) || angular.isArray(obj)) {
									fillKvArr(obj);
								}

								scope.$watch(attrs.keyValueOptions, function(newValue) {
									fillKvArr(newValue || {});
								});

							}

							if (attrs.selectOptions) {
								selectOptions = angular.extend(selectOptions, scope.$eval(attrs.selectOptions));
							}

							scope.selectOptions = selectOptions;

							scope.$watch(attrs.ngModel, function (newVal, oldVal) {
								if (newVal !== oldVal) {
									scope.$emit('select');

									if (attrs.change) {
										scope.$eval(attrs.change)
									}
								}
							});
						}
					};
				}
			};
		}]);

		// Custom filterss
		opakeCore.filter('opkSelectCode', function ($rootScope, $sce) {
			return function (label, query, item) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return $sce.trustAsHtml(item.code + closeIcon);
			};
		});

		opakeCore.filter('opkSelectCpt', function ($rootScope, $sce) {
			return function (label, query, item) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return '<a href="" uib-tooltip="' + item.name + '" tooltip-placement="bottom">' + item.code + '</a>' + closeIcon;
			};
		});

		opakeCore.filter('opkSelectConditionCodes', function ($rootScope, $sce) {
			return function (label, query, item) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return '<a href="" uib-tooltip="' + item.description + '" tooltip-placement="bottom">' + item.code + '</a>' + closeIcon;
			};
		});

		opakeCore.filter('opkSelectOccurrenceCodes', function ($rootScope, $sce) {
			return function (label, query, item) {
				return $sce.trustAsHtml(item.code);
			};
		});

		opakeCore.filter('opkSelectInventoryItemNumber', function ($rootScope, $sce) {
			return function (label, query, item) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return '<a href="" uib-tooltip="' + item.name + '" tooltip-placement="bottom">' + item.number + '</a>' + closeIcon;
			};
		});

		opakeCore.filter('opkSelectICD', function ($rootScope, $sce) {
			return function (label, query, item, options, element) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return '<a href="" uib-tooltip="' + item.desc + '" tooltip-placement="bottom">' + item.code + '</a>' + closeIcon;
			};
		});

		opakeCore.filter('opkSelectColor', function($sce) {
			return function(label, query, item, options, element) {
				var html = '<span class="color-value color-' + item.key +'"></span>' +
					'<span class="color-name">' + item.name + '</span>';
				return $sce.trustAsHtml(html);
			};
		});

		opakeCore.filter('opkSelectUploadedDocument', function ($rootScope, $sce, $filter) {
			return function (label, query, item) {
				return 'Uploaded - ' + $filter('date')(item.uploaded_date, 'M/d/yyyy');
			};
		});

		opakeCore.filter('opkSelectInventory', function ($rootScope, $sce) {
			return function (label, query, item, options, element) {
				var closeIcon = '<span ng-click="$parent.removeItem($index)" class="close select-search-list-item_selection-remove">×</span>';
				return '<a href="" uib-tooltip="' + item.name + '" tooltip-placement="bottom">' + item.id + '</a>' + closeIcon;
			};
		});

		opakeCore.filter('opkSelectEmptyField', function ($rootScope, $sce) {
			return function (list, query, getLabel, options, element) {
				var newList = [];
				newList.push({key: null, value: null});
				angular.forEach(list, function (item) {
					newList.push(item);
				});
				return newList;
			};
		});

		opakeCore.filter('opkSelectEmptyFieldIdName', function ($rootScope, $sce) {
			// options[0] - id value for empty element, default is null
			return function (list, query, getLabel, options, element) {
				var idValue = null;
				if(angular.isDefined(options)) {
					idValue = options[0];
				}
				var newList = [];
				newList.push({id: idValue, name: ''});
				angular.forEach(list, function (item) {
					newList.push(item);
				});
				return newList;
			};
		});
	opakeCore.filter('opkSelectEmptyFieldIdTitle', function ($rootScope, $sce) {
		return function (list, query, getLabel, options, element) {
			var newList = [];
			newList.push({id: null, title: ''});
			angular.forEach(list, function (item) {
				newList.push(item);
			});
			return newList;
		};
	});
		opakeCore.filter('opkSelectEmptyFieldTime', function ($rootScope, $sce) {
			return function (list, query, getLabel, options, element) {
				var newList = [];
				newList.push('');
				angular.forEach(list, function (item) {
					newList.push(item);
				});
				return newList;
			};
		});

		// Custom filter
		opakeCore.filter('opkRelationshipToInsuredSearchFilter', [
			'$rootScope',
			'CaseRegistrationConst',
			function ($rootScope, CaseRegistrationConst) {
				return function (label, query, item) {
					return CaseRegistrationConst.RELATIONSHIP_TO_INSURED[item];
				}
			}
		]);

		opakeCore.filter('opkSearchPatient', function () {
				return function (list, query, getLabel,  options, element) {
					return list;
				}
			}
		);

	opakeCore.filter('opkSelectBillingStatus', function ($rootScope, $sce, BillingConst) {
		return function (label, query, item, options, element) {
			if(BillingConst.MANUAL_BILLING_STATUSES_DESC[item.key]) {
				label = '<a href="" uib-tooltip="' + BillingConst.MANUAL_BILLING_STATUSES_DESC[item.key] + '" tooltip-placement="top">' + item.value + '</a>';
			}
			return $sce.trustAsHtml(label);



		};
	});

})(opakeCore, angular);
