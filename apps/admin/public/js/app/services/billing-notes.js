// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('BillingNotes', [
		'$http',
		'$filter',
		'$window',
		'$q',
		'$rootScope',
		'BillingNote',

		function ($http, $filter, $window, $q, $rootScope, BillingNote) {

			var self = this;
			self.notes = [];
			self.hasUnreadNotes = [];

			this.getNotes = function(caseId, patientId, updateResults) {
				var deferred = $q.defer();
				if (angular.isDefined(this.notes[caseId]) && !updateResults && !patientId) {
					deferred.resolve(this.notes[caseId]);
				} else {
					var data = {
						'patient_id': patientId
					};

					$http.get('/billings/ajax/note/' + $rootScope.org_id + '/list/' + caseId, {params: data}).then(function (result) {
						var notes = [];
						angular.forEach(result.data, function(note) {
							notes.push(new BillingNote(note));
						});
						self.notes[caseId] = notes;
						deferred.resolve(notes);
					});
				}

				return deferred.promise;
			};

			this.getNotesForCases = function(caseIds) {
				var deferred = $q.defer();
				var notesForCases = [];
				angular.forEach(caseIds, function(caseId) {
					self.getNotes(caseId).then(function (result) {
						notesForCases[caseId] = result;
					});
				});
				deferred.resolve(notesForCases);

				return deferred.promise;
			};

			this.getUnreadNotes = function(caseIds) {
				if (!angular.isArray(caseIds)) {
					caseIds = [caseIds];
				}

				var hasNewCaseIds = false;
				angular.forEach(caseIds, function(caseId) {
					if (!angular.isDefined(self.hasUnreadNotes[caseId])) {
						hasNewCaseIds = true;
					}
				});

				if (hasNewCaseIds) {
					$http.post('/billings/ajax/note/' + $rootScope.org_id + '/hasUnreadNotes/', $.param({data: JSON.stringify(caseIds)})).then(function (result) {
						angular.forEach(result.data, function(value, key) {
							self.hasUnreadNotes[key] = value;
						});
					});
				}
			};

			this.readNotes = function(caseId) {
				$http.get('/billings/ajax/note/' + $rootScope.org_id + '/readNotes/' + caseId).then(function () {
					self.hasUnreadNotes[caseId] = false;
				});
			};

			this.getNotesCount = function(caseId, notesCount) {
				if (caseId) {
					if (this.notes[caseId]) {
						return this.notes[caseId].length;
					} else if (notesCount) {
						return notesCount;
					}
				}
				return 0;
			};

			this.addNote = function (note) {
				var deferred = $q.defer();
				$http.post('/billings/ajax/note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function (result) {
					var newNote = new BillingNote(result.data);
					self.notes[newNote.case_id].push(newNote);
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.updateNote = function (note) {
				var deferred = $q.defer();
				$http.post('/billings/ajax/note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.deleteNote = function (noteId) {
				var deferred = $q.defer();
				$http.get('/billings/ajax/note/' + $rootScope.org_id + '/delete/' + noteId).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.addAllNotes = function (notes) {
				var deferred = $q.defer();
				$http.post('/billings/ajax/note/' + $rootScope.org_id + '/saveNotes/', $.param({
					data: JSON.stringify(notes)
				})).then(function (result) {
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.hasFlaggedNotes = function(caseObj, caseId) {
				if (caseId) {
					if (this.notes[caseId]) {
						var hasFlaggedNote = false;
						angular.forEach(this.notes[caseId], function (note) {
							if (note.flagged) {
								hasFlaggedNote = true;
							}
						});

						return hasFlaggedNote;
					} else {
						return caseObj.has_billing_flagged_comments;
					}
				}

				return false;
			};

		}]);
})(opakeApp, angular);
