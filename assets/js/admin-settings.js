jQuery(document).ready(function($){
  $( document ).on( 'submit', '#qmean-admin-form', function ( e ) {

       e.preventDefault();

       // We inject some extra fields required for the security
       $('#qmean-security').val(qmean._nonce);

       // We make our call
       $.ajax( {
           url: qmean.ajaxurl,
           type: 'post',
           data: $(this).serialize(),
           beforeSend:function(){
             $('.qmean-settings-notification').html('Saving ...').addClass('loading');
           },
           success: function (response) {
              $('.qmean-settings-notification').html(response).removeClass('loading').addClass('show');
           }
       } );

   } );
   $('.qmean-hint-toggler').click(function(e){
     $(this).parent().find('.qmean-hint-toggle-wrapper').toggle(500);
     $(this).toggleClass('opened');
   })
});
