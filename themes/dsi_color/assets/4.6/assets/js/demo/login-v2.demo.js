/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
Version: 4.4.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin/admin/
*/

(function ($, Drupal) {
var handleLoginPageChangeBackground = function() {
	$(document).on('click', '[data-click="change-bg"]', function(e) {
		e.preventDefault();
		var targetImage = '[data-id="login-cover-image"]';
		var targetImageSrc = 'url(' + $(this).attr('data-img') +')';

		$(targetImage).css('background-image', targetImageSrc);
		$('[data-click="change-bg"]').closest('li').removeClass('active');
		$(this).closest('li').addClass('active');
	});
};

var LoginV2 = function () {
	"use strict";
	return {
		//main function
		init: function () {
			handleLoginPageChangeBackground();
		}
	};
}();

Drupal.behaviors.table_manage_default = {
  attach: function attach(context) {
    $(document).once('document_login_v2').ready(function () {
      LoginV2.init();
    });
  }
};
})(jQuery, Drupal);
