/**
 * @file
 * @todo.
 */

(function ($) {

  "use strict";

  /**
   * Redirects to a given destination after a delay.
   */
  Drupal.behaviors.gsbConnectingLinks = {
    attach: function (context, settings) {
      window.setTimeout(function () {
        window.location = Drupal.settings.gsb_connecting_link.destination;
      }, settings.gsb_connecting_link.redirect_delay * 1000);
    }
  };

})(jQuery);
