// Geo
(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('geo', ['$q', '$timeout', 'Source', function ($q, $timeout, Source) {
			return {
				restrict: "A",
				scope: true,
				bindToController: {
					model: "=",
					props: "="
				},
				controller: function () {
					var vm = this;

					vm.countries = [];
					vm.cities = [];
					vm.states = [];

					vm.props = vm.props || {country: 'country', state: 'state', city: 'city'};

					Source.getCountries().then(function(data){
						vm.countries = data;

						if (vm.props.country && !vm.model[vm.props.country]) {
							vm.model[vm.props.country] = vm.countries[0];
						}
					});

					vm.uploadStates = function () {
						var def = $q.defer();
						Source.getStates().then(function(data) {
							vm.states = data;
							def.resolve();
						});

						return def.promise;
					};

					vm.uploadCities = function () {
						var def = $q.defer();
						Source.getCities(vm.model[vm.props.state].id, null, true).then(function(data) {
							vm.cities = data;
							def.resolve();
						});

						return def.promise;
					};

					vm.newCity = function(query) {
						if (!vm.model[vm.props.state]) {
							return undefined;
						}

						return {
							id: null,
							name: query,
							state_id: vm.model[vm.props.state].id
						};
					}

				},
				controllerAs: 'geoVm',
				link: function (scope, elem, attrs, ctrl) {

					var active_city = null;

					$timeout(function() {
						if (scope.geoVm.props.country) {
							scope.$watch('geoVm.model.' + scope.geoVm.props.country, function (country, oldCountry) {
								if (country) {
									if (country.id === '235') {
										elem.find('.state, .city').show();
										elem.find('.custom-state, .custom-city').hide();
										ctrl.uploadStates();
									} else {
										elem.find('.custom-state, .custom-city').show();
										elem.find('.state, .city').hide();
									}

									if (oldCountry && country.id !== oldCountry.id) {
										scope.geoVm.model[scope.geoVm.props.city] = null;
										scope.geoVm.model[scope.geoVm.props.state] = null;
										scope.geoVm.model[scope.geoVm.props.custom_city] = null;
										scope.geoVm.model[scope.geoVm.props.custom_state] = null;
									}
								}
							});
						} else {
							ctrl.uploadStates();
						}
					});

					scope.$watch('geoVm.model.' + scope.geoVm.props.state, function (state, oldState) {
						if (state && state.id) {
							ctrl.uploadCities().then(function () {
								var oldCity = scope.geoVm.model[scope.geoVm.props.city];
								if (oldState && state.id !== oldState.id) {
									scope.geoVm.model[scope.geoVm.props.city] = null;
								}
								if (oldCity && oldCity.id !== null) {
									var keepOldCity = false;
									angular.forEach(scope.geoVm.cities, function (city) {
										if (city.id == oldCity.id) {
											keepOldCity = true;
											return false;
										}
									});
									if (keepOldCity) {
										scope.geoVm.model[scope.geoVm.props.city] = oldCity;
									}
								}
							});
						}
					});

					scope.$watch('geoVm.model.' + scope.geoVm.props.city, function (city) {
						if (angular.isObject(city) && city.id) {
							active_city = city;
						}
					});

					elem.find('.city input').blur(function() {
						var city = scope.geoVm.model[scope.geoVm.props.city];
						if (angular.isString(city)) {
							scope.$apply(function(){
								if (!city) {
									active_city = null;
								}
								scope.geoVm.model[scope.geoVm.props.city] = active_city;
							});
						}
					});
				}
			};
		}]);

})(opakeCore, angular);
