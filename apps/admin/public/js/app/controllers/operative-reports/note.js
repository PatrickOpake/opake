// Report view
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ReportNoteCrtl', [
		'$scope',
		'$filter',
		'$http',
		'View',
		'Case',
		'ReportNote',
		'Permissions',
		'ReportNotes',

		function ($scope, $filter, $http, View, Case, ReportNote, Permissions, ReportNotes) {

			var vm = this;
			vm.reportNotes = ReportNotes;
			vm.notes = [];

			vm.updateNotes = function(updateResults) {
				ReportNotes.getNotes(vm.report_id, updateResults).then(function (result) {
					vm.notes = result;
				});
			};

			vm.openNotesDialog = function(report_id) {
				vm.report_id = report_id;
				ReportNotes.readNotes(vm.report_id);
				vm.updateNotes();
				vm.modal = $scope.dialog(View.get('widgets/notes.html'), $scope,  {size: 'md'});
				vm.modal.result.then(function () {
				});
			};

			vm.initForOverviewPrint = function(report_id) {
				vm.report_id = report_id;
				vm.updateNotes();
			};

			vm.noteInputKeyDown = function(e) {
				if (e.keyCode === 13) {
					vm.addNote();
				}
			};

			vm.addNote = function() {
				if (vm.note_temp_text) {
					var note_temp = new ReportNote();
					note_temp.report_id = vm.report_id;
					note_temp.user = Permissions.user;
					note_temp.text = vm.note_temp_text;
					note_temp.time_add = moment(new Date()).toDate();

					ReportNotes.addNote(note_temp).then(function () {
						vm.updateNotes();
					});

					vm.note_temp_text = null;
				}
			};

			vm.editMode = function(note) {
				note.original_text = note.text;
				note.delete = false;
				note.edit = true;
			};

			vm.cancelEdit = function($event, note) {
				note.text = note.original_text;
				note.edit = false;
			};

			vm.editNote = function(note) {
				if (note.text != note.original_text) {
					ReportNotes.updateNote(note).then(function () {
						vm.updateNotes(true);
					});
				}
				note.edit = false;
			};

			vm.deleteMode = function(note) {
				note.edit = false;
				note.delete = true;
			};

			vm.cancelDelete = function(note) {
				note.delete = false;
			};

			vm.deleteNote = function(note) {
				ReportNotes.deleteNote(note.id).then(function () {
					note.delete = false;
					vm.updateNotes(true);
				});
			};

			vm.getDate = function(note) {
				var noteNumber = vm.notes.indexOf(note);
				if (noteNumber > 0) {
					var previousNote = vm.notes[noteNumber - 1];
					if (note.time_add.toDateString() === previousNote.time_add.toDateString()) {
						return $filter('date')(note.time_add, 'h:mm a');
					}
				}
				return $filter('date')(note.time_add, 'M/d/yyyy, h:mm a');
			};

		}]);

})(opakeApp, angular);
