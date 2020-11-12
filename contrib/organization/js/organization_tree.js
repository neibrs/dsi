// Organization tree
(function ($, Drupal) {
  Drupal.behaviors.organization_tree= {
    attach: function attach(context, settings) {
      $('#organization_tree').once('organization_tree').jstree();
      $('#organization_tree').once('organization_tree_change').on("changed.jstree", function (e, data) {
        var $href = data.node.a_attr.href;
        window.open($href, '_self');
      });
    }
  };
  // $(document).ready(function () {
  //   $('#organization_tree li a[href="#"]').parent().remove();
  // });
})(jQuery, Drupal);
