// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('InServiceNotes', [
		'$http',
		'$filter',
		'$window',
		'$q',
		'$rootScope',
		'InServiceNote',

		function ($http, $filter, $window, $q, $rootScope, InServiceNote) {

			var self = this;
			self.notes = [];
			self.hasUnreadNotes = [];

			this.getNotes = function(inServiceId, updateResults) {
				var deferred = $q.defer();
				if (angular.isDefined(this.notes[inServiceId]) && !updateResults) {
					deferred.resolve(this.notes[inServiceId]);
				} else {
					$http.get('/cases/ajax/in-service-note/' + $rootScope.org_id + '/list/' + inServiceId).then(function (result) {
						var notes = [];
						angular.forEach(result.data, function(note) {
							notes.push(new InServiceNote(note));
						});
						self.notes[inServiceId] = notes;
						deferred.resolve(notes);
					});
				}

				return deferred.promise;
			};

			this.getNotesForCases = function(inServiceIds) {
				var deferred = $q.defer();
				var notesForCases = [];
				angular.forEach(inServiceIds, function(inServiceId) {
					self.getNotes(inServiceId).then(function (result) {
						notesForCases[inServiceId] = result;
					});
				});
				deferred.resolve(notesForCases);

				return deferred.promise;
			};

			this.getUnreadNotes = function(inServiceIds) {
				if (!angular.isArray(inServiceIds)) {
					inServiceIds = [inServiceIds];
				}

				var hasNewCaseIds = false;
				angular.forEach(inServiceIds, function(inServiceId) {
					if (!angular.isDefined(self.hasUnreadNotes[inServiceId])) {
						hasNewCaseIds = true;
					}
				});

				if (hasNewCaseIds) {
					$http.post('/cases/ajax/in-service-note/' + $rootScope.org_id + '/hasUnreadNotes/', $.param({data: JSON.stringify(inServiceIds)})).then(function (result) {
						angular.forEach(result.data, function(value, key) {
							self.hasUnreadNotes[key] = value;
						});
					});
				}
			};

			this.readNotes = function(inServiceId) {
				$http.get('/cases/ajax/in-service-note/' + $rootScope.org_id + '/readNotes/' + inServiceId).then(function () {
					self.hasUnreadNotes[inServiceId] = false;
				});
			};

			this.getNotesCount = function(caseObj) {
				if (caseObj && caseObj.id) {
					if (this.notes[caseObj.id]) {
						return this.notes[caseObj.id].length;
					} else if (caseObj.notes_count) {
						return caseObj.notes_count;
					}
				}
				return 0;
			};

			this.addNote = function (note) {
				var deferred = $q.defer();
				$http.post('/cases/ajax/in-service-note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function (result) {
					var newNote = new InServiceNote(result.data);
					self.notes[newNote.in_service_id].push(newNote);
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.updateNote = function (note) {
				var deferred = $q.defer();
				$http.post('/cases/ajax/in-service-note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.deleteNote = function (noteId) {
				var deferred = $q.defer();
				$http.get('/cases/ajax/in-service-note/' + $rootScope.org_id + '/delete/' + noteId).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

		}]);
})(opakeApp, angular);
