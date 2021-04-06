var qmean_ajax_xhr;
var qmean_delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();


function qmean_hook_close_suggestions(){
  jQuery(document).on('click',function(e){
    if(jQuery('#qmean-suggestion-results').length > 0 && jQuery(e.target).attr('id') != 'qmean-suggestion-results'){
      jQuery('#qmean-suggestion-results').remove();
    }
  });
}

jQuery(document).ready(function($) {

  var qmean_selector;
  if(qmean.selector != '' && typeof qmean.selector !== 'undefined'){
    qmean_selector = $(qmean.selector);
    if(typeof qmean_selector !== 'undefined' && qmean_selector.length > 0){

      if(qmean.parent_position != '') qmean_selector.parent().css('position',qmean.parent_position);
      var queries = [];
      $(document).delegate(qmean.selector,'keyup',function(e){
          var t = $(this);

          // to position automatically
          var x = t.offset().left;
          var y = t.offset().top;
          var h = t.outerHeight();
          var w = t.outerWidth();

          // defaulted positioning
          var dx = qmean.posx == '-' ? x : qmean.posx;
          var dy = qmean.posy == '-' ? h : qmean.posy;
          var dw = qmean.width == '-' ? w : qmean.width;
          var dh = qmean.height == '-' ? 250 : qmean.height;

          var suggestion_elm;
          var suggestion_html = '';

          $('#qmean-suggestion-results').remove();

          var q = '';
          var query = $(this).val();
          query = query.trim();

          if(query.length >= 3){
            if(qmean.search_mode == 'word_by_word'){
              queries = query.split(" ");
              q = queries[queries.length - 1]
            } else {
              q = query;
            }

            qmean_delay(function(){
              qmean_ajax_xhr = $.ajax({
                url: qmean.ajaxurl,
                type:'post',
                data:{
                  'action': 'qmean_search',
                  'query': q,
                  '_wpnonce': qmean._nonce
                },
                beforeSend:function(){
                  // abort pending ajax request if exists to avoid multiple on uneccessary xhr requests
                  if(qmean_ajax_xhr != null) {
                      qmean_ajax_xhr.abort();
                  }

                  t.parent().append('<div id="qmean-suggestion-results"></div>');
                  suggestion_elm = $('#qmean-suggestion-results');
                  suggestion_elm.css({
                    'top':dy,
                    'max-height':dh,
                    'background' : qmean.wrapper_background,
                    'border-radius' : qmean.wrapper_border_radius,
                    'padding' : qmean.wrapper_padding,
                  });
                  if(qmean.rtl_support == 'yes'){
                    suggestion_elm.css('right',dx);
                  } else {
                    suggestion_elm.css('left',dx);
                  }
                  suggestion_elm.width(dw);
                  // suggestion_elm.addClass('qmean-loading show');
                  qmean_hook_close_suggestions();
                },
                success:function(data){
                  if(data.status != 'not_found'){
                    suggestion_elm.removeClass('qmean-loading');
                    if(qmean.search_mode == 'word_by_word'){
                      var queries_str = '';
                      var fixed_queries = queries.slice(0,queries.length - 1);
                      if(queries.length > 1 ) queries_str = fixed_queries.join(" ");
                      $(data.suggestions).each(function(i,v){
                        suggestion_html += '<div class="qmean-suggestion-item" data-query="'+queries_str+' '+v+'">'+queries_str+' '+v+'</div>';
                      });
                    } else {
                      $(data.suggestions).each(function(i,v){
                        suggestion_html += '<div class="qmean-suggestion-item" data-query="'+v+'">'+v+'</div>';
                      });
                    }


                    suggestion_elm.html(suggestion_html);
                  } else {

                  }
                }
              });
            },500);
          }
        });

      $(document).delegate('.qmean-suggestion-item','click',function(e){
          var t = $(this);
          $(qmean_selector).val(t.attr('data-query'));
        });
    }
  }



} );
