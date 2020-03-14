(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('caseTimeline', ['$timeout', '$compile', function ($timeout, $compile) {
			return {
				restrict: "AC",
				scope: {
					start: '=',
					end: '=',
					startFact: '=',
					endFact: '='
				},
				link: function (scope, elem, attrs) {
					if (!vis) {
						return;
					}
					var timeline = null,
						data = null;

					var checkRange = function (props) {
						var currtime = elem.find('.vis-current-time');
						if (timeline.getCurrentTime() < props.start) {
							currtime.addClass('left');
						} else {
							currtime.removeClass('left');

							if (timeline.getCurrentTime() > props.end) {
								currtime.addClass('right');
							} else {
								currtime.removeClass('right');
							}
						}
					};

					scope.$watchGroup(['start', 'end', 'startFact', 'endFact'], function(newVal, newOld) {
						if (angular.equals(newVal, newOld)) {
							return;
						}
						if (timeline !== null) {
							timeline.destroy();
						}

						data = [
							{start: moment(scope.start), type: 'point', className: 'start', content: 'Case scheduled to begin'},
							{start: moment(scope.end), type: 'point', className: 'end', content: 'Case scheduled to finish'}
						];

						if (scope.startFact) {
							var startFact = moment(scope.startFact);
							data.push({start: startFact, type: 'point', className: 'start fact', content: 'Case actually began'});

							if (scope.endFact) {
								var endFact = moment(scope.endFact);
								data.push({start: endFact, type: 'point', className: 'end fact', content: 'Case actually finished'});
								data.push({start: startFact, end: endFact, type: 'background'});
							}
						}

						var min = newVal[0], max = newVal[0];
						angular.forEach(newVal, function (val) {
							if (val) {
								if (val < min) {
									min = val;
								} else if (val > max) {
									max = val;
								}
							}
						});

						var options = {
							timeAxis: {
								scale: 'minute',
								step: 1
							},
							orientation: 'top',
							height: '65px',
							zoomMax: 30000000,
							zoomMin: 7000000,
							min: moment(min).add(-10, 'hours'),
							max: moment(max).add(10, 'hours'),
							selectable: false,
							format: {
								majorLabels: {
									minute: 'dddd M/D'
								}
							}
						};

						vis.timeline.TimeStep.prototype.getClassName = function(){
							if (this.scale === 'minute') {
								if (this.current.getMinutes() === 0) {
									return 'hour';
								} else if (this.current.getMinutes() % 15 === 0) {
									return 'quarter';
								}
							}
							return '';
						}
						vis.timeline.TimeStep.prototype.getLabelMinor = function (date) {
							if (this.current.getMinutes() === 0) {
								return moment(this.current).format('h:mm A');
							}
							return '';
						};

						timeline = new vis.Timeline(elem[0], new vis.DataSet(data), options);
						timeline.on('rangechange', checkRange);
						checkRange(timeline.getWindow());

						$timeout(function () {
							angular.forEach(elem.find('.vis-content .vis-item-content'), function (item) {
								item = angular.element(item);
								var ttip = angular.element('<div class="vis-tooltip" uib-tooltip="' + item.html() + '" tooltip-append-to-body="true"></div>');
								if (item.parent().hasClass('fact')) {
									ttip.attr('tooltip-class', 'red');
								}
								item.html($compile(ttip)(scope));
							});
						});
					});
				}
			};
		}]);

})(opakeApp, angular);
