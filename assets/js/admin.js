jQuery(document).ready(function ($) {

			$(document).delegate('.qmean-remove-keyword', "click", function (e) {
				e.preventDefault();
				var t = $(this);
				var p = t.parents('tr');
				var id = t.attr('data-id');
				$.ajax({
					url: qmean.ajaxurl,
					type: "post",
					data: {
						action: "qmean_remove_keyword",
						id: id,
						_wpnonce: qmean._nonce,
					},
					beforeSend: function () {
						t.parents('tr').addClass('loading');
					},
					success: function (data) {
						p.removeClass('loading');
						if(data.status == 'success'){
							p.remove();
						} else {
							alert(data.message);
						}
					}
				});
			});
});
