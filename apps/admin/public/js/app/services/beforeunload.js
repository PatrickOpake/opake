// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('BeforeUnload', [
		function () {

			var self = this;
			self.reseted = false;
			self.callbacks = [];
			self.forms = {};

			this.addForms = function (form, old_form, type) {
				self.forms[type] = [form, old_form];
			};

			this.addUtilityFields = function (form1, form2) {

			};

			this.clearForms = function (type) {
				if (!type) {
					self.forms = {};
				} else {
					delete self.forms[type];
				}
			};

			this.add = function (callback) {
				self.callbacks.push(callback);
			};

			this.remove = function (callback) {
				var index = self.callbacks.indexOf(callback);
				if (index > -1) {
					self.callbacks.splice(index, 1);
				}
			};

			this.reset = function (isReseted) {
				self.callbacks = [];
				if(isReseted) {
					self.reseted = true;
				}
			};

			this.confirm = function (msg) {
				if (self.reseted) {
					self.reseted = false;
					return true;
				} else {
					return confirm(msg);
				}
			};

			this.checkForm = function (callback) {
				var confirmed = true;
				var errors = [];
				var self = this;
				angular.forEach(self.forms, function (item, key) {
					if (!self.compareForms(item[0], item[1])) {
						if (key === 'case') {
							errors.push('Case form has been changed. All changes will not be saved.');
						} else if (key === 'case_coding') {
							errors.push('Coding form has been changed. All changes will not be saved.');
						} else if (key === 'case_verification') {
							errors.push('Verification form has been changed. All changes will not be saved.');
						} else {
							errors.push('Are you sure you want to continue without saving your changes?');
						}
					}
				});

				if (errors.length) {
					if (self.confirm(errors.join('\n'))) {
						self.clearForms();
					} else {
						confirmed = false;
					}
				}

				if (confirmed) {
					callback();
				}
			};

			this.compareForms = function(newObject, oldObject) {

				if (!angular.isObject(newObject) || !angular.isObject(oldObject)) {
					return angular.equals(newObject, oldObject);
				}

				if (newObject.registration) {
					return compareObjects(newObject, oldObject, {
						compareFunc: {
							'registration': function(newObject, oldObject) {
								return compareObjects(newObject, oldObject, {
									compareFunc: {
										'insurances': compareInsurances
									}
								})
							}
						}
					});
				}

				if (newObject.case_insurances) {
					return compareObjects(newObject, oldObject, {
						compareFunc: {
							'case_insurances': compareInsurances
						}
					});
				}

				if (newObject.insurances) {
					return compareObjects(newObject, oldObject, {
						compareFunc: {
							'insurances': compareInsurances
						}
					});
				}

				return compareObjects(newObject, oldObject);
			};

			function compareObjects(newObject, oldObject, options) {
				options = options || {};
				var isEquals = true;

				angular.forEach(newObject, function(newValue, key) {
					if (key.indexOf('$$') === 0) {
						return;
					}

					if (!angular.isObject(oldObject) || !(key in oldObject)) {
						isEquals = false;
						//console.log('OBJ have no key', key, oldObject);
						return false;
					}

					if (options.compareFunc && options.compareFunc[key] && angular.isFunction(options.compareFunc[key])) {
						if (!options.compareFunc[key](newValue, oldObject[key])) {
							isEquals = false;
							//console.log('OBJ compare fun returns false', key, oldObject);
							return false;
						}
						return;
					}

					if (!angular.equals(newValue, oldObject[key])) {
						isEquals = false;
						//console.log('OBJ not equals', key, newValue, oldObject[key]);
						return false;
					}
				});

				return isEquals;
			}

			function compareInsurances(newInsurances, oldInsurances) {
				var isEquals = true;
				var ignoredFields = ['is_model_insurance', 'is_base_insurance', 'is_empty_insurance'];
				angular.forEach(newInsurances, function(newObject, index) {
					if (!oldInsurances[index]) {
						isEquals = false;
						return false;
					}

					var oldObject = oldInsurances[index];
					angular.forEach(newObject, function(newValue, key) {
						if (key.indexOf('$$') === 0) {
							return;
						}

						if (ignoredFields.indexOf(key) !== -1) {
							return;
						}

						if (!angular.isObject(oldObject) || !(key in oldObject)) {
							isEquals = false;
							return false;
						}

						if (!(angular.isFunction(newValue) && angular.isFunction(oldObject[key])) && !angular.equals(newValue, oldObject[key])) {
							isEquals = false;
							return false;
						}
					});

					if (!isEquals) {
						return false;
					}
				});

				return isEquals;
			}

			window.onbeforeunload = function () {
				var errors = [];
				angular.forEach(self.callbacks, function (callback) {
					var result = callback();
					if (result) {
						errors.push(result);
					}
				});
				if (errors.length) {
					return errors.join('\n');
				}
			};
		}]);
})(opakeApp, angular);
