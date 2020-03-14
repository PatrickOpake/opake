// Case view
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseNoteCrtl', [
		'$scope',
		'$filter',
		'$http',
		'View',
		'Case',
		'CaseNote',
		'Permissions',
		'CaseNotes',
		'NotesConst',

		function ($scope, $filter, $http, View, Case, CaseNote, Permissions, CaseNotes, NotesConst) {

			var vm = this;
			vm.caseNotes = CaseNotes;
			vm.notes = [];
			vm.placeholderText = 'Type here to enter a comment for the case';

			vm.updateNotes = function(updateResults) {
				CaseNotes.getNotes(vm.case_id, updateResults).then(function (result) {
					vm.flaggedNotes = $filter('filter')(result, {flagged: true});
					vm.notes = $filter('filter')(result, {flagged: false});
				});
			};

			vm.openNotesDialog = function(case_id) {
				vm.case_id = case_id;
				CaseNotes.readNotes(vm.case_id);
				vm.updateNotes(true);
				vm.modal = $scope.dialog(View.get('cases/notes.html'), $scope,  {size: 'md'});
				vm.modal.result.then(function () {
				});
			};

			vm.initForOverviewPrint = function(case_id) {
				vm.case_id = case_id;
				vm.updateNotes();
			};

			vm.noteInputKeyDown = function(e) {
				if (e.keyCode === 13) {
					vm.addNote();
				}
			};

			vm.addNote = function() {
				if (vm.note_temp_text) {
					var note_temp = new CaseNote();
					note_temp.case_id = vm.case_id;
					note_temp.user = Permissions.user;
					note_temp.text = vm.note_temp_text;
					note_temp.time_add = moment(new Date()).toDate();

					CaseNotes.addNote(note_temp).then(function () {
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
					CaseNotes.updateNote(note).then(function () {
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
				CaseNotes.deleteNote(note.id).then(function () {
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

			vm.flagNote = function(note) {
				$http.get('/cases/ajax/note/' + $scope.org_id + '/flagNote/' + note.id).then(function () {
					vm.updateNotes(true);
				});
			};

			vm.remindNote = function(date, note) {
				var data = {
					note_id: note.id,
					note_type: NotesConst.TYPES.TYPE_NOTE_CASES,
					is_completed: 0
				};
				if (date) {
					data.reminder_date = moment(date).format('YYYY-MM-DD');
				}
				$http.post('/cases/ajax/note/' + $scope.org_id + '/remindNote/', $.param({
					data: JSON.stringify(data)
				})).then(function () {
					vm.updateNotes(true);
				});
			};

			vm.unremind = function (note) {
				if(note.reminder && note.reminder.id) {
					$http.post('/cases/ajax/note/' + $scope.org_id + '/unremindNote/' + note.reminder.id).then(function () {
						vm.updateNotes(true);
					});
				}
			};

			vm.changeReminderDate = function (date, note) {
				vm.remindNote(date, note);
			};

			vm.unflagNote = function(note) {
				if(note.user_id != $scope.loggedUser.id) {
					return false;
				}
				$http.get('/cases/ajax/note/' + $scope.org_id + '/unflagNote/' + note.id).then(function () {
					vm.updateNotes(true);
				});
			};

		}]);

})(opakeApp, angular);
