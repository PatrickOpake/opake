// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('CaseNotes', [
		'$http',
		'$filter',
		'$window',
		'$q',
		'$rootScope',
		'CaseNote',

		function ($http, $filter, $window, $q, $rootScope, CaseNote) {

			var self = this;
			self.notes = [];
			self.hasUnreadNotes = [];

			this.getNotes = function(caseId, updateResults) {
				var deferred = $q.defer();
				if (angular.isDefined(this.notes[caseId]) && !updateResults) {
					deferred.resolve(this.notes[caseId]);
				} else {
					$http.get('/cases/ajax/note/' + $rootScope.org_id + '/list/' + caseId).then(function (result) {
						var notes = [];
						angular.forEach(result.data, function(note) {
							notes.push(new CaseNote(note));
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
					$http.post('/cases/ajax/note/' + $rootScope.org_id + '/hasUnreadNotes/', $.param({data: JSON.stringify(caseIds)})).then(function (result) {
						angular.forEach(result.data, function(value, key) {
							self.hasUnreadNotes[key] = value;
						});
					});
				}
			};

			this.readNotes = function(caseId) {
				$http.get('/cases/ajax/note/' + $rootScope.org_id + '/readNotes/' + caseId).then(function () {
					self.hasUnreadNotes[caseId] = false;
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

			this.hasFlaggedNotes = function(caseObj) {
				if (caseObj && caseObj.id) {
					if (this.notes[caseObj.id]) {
						var hasFlaggedNote = false;
						angular.forEach(this.notes[caseObj.id], function (note) {
							if (note.flagged) {
								hasFlaggedNote = true;
							}
						});

						return hasFlaggedNote;
					} else {
						return caseObj.has_flagged_comments;
					}
				}

				return false;
			};

			this.addNote = function (note) {
				var deferred = $q.defer();
				$http.post('/cases/ajax/note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function (result) {
					var newNote = new CaseNote(result.data);
					self.notes[newNote.case_id].push(newNote);
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.updateNote = function (note) {
				var deferred = $q.defer();
				$http.post('/cases/ajax/note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.deleteNote = function (noteId) {
				var deferred = $q.defer();
				$http.get('/cases/ajax/note/' + $rootScope.org_id + '/delete/' + noteId).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

		}]);
})(opakeApp, angular);
