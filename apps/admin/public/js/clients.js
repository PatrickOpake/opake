$(function () {
	Clients.init();
});

var Clients = {
	initTypeAheadClick: function () {
		var $typeahead = $('.typeahead-add');
		var $typeaheadResult = $('.typeahead-result');

		var onDeleteClick = function (e) {
			var $div = $(this).closest('div');
			$div.remove();
			e.preventDefault();
		};
		var onKeyPress = function (e) {
			var $this = $(this);
			var value = $this.val();
			var checkNoDups = function (value) {
				var result = true;
				$('.title', $typeaheadResult).each(function () {
					if ($(this).text() == value) {
						result = false;
					}
				});
				return result;
			};
			if (e.keyCode === 13) {
				var html = "<div class='clearfix'>"
					+ "<div class='title pull-left'>" + value + "</div>"
					+ "<a class='action pull-right delete' href='#'>x</a>"
					+ "<input type='hidden' name='locations[]' value='" + value + "'/>"
					+ "</div>";
				if (checkNoDups(value)) {
					$typeaheadResult.append(html);
					$this.val('');
				}
			}
			return false;
		};
		$typeahead.on('keyup', onKeyPress);
		$typeaheadResult.on('click', 'a.action', onDeleteClick);
	},
	init: function () {
		this.initTypeAheadClick();
	}
};