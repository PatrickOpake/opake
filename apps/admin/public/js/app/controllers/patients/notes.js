(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientNotesCtrl', [
		'$scope',
		'$http',
		'$filter',
		'CaseNote',
		'BillingNote',
		'BookingNote',

		function ($scope, $http, $filter, CaseNote, BillingNote, BookingNote) {

			var vm = this;

			vm.init = function(patientId) {
				$http.get('/cases/ajax/note/' + $scope.org_id + '/listForPatient/' + patientId).then(function (result) {
					var notes = [];
					angular.forEach(result.data.cases_note, function (data) {
						notes.push(new CaseNote(data));
					});

					vm.generalNotes = [];
					angular.forEach(result.data.general_note, function (data) {
						vm.generalNotes.push(new BookingNote(data));
					});

					var flaggedGeneralNotes = $filter('filter')(vm.generalNotes, {flagged: true});

					vm.flaggedNotes = $filter('filter')(notes, {flagged: true});
					angular.extend(vm.flaggedNotes, flaggedGeneralNotes);

					var notesByCases = [];
					angular.forEach(notes, function (note) {
						if ($filter('filter')(notesByCases, {case_id: note.case_id}, true).length) {
							$filter('filter')(notesByCases, {case_id: note.case_id}, true)[0].notes.push(note);
						} else {
							var notesByCaseNew = {case_id: note.case_id, notes: []};
							notesByCaseNew.notes.push(note);
							notesByCases.push(notesByCaseNew);
						}
					});
					vm.notesByCases = notesByCases;
				});
			};

			vm.initBillingNotes = function(patientId) {
				$http.get('/billings/ajax/note/' + $scope.org_id + '/listForPatient/' + patientId).then(function (result) {
					var notes = [];
					angular.forEach(result.data, function (data) {
						notes.push(new BillingNote(data));
					});
					vm.flaggedNotes = $filter('filter')(notes, {flagged: true});
					var notesByCases = [];
					angular.forEach(notes, function (note) {
						if ($filter('filter')(notesByCases, {case_id: note.case_id}, true).length) {
							$filter('filter')(notesByCases, {case_id: note.case_id}, true)[0].notes.push(note);
						} else {
							var notesByCaseNew = {case_id: note.case_id, notes: []};
							notesByCaseNew.notes.push(note);
							notesByCases.push(notesByCaseNew);
						}
					});
					vm.notesByCases = notesByCases;
				});
			};

		}]);

})(opakeApp, angular);
