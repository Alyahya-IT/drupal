(function($, Drupal) {
  Drupal.multSelect = Drupal.multSelect || {};
  Drupal.behaviors.multSelect = {
    attach: function (context, settings) {
      // $(once('services', '#edit-services')).multiselect({
      //   checkboxAutoFit: true,
      // });  
      $(once('sub-services', '[id^=edit-sub-services]')).multiselect({
        checkboxAutoFit: true,
      }); 
      
    }
  }
})(jQuery, Drupal);
