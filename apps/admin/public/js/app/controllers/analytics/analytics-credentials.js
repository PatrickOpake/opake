(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AnalyticsCredentialsCtrl', [
		'$controller',
		'$scope',
		'$http',
		'$window',
		'Tools',
		'UserCredentials',
		function ($controller, $scope, $http, $window, Tools, UserCredentials) {

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = angular.copy(vm.search_params);
				data.type = vm.type;

				$http.get('/analytics/credentials/ajax/' + $scope.org_id + '/search/', {params: data}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new UserCredentials(data));
					});
					vm.items = items;
					vm.staffs_with_approaching_dates_count = response.data.staffs_with_approaching_dates_count;

					vm.expired_count = {
						medical_licence_exp_date: response.data.expired_count.medical_licence_exp_date,
						dea_exp_date: response.data.expired_count.dea_exp_date,
						cds_exp_date: response.data.expired_count.cds_exp_date,
						insurance: parseInt(response.data.expired_count.insurance_exp_date) 
							+ parseInt(response.data.expired_count.insurance_reappointment_date),
						acls_date: response.data.expired_count.acls_date,
						immunizations: getImmunizationsExpiredDatesCount(response.data),
						retest_date: response.data.expired_count.retest_date,
						licence_expr_date: response.data.expired_count.licence_expr_date,
						bls_date: response.data.expired_count.bls_date,
						cnor_date: response.data.expired_count.cnor_date,
						malpractice_exp_date: response.data.expired_count.malpractice_exp_date,
						hp_exp_date: response.data.expired_count.hp_exp_date
					};

					vm.total_count = response.data.total_count;
				});
			};

			function getImmunizationsExpiredDatesCount(data) {
				return parseInt(data.expired_count.immunizations_ppp_due)
					+ parseInt(data.expired_count.immunizations_help_b)
					+ parseInt(data.expired_count.immunizations_rubella)
					+ parseInt(data.expired_count.immunizations_rubeola)
					+ parseInt(data.expired_count.immunizations_varicela)
					+ parseInt(data.expired_count.immunizations_mumps)
					+ parseInt(data.expired_count.immunizations_flue);
			}

			vm.exportMedicalStaffs = function() {
				var data = angular.copy(vm.search_params);
				data.type = vm.type;

				$http.get('/analytics/credentials/ajax/' + $scope.org_id + '/exportMedicalStaffs/', {params: data}).then(function (response) {
					if (response.data.success) {
						Tools.export(response.data.url);
					}
				});
			};

			vm.exportNonSurgicalStaffs = function() {
				var data = angular.copy(vm.search_params);
				data.type = vm.type;

				$http.get('/analytics/credentials/ajax/' + $scope.org_id + '/exportNonSurgicalStaffs/', {params: data}).then(function (response) {
					if (response.data.success) {
						Tools.export(response.data.url);
					}
				});
			};

			vm.isDateExpired = function (date) {
				return moment(date).format('YYYY-MM-DD') <= moment().format('YYYY-MM-DD');
			};

			vm.redirectToUserCredentialsPage = function (userId) {
				$window.location = '/credentials/' + $scope.org_id + '#?user_id=' + userId;
			};

			vm.downloadFile = function (fileUrl) {
				if (fileUrl) {
					Tools.export(fileUrl);
				}
			};

			vm.uploadFile = function(item, files, fileType) {
				var file = files[0];
				var fd = new FormData();
				fd.append('file', file);

				return $http.post('/profiles/credentials/ajax/' + $scope.org_id + '/uploadFile/' + item.user_id, fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				}).then(function (result) {
					if (result.data.file.uploaded_date) {
						result.data.file.uploaded_date = moment(result.data.file.uploaded_date).toDate();
					}

					switch (fileType) {
						case 'npi_file':
							item.npi_file = result.data.file;
							break;
						case 'medical_licence_file':
							item.medical_licence_file = result.data.file;
							break;
						case 'dea_file':
							item.dea_file = result.data.file;
							break;
						case 'cds_file':
							item.cds_file = result.data.file;
							break;
						case 'insurance_file':
							item.insurance_file = result.data.file;
							break;
						case 'acls_file':
							item.acls_file = result.data.file;
							break;
						case 'immunizations_file':
							item.immunizations_file = result.data.file;
							break;
						case 'licence_file':
							item.licence_file = result.data.file;
							break;
						case 'bls_file':
							item.bls_file = result.data.file;
							break;
						case 'cnor_file':
							item.cnor_file = result.data.file;
							break;
						case 'malpractice_file':
							item.malpractice_file = result.data.file;
							break;
						case 'hp_file':
							item.hp_file = result.data.file;
							break;
					}

					$http.post('/profiles/credentials/ajax/' + $scope.org_id + '/save/' + item.user_id,
						$.param({data: angular.toJson(item)})
					).then(function() {
						vm.search();
					})
				});
			};


		}]);
})(opakeApp, angular);
