(function (opakeCore, angular) {
	'use strict';

	opakeCore.controller('CasesFormsPreOperativeCtrl', [
		'vm',
		'$http',
		function (vm, $http) {

			var INPUTS_LIMIT = 20;
			var CHARACTER_LIMIT = 250;

			vm.characterLimit = CHARACTER_LIMIT;
			vm.form = null;
			vm.errors = null;
			vm.conditionTypes = [
				{
					name: 'heart',
					label: 'Heart'
				},
				{
					name: 'hypertension',
					label: 'Hypertension'
				},
				{
					name: 'respiratory',
					label: 'Respiratory'
				},
				{
					name: 'gastrointestinal',
					label: 'Gastrointestinal'
				},
				{
					name: 'renal',
					label: 'Renal'
				},
				{
					name: 'sleep_apnea',
					label: 'Sleep Apnea'
				},
				{
					name: 'circulation_bleeding',
					label: 'Circulation / Bleeding'
				},
				{
					name: 'endocrine',
					label: 'Endocrine'
				},
				{
					name: 'liver',
					label: 'Liver'
				},
				{
					name: 'other',
					label: 'Other'
				}
			];

			vm.painManagementTypes = [
				{
					name: 'return_visit_update_1',
					label: 'Return Visit Update',
					type: 'procedure'
				},
				{
					name: 'left_massage_1',
					label: 'Left Massage',
					type: 'transportation'
				},
				{
					name: 'return_visit_update_2',
					label: 'Return Visit Update',
					type: 'procedure'
				},
				{
					name: 'left_massage_2',
					label: 'Left Massage',
					type: 'transportation'
				},
				{
					name: 'return_visit_update_3',
					label: 'Return Visit Update',
					type: 'procedure'
				},
				{
					name: 'left_massage_3',
					label: 'Left Massage',
					type: 'transportation'
				}
			];

			vm.newForm = {
				'primary_care_phone': null,
				'transportation_phone': null,
				'caretaker_phone': null,
				'leave_message_phone': null,
				'medications': [
					{
						name: ''
					},
					{
						name: ''
					},
					{
						name: ''
					}
				],
				'allergies': [
					{
						name: ''
					},
					{
						name: ''
					},
					{
						name: ''
					}
				],
				'surgeries_hospitalizations': [
					{
						name: ''
					}
				],
				'family_problems': [
					{
						name: ''
					}
				],
				'family_anesthesia_problems': [
					{
						name: ''
					}
				],
				'travel_outside': [
					{
						name: ''
					}
				],
				'communicable_diseases': [
					{
						name: ''
					}
				],
				'cultural_limitations': [
					{
						name: ''
					}
				]
			};

			vm.addMedication = function() {
				if (vm.form.medications.length < INPUTS_LIMIT) {
					vm.form.medications.push({
						name: ''
					});
				}

			};

			vm.addAllergies = function() {
				if (vm.form.allergies.length < INPUTS_LIMIT) {
					vm.form.allergies.push({
						name: ''
					});
				}
			};

			vm.addSurgeryHospitalization = function() {
				if (vm.form.surgeries_hospitalizations.length < INPUTS_LIMIT) {
					vm.form.surgeries_hospitalizations.push({
						name: ''
					});
				}
			};

			vm.addFamilyProblems = function() {
				if (vm.form.family_problems.length < INPUTS_LIMIT) {
					vm.form.family_problems.push({
						name: ''
					});
				}
			};

			vm.addFamilyAnesthesiaProblems = function() {
				if (vm.form.family_anesthesia_problems.length < INPUTS_LIMIT) {
					vm.form.family_anesthesia_problems.push({
						name: ''
					});
				}
			};

			vm.addTravelOutsideCountries = function() {
				if (vm.form.travel_outside.length < INPUTS_LIMIT) {
					vm.form.travel_outside.push({
						name: ''
					});
				}
			};

			vm.addCommunicableDeceases = function() {
				if (vm.form.communicable_diseases.length < INPUTS_LIMIT) {
					vm.form.communicable_diseases.push({
						name: ''
					});
				}
			};

			vm.addCulturalLimitations = function() {
				if (vm.form.cultural_limitations.length < INPUTS_LIMIT) {
					vm.form.cultural_limitations.push({
						name: ''
					});
				}
			};

		}]);

})(opakeCore, angular);
