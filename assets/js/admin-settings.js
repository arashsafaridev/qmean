jQuery(document).ready(function($){
  jQuery( document ).on( 'submit', '#qmean-admin-form', function ( e ) {

       e.preventDefault();

       // We inject some extra fields required for the security
       jQuery('#qmean-security').val(qmean._nonce);

       // We make our call
       jQuery.ajax( {
           url: qmean.ajaxurl,
           type: 'post',
           data: jQuery(this).serialize(),
           beforeSend:function(){
             jQuery('.qmean-settings-notification').html('Saving ...').addClass('loading');
           },
           success: function (response) {
              jQuery('.qmean-settings-notification').html(response).removeClass('loading').addClass('show');
           }
       } );

   } );
});
