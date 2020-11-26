/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
Version: 4.4.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin/admin/
*/

(function ($, Drupal) {
var handleDataTableDefault = function() {
	"use strict";

	if ($(".views-view-table").length !== 0) {
		$(".views-view-table").DataTable({
			responsive: true
		});
	}
};

var TableManageDefault = function () {
	"use strict";
	return {
		//main function
		init: function () {
			handleDataTableDefault();
		}
	};
}();

Drupal.behaviors.table_manage_default = {
  attach: function attach(context) {
    $(document).once("document_table_manage_default").ready(function () {
      TableManageDefault.init();
    });
  }
};
})(jQuery, Drupal);
