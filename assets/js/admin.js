jQuery(document).ready(function ($) {
	$(document).delegate(".qmean-open-modal", "click", function (e) {
		e.preventDefault();
		$("body").append(
			'<div class="qmean-modal"><div class="qmean-modal-inner"></div></div>'
		);
		var t = $(this);
		var type = t.attr("data-type");
		var keyword = t.attr("data-keyword");
		var modal = $(".qmean-modal-inner");
		var close_btn = '<i class="qmean-close-modal">close</i>';
		$.ajax({
			url: qmean.ajax_url,
			type: "post",
			data: {
				action: "qmean_get_modal",
				type: type,
				keyword: keyword,
				_wpnonce: qmean._nonce,
			},
			beforeSend: function () {
				modal.addClass("loading");
				if (t.hasClass("minimal")) {
					modal.addClass("minimal");
				}
			},
			success: function (data) {
				modal.removeClass("loading");
				modal.html(close_btn + data.html);
			},
		});
	});

	$(document).delegate(
		".qmean-modal,.qmean-close-modal",
		"click",
		function (e) {
			if (
				!$(e.target).hasClass("qmean-modal") &&
				!$(e.target).hasClass("qmean-close-modal")
			) {
				return false;
			}
			$(".qmean-modal").remove();
		}
	);

	$(document).delegate(".qmean-remove-keyword", "click", function (e) {
		e.preventDefault();
		var t = $(this);
		var p = t.parents("tr");
		var id = t.attr("data-id");
		$.ajax({
			url: qmean.ajax_url,
			type: "post",
			data: {
				action: "qmean_remove_keyword",
				id: id,
				_wpnonce: qmean._nonce,
			},
			beforeSend: function () {
				t.parents("tr").addClass("loading");
			},
			success: function (data) {
				p.removeClass("loading");
				if (data.status == "success") {
					p.remove();
				} else {
					alert(data.message);
				}
			},
		});
	});
});
