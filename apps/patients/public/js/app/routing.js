// App routing
(function (opakeApp) {
	'use strict';
	opakeApp.config([
		'$stateProvider',
		'$urlRouterProvider',
		'$locationProvider',
		'config',
		function ($stateProvider, $urlRouterProvider, $locationProvider, config) {

			var ts = new Date().getTime();
			var getView = function(view) {
				return config.view.src + view + '?ts=' + ts;
			};

			$locationProvider.html5Mode(true);

			$stateProvider
				.state('auth', {
					controller: 'AuthCtrl',
					controllerAs: 'authVm',
					templateUrl: getView('public.html')
				})
				.state('app', {
					abstract: true,
					controller: 'AppCtrl',
					controllerAs: 'appVm',
					templateUrl: getView('app.html'),
					resolve: {
						loggedUser: function ($state, AuthService) {
							return AuthService.getUser().then(function (result) {
								return result;
							}, function(error){
								if (error.status === 401) {
									$state.go('auth');
								}
							});
						}
					}
				})
				.state('app.home', {
					url: '/',
					controller: function ($state, AuthService, PatientService) {
						AuthService.getUser().then(function (result) {
							PatientService.isRedirectedToInsurance(result.patient_id).then(function (result) {
								if (result == "true") {
									$state.go('app.insurance');
								} else {
									$state.go('app.profile');
								}
							});
						});
					}
				})
				.state('app.profile', {
					url: '/profile',
					controllerAs: 'profileVm',
					templateUrl: getView('app/profile/index.html')
				})
				.state('app.insurance', {
					url: '/insurance',
					controllerAs: 'insuranceVm',
					templateUrl: getView('app/insurance/index.html')
				})
				.state('app.logout', {
					controller: function (AuthService) {
						AuthService.logout();
					}
				})
				.state('app.appointments', {
					url: '/my-appointments',
					controller: 'AppointmentsListCtrl',
					controllerAs: 'listVm',
					templateUrl: getView('app/appointments/index.html')
				})
				.state('app.view-appointment', {
					url: '/appointment/:appointment',
					controller: 'ViewAppointmentCtrl',
					controllerAs: 'appointmentVm',
					templateUrl: getView('app/appointments/view.html')
				})
				.state('app.view-appointment.info', {
					url: '/info',
					controller: 'ViewAppointmentInfoCtrl',
					controllerAs: 'infoVm',
					templateUrl: getView('app/appointments/view/info.html')
				})
				.state('app.view-appointment.insurance', {
					url: '/insurance',
					controller: 'ViewAppointmentInsuranceCtrl',
					controllerAs: 'insuranceVm',
					templateUrl: getView('app/appointments/view/insurance.html')
				})
				.state('app.view-appointment.forms', {
					url: '/forms',
					controller: 'ViewAppointmentFormsCtrl',
					controllerAs: 'formsVm',
					templateUrl: getView('app/appointments/view/forms.html')
				})
				.state('app.view-appointment.forms.pre-operative', {
					url: '/pre-operative',
					controller: 'ViewAppointmentFormsPreOperativeCtrl',
					controllerAs: 'preoperativeVm',
					templateUrl: getView('app/appointments/view/forms/pre-operative.html')
				})
				.state('app.view-appointment.forms.influenza', {
					url: '/influenza',
					controller: 'ViewAppointmentFormsInfluenzaCtrl',
					controllerAs: 'influenzaVm',
					templateUrl: getView('app/appointments/view/forms/influenza.html')
				})
				.state('app.forms', {
					url: '/my-forms',
					controller: 'FormsListCtrl',
					controllerAs: 'listVm',
					templateUrl: getView('app/forms/index.html')
				})
				.state('error', {
					abstract: true,
					templateUrl: getView('error/template.html')
				})
				.state('error.404', {
					template: 'Page Not Found'
				});

			$urlRouterProvider.otherwise(function ($injector, $location) {
				$injector.get('$state').go('error.404');
			});
		}]);

})(opakeApp);
