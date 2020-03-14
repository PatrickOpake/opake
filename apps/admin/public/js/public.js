$(function () {

	/* Авторизация */
	(function ($) {
		var backdrop = $('<div class="modal-backdrop fade in"></div>'),
			panel = $('.panel-auth');

		var close = function () {
			backdrop.remove();
			panel.slideUp(200);
			$('.navbar').animate({'margin-top': 0}, 200);
		};

		$(".auth").click(function (e) {
			backdrop.appendTo(document.body).click(close);
			panel.slideDown(200);

			var height = $(window).width() < 768 ? '300px' : '180px';
			$('.navbar').animate({'margin-top': height}, 200);

			e.preventDefault();
		});
		$(".auth-close").click(function (e) {
			close();
			e.preventDefault();
		});

		$("form", panel).submit(function(e){
			e.preventDefault();
			var form = $(this);
			$.post("/auth/login", form.serializeArray(), function (data) {
				if (data.success) {
					window.location.href = '/';
				} else {
					$(".form-group", form).addClass("has-error");
					$(".error", panel).html(data.error);
				}
			});
		});
	})($);

	/* Скролл для меню */
	$('.scroller a').click(function () {
		var link = $(this).attr('href');
		if (link[0] === '#') {
			var pos = $(link).offset().top;
			$('body,html').animate({scrollTop: pos}, 700);
		}
	});

	/* Крутилка айпадов */
	var swiper = new Swiper('.swiper-container', {
		pagination: '.swiper-pagination',
		autoplay: 5000,
		effect: 'coverflow',
		grabCursor: true,
		centeredSlides: true,
		loop: true,
		slidesPerView: 2,
		coverflow: {
			rotate: 0,
			stretch: 0,
			depth: 300,
			modifier: 2,
			slideShadows: false,
		}
	});

	// Форма обратной связи
	$(".contact-form").submit(function (e) {
		e.preventDefault();
		var form = $(this);
		$('.result', form).html('');
		$('.help', form).html('');

		$.post('/mail', form.serializeArray(),function (data) {
			if (data.success) {
				$('.result', form).html('<label>Message has been sent!</label>');
				form[0].reset();
			} else {
				if (data.errors) {
					$.each(data.errors, function(key, data){
						var input = $(':input[name="message[' + key + ']"]', form);
						$('.help', input.parent()).html(data[0]);
					});
				}
			}
		});
	});

	// Калькулятор
	(function ($) {
		var calc = $(".calculator");
		var checkboxes = [];

		// Блокируем все кроме цифер
		$("input", calc).keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
			     // Allow: Ctrl+A
			    (e.keyCode == 65 && e.ctrlKey === true) || 
			     // Allow: home, end, left, right, down, up
			    (e.keyCode >= 35 && e.keyCode <= 40)) {
				 // let it happen, don't do anything
				 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			    e.preventDefault();
			}
		    });


		var showStep = function (step) {
			$(".body > *, .control > *", calc).hide();
			$(".notes", calc).hide();
			$("." + step, calc).show();
		};
		$(".reset, .restart", calc).click(function (e) {
			checkboxes = [];
			$("input[type=text]", calc).val("");
			$("input[type=checkbox]", calc).prop("checked", false);
			$(".set-specialization", calc).removeClass("highlight");
			$(".title", calc).html("Calculator");
			showStep("questions");
		});
		$(".back", calc).click(function (e) {
			$(".specialization input[type=checkbox]", calc).prop("checked", false);

			if (checkboxes.length) {
				$.each(checkboxes, function(index, value) {
					$(".specialization input[name=" + value + "]", calc).prop("checked", true);
				});
			}
			showStep("questions");
		});
		$(".save", calc).click(function (e) {
			checkboxes = [];
			$( ".specialization input:checked", calc).each(function(){
				checkboxes.push($(this).attr("name"));
			});
			showStep("questions");
		});
		$(".more", calc).click(function (e) {
			var pos = $("#contact").offset().top;
			$('body, html').animate({scrollTop: pos}, 700);
		});
		$(".set-specialization", calc).click(function (e) {
			showStep("specialization");
			$(".set-specialization", calc).addClass("highlight");
		});
		$(".calc", calc).click(function (e) {
			var questions = $(".questions", calc);
			var results = $(".results", calc);

			var pharm = questions.find('input[name="pharm"]').val();
			pharm = parseFloat(pharm ? pharm : 0) * 0.2;
			$(".pharm", results).html("$ " + pharm.toFixed(2));

			var devices = questions.find('input[name="devices"]').val();
			devices = parseFloat(devices ? devices : 0) * 0.2;
			$(".devices", results).html("$ " + devices.toFixed(2));

			var margin = questions.find('input[name="margin"]').val();
			margin = margin ? ((pharm + devices) / (margin / 100)) : 0;
			$(".margin", results).html("$ " + margin.toFixed(2));

			showStep("results");
			$(".title", calc).html("Results");
			$(".notes", calc).show();
		});
	})($);

});
