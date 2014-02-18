/**
 * @file
 * @todo.
 */

(function ($) {

  "use strict";

  /**
   * Transform a set of A-Z links to update via AJAX.
   */
  Drupal.behaviors.gsbConnectingLinks = {
    attach: function (context, settings) {
      window.setTimeout(function () {
        window.location = Drupal.settings.gsb_connecting_link.destination;
      }, settings.gsb_connecting_link.redirect_delay * 1000);
    }
  };

})(jQuery);
