jQuery(document).ready(function ($) {
	$(document).on("submit", "#qmean-admin-form", function (e) {
		e.preventDefault();

		// We inject some extra fields required for the security
		$("#qmean-security").val(qmean._nonce);

		// We make our call
		$.ajax({
			url: qmean.ajax_url,
			type: "post",
			data: $(this).serialize(),
			beforeSend: function () {
				$("html, body").animate({ scrollTop: 0 }, 500);
				$(".qmean-settings-notification")
					.html("Saving ...")
					.addClass("loading");
			},
			success: function (response) {
				$(".qmean-settings-notification")
					.html(response)
					.removeClass("loading")
					.addClass("show");
			},
		});
	});
	$(document).delegate(".qmean-hint-toggler", "click", function (e) {
		$(this).next().toggle();

		$(this).toggleClass("opened");
	});

	$("#qmean_wrapper_background").wpColorPicker();

	$(document).delegate(".qmean-tooltip", "click", function () {
		var t = $(this);
		var target = t.attr("data-target");
		var content = $("#" + target + "_help").html();
		$("#" + target)
			.pointer({
				content: content,
				position: "top",
			})
			.pointer("open");
	});

	$(document).delegate("#qmean_search_mode", "change", function () {
		var t = $(this);
		var value = t.val();

		if (value == "word_by_word") {
			$("#qmean-word-count-wrapper").hide();
		} else {
			$("#qmean-word-count-wrapper").show();
		}
	});
});
