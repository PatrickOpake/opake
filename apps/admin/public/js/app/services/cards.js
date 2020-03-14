// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Cards', ['$http', '$rootScope', function ($http, $rootScope) {

			var self = this;

			this.savePrefCard = function (data, callback) {
				return $http.post('/cards/ajax/prefSave/' + $rootScope.org_id + '/card/', $.param({
					data: JSON.stringify(data),
				})).then(function (result) {
					callback(result);
				});
			};

			this.getCard = function(card_id) {
				return $http.get('/cards/ajax/' + $rootScope.org_id + '/card/'+ card_id).then(function (result) {
					return result.data;
				});
			};

			this.getCaseCard = function (caseId) {
				return $http.get('/cases/ajax/' + $rootScope.org_id + '/card/'+ caseId).then(function (result) {
					return result.data;
				});
			};

			this.saveCard = function (card) {
				return $http.post('/cards/ajax/save/staff', $.param({data: JSON.stringify(card)})).then(function (result) {
					if (result.data) {
						if(result.data.id) {
							card.id = result.data.id;
						}
						var i, count;
						if (result.data.notes) {
							for (i = 0, count = result.data.notes.length; i < count; ++i) {
								card.notes[i].id = result.data.notes[i].id;
							}
						}
						if (result.data.items) {
							for (i = 0, count = result.data.items.length; i < count; ++i) {
								if (card.items && card.items[i]) {
									card.items[i].id = result.data.items[i].id;
								}
							}
						}
					}
					return result.data;
				});
			};

		}]);
})(opakeApp, angular);
