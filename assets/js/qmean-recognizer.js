jQuery(document).ready(function ($) {
	var qmean_tooltip = $(".qmean-field-recognizer-tooltip");
	var qmean_recognized_selector = "";
	$(document).on("mousemove", function (e) {
		var x = e.pageX + 30;
		var y = e.pageY + 30;
		qmean_tooltip.css({ left: x, top: y });
	});

	$(document).on("click", function (e) {
		var selector = "";
		var elm = $(e.target);
		var elm_parent = elm.parent();

		var tag_name = elm.prop("tagName");
		var elm_id = elm.attr("id");
		var elm_class = elm.attr("class");
		var parent_id = elm_parent.attr("id");
		var parent_class = elm_parent.attr("class");

		if (tag_name != "INPUT" && tag_name != "TEXTAREA") {
			if (elm.hasClass("qmean-skip-click")) {
				return false;
			}
			qmean_tooltip.find(".success").remove();
			if (qmean_tooltip.find(".error").length > 0) {
				qmean_tooltip
					.find(".error")
					.html(
						"<strong>" +
							tag_name +
							"</strong>" +
							qmean.labels.isNotValid +
							" " +
							qmean.labels.pleaseChooseAnInputType
					);
			} else {
				qmean_tooltip.append(
					'<span class="error">' +
						"<strong>" +
						tag_name +
						"</strong>" +
						qmean.labels.isNotValid +
						" " +
						qmean.labels.pleaseChooseAnInputType +
						"</span>"
				);
			}
			$("#qmean-finalize-recognized-selector").remove();
			return false;
		} else {
			if (elm_id !== undefined && elm_id != "") {
				selector = "#" + elm_id;
			} else if (elm_class !== undefined && elm_class != "") {
				selector = "." + elm_class.trim().split(" ").join(".");
				if (parent_id !== undefined && parent_id != "") {
					selector = "#" + parent_id + " ." + selector;
				} else if (parent_class !== undefined && parent_class != "") {
					selector =
						"." + parent_class.trim().split(" ").join(".") + " ." + selector;
				}
			}

			qmean_tooltip.find(".error").remove();
			if (qmean_tooltip.find(".success").length > 0) {
				qmean_tooltip.find(".success").html(selector);
			} else {
				qmean_tooltip.append('<span class="success">' + selector + "</span>");
			}
			qmean_recognized_selector = selector;
			qmean_tooltip.after(
				'<div id="qmean-finalize-recognized-selector" class="qmean-skip-click"><span class="qmean-skip-click">' +
					qmean.labels.yourSelectorIs +
					': <strong class="qmean-skip-click">' +
					selector +
					'</strong></span><button id="qmean-save-recognizer" class="qmean-skip-click">' +
					qmean.labels.saveSelector +
					"</button></div>"
			);
		}
	});

	$(document).delegate("#qmean-save-recognizer", "click", function (e) {
		e.preventDefault();
		$.ajax({
			url: qmean.ajax_url,
			type: "post",
			data: {
				action: "qmean_save_from_recognizer",
				selector: qmean_recognized_selector,
				_wpnonce: qmean._nonce,
			},
			beforeSend: function () {
				$("#qmean-finalize-recognized-selector").addClass("loading");
			},
			success: function (data) {
				$("#qmean-finalize-recognized-selector").removeClass("loading");
				alert(data.message);
				$(
					"#qmean-finalize-recognized-selector,.qmean-field-recognizer-tooltip .error,.qmean-field-recognizer-tooltip .success"
				).remove();
				document.location.href = data.url;
			},
		});
	});
});
