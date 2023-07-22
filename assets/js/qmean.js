var qmean_ajax_xhr;
var qmean_delay = (function () {
	var timer = 0;
	return function (callback, ms) {
		clearTimeout(timer);
		timer = setTimeout(callback, ms);
	};
})();

function qmean_hook_close_suggestions() {
	jQuery(document).on("click", function (e) {
		if (
			jQuery("#qmean-suggestion-results").length > 0 &&
			jQuery(e.target).attr("id") != "qmean-suggestion-results"
		) {
			jQuery("#qmean-suggestion-results").remove();
		}
	});
}

jQuery(document).ready(function ($) {
	var qmean_selector;
	var qmean_result_li;
	var qmean_li_selected;

	if (qmean.selector != "" && typeof qmean.selector !== "undefined") {
		console.log("qmean.selector: " + qmean.selector);
		qmean_selector = $(qmean.selector);
		console.log("qmean_selector: " + qmean_selector);
		if (typeof qmean_selector !== "undefined" && qmean_selector.length > 0) {
			if (qmean.parent_position != "")
				qmean_selector.parent().css("position", qmean.parent_position);
			var queries = [];

			$(qmean.selector).attr("autocomplete", "off");

			$(document).delegate(qmean.selector, "keyup", function (e) {
				if (e.which === 40 || e.which === 38) {
					return false;
				}
				var t = $(this);
				var p = t.parents("form");

				var custom_areas = p.attr("data-areas");
				custom_areas = typeof custom_areas != "undefined" ? custom_areas : "";
				var custom_post_types = p.attr("data-post_types");
				custom_post_types =
					typeof custom_post_types != "undefined" ? custom_post_types : "";
				// to position automatically
				var x = t.offset().left;
				var y = t.offset().top;
				var h = t.outerHeight();
				var w = t.outerWidth();

				// defaulted positioning
				var zindex = qmean.zindex;
				var dx = qmean.posx == "-" ? x : qmean.posx;
				var dy = qmean.posy == "-" ? h : qmean.posy;
				var dw = qmean.width == "-" ? w : qmean.width;
				var dh = qmean.height == "-" ? 250 : qmean.height;

				var suggestion_elm;
				var suggestion_html = "";
				var ajax_payload = {};
				var q_length_limit = 3;

				$("#qmean-suggestion-results").remove();

				var q = "";
				var query = $(this).val();
				query = query.trim();

				if (qmean.search_mode == "word_by_word") {
					queries = query.split(" ");
					q = queries[queries.length - 1];
				} else {
					q = query;
				}

				if (qmean.suggest_engine === "google") {
					ajax_payload.url = "https://clients1.google.com/complete/search";
					ajax_payload.type = "get";
					ajax_payload.data = { q: q, hl: "en", client: "hp" };
					ajax_payload.dataType = "jsonp";
					q_length_limit = 1;
				} else {
					ajax_payload.url = qmean.ajax_url;
					ajax_payload.type = "post";
					ajax_payload.dataType = "json";
					ajax_payload.data = {
						action: "qmean_search",
						areas: custom_areas,
						post_types: custom_post_types,
						query: q,
						_wpnonce: qmean._nonce,
					};
				}
				if (q.length >= q_length_limit) {
					qmean_delay(function () {
						qmean_ajax_xhr = $.ajax({
							url: ajax_payload.url,
							type: ajax_payload.type,
							data: ajax_payload.data,
							dataType: ajax_payload.dataType,
							beforeSend: function () {
								// abort pending ajax request if exists to avoid multiple on uneccessary xhr requests
								if (qmean_ajax_xhr != null) {
									qmean_ajax_xhr.abort();
								}

								t.parent().append(
									'<div id="qmean-suggestion-results"><div class="qmean-suggestion-loading">' +
										qmean.labels.loading +
										"</div></div>"
								);
								suggestion_elm = $("#qmean-suggestion-results");
								suggestion_elm.css({
									"z-index": zindex,
									top: dy,
									"max-height": dh,
									background: qmean.wrapper_background,
									"border-radius": qmean.wrapper_border_radius,
									padding: qmean.wrapper_padding,
								});
								if (qmean.rtl_support == "yes") {
									suggestion_elm.css("right", dx);
								} else {
									suggestion_elm.css("left", dx);
								}
								suggestion_elm.width(dw);
								// suggestion_elm.addClass('qmean-loading show');
								qmean_hook_close_suggestions();
							},
							success: function (data) {
								if (data.status != "not_found") {
									var suggestions = [];
									if (qmean.suggest_engine === "google") {
										if (typeof data[1] !== "undefined") {
											$(data[1]).each(function (i, v) {
												if (typeof v[0] !== "undefined") {
													suggestions.push(v[0]);
												}
											});
										} else {
											suggestions = [];
										}
									} else {
										suggestions = data.suggestions;
									}
									suggestion_elm.removeClass("qmean-loading");
									if (qmean.search_mode == "word_by_word") {
										var queries_str = "";
										var fixed_queries = queries.slice(0, queries.length - 1);
										if (queries.length > 1)
											queries_str = fixed_queries.join(" ");

										$(suggestions).each(function (i, v) {
											var cleaned_v = v
												.replace("&hellip;", "")
												.replace(/(<([^>]+)>)/gi, "");
											suggestion_html +=
												'<div class="qmean-suggestion-item" data-query="' +
												queries_str +
												" " +
												cleaned_v +
												'">' +
												queries_str +
												" " +
												(qmean.suggest_engine === "google" ? v : cleaned_v) +
												"</div>";
										});
									} else {
										$(suggestions).each(function (i, v) {
											var cleaned_v = v
												.replace("&hellip;", "")
												.replace(/(<([^>]+)>)/gi, "");
											suggestion_html +=
												'<div class="qmean-suggestion-item" data-query="' +
												cleaned_v +
												'">' +
												(qmean.suggest_engine === "google" ? v : cleaned_v) +
												"</div>";
										});
									}

									suggestion_elm.html(suggestion_html);
									qmean_result_li = suggestion_elm.find(
										".qmean-suggestion-item"
									);
									qmean_li_selected = 0;
								} else {
									suggestion_elm.html(
										'<div class="qmean-suggestion-notfound">' +
											qmean.labels.notFound +
											"</div>"
									);
								}
							},
						});
					}, 200);
				}
			});

			$(window).keydown(function (e) {
				if (e.which === 40) {
					if (qmean_li_selected) {
						qmean_li_selected.removeClass("selected");
						next = qmean_li_selected.next();
						if (next.length > 0) {
							qmean_li_selected = next.addClass("selected");
							$(qmean.selector).val(next.attr("data-query").trim() + " ");
						} else {
							qmean_li_selected = qmean_result_li.eq(0).addClass("selected");
							$(qmean.selector).val(
								qmean_result_li.eq(0).attr("data-query").trim() + " "
							);
						}
					} else {
						qmean_li_selected = qmean_result_li.eq(0).addClass("selected");
						$(qmean.selector).val(
							qmean_result_li.eq(0).attr("data-query").trim() + " "
						);
					}
					$(qmean.selector).focus();
				} else if (e.which === 38) {
					if (qmean_li_selected) {
						qmean_li_selected.removeClass("selected");
						next = qmean_li_selected.prev();
						if (next.length > 0) {
							qmean_li_selected = next.addClass("selected");
							$(qmean.selector).val(next.attr("data-query").trim() + " ");
						} else {
							qmean_li_selected = qmean_result_li.last().addClass("selected");
							$(qmean.selector).val(
								qmean_result_li.last().attr("data-query").trim() + " "
							);
						}
					} else {
						qmean_li_selected = qmean_result_li.last().addClass("selected");
						$(qmean.selector).val(
							qmean_result_li.last().attr("data-query").trim() + " "
						);
					}
					$(qmean.selector).focus();
				}
			});

			$(document).delegate(".qmean-suggestion-item", "click", function (e) {
				var t = $(this);
				$(qmean_selector).val(t.attr("data-query").trim());
				if (qmean.submit_after_click == "yes") {
					$(qmean.selector).parents("form").submit();
				}
			});

			$(qmean.selector).focus(function () {
				setTimeout(
					(function (el) {
						var strLength = el.value.length;
						return function () {
							if (el.setSelectionRange !== undefined) {
								el.setSelectionRange(strLength, strLength);
							} else {
								$(el).val(el.value);
							}
						};
					})(this),
					0
				);
			});
		}
	}
});
