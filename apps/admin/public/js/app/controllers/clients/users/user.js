(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClientUserCtrl', [
		'$scope',
		'$http',
		'$q',
		'View',
		'User',
		'Source',
		'Permissions',
		'UserConst',

		function ($scope, $http, $q, View, User, Source, Permissions, UserConst) {

			var vm = this;
			vm.isShowForm = false;
			vm.isSelfUser = false;

			vm.canEditUser = false;
			vm.canEditPermission = false;
			vm.canEditNotBasic = false;
			vm.canSendUserPassword = false;
			vm.canEditPracticeGroups = false;
			vm.isSendPwdButtonDisabled = false;
			vm.profileImageOptions = null;
			vm.profileImageControl = {};
			vm.isSaveFormSent = false;

			vm.errors = null;

			vm.usernameOptions = {};
			vm.passwordOptions = {};

			$scope.userConst = UserConst;

			vm.init = function(userId) {

				if (userId) {
					$http.get('/users/ajax/' + $scope.org_id + '/user/' + userId).then(function (result) {
						vm.user = new User(result.data);
						vm.isSelfUser = vm.user.id == Permissions.user.id;
						changePermissions(vm.user);
						vm.profileImageOptions = {
							imageSrc: vm.user.image_default
						};
					});

				} else {
					vm.user = new User({
						'organization_id': $scope.org_id,
						'image_default': '/i/user_profile_default.png',
						'status': 'active'
					});
					vm.isShowForm = true;
					changePermissions(vm.user);

					vm.profileImageOptions = {
						imageSrc: vm.user.image_default
					};
				}
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalUser = angular.copy(vm.user);
				vm.isSendPwdButtonDisabled = false;
			};

			vm.cancel = function() {
				if (vm.user.id) {
					vm.user = vm.originalUser;
					vm.isShowForm = false;
					vm.usernameOptions = {};
					vm.passwordOptions = {};
					vm.errors = null;
				} else {
					history.back();
				}

			};

			vm.save = function(sendPassword) {
				var def = $q.defer();
				var isCreate = !vm.user.id;

				if (!vm.isSaveFormSent) {
					vm.isSaveFormSent = true;
					vm.errors = null;

					vm.profileImageControl.saveImage({
						imageType: 'user'
					}).then(function(uploadedFile) {
						if (uploadedFile) {
							vm.user.photo_id = uploadedFile.image_id;
						}
						$http.post('/users/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.user)})).then(function (result) {
							if (result.data.id) {
								var savingDone = function() {
									if (isCreate) {
										window.location = '/clients/users/' + $scope.org_id + '/view/' + result.data.id;
										def.resolve();
									} else {
										window.location.reload();
										def.resolve();
									}

									vm.isSaveFormSent = false;
								};

								if (sendPassword) {
									$http.post('/users/ajax/' + $scope.org_id + '/sendPasswordEmail/' + result.data.id).finally(function (result) {
										savingDone();
									});
								} else {
									savingDone();
								}
							} else if (result.data.errors) {
								vm.errors =  angular.fromJson(result.data.errors);
								def.reject();
								vm.isSaveFormSent = false;
							}
						});
					}, function(errors) {
						vm.errors = errors;
						vm.isSaveFormSent = false;
					});

				}

				return def.promise;
			};

			vm.activateUser = function() {
				vm.user.is_active = true;
			};

			vm.deactivateUser = function() {
				vm.user.is_active = false;
			};

			vm.getView = function () {
				var view = 'users/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.sendPasswordEmail = function() {
				vm.isSendPwdButtonDisabled = true;
				$http.post('/users/ajax/' + $scope.org_id + '/sendPasswordEmail/' + vm.user.id).then(function (result) {
					vm.cancel();
				});
			};

			vm.formatMultipleValues = function(values) {
				var result = [];
				angular.forEach(values, function(value) {
					result.push(value.name);
				});

				return result.join(', ');
			};

			vm.isSelf = function() {
				return vm.user.id == $scope.loggedUser.id;
			};

			vm.validateUsername = function() {
				var userId = vm.user.id || '';
				$http.post('/users/ajax/' + $scope.org_id + '/validateUsername/' + userId,  $.param({
						'username': vm.user.username
					}
				)).then(function (result) {
					if (!vm.usernameOptions.isChanged) {
						vm.usernameOptions.isChanged = true;
					}
					vm.usernameOptions.isCorrect = result.data.success;
					vm.usernameOptions.errorMessage = result.data.error;
				});
			};

			vm.validatePassword = function() {
				var userId = vm.user.id || '';
				$http.post('/users/ajax/' + $scope.org_id + '/validatePassword/' + userId,  $.param({
						'new_password': vm.user.new_password,
						'confirm_new_password': vm.user.confirm_new_password
					}
				)).then(function (result) {
					if (!vm.passwordOptions.isChanged) {
						vm.passwordOptions.isChanged = true;
					}

					var isConfirmError = (result.data.error === 'Passwords do not match!' || result.data.error === 'Password confirm is empty');

					vm.passwordOptions.isCorrect = result.data.success;
					if (isConfirmError) {
						vm.passwordOptions.errorMessage = '';
						vm.passwordOptions.confirmErrorMessage = result.data.error;
						vm.passwordOptions.isConfirmCorrect = false;
					} else {
						vm.passwordOptions.errorMessage = result.data.error;
						vm.passwordOptions.confirmErrorMessage = '';
						vm.passwordOptions.isConfirmCorrect = true;
					}

					if (vm.passwordOptions.isCorrect || !vm.passwordOptions.isConfirmCorrect) {
						vm.passwordOptions.showConfirmMark = true;
					} else {
						vm.passwordOptions.showConfirmMark = false;
					}
				});
			};

			vm.changeRole = function () {
				if(vm.user.role_id == UserConst.ROLES.DICTATION) {
					vm.user.profession_id = UserConst.PROFESSION.DICTATION;
				}

				if(vm.user.role_id == UserConst.ROLES.BILLER) {
					vm.user.profession_id = UserConst.PROFESSION.BILLER;
				}

				if(vm.user.role_id == UserConst.ROLES.SCHEDULER) {
					vm.user.profession_id = UserConst.PROFESSION.SCHEDULER;
				}
			};

			vm.isDisableProfessionField = function (roleId) {
				return roleId == UserConst.ROLES.DICTATION || roleId == UserConst.ROLES.BILLER;
			} ;

			function changePermissions(user) {
				vm.canEditUser = Permissions.hasAccess('user', 'edit', user);
				vm.canEditPermission = Permissions.hasAccess('user', 'edit_permissions', user);
				vm.canEditNotBasic = Permissions.hasAccess('user', 'edit_not_basic', user);
				vm.canSendUserPassword = Permissions.hasAccess('user', 'send_password_email', user);
				vm.canEditPracticeGroups = Permissions.hasAccess('user', 'edit_practice_groups', user);
			}

		}]);

})(opakeApp, angular);
