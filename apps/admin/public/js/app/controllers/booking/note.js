// Case view
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BookingNoteCrtl', [
		'$scope',
		'$filter',
		'$http',
		'View',
		'Case',
		'BookingNote',
		'Permissions',
		'BookingNotes',
		'NotesConst',

		function ($scope, $filter, $http, View, Case, BookingNote, Permissions, BookingNotes, NotesConst) {

			var vm = this;
			vm.bookingNotes = BookingNotes;
			vm.notes = [];
			vm.flaggedNotes = [];

			vm.updateNotes = function(updateResults) {
				BookingNotes.getNotes(vm.booking_id, vm.patient_id, updateResults).then(function (result) {
					vm.flaggedNotes = $filter('filter')(result, {flagged: true});
					vm.notes = $filter('filter')(result, {flagged: false});
				});
			};

			$scope.$on('Booking.PatientChanged', function(e, patientId, patient, booking) {
				vm.booking_id = booking.id;
				vm.patient_id = patientId;
				vm.updateNotes();
			});

			vm.openNotesDialog = function(booking_id, patient) {
				if(patient && patient.id) {
					vm.patient_id = patient.id;
				}
				if(booking_id) {
					vm.booking_id = booking_id;
					BookingNotes.readNotes(vm.booking_id);
					vm.updateNotes();
				}
				vm.modal = $scope.dialog(View.get('cases/notes.html'), $scope,  {size: 'md'});
				vm.modal.result.then(function () {
				});
			};

			vm.initForOverviewPrint = function(booking_id) {
				vm.booking_id = booking_id;
				vm.updateNotes();
			};

			vm.noteInputKeyDown = function(e) {
				if (e.keyCode === 13) {
					vm.addNote();
				}
			};

			vm.addNote = function() {
				if (vm.note_temp_text) {
					var note_temp = new BookingNote();
					note_temp.booking_id = vm.booking_id;
					note_temp.user = Permissions.user;
					note_temp.text = vm.note_temp_text;
					note_temp.time_add = moment(new Date()).toDate();

					if(vm.booking_id) {
						BookingNotes.addNote(note_temp).then(function () {
							vm.updateNotes();
						});
					} else {
						vm.notes.push(note_temp);
						BookingNotes.notes.push(note_temp);
					}


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
					BookingNotes.updateNote(note).then(function () {
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
				BookingNotes.deleteNote(note.id).then(function () {
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
				if (vm.booking_id) {
					$http.get('/booking/ajax/note/' + $scope.org_id + '/flagNote/' + note.id).then(function () {
						vm.updateNotes(true);
					});
				} else {
					note.flagged = true;
					updateFlaggedNotes();
				}
			};

			vm.unflagNote = function(note) {
				if(note.user_id != $scope.loggedUser.id) {
					return false;
				}
				if (vm.booking_id) {
					$http.get('/booking/ajax/note/' + $scope.org_id + '/unflagNote/' + note.id).then(function () {
						vm.updateNotes(true);
					});
				} else {
					note.flagged = true;
					updateFlaggedNotes();
				}
			};

			vm.remindNote = function(date, note) {
				var data = {
					note_id: note.id,
					note_type: NotesConst.TYPES.TYPE_NOTE_BOOKING,
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


			function updateFlaggedNotes() {
				var allNotes = vm.notes.concat(vm.flaggedNotes);
				vm.notes = [];
				vm.flaggedNotes = [];

				BookingNotes.notes = allNotes;

				angular.forEach(allNotes, function (note) {
					if (note.flagged) {
						vm.flaggedNotes.push(note);
					} else {
						vm.notes.push(note);
					}
				});
			}


		}]);

})(opakeApp, angular);
