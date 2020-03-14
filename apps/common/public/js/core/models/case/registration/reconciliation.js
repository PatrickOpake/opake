(function (opakeCore, angular) {
	'use strict';

	opakeCore.factory('CaseRegistrationReconciliation', 
		['ReconciliationAllergy', 'ReconciliationMedication', 'ReconciliationVisitUpdate', 
			function (ReconciliationAllergy, ReconciliationMedication, ReconciliationVisitUpdate) {

			var CaseRegistrationReconciliation = function (data) {
				angular.extend(this, data);

				var self = this;

				this.allergies = [];
				this.medications = [];
				this.visit_updates = [];

				if (data.allergies && data.allergies.length) {
					this.allergies = [];
					angular.forEach(data.allergies, function (allergy) {
						self.allergies.push(new ReconciliationAllergy(allergy));
					});
				} else {
					for (var i = 1; i <= 8; i++) {
						var allergy = new ReconciliationAllergy();
						this.allergies.push(allergy);
					}
				}

				if (data.medications && data.medications.length) {
					this.medications = [];
					angular.forEach(data.medications, function (medication) {
						self.medications.push(new ReconciliationMedication(medication));
					});
				} else {
					for (var j = 1; j <= 15; j++) {
						var medication = new ReconciliationMedication();
						this.medications.push(medication);
					}
				}

				if (data.visit_updates && data.visit_updates.length) {
					this.visit_updates = [];
					angular.forEach(data.visit_updates, function (visit_update) {
						self.visit_updates.push(new ReconciliationVisitUpdate(visit_update));
					});
				} else {
					for (var k = 1; k <= 6; k++) {
						var visit_update = new ReconciliationVisitUpdate();
						this.visit_updates.push(visit_update);
					}
				}

				this.addAllergy = function() {
					this.allergies.push(new ReconciliationAllergy());
				};

				this.addMedication = function() {
					this.medications.push(new ReconciliationMedication());
				};

			};

			return (CaseRegistrationReconciliation);
		}]);
})(opakeCore, angular);