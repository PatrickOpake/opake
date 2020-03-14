// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('ReportNotes', [
		'$http',
		'$filter',
		'$window',
		'$q',
		'$rootScope',
		'ReportNote',

		function ($http, $filter, $window, $q, $rootScope, ReportNote) {

			var self = this;
			self.notes = [];
			self.hasUnreadNotes = [];

			this.getNotes = function(reportId, updateResults) {
				var deferred = $q.defer();
				if (angular.isDefined(this.notes[reportId]) && !updateResults) {
					deferred.resolve(this.notes[reportId]);
				} else {
					$http.get('/cases/operative-reports/ajax/note/' + $rootScope.org_id + '/list/' + reportId).then(function (result) {
						var notes = [];
						angular.forEach(result.data, function(note) {
							notes.push(new ReportNote(note));
						});
						self.notes[reportId] = notes;
						deferred.resolve(notes);
					});
				}

				return deferred.promise;
			};

			this.getNotesForCases = function(reportIds) {
				var deferred = $q.defer();
				var notesForCases = [];
				angular.forEach(reportIds, function(reportId) {
					self.getNotes(reportId).then(function (result) {
						notesForCases[reportId] = result;
					});
				});
				deferred.resolve(notesForCases);

				return deferred.promise;
			};

			this.getUnreadNotes = function(reportIds) {
				if (!angular.isArray(reportIds)) {
					reportIds = [reportIds];
				}

				var hasNewreportIds = false;
				angular.forEach(reportIds, function(reportId) {
					if (!angular.isDefined(self.hasUnreadNotes[reportId])) {
						hasNewreportIds = true;
					}
				});

				if (hasNewreportIds) {
					$http.post('/cases/operative-reports/ajax/note/' + $rootScope.org_id + '/hasUnreadNotes/', $.param({data: JSON.stringify(reportIds)})).then(function (result) {
						angular.forEach(result.data, function(value, key) {
							self.hasUnreadNotes[key] = value;
						});
					});
				}
			};

			this.readNotes = function(reportId) {
				$http.get('/cases/operative-reports/ajax/note/' + $rootScope.org_id + '/readNotes/' + reportId).then(function () {
					self.hasUnreadNotes[reportId] = false;
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
				$http.post('/cases/operative-reports/ajax/note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function (result) {
					var newNote = new ReportNote(result.data);
					self.notes[newNote.report_id].push(newNote);
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.updateNote = function (note) {
				var deferred = $q.defer();
				$http.post('/cases/operative-reports/ajax/note/' + $rootScope.org_id + '/save/', $.param({
					data: JSON.stringify(note)
				})).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

			this.deleteNote = function (noteId) {
				var deferred = $q.defer();
				$http.get('/cases/operative-reports/ajax/note/' + $rootScope.org_id + '/delete/' + noteId).then(function () {
					deferred.resolve();
				});

				return deferred.promise;
			};

		}]);
})(opakeApp, angular);
