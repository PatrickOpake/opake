$(function () {
	$(".log_list").change(function () {
		$.post("/dev/log", {file: $(this).val()}, function (data) {
			$(".log_view").html(data);
		});
	});
	$(".log_clear").click(function () {
		$.post("/dev/logClear", function () {
			location.reload();
		});
	});
});