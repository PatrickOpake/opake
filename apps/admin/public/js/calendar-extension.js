(function ($, moment) {
	;
	var COL_MIN_WIDTH, Calendar, CalendarExtension, Class, EmitterMixin, EventRow, FC, Grid, MAX_AUTO_CELLS, MAX_AUTO_SLOTS_PER_LABEL, MAX_CELLS, MIN_AUTO_LABELS, ResourceAgendaView, ResourceDayGrid, ResourceDayTableMixin, ResourceGridMixin, ResourceManager, ResourceRow, ResourceTimeGrid, ResourceViewMixin, RowGroup, RowParent, VRowGroup, View, capitaliseFirstLetter, compareByFieldSpecs, computeOffsetForSeg, computeOffsetForSegs, cssToStr, debounce, getOwnCells, getScrollbarWidths, htmlEscape, intersectRanges, multiplyDuration, syncThen, origDisplayEvents, origDisplayView, origGetSegClasses, origRenderSkeleton, origUnrenderSkeleton, parseFieldSpecs, proxy, rtlScrollSystem,
		slice = [].slice,
		extend = function (child, parent) {
			for (var key in parent) {
				if (hasProp.call(parent, key))
					child[key] = parent[key];
			}
			function ctor() {
				this.constructor = child;
			}
			ctor.prototype = parent.prototype;
			child.prototype = new ctor();
			child.__super__ = parent.prototype;
			return child;
		},
		hasProp = {}.hasOwnProperty;

	FC = $.fullCalendar;

	Calendar = FC.Calendar;

	Class = FC.Class;

	View = FC.View;

	Grid = FC.Grid;

	intersectRanges = FC.intersectRanges;

	debounce = FC.debounce;

	getScrollbarWidths = FC.getScrollbarWidths;

	htmlEscape = FC.htmlEscape;

	proxy = FC.proxy;

	capitaliseFirstLetter = FC.capitaliseFirstLetter;

	EmitterMixin = FC.EmitterMixin;

	multiplyDuration = FC.multiplyDuration;

	parseFieldSpecs = FC.parseFieldSpecs;

	compareByFieldSpecs = FC.compareByFieldSpecs;


	getOwnCells = function (trs) {
		return trs.find('> td').filter(function (i, tdNode) {
			return tdNode.rowSpan <= 1;
		});
	};

	syncThen = function (promise, doneFunc, failFunc) {
		if (!promise || !promise.then || promise.state() === 'resolved') {
			if (doneFunc) {
				return $.when(doneFunc());
			} else {
				return $.when();
			}
		} else if (promise.state() === 'rejected') {
			if (failFunc) {
				failFunc();
			}
			return $.Deferred().reject().promise();
		} else {
			return promise.then(doneFunc, failFunc);
		}
	};

	CalendarExtension = (function (superClass) {
		extend(CalendarExtension, superClass);

		function CalendarExtension() {
			return CalendarExtension.__super__.constructor.apply(this, arguments);
		}

		CalendarExtension.prototype.resourceManager = null;

		CalendarExtension.prototype.initialize = function () {
			return this.resourceManager = new ResourceManager(this);
		};

		CalendarExtension.prototype.instantiateView = function (viewType) {
			var spec, viewClass;
			spec = this.getViewSpec(viewType);
			viewClass = spec['class'];
			if (this.options.resources && spec.options.resources !== false) {
				if (spec.queryResourceClass) {
					viewClass = spec.queryResourceClass(spec) || viewClass;
				} else if (spec.resourceClass) {
					viewClass = spec.resourceClass;
				}
			}
			return new viewClass(this, viewType, spec.options, spec.duration);
		};

		CalendarExtension.prototype.getResources = function () {
			return this.resourceManager.topLevelResources;
		};

		CalendarExtension.prototype.addResource = function (resourceInput, scroll) {
			var promise;
			if (scroll == null) {
				scroll = false;
			}
			promise = this.resourceManager.addResource(resourceInput);
			if (scroll && this.view.scrollToResource) {
				promise.done((function (_this) {
					return function (resource) {
						return _this.view.scrollToResource(resource);
					};
				})(this));
			}
		};

		CalendarExtension.prototype.removeResource = function (idOrResource) {
			return this.resourceManager.removeResource(idOrResource);
		};

		CalendarExtension.prototype.refetchResources = function () {
			this.resourceManager.fetchResources();
		};

		CalendarExtension.prototype.rerenderResources = function () {
			this.resourceManager.resetResources();
		};

		CalendarExtension.prototype.getPeerEvents = function (span, event) {
			var filteredPeerEvents, isPeer, j, k, l, len, len1, len2, newResourceId, newResourceIds, peerEvent, peerEvents, peerResourceId, peerResourceIds;
			peerEvents = CalendarExtension.__super__.getPeerEvents.apply(this, arguments);
			newResourceIds = span.resourceId ? [span.resourceId] : event ? this.getEventResourceIds(event) : [];
			filteredPeerEvents = [];
			for (j = 0, len = peerEvents.length; j < len; j++) {
				peerEvent = peerEvents[j];
				isPeer = false;
				peerResourceIds = this.getEventResourceIds(peerEvent);
				if (!peerResourceIds.length || !newResourceIds.length) {
					isPeer = true;
				} else {
					for (k = 0, len1 = peerResourceIds.length; k < len1; k++) {
						peerResourceId = peerResourceIds[k];
						for (l = 0, len2 = newResourceIds.length; l < len2; l++) {
							newResourceId = newResourceIds[l];
							if (newResourceId === peerResourceId) {
								isPeer = true;
								break;
							}
						}
					}
				}
				if (isPeer) {
					filteredPeerEvents.push(peerEvent);
				}
			}
			return filteredPeerEvents;
		};

		CalendarExtension.prototype.buildSelectSpan = function (startInput, endInput, resourceId) {
			var span;
			span = CalendarExtension.__super__.buildSelectSpan.apply(this, arguments);
			if (resourceId) {
				span.resourceId = resourceId;
			}
			return span;
		};

		CalendarExtension.prototype.getResourceById = function (id) {
			return this.resourceManager.getResourceById(id);
		};

		CalendarExtension.prototype.getEventResourceId = function (event) {
			return this.getEventResourceIds(event)[0];
		};

		CalendarExtension.prototype.getEventResourceIds = function (event) {
			var j, len, normalResourceId, normalResourceIds, ref, ref1, ref2, resourceId;
			resourceId = String((ref = (ref1 = event[this.getEventResourceField()]) != null ? ref1 : event.resourceId) != null ? ref : '');
			if (resourceId) {
				return [resourceId];
			} else if (event.resourceIds) {
				normalResourceIds = [];
				ref2 = event.resourceIds;
				for (j = 0, len = ref2.length; j < len; j++) {
					resourceId = ref2[j];
					normalResourceId = String(resourceId != null ? resourceId : '');
					if (normalResourceId) {
						normalResourceIds.push(normalResourceId);
					}
				}
				return normalResourceIds;
			} else {
				return [];
			}
		};

		CalendarExtension.prototype.setEventResourceId = function (event, resourceId) {
			return event[this.getEventResourceField()] = String(resourceId != null ? resourceId : '');
		};

		CalendarExtension.prototype.getEventResourceField = function () {
			return this.options['eventResourceField'] || 'resourceId';
		};

		CalendarExtension.prototype.getResourceEvents = function (idOrResource) {
			var resource;
			resource = typeof idOrResource === 'object' ? idOrResource : this.getResourceById(idOrResource);
			if (resource) {
				return this.clientEvents((function (_this) {
					return function (event) {
						return $.inArray(resource.id, _this.getEventResourceIds(event)) !== -1;
					};
				})(this));
			} else {
				return [];
			}
		};

		CalendarExtension.prototype.getEventResource = function (idOrEvent) {
			return this.getEventResources(idOrEvent)[0];
		};

		CalendarExtension.prototype.getEventResources = function (idOrEvent) {
			var event, j, len, ref, resource, resourceId, resources;
			event = typeof idOrEvent === 'object' ? idOrEvent : this.clientEvents(idOrEvent)[0];
			resources = [];
			if (event) {
				ref = this.getEventResourceIds(event);
				for (j = 0, len = ref.length; j < len; j++) {
					resourceId = ref[j];
					resource = this.getResourceById(resourceId);
					if (resource) {
						resources.push(resource);
					}
				}
			}
			return resources;
		};

		return CalendarExtension;

	})(Calendar);

	Calendar.prototype = CalendarExtension.prototype;

	origDisplayView = View.prototype.displayView;

	origRenderSkeleton = View.prototype.renderSkeleton;

	origUnrenderSkeleton = View.prototype.unrenderSkeleton;

	origDisplayEvents = View.prototype.displayEvents;

	View.prototype.isResourcesBound = false;

	View.prototype.settingResources = null;

	View.prototype.displayView = function () {
		origDisplayView.apply(this, arguments);
		this.bindResources();
		return this.whenResources();
	};

	View.prototype.unrenderSkeleton = function () {
		origUnrenderSkeleton.apply(this, arguments);
		return this.unbindResources();
	};

	View.prototype.displayEvents = function (events) {
		return this.whenResources((function (_this) {
			return function () {
				return origDisplayEvents.call(_this, events);
			};
		})(this));
	};

	View.prototype.bindResources = function () {
		var setResources;
		if (!this.isResourcesBound) {
			this.isResourcesBound = true;
			this.settingResources = $.Deferred();
			setResources = (function (_this) {
				return function (resources) {
					_this.setResources(resources);
					return _this.settingResources.resolve();
				};
			})(this);
			this.calendar.resourceManager.on('set', this._setResources = setResources).on('unset', this._unsetResources = proxy(this, 'unsetResources')).on('reset', this._resetResources = proxy(this, 'resetResources')).on('add', this._addResource = proxy(this, 'addResource')).on('remove', this._removeResource = proxy(this, 'removeResource'));
			if (this.calendar.resourceManager.hasFetched()) {
				return setResources(this.calendar.resourceManager.topLevelResources);
			} else {
				return this.calendar.resourceManager.getResources();
			}
		}
	};

	View.prototype.unbindResources = function () {
		if (this.isResourcesBound) {
			this.calendar.resourceManager.off('set', this._setResources).off('unset', this._unsetResources).off('reset', this._resetResources).off('add', this._addResource).off('remove', this._removeResource);
			if (this.settingResources.state() === 'resolved') {
				this.unsetResources();
			}
			this.settingResources = null;
			return this.isResourcesBound = false;
		}
	};

	View.prototype.whenResources = function (thenFunc) {
		if (this.isResourcesBound) {
			if (this.settingResources.state() === 'resolved') {
				return $.when(thenFunc ? thenFunc() : void 0);
			} else if (thenFunc) {
				return this.settingResources.then(thenFunc);
			} else {
				return this.settingResources.promise();
			}
		}
	};

	View.prototype.setResources = function (resources) {
	};

	View.prototype.unsetResources = function () {
	};

	View.prototype.resetResources = function (resources) {
		return this.calendar.rerenderEvents();
	};

	View.prototype.addResource = function (resource) {
		return this.resetResources(this.calendar.resourceManager.topLevelResources);
	};

	View.prototype.removeResource = function (resource) {
		return this.resetResources(this.calendar.resourceManager.topLevelResources);
	};

	origGetSegClasses = Grid.prototype.getSegClasses;

	Grid.prototype.getSegClasses = function (seg) {
		var classes, j, len, ref, resource;
		classes = origGetSegClasses.apply(this, arguments);
		ref = this.getSegResources(seg);
		for (j = 0, len = ref.length; j < len; j++) {
			resource = ref[j];
			classes = classes.concat(resource.eventClassName || []);
		}
		return classes;
	};

	Grid.prototype.getSegSkinCss = function (seg) {
		var event, eventColor, getResourceBackgroundColor, getResourceBorderColor, getResourceTextColor, optionColor, resources, source, sourceColor, view;
		view = this.view;
		event = seg.event;
		source = event.source || {};
		eventColor = event.color;
		sourceColor = source.color;
		optionColor = view.opt('eventColor');
		resources = this.getSegResources(seg);
		getResourceBackgroundColor = function () {
			var currentResource, j, len, val;
			val = null;
			for (j = 0, len = resources.length; j < len; j++) {
				currentResource = resources[j];
				while (currentResource && !val) {
					val = currentResource.eventBackgroundColor || currentResource.eventColor;
					currentResource = currentResource._parent;
				}
			}
			return val;
		};
		getResourceBorderColor = function () {
			var currentResource, j, len, val;
			val = null;
			for (j = 0, len = resources.length; j < len; j++) {
				currentResource = resources[j];
				while (currentResource && !val) {
					val = currentResource.eventBorderColor || currentResource.eventColor;
					currentResource = currentResource._parent;
				}
			}
			return val;
		};
		getResourceTextColor = function () {
			var currentResource, j, len, val;
			val = null;
			for (j = 0, len = resources.length; j < len; j++) {
				currentResource = resources[j];
				while (currentResource && !val) {
					val = currentResource.eventTextColor;
					currentResource = currentResource._parent;
				}
			}
			return val;
		};
		return {
			'background-color': event.backgroundColor || eventColor || getResourceBackgroundColor() || source.backgroundColor || sourceColor || view.opt('eventBackgroundColor') || optionColor,
			'border-color': event.borderColor || eventColor || getResourceBorderColor() || source.borderColor || sourceColor || view.opt('eventBorderColor') || optionColor,
			'color': event.textColor || getResourceTextColor() || source.textColor || view.opt('eventTextColor')
		};
	};

	Grid.prototype.getSegResources = function (seg) {
		if (seg.resource) {
			return [seg.resource];
		} else {
			return this.view.calendar.getEventResources(seg.event);
		}
	};

	ResourceManager = (function (superClass) {
		extend(ResourceManager, superClass);

		ResourceManager.mixin(EmitterMixin);

		ResourceManager.resourceGuid = 1;

		ResourceManager.ajaxDefaults = {
			dataType: 'json',
			cache: false
		};

		ResourceManager.prototype.calendar = null;

		ResourceManager.prototype.topLevelResources = null;

		ResourceManager.prototype.resourcesById = null;

		ResourceManager.prototype.fetching = null;

		function ResourceManager(calendar1) {
			this.calendar = calendar1;
			this.initializeCache();
		}

		ResourceManager.prototype.hasFetched = function () {
			return this.fetching && this.fetching.state() === 'resolved';
		};

		ResourceManager.prototype.getResources = function () {
			var getting;
			if (!this.fetching) {
				getting = $.Deferred();
				syncThen(this.fetchResources(), function () {
					return getting.resolve(this.topLevelResources);
				}, function () {
					return getting.resolve([]);
				});
				return getting.promise();
			} else {
				return $.Deferred().resolve(this.topLevelResources).promise();
			}
		};

		ResourceManager.prototype.fetchResources = function () {
			var prevFetching;
			prevFetching = this.fetching;
			return syncThen(prevFetching, (function (_this) {
				return function () {
					_this.fetching = $.Deferred();
					_this.fetchResourceInputs(function (resourceInputs) {
						_this.setResources(resourceInputs, Boolean(prevFetching));
						return _this.fetching.resolve(_this.topLevelResources);
					});
					return _this.fetching.promise();
				};
			})(this));
		};

		ResourceManager.prototype.fetchResourceInputs = function (callback) {
			var source;
			source = this.calendar.options['resources'];
			if ($.type(source) === 'string') {
				source = {
					url: source
				};
			}
			switch ($.type(source)) {
				case 'function':
					this.calendar.pushLoading();
					return source((function (_this) {
						return function (resourceInputs) {
							_this.calendar.popLoading();
							return callback(resourceInputs);
						};
					})(this));
				case 'object':
					this.calendar.pushLoading();
					return $.ajax($.extend({}, ResourceManager.ajaxDefaults, source)).done((function (_this) {
						return function (resourceInputs) {
							_this.calendar.popLoading();
							return callback(resourceInputs);
						};
					})(this));
				case 'array':
					return callback(source);
				default:
					return callback([]);
			}
		};

		ResourceManager.prototype.resetResources = function () {
			return syncThen(this.getResources(), (function (_this) {
				return function () {
					return _this.trigger('reset', _this.topLevelResources);
				};
			})(this));
		};

		ResourceManager.prototype.getResourceById = function (id) {
			return this.resourcesById[id];
		};

		ResourceManager.prototype.getFlatResources = function () {
			var id, results;
			results = [];
			for (id in this.resourcesById) {
				results.push(this.resourcesById[id]);
			}
			return results;
		};

		ResourceManager.prototype.initializeCache = function () {
			this.topLevelResources = [];
			return this.resourcesById = {};
		};

		ResourceManager.prototype.setResources = function (resourceInputs, isReset) {
			var j, len, resource, resourceInput, resources, validResources;
			this.initializeCache();
			resources = (function () {
				var j, len, results;
				results = [];
				for (j = 0, len = resourceInputs.length; j < len; j++) {
					resourceInput = resourceInputs[j];
					results.push(this.buildResource(resourceInput));
				}
				return results;
			}).call(this);
			validResources = (function () {
				var j, len, results;
				results = [];
				for (j = 0, len = resources.length; j < len; j++) {
					resource = resources[j];
					if (this.addResourceToIndex(resource)) {
						results.push(resource);
					}
				}
				return results;
			}).call(this);
			for (j = 0, len = validResources.length; j < len; j++) {
				resource = validResources[j];
				this.addResourceToTree(resource);
			}
			if (isReset) {
				this.trigger('reset', this.topLevelResources);
			} else {
				this.trigger('set', this.topLevelResources);
			}
			return this.calendar.trigger('resourcesSet', null, this.topLevelResources);
		};

		ResourceManager.prototype.addResource = function (resourceInput) {
			return syncThen(this.fetching, (function (_this) {
				return function () {
					var resource;
					resource = _this.buildResource(resourceInput);
					if (_this.addResourceToIndex(resource)) {
						_this.addResourceToTree(resource);
						_this.trigger('add', resource);
						return resource;
					} else {
						return false;
					}
				};
			})(this));
		};

		ResourceManager.prototype.addResourceToIndex = function (resource) {
			var child, j, len, ref;
			if (this.resourcesById[resource.id]) {
				return false;
			} else {
				this.resourcesById[resource.id] = resource;
				ref = resource.children;
				for (j = 0, len = ref.length; j < len; j++) {
					child = ref[j];
					this.addResourceToIndex(child);
				}
				return true;
			}
		};

		ResourceManager.prototype.addResourceToTree = function (resource) {
			var parent, parentId, ref, siblings;
			if (!resource.parent) {
				parentId = String((ref = resource['parentId']) != null ? ref : '');
				if (parentId) {
					parent = this.resourcesById[parentId];
					if (parent) {
						resource.parent = parent;
						siblings = parent.children;
					} else {
						return false;
					}
				} else {
					siblings = this.topLevelResources;
				}
				siblings.push(resource);
			}
			return true;
		};

		ResourceManager.prototype.removeResource = function (idOrResource) {
			var id;
			id = typeof idOrResource === 'object' ? idOrResource.id : idOrResource;
			return syncThen(this.fetching, (function (_this) {
				return function () {
					var resource;
					resource = _this.removeResourceFromIndex(id);
					if (resource) {
						_this.removeResourceFromTree(resource);
						_this.trigger('remove', resource);
					}
					return resource;
				};
			})(this));
		};

		ResourceManager.prototype.removeResourceFromIndex = function (resourceId) {
			var child, j, len, ref, resource;
			resource = this.resourcesById[resourceId];
			if (resource) {
				delete this.resourcesById[resourceId];
				ref = resource.children;
				for (j = 0, len = ref.length; j < len; j++) {
					child = ref[j];
					this.removeResourceFromIndex(child.id);
				}
				return resource;
			} else {
				return false;
			}
		};

		ResourceManager.prototype.removeResourceFromTree = function (resource, siblings) {
			var i, j, len, sibling;
			if (siblings == null) {
				siblings = this.topLevelResources;
			}
			for (i = j = 0, len = siblings.length; j < len; i = ++j) {
				sibling = siblings[i];
				if (sibling === resource) {
					resource.parent = null;
					siblings.splice(i, 1);
					return true;
				}
				if (this.removeResourceFromTree(resource, sibling.children)) {
					return true;
				}
			}
			return false;
		};

		ResourceManager.prototype.buildResource = function (resourceInput) {
			var child, childInput, rawClassName, ref, resource;
			resource = $.extend({}, resourceInput);
			resource.id = String((ref = resourceInput.id) != null ? ref : '_fc' + ResourceManager.resourceGuid++);
			rawClassName = resourceInput.eventClassName;
			resource.eventClassName = (function () {
				switch ($.type(rawClassName)) {
					case 'string':
						return rawClassName.split(/\s+/);
					case 'array':
						return rawClassName;
					default:
						return [];
				}
			})();
			resource.children = (function () {
				var j, len, ref1, ref2, results;
				ref2 = (ref1 = resourceInput.children) != null ? ref1 : [];
				results = [];
				for (j = 0, len = ref2.length; j < len; j++) {
					childInput = ref2[j];
					child = this.buildResource(childInput);
					child.parent = resource;
					results.push(child);
				}
				return results;
			}).call(this);
			return resource;
		};

		return ResourceManager;

	})(Class);

	ResourceViewMixin = {
		resourceTextFunc: null,
		unsetResources: function () {
			return this.clearEvents();
		},
		resetResources: function (resources) {
			var scrollState;
			scrollState = this.queryScroll();
			this.unsetResources();
			this.setResources(resources);
			this.setScroll(scrollState);
			return this.calendar.rerenderEvents();
		},
		getResourceText: function (resource) {
			return this.getResourceTextFunc()(resource);
		},
		getResourceTextFunc: function () {
			var func;
			if (this.resourceTextFunc) {
				return this.resourceTextFunc;
			} else {
				func = this.opt('resourceText');
				if (typeof func !== 'function') {
					func = function (resource) {
						return resource.title || resource.id;
					};
				}
				return this.resourceTextFunc = func;
			}
		},
		triggerDayClick: function (span, dayEl, ev) {
			var resourceManager;
			resourceManager = this.calendar.resourceManager;
			return this.trigger('dayClick', dayEl, this.calendar.applyTimezone(span.start), ev, this, resourceManager.getResourceById(span.resourceId));
		},
		triggerSelect: function (span, ev) {
			var resourceManager;
			resourceManager = this.calendar.resourceManager;
			return this.trigger('select', null, this.calendar.applyTimezone(span.start), this.calendar.applyTimezone(span.end), ev, this, resourceManager.getResourceById(span.resourceId));
		},
		triggerExternalDrop: function (event, dropLocation, el, ev, ui) {
			this.trigger('drop', el[0], dropLocation.start, ev, ui, dropLocation.resourceId);
			if (event) {
				return this.trigger('eventReceive', null, event);
			}
		},
		reportEventDrop: function () {
			var dropLocation, event, otherArgs, ref;
			event = arguments[0], dropLocation = arguments[1], otherArgs = 3 <= arguments.length ? slice.call(arguments, 2) : [];
			dropLocation = this.normalizeDropLocation(dropLocation);
			if (dropLocation.resourceId && event.resourceIds) {
				dropLocation.resourceIds = null;
			}
			return (ref = View.prototype.reportEventDrop).call.apply(ref, [this, event, dropLocation].concat(slice.call(otherArgs)));
		},
		reportExternalDrop: function () {
			var dropLocation, meta, otherArgs, ref;
			meta = arguments[0], dropLocation = arguments[1], otherArgs = 3 <= arguments.length ? slice.call(arguments, 2) : [];
			dropLocation = this.normalizeDropLocation(dropLocation);
			return (ref = View.prototype.reportExternalDrop).call.apply(ref, [this, meta, dropLocation].concat(slice.call(otherArgs)));
		},
		normalizeDropLocation: function (dropLocation) {
			var out;
			out = $.extend({}, dropLocation);
			delete out.resourceId;
			this.calendar.setEventResourceId(out, dropLocation.resourceId);
			return out;
		}
	};

	ResourceGridMixin = {
		allowCrossResource: true,
		eventRangeToSpans: function (range, event) {
			var j, len, resourceId, resourceIds, results;
			resourceIds = this.view.calendar.getEventResourceIds(event);
			if (resourceIds.length) {
				results = [];
				for (j = 0, len = resourceIds.length; j < len; j++) {
					resourceId = resourceIds[j];
					results.push($.extend({}, range, {
						resourceId: resourceId
					}));
				}
				return results;
			} else if (FC.isBgEvent(event)) {
				return Grid.prototype.eventRangeToSpans.apply(this, arguments);
			} else {
				return [];
			}
		},
		fabricateHelperEvent: function (eventLocation, seg) {
			var event;
			event = Grid.prototype.fabricateHelperEvent.apply(this, arguments);
			this.view.calendar.setEventResourceId(event, eventLocation.resourceId);
			return event;
		},
		computeEventDrop: function (startSpan, endSpan, event) {
			var allowResourceChange, dropLocation;
			allowResourceChange = true;
			if (!allowResourceChange && startSpan.resourceId !== endSpan.resourceId) {
				return null;
			}
			dropLocation = Grid.prototype.computeEventDrop.apply(this, arguments);
			if (dropLocation) {
				dropLocation.resourceId = endSpan.resourceId;
			}
			return dropLocation;
		},
		computeExternalDrop: function (span, meta) {
			var dropLocation;
			dropLocation = Grid.prototype.computeExternalDrop.apply(this, arguments);
			if (dropLocation) {
				dropLocation.resourceId = span.resourceId;
			}
			return dropLocation;
		},
		computeEventResize: function (type, startSpan, endSpan, event) {
			var resizeLocation;
			if (!this.allowCrossResource && startSpan.resourceId !== endSpan.resourceId) {
				return;
			}
			resizeLocation = Grid.prototype.computeEventResize.apply(this, arguments);
			if (resizeLocation) {
				resizeLocation.resourceId = startSpan.resourceId;
			}
			return resizeLocation;
		},
		computeSelectionSpan: function (startSpan, endSpan) {
			var selectionSpan;
			if (!this.allowCrossResource && startSpan.resourceId !== endSpan.resourceId) {
				return;
			}
			selectionSpan = Grid.prototype.computeSelectionSpan.apply(this, arguments);
			if (selectionSpan) {
				selectionSpan.resourceId = startSpan.resourceId;
			}
			return selectionSpan;
		}
	};

	ResourceDayTableMixin = {
		flattenedResources: null,
		resourceCnt: 0,
		datesAboveResources: false,
		allowCrossResource: false,
		setResources: function (resources) {
			this.flattenedResources = this.flattenResources(resources);
			this.resourceCnt = this.flattenedResources.length;
			return this.updateDayTableCols();
		},
		unsetResources: function () {
			this.flattenedResources = null;
			this.resourceCnt = 0;
			return this.updateDayTableCols();
		},
		flattenResources: function (resources) {
			var orderSpecs, orderVal, res, sortFunc;
			orderVal = this.view.opt('resourceOrder');
			if (orderVal) {
				orderSpecs = parseFieldSpecs(orderVal);
				sortFunc = function (a, b) {
					return compareByFieldSpecs(a, b, orderSpecs);
				};
			} else {
				sortFunc = null;
			}
			res = [];
			this.accumulateResources(resources, sortFunc, res);
			return res;
		},
		accumulateResources: function (resources, sortFunc, res) {
			var j, len, resource, results, sortedResources;
			if (sortFunc) {
				sortedResources = resources.slice(0);
				sortedResources.sort(sortFunc);
			} else {
				sortedResources = resources;
			}
			results = [];
			for (j = 0, len = sortedResources.length; j < len; j++) {
				resource = sortedResources[j];
				res.push(resource);
				results.push(this.accumulateResources(resource.children, sortFunc, res));
			}
			return results;
		},
		updateDayTableCols: function () {
			this.datesAboveResources = this.view.opt('groupByDateAndResource');
			return FC.DayTableMixin.updateDayTableCols.call(this);
		},
		computeColCnt: function () {
			return (this.resourceCnt || 1) * this.daysPerRow;
		},
		getColDayIndex: function (col) {
			if (this.isRTL) {
				col = this.colCnt - 1 - col;
			}
			if (this.datesAboveResources) {
				return Math.floor(col / (this.resourceCnt || 1));
			} else {
				return col % this.daysPerRow;
			}
		},
		getColResource: function (col) {
			return this.flattenedResources[this.getColResourceIndex(col)];
		},
		getColResourceIndex: function (col) {
			if (this.isRTL) {
				col = this.colCnt - 1 - col;
			}
			if (this.datesAboveResources) {
				return col % (this.resourceCnt || 1);
			} else {
				return Math.floor(col / this.daysPerRow);
			}
		},
		indicesToCol: function (resourceIndex, dayIndex) {
			var col;
			col = this.datesAboveResources ? dayIndex * (this.resourceCnt || 1) + resourceIndex : resourceIndex * this.daysPerRow + dayIndex;
			if (this.isRTL) {
				col = this.colCnt - 1 - col;
			}
			return col;
		},
		renderHeadTrHtml: function () {
			if (!this.resourceCnt) {
				return FC.DayTableMixin.renderHeadTrHtml.call(this);
			} else {
				if (this.daysPerRow > 1) {
					if (this.datesAboveResources) {
						return this.renderHeadDateAndResourceHtml();
					} else {
						return this.renderHeadResourceAndDateHtml();
					}
				} else {
					return this.renderHeadResourceHtml();
				}
			}
		},
		renderHeadResourceHtml: function () {
			var j, len, ref, resource, resourceHtmls;
			resourceHtmls = [];
			ref = this.flattenedResources;
			for (j = 0, len = ref.length; j < len; j++) {
				resource = ref[j];
				resourceHtmls.push(this.renderHeadResourceCellHtml(resource));
			}
			return this.wrapTr(resourceHtmls, 'renderHeadIntroHtml');
		},
		renderHeadResourceAndDateHtml: function () {
			var date, dateHtmls, dayIndex, j, k, len, ref, ref1, resource, resourceHtmls;
			resourceHtmls = [];
			dateHtmls = [];
			ref = this.flattenedResources;
			for (j = 0, len = ref.length; j < len; j++) {
				resource = ref[j];
				resourceHtmls.push(this.renderHeadResourceCellHtml(resource, null, this.daysPerRow));
				for (dayIndex = k = 0, ref1 = this.daysPerRow; k < ref1; dayIndex = k += 1) {
					date = this.dayDates[dayIndex].clone();
					dateHtmls.push(this.renderHeadResourceDateCellHtml(date, resource));
				}
			}
			return this.wrapTr(resourceHtmls, 'renderHeadIntroHtml') + this.wrapTr(dateHtmls, 'renderHeadIntroHtml');
		},
		renderHeadDateAndResourceHtml: function () {
			var date, dateHtmls, dayIndex, j, k, len, ref, ref1, resource, resourceHtmls;
			dateHtmls = [];
			resourceHtmls = [];
			for (dayIndex = j = 0, ref = this.daysPerRow; j < ref; dayIndex = j += 1) {
				date = this.dayDates[dayIndex].clone();
				dateHtmls.push(this.renderHeadDateCellHtml(date, this.resourceCnt));
				ref1 = this.flattenedResources;
				for (k = 0, len = ref1.length; k < len; k++) {
					resource = ref1[k];
					resourceHtmls.push(this.renderHeadResourceCellHtml(resource, date));
				}
			}
			return this.wrapTr(dateHtmls, 'renderHeadIntroHtml') + this.wrapTr(resourceHtmls, 'renderHeadIntroHtml');
		},
		renderHeadResourceCellHtml: function (resource, date, colspan) {
			return '<th class="fc-resource-cell"' + ' data-resource-id="' + resource.id + '"' + (date ? ' data-date="' + date.format('YYYY-MM-DD') + '"' : '') + (colspan > 1 ? ' colspan="' + colspan + '"' : '') + '>' + htmlEscape(this.view.getResourceText(resource)) + '</th>';
		},
		renderHeadResourceDateCellHtml: function (date, resource, colspan) {
			return this.renderHeadDateCellHtml(date, colspan, 'data-resource-id="' + resource.id + '"');
		},
		processHeadResourceEls: function (containerEl) {
			return containerEl.find('.fc-resource-cell').each((function (_this) {
				return function (col, node) {
					var resource;
					if (_this.datesAboveResources) {
						resource = _this.getColResource(col);
					} else {
						resource = _this.flattenedResources[_this.isRTL ? _this.flattenedResources.length - 1 - col : col];
					}
					return _this.view.trigger('resourceRender', resource, resource, $(node), $());
				};
			})(this));
		},
		renderBgCellsHtml: function (row) {
			var col, date, htmls, j, ref, resource;
			if (!this.resourceCnt) {
				return FC.DayTableMixin.renderBgCellsHtml.call(this, row);
			} else {
				htmls = [];
				for (col = j = 0, ref = this.colCnt; j < ref; col = j += 1) {
					date = this.getCellDate(row, col);
					resource = this.getColResource(col);
					htmls.push(this.renderResourceBgCellHtml(date, resource));
				}
				return htmls.join('');
			}
		},
		renderResourceBgCellHtml: function (date, resource) {
			return this.renderBgCellHtml(date, 'data-resource-id="' + resource.id + '"');
		},
		wrapTr: function (cellHtmls, introMethodName) {
			if (this.isRTL) {
				cellHtmls.reverse();
				return '<tr>' + cellHtmls.join('') + this[introMethodName]() + '</tr>';
			} else {
				return '<tr>' + this[introMethodName]() + cellHtmls.join('') + '</tr>';
			}
		},
		computePerResourceBusinessHourSegs: function (wholeDay) {
			var allSegs, anyCustomBusinessHours, businessHours, event, events, j, k, l, len, len1, len2, ref, ref1, resource, segs;
			if (this.flattenedResources) {
				anyCustomBusinessHours = false;
				ref = this.flattenedResources;
				for (j = 0, len = ref.length; j < len; j++) {
					resource = ref[j];
					if (resource.businessHours) {
						anyCustomBusinessHours = true;
					}
				}
				if (anyCustomBusinessHours) {
					allSegs = [];
					ref1 = this.flattenedResources;
					for (k = 0, len1 = ref1.length; k < len1; k++) {
						resource = ref1[k];
						businessHours = resource.businessHours || this.view.opt('businessHours');
						events = this.view.calendar.computeBusinessHourEvents(wholeDay, businessHours);
						for (l = 0, len2 = events.length; l < len2; l++) {
							event = events[l];
							event.resourceId = resource.id;
						}
						segs = this.eventsToSegs(events);
						allSegs.push.apply(allSegs, segs);
					}
					return allSegs;
				}
			}
			return null;
		}
	};

	ResourceDayGrid = (function (superClass) {
		extend(ResourceDayGrid, superClass);

		function ResourceDayGrid() {
			return ResourceDayGrid.__super__.constructor.apply(this, arguments);
		}

		ResourceDayGrid.mixin(ResourceGridMixin);

		ResourceDayGrid.mixin(ResourceDayTableMixin);

		ResourceDayGrid.prototype.getHitSpan = function (hit) {
			var span;
			span = ResourceDayGrid.__super__.getHitSpan.apply(this, arguments);
			if (this.resourceCnt) {
				span.resourceId = this.getColResource(hit.col).id;
			}
			return span;
		};

		ResourceDayGrid.prototype.spanToSegs = function (span) {
			var copy, genericSegs, j, k, l, len, len1, ref, resourceCnt, resourceIndex, resourceObj, resourceSegs, seg;
			resourceCnt = this.resourceCnt;
			genericSegs = this.datesAboveResources ? this.sliceRangeByDay(span) : this.sliceRangeByRow(span);
			if (!resourceCnt) {
				for (j = 0, len = genericSegs.length; j < len; j++) {
					seg = genericSegs[j];
					if (this.isRTL) {
						seg.leftCol = seg.lastRowDayIndex;
						seg.rightCol = seg.firstRowDayIndex;
					} else {
						seg.leftCol = seg.firstRowDayIndex;
						seg.rightCol = seg.lastRowDayIndex;
					}
				}
				return genericSegs;
			} else {
				resourceSegs = [];
				for (k = 0, len1 = genericSegs.length; k < len1; k++) {
					seg = genericSegs[k];
					for (resourceIndex = l = 0, ref = resourceCnt; l < ref; resourceIndex = l += 1) {
						resourceObj = this.flattenedResources[resourceIndex];
						if (!span.resourceId || span.resourceId === resourceObj.id) {
							copy = $.extend({}, seg);
							copy.resource = resourceObj;
							if (this.isRTL) {
								copy.leftCol = this.indicesToCol(resourceIndex, seg.lastRowDayIndex);
								copy.rightCol = this.indicesToCol(resourceIndex, seg.firstRowDayIndex);
							} else {
								copy.leftCol = this.indicesToCol(resourceIndex, seg.firstRowDayIndex);
								copy.rightCol = this.indicesToCol(resourceIndex, seg.lastRowDayIndex);
							}
							resourceSegs.push(copy);
						}
					}
				}
				return resourceSegs;
			}
		};

		ResourceDayGrid.prototype.renderBusinessHours = function () {
			var segs;
			segs = this.computePerResourceBusinessHourSegs(true);
			if (segs) {
				return this.renderFill('businessHours', segs, 'bgevent');
			} else {
				return ResourceDayGrid.__super__.renderBusinessHours.apply(this, arguments);
			}
		};

		return ResourceDayGrid;

	})(FC.DayGrid);

	ResourceTimeGrid = (function (superClass) {
		extend(ResourceTimeGrid, superClass);

		function ResourceTimeGrid() {
			return ResourceTimeGrid.__super__.constructor.apply(this, arguments);
		}

		ResourceTimeGrid.mixin(ResourceGridMixin);

		ResourceTimeGrid.mixin(ResourceDayTableMixin);

		ResourceTimeGrid.prototype.getHitSpan = function (hit) {
			var span;
			span = ResourceTimeGrid.__super__.getHitSpan.apply(this, arguments);
			if (this.resourceCnt) {
				span.resourceId = this.getColResource(hit.col).id;
			}
			return span;
		};

		ResourceTimeGrid.prototype.spanToSegs = function (span) {
			var copy, genericSegs, j, k, l, len, len1, ref, resourceCnt, resourceIndex, resourceObj, resourceSegs, seg;
			resourceCnt = this.resourceCnt;
			genericSegs = this.sliceRangeByTimes(span);
			if (!resourceCnt) {
				for (j = 0, len = genericSegs.length; j < len; j++) {
					seg = genericSegs[j];
					seg.col = seg.dayIndex;
				}
				return genericSegs;
			} else {
				resourceSegs = [];
				for (k = 0, len1 = genericSegs.length; k < len1; k++) {
					seg = genericSegs[k];
					for (resourceIndex = l = 0, ref = resourceCnt; l < ref; resourceIndex = l += 1) {
						resourceObj = this.flattenedResources[resourceIndex];
						if (!span.resourceId || span.resourceId === resourceObj.id) {
							copy = $.extend({}, seg);
							copy.resource = resourceObj;
							copy.col = this.indicesToCol(resourceIndex, seg.dayIndex);
							resourceSegs.push(copy);
						}
					}
				}
				return resourceSegs;
			}
		};

		ResourceTimeGrid.prototype.renderBusinessHours = function () {
			var segs;
			segs = this.computePerResourceBusinessHourSegs(false);
			if (segs) {
				return this.renderBusinessSegs(segs);
			} else {
				return ResourceTimeGrid.__super__.renderBusinessHours.apply(this, arguments);
			}
		};

		return ResourceTimeGrid;

	})(FC.TimeGrid);

	cssToStr = FC.cssToStr;

	computeOffsetForSegs = function (segs) {
		var j, len, max, seg;
		max = 0;
		for (j = 0, len = segs.length; j < len; j++) {
			seg = segs[j];
			max = Math.max(max, computeOffsetForSeg(seg));
		}
		return max;
	};

	computeOffsetForSeg = function (seg) {
		if (seg.top == null) {
			seg.top = computeOffsetForSegs(seg.above);
		}
		return seg.top + seg.height;
	};

	MIN_AUTO_LABELS = 18;

	MAX_AUTO_SLOTS_PER_LABEL = 6;

	MAX_AUTO_CELLS = 200;

	MAX_CELLS = 1000;

	COL_MIN_WIDTH = 30;

	/*
	 An abstract node in a row-hierarchy tree.
	 May be a self-contained single row, a row with subrows,
	 OR a grouping of rows without its own distinct row.
	 */

	RowParent = (function () {
		RowParent.prototype.view = null;

		RowParent.prototype.parent = null;

		RowParent.prototype.prevSibling = null;

		RowParent.prototype.children = null;

		RowParent.prototype.depth = 0;

		RowParent.prototype.hasOwnRow = false;

		RowParent.prototype.trHash = null;

		RowParent.prototype.trs = null;

		RowParent.prototype.isRendered = false;

		RowParent.prototype.isExpanded = true;

		RowParent.prototype.isShown = false;

		function RowParent(view1) {
			console.log('row');
			this.view = view1;
			this.children = [];
			this.trHash = {};
			this.trs = $();
		}


		/*
		 Adds the given node as a child.
		 Will be inserted at the `index`. If not given, will be appended to the end.
		 */

		RowParent.prototype.addChild = function (child, index) {
			var children, j, len, node, ref;
			child.remove();
			children = this.children;
			if (index != null) {
				children.splice(index, 0, child);
			} else {
				index = children.length;
				children.push(child);
			}
			child.prevSibling = index > 0 ? children[index - 1] : null;
			if (index < children.length - 1) {
				children[index + 1].prevSibling = child;
			}
			child.parent = this;
			child.depth = this.depth + (this.hasOwnRow ? 1 : 0);
			ref = child.getNodes();
			for (j = 0, len = ref.length; j < len; j++) {
				node = ref[j];
				node.added();
			}
			if (this.isShown && this.isExpanded) {
				return child.show();
			}
		};

		RowParent.prototype.removeChild = function (child) {
			var children, i, isFound, j, k, len, len1, ref, row, testChild;
			children = this.children;
			isFound = false;
			for (i = j = 0, len = children.length; j < len; i = ++j) {
				testChild = children[i];
				if (testChild === child) {
					isFound = true;
					break;
				}
			}
			if (!isFound) {
				return false;
			} else {
				if (i < children.length - 1) {
					children[i + 1].prevSibling = child.prevSibling;
				}
				children.splice(i, 1);
				child.recursivelyUnrender();
				ref = child.getNodes();
				for (k = 0, len1 = ref.length; k < len1; k++) {
					row = ref[k];
					row.removed();
				}
				child.parent = null;
				child.prevSibling = null;
				return child;
			}
		};

		RowParent.prototype.removeChildren = function () {
			var child, j, k, len, len1, ref, ref1;
			ref = this.children;
			for (j = 0, len = ref.length; j < len; j++) {
				child = ref[j];
				child.recursivelyUnrender();
			}
			ref1 = this.getDescendants();
			for (k = 0, len1 = ref1.length; k < len1; k++) {
				child = ref1[k];
				child.removed();
			}
			return this.children = [];
		};


		/*
		 Removes this node from its parent
		 */

		RowParent.prototype.remove = function () {
			if (this.parent) {
				return this.parent.removeChild(this);
			}
		};


		/*
		 Gets the last direct child node
		 */

		RowParent.prototype.getLastChild = function () {
			var children;
			children = this.children;
			return children[children.length - 1];
		};


		/*
		 Walks backward in the hierarchy to find the previous row leaf node.
		 When looking at the hierarchy in a flat linear fashion, this is the revealed row just before the current.
		 */

		RowParent.prototype.getPrevRow = function () {
			var lastChild, node;
			node = this;
			while (node) {
				if (node.prevSibling) {
					node = node.prevSibling;
					while ((lastChild = node.getLastChild())) {
						node = lastChild;
					}
				} else {
					node = node.parent;
				}
				if (node && node.hasOwnRow && node.isShown) {
					return node;
				}
			}
			return null;
		};


		/*
		 Returns the first node in the subtree that has a revealed row
		 */

		RowParent.prototype.getLeadingRow = function () {
			if (this.hasOwnRow) {
				return this;
			} else if (this.isExpanded && this.children.length) {
				return this.children[0].getLeadingRow();
			}
		};

		RowParent.prototype.getRows = function (batchArray) {
			var child, j, len, ref;
			if (batchArray == null) {
				batchArray = [];
			}
			if (this.hasOwnRow) {
				batchArray.push(this);
			}
			ref = this.children;
			for (j = 0, len = ref.length; j < len; j++) {
				child = ref[j];
				child.getRows(batchArray);
			}
			return batchArray;
		};

		RowParent.prototype.getNodes = function (batchArray) {
			var child, j, len, ref;
			if (batchArray == null) {
				batchArray = [];
			}
			batchArray.push(this);
			ref = this.children;
			for (j = 0, len = ref.length; j < len; j++) {
				child = ref[j];
				child.getNodes(batchArray);
			}
			return batchArray;
		};

		RowParent.prototype.getDescendants = function () {
			var batchArray, child, j, len, ref;
			batchArray = [];
			ref = this.children;
			for (j = 0, len = ref.length; j < len; j++) {
				child = ref[j];
				child.getNodes(batchArray);
			}
			return batchArray;
		};

		RowParent.prototype.render = function () {
			var prevRow, ref, renderMethodName, tbody, tr, trNodes, type;
			this.trHash = {};
			trNodes = [];
			if (this.hasOwnRow) {
				prevRow = this.getPrevRow();
				ref = this.view.tbodyHash;
				for (type in ref) {
					tbody = ref[type];
					tr = $('<tr/>');
					this.trHash[type] = tr;
					trNodes.push(tr[0]);
					renderMethodName = 'render' + capitaliseFirstLetter(type) + 'Content';
					if (this[renderMethodName]) {
						this[renderMethodName](tr);
					}
					if (prevRow) {
						prevRow.trHash[type].after(tr);
					} else {
						tbody.prepend(tr);
					}
				}
			}
			this.trs = $(trNodes).on('click', '.fc-expander', proxy(this, 'toggleExpanded'));
			return this.isRendered = true;
		};

		RowParent.prototype.unrender = function () {
			var ref, tr, type, unrenderMethodName;
			if (this.isRendered) {
				ref = this.trHash;
				for (type in ref) {
					tr = ref[type];
					unrenderMethodName = 'unrender' + capitaliseFirstLetter(type) + 'Content';
					if (this[unrenderMethodName]) {
						this[unrenderMethodName](tr);
					}
				}
				this.trHash = {};
				this.trs.remove();
				this.trs = $();
				this.isRendered = false;
				this.isShown = false;
				return this.hidden();
			}
		};

		RowParent.prototype.recursivelyUnrender = function () {
			var child, j, len, ref, results;
			this.unrender();
			ref = this.children;
			results = [];
			for (j = 0, len = ref.length; j < len; j++) {
				child = ref[j];
				results.push(child.recursivelyUnrender());
			}
			return results;
		};

		RowParent.prototype.getTr = function (type) {
			return this.trHash[type];
		};

		RowParent.prototype.show = function () {
			var child, j, len, ref, results;
			if (!this.isShown) {
				if (!this.isRendered) {
					this.render();
				} else {
					this.trs.css('display', '');
				}
				if (this.ensureSegsRendered) {
					this.ensureSegsRendered();
				}
				if (this.isExpanded) {
					this.indicateExpanded();
				} else {
					this.indicateCollapsed();
				}
				this.isShown = true;
				this.shown();
				if (this.isExpanded) {
					ref = this.children;
					results = [];
					for (j = 0, len = ref.length; j < len; j++) {
						child = ref[j];
						results.push(child.show());
					}
					return results;
				}
			}
		};


		/*
		 Temporarily hides this node's TRs (if applicable) as well as all nodes in the subtree
		 */

		RowParent.prototype.hide = function () {
			var child, j, len, ref, results;
			if (this.isShown) {
				if (this.isRendered) {
					this.trs.hide();
				}
				this.isShown = false;
				this.hidden();
				if (this.isExpanded) {
					ref = this.children;
					results = [];
					for (j = 0, len = ref.length; j < len; j++) {
						child = ref[j];
						results.push(child.hide());
					}
					return results;
				}
			}
		};


		/*
		 Reveals this node's children if they have not already been revealed. Changes any expander icon.
		 */

		RowParent.prototype.expand = function () {
			var child, j, len, ref;
			if (!this.isExpanded) {
				this.isExpanded = true;
				this.indicateExpanded();
				this.view.batchRows();
				ref = this.children;
				for (j = 0, len = ref.length; j < len; j++) {
					child = ref[j];
					child.show();
				}
				this.view.unbatchRows();
				return this.animateExpand();
			}
		};


		/*
		 Hides this node's children if they are not already hidden. Changes any expander icon.
		 */

		RowParent.prototype.collapse = function () {
			var child, j, len, ref;
			if (this.isExpanded) {
				this.isExpanded = false;
				this.indicateCollapsed();
				this.view.batchRows();
				ref = this.children;
				for (j = 0, len = ref.length; j < len; j++) {
					child = ref[j];
					child.hide();
				}
				return this.view.unbatchRows();
			}
		};


		/*
		 Switches between expanded/collapsed states
		 */

		RowParent.prototype.toggleExpanded = function () {
			if (this.isExpanded) {
				return this.collapse();
			} else {
				return this.expand();
			}
		};


		/*
		 Changes the expander icon to the "expanded" state
		 */

		RowParent.prototype.indicateExpanded = function () {
			return this.trs.find('.fc-expander').removeClass(this.getCollapsedIcon()).addClass(this.getExpandedIcon());
		};


		/*
		 Changes the expander icon to the "collapsed" state
		 */

		RowParent.prototype.indicateCollapsed = function () {
			return this.trs.find('.fc-expander').removeClass(this.getExpandedIcon()).addClass(this.getCollapsedIcon());
		};


		/*
		 */

		RowParent.prototype.enableExpanding = function () {
			return this.trs.find('.fc-expander-space').addClass('fc-expander');
		};


		/*
		 */

		RowParent.prototype.disableExpanding = function () {
			return this.trs.find('.fc-expander-space').removeClass('fc-expander').removeClass(this.getExpandedIcon()).removeClass(this.getCollapsedIcon());
		};

		RowParent.prototype.getExpandedIcon = function () {
			return 'fc-icon-down-triangle';
		};

		RowParent.prototype.getCollapsedIcon = function () {
			var dir;
			dir = this.view.isRTL ? 'left' : 'right';
			return 'fc-icon-' + dir + '-triangle';
		};


		/*
		 Causes a slide-down CSS transition to demonstrate that the expand has happened
		 */

		RowParent.prototype.animateExpand = function () {
			var ref, ref1, trs;
			trs = (ref = this.children[0]) != null ? (ref1 = ref.getLeadingRow()) != null ? ref1.trs : void 0 : void 0;
			if (trs) {
				trs.addClass('fc-collapsed');
				setTimeout(function () {
					trs.addClass('fc-transitioning');
					return trs.removeClass('fc-collapsed');
				});
				return trs.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function () {
					return trs.removeClass('fc-transitioning');
				});
			}
		};


		/*
		 Find each TRs "inner div" (div within first cell). This div controls each TRs height.
		 Returns the max pixel height.
		 */

		RowParent.prototype.getMaxTrInnerHeight = function () {
			var max;
			max = 0;
			$.each(this.trHash, (function (_this) {
				return function (type, tr) {
					var innerEl;
					innerEl = getOwnCells(tr).find('> div:not(.fc-cell-content):first');
					return max = Math.max(innerEl.height(), max);
				};
			})(this));
			return max;
		};


		/*
		 Find each TRs "inner div" and sets all of their heights to the same value.
		 */

		RowParent.prototype.setTrInnerHeight = function (height) {
			return $.each(this.trHash, (function (_this) {
				return function (type, tr) {
					return getOwnCells(tr).find('> div:not(.fc-cell-content):first').height(height);
				};
			})(this));
		};


		/*
		 Triggered when the current node has been shown (either freshly rendered or re-shown)
		 when it had previously been unrendered or hidden. `shown` does not bubble up the hierarchy.
		 */

		RowParent.prototype.shown = function () {
			if (this.hasOwnRow) {
				return this.rowShown(this);
			}
		};


		/*
		 Triggered when the current node has been hidden (either temporarily or permanently)
		 when it had previously been shown. `hidden` does not bubble up the hierarchy.
		 */

		RowParent.prototype.hidden = function () {
			if (this.hasOwnRow) {
				return this.rowHidden(this);
			}
		};


		/*
		 Just like `shown`, but only triggered for nodes that are actual rows. Bubbles up the hierarchy.
		 */

		RowParent.prototype.rowShown = function (row) {
			return (this.parent || this.view).rowShown(row);
		};


		/*
		 Just like `hidden`, but only triggered for nodes that are actual rows. Bubbles up the hierarchy.
		 */

		RowParent.prototype.rowHidden = function (row) {
			return (this.parent || this.view).rowHidden(row);
		};


		/*
		 Triggered when the current node has been added to the hierarchy. `added` does not bubble up.
		 */

		RowParent.prototype.added = function () {
			if (this.hasOwnRow) {
				return this.rowAdded(this);
			}
		};


		/*
		 Triggered when the current node has been removed from the hierarchy. `removed` does not bubble up.
		 */

		RowParent.prototype.removed = function () {
			if (this.hasOwnRow) {
				return this.rowRemoved(this);
			}
		};


		/*
		 Just like `added`, but only triggered for nodes that are actual rows. Bubbles up the hierarchy.
		 */

		RowParent.prototype.rowAdded = function (row) {
			return (this.parent || this.view).rowAdded(row);
		};


		/*
		 Just like `removed`, but only triggered for nodes that are actual rows. Bubbles up the hierarchy.
		 */

		RowParent.prototype.rowRemoved = function (row) {
			return (this.parent || this.view).rowRemoved(row);
		};

		return RowParent;

	})();


	/*
	 An abstract node in a row-hierarchy tree that contains other nodes.
	 Will have some sort of rendered label indicating the grouping,
	 up to the subclass for determining what to do with it.
	 */

	RowGroup = (function (superClass) {
		extend(RowGroup, superClass);

		RowGroup.prototype.groupSpec = null;

		RowGroup.prototype.groupValue = null;

		function RowGroup(view, groupSpec1, groupValue1) {
			this.groupSpec = groupSpec1;
			this.groupValue = groupValue1;
			RowGroup.__super__.constructor.apply(this, arguments);
		}


		/*
		 Called when this row (if it renders a row) or a subrow is removed
		 */

		RowGroup.prototype.rowRemoved = function (row) {
			RowGroup.__super__.rowRemoved.apply(this, arguments);
			if (row !== this && !this.children.length) {
				return this.remove();
			}
		};


		/*
		 Renders the content wrapper element that will be inserted into this row's TD cell
		 */

		RowGroup.prototype.renderGroupContentEl = function () {
			var contentEl, filter;
			contentEl = $('<div class="fc-cell-content" />').append(this.renderGroupTextEl());
			filter = this.groupSpec.render;
			if (typeof filter === 'function') {
				contentEl = filter(contentEl, this.groupValue) || contentEl;
			}
			return contentEl;
		};


		/*
		 Renders the text span element that will be inserted into this row's TD cell.
		 Goes within the content element.
		 */

		RowGroup.prototype.renderGroupTextEl = function () {
			var filter, text;
			text = this.groupValue || '';
			filter = this.groupSpec.text;
			if (typeof filter === 'function') {
				text = filter(text) || text;
			}
			return $('<span class="fc-cell-text" />').text(text);
		};

		return RowGroup;

	})(RowParent);


	/*
	 A row grouping that renders as a tall multi-cell vertical span in the "spreadsheet" area
	 */

	VRowGroup = (function (superClass) {
		extend(VRowGroup, superClass);

		function VRowGroup() {
			return VRowGroup.__super__.constructor.apply(this, arguments);
		}

		VRowGroup.prototype.rowspan = 0;

		VRowGroup.prototype.leadingTr = null;

		VRowGroup.prototype.groupTd = null;


		/*
		 Called when a row somewhere within the grouping is shown
		 */

		VRowGroup.prototype.rowShown = function (row) {
			this.rowspan += 1;
			this.renderRowspan();
			return VRowGroup.__super__.rowShown.apply(this, arguments);
		};


		/*
		 Called when a row somewhere within the grouping is hidden
		 */

		VRowGroup.prototype.rowHidden = function (row) {
			this.rowspan -= 1;
			this.renderRowspan();
			return VRowGroup.__super__.rowHidden.apply(this, arguments);
		};


		/*
		 Makes sure the groupTd has the correct rowspan / place in the DOM.
		 PRECONDITION: in the case of multiple group nesting, a child's renderRowspan()
		 will be called before the parent's renderRowspan().
		 */

		VRowGroup.prototype.renderRowspan = function () {
			var leadingTr;
			if (this.rowspan) {
				if (!this.groupTd) {
					this.groupTd = $('<td class="' + this.view.widgetContentClass + '"/>').append(this.renderGroupContentEl());
				}
				this.groupTd.attr('rowspan', this.rowspan);
				leadingTr = this.getLeadingRow().getTr('spreadsheet');
				if (leadingTr !== this.leadingTr) {
					if (leadingTr) {
						leadingTr.prepend(this.groupTd);
					}
					return this.leadingTr = leadingTr;
				}
			} else {
				if (this.groupTd) {
					this.groupTd.remove();
					this.groupTd = null;
				}
				return this.leadingTr = null;
			}
		};

		return VRowGroup;

	})(RowGroup);

	EventRow = (function (superClass) {
		extend(EventRow, superClass);

		function EventRow() {
			return EventRow.__super__.constructor.apply(this, arguments);
		}

		EventRow.prototype.hasOwnRow = true;

		EventRow.prototype.segContainerEl = null;

		EventRow.prototype.segContainerHeight = null;

		EventRow.prototype.innerEl = null;

		EventRow.prototype.bgSegContainerEl = null;

		EventRow.prototype.isSegsRendered = false;

		EventRow.prototype.bgSegs = null;

		EventRow.prototype.fgSegs = null;

		EventRow.prototype.renderEventContent = function (tr) {
			tr.html('<td class="' + this.view.widgetContentClass + '"> <div> <div class="fc-event-container" /> </div> </td>');
			this.segContainerEl = tr.find('.fc-event-container');
			this.innerEl = this.bgSegContainerEl = tr.find('td > div');
			return this.ensureSegsRendered();
		};

		EventRow.prototype.ensureSegsRendered = function () {
			if (!this.isSegsRendered) {
				if (this.bgSegs) {
					this.view.timeGrid.renderFillInContainer('bgEvent', this, this.bgSegs);
				}
				if (this.fgSegs) {
					this.view.timeGrid.renderFgSegsInContainers([[this, this.fgSegs]]);
				}
				return this.isSegsRendered = true;
			}
		};

		EventRow.prototype.unrenderEventContent = function () {
			this.bgSegs = null;
			this.fgSegs = null;
			return this.isSegsRendered = false;
		};

		return EventRow;

	})(RowParent);


	/*
	 A row that renders information about a particular resource, as well as it events (handled by superclass)
	 */

	ResourceRow = (function (superClass) {
		extend(ResourceRow, superClass);

		ResourceRow.prototype.resource = null;

		function ResourceRow(view, resource1) {
			this.resource = resource1;
			ResourceRow.__super__.constructor.apply(this, arguments);
		}


		/*
		 Called when a row in the tree has been added
		 */

		ResourceRow.prototype.rowAdded = function (row) {
			ResourceRow.__super__.rowAdded.apply(this, arguments);
			if (row !== this && this.isRendered) {
				if (this.children.length === 1) {
					this.enableExpanding();
					if (this.isExpanded) {
						return this.indicateExpanded();
					} else {
						return this.indicateCollapsed();
					}
				}
			}
		};


		/*
		 Called when a row in the tree has been removed
		 */

		ResourceRow.prototype.rowRemoved = function (row) {
			ResourceRow.__super__.rowRemoved.apply(this, arguments);
			if (row !== this && this.isRendered) {
				if (!this.children.length) {
					return this.disableExpanding();
				}
			}
		};

		ResourceRow.prototype.render = function () {
			ResourceRow.__super__.render.apply(this, arguments);
			if (this.children.length > 0) {
				this.enableExpanding();
			} else {
				this.disableExpanding();
			}
			return this.view.trigger('resourceRender', this.resource, this.resource, this.getTr('spreadsheet').find('> td'), this.getTr('event').find('> td'));
		};

		ResourceRow.prototype.renderEventContent = function (tr) {
			ResourceRow.__super__.renderEventContent.apply(this, arguments);
			return tr.attr('data-resource-id', this.resource.id);
		};


		/*
		 Populates the TR with cells containing data about the resource
		 */

		ResourceRow.prototype.renderSpreadsheetContent = function (tr) {
			var colSpec, contentEl, input, j, len, ref, resource, td, text;
			resource = this.resource;
			ref = this.view.colSpecs;
			for (j = 0, len = ref.length; j < len; j++) {
				colSpec = ref[j];
				if (colSpec.group) {
					continue;
				}
				input = colSpec.field ? resource[colSpec.field] || null : resource;
				text = typeof colSpec.text === 'function' ? colSpec.text(resource, input) : input;
				contentEl = $('<div class="fc-cell-content">' + (colSpec.isMain ? this.renderGutterHtml() : '') + '<span class="fc-cell-text">' + (text ? htmlEscape(text) : '&nbsp;') + '</span>' + '</div>');
				if (typeof colSpec.render === 'function') {
					contentEl = colSpec.render(resource, contentEl, input) || contentEl;
				}
				td = $('<td class="' + this.view.widgetContentClass + '"/>').append(contentEl);
				if (colSpec.isMain) {
					td.wrapInner('<div/>');
				}
				tr.append(td);
			}
			return tr.attr('data-resource-id', resource.id);
		};


		/*
		 Renders the HTML responsible for the subrow expander area,
		 as well as the space before it (used to align expanders of similar depths)
		 */

		ResourceRow.prototype.renderGutterHtml = function () {
			var html, i, j, ref;
			html = '';
			for (i = j = 0, ref = this.depth; j < ref; i = j += 1) {
				html += '<span class="fc-icon"/>';
			}
			html += '<span class="fc-icon fc-expander-space"/>';
			return html;
		};

		return ResourceRow;

	})(EventRow);

	ResourceAgendaView = (function (superClass) {
		extend(ResourceAgendaView, superClass);

		function ResourceAgendaView() {
			return ResourceAgendaView.__super__.constructor.apply(this, arguments);
		}

		ResourceAgendaView.mixin(ResourceViewMixin);

		ResourceAgendaView.prototype.timeGridClass = ResourceTimeGrid;

		ResourceAgendaView.prototype.dayGridClass = ResourceDayGrid;

		ResourceAgendaView.prototype.renderHead = function () {
			ResourceAgendaView.__super__.renderHead.apply(this, arguments);
			return this.timeGrid.processHeadResourceEls(this.headContainerEl);
		};

		ResourceAgendaView.prototype.setResources = function (resources) {
			this.timeGrid.setResources(resources);
			if (this.dayGrid) {
				this.dayGrid.setResources(resources);
			}
			this.clearView();
			return this.displayView();
		};

		ResourceAgendaView.prototype.unsetResources = function (isDestroying) {
			this.clearEvents();
			this.timeGrid.unsetResources();
			if (this.dayGrid) {
				this.dayGrid.unsetResources();
			}
			if (!isDestroying) {
				this.clearView();
				return this.displayView();
			}
		};

		return ResourceAgendaView;

	})(FC.AgendaView);

	FC.views.agenda.queryResourceClass = function (viewSpec) {
		var ref;
		if ((ref = viewSpec.options.groupByResource || viewSpec.options.groupByDateAndResource) != null ? ref : viewSpec.duration.as('days') === 1) {
			return ResourceAgendaView;
		}
	};

})(jQuery, moment);
