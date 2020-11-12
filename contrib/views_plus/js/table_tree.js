/**
 * @file
 * Attaches behaviors for Table tree.
 */
(function ($, Drupal) {
  Drupal.behaviors.tableTree = {
    attach: function attach(context, settings) {
      function fixLastRow($tr) {
        var $span = $tr.find('td.tree-column span.views-tree-icon');
        if ($span.hasClass('views-tree-icon-folder')) {
          $span.removeClass('views-tree-icon-folder');
          $span.addClass('views-tree-icon-last-folder');
        }
        else {
          $span.removeClass('views-tree-icon-leaf');
          $span.addClass('views-tree-icon-last-leaf');
        }
      }

      // Fix the last tr class
      $(context).find('table.tree-table > tbody > tr:last').once('tree-table-last-tr').each(function() {
        fixLastRow($(this));
      });

      function onChanageTree($v) {
        var $this = $($v);
        var $tr = $($v).parent().parent();
         var $tbody = $tr.parent();
         var entity_id = $tr.attr('entity-id');
         var level = $tr.attr('level');
         var parent_class = $tr.attr('class');
         var this_class = 'pa-cl-' + entity_id;
         if (typeof level == "undefined") {
           level = 0;
         }
         var $children = $tbody.find('tr[parent-id="' + entity_id + '"]');
         if ($this.hasClass('is-expanded')) {
           $this.removeClass('is-expanded');

           // hideChildren($this, $children, $tbody);
           $tbody.find('.pa-cl-' + entity_id + ' span').removeClass('is-expanded');
           $tbody.find('.pa-cl-' + entity_id).hide();
           Drupal.attachBehaviors($tbody, settings);
         }
         else {
           $this.addClass('is-expanded');

           if ($children.length > 0) {
             $children.each(function () {
               $(this).show();
             });
           }
           else {
             var $url = $this.attr('data-url');
             $.ajax({
               url: $url,
               async: true,
               type: 'GET',
               dateType: 'html',
               success: function success(data) {
                 var $result = $(data).find('table.tree-table > tbody > tr');
                 $result.attr('parent-id', entity_id);
                 $result.attr('level', parseInt(level) + 1);
                 $result.addClass(parent_class).addClass(this_class);

                 // Fix last tr class
                 fixLastRow($result.last())

                 // Copy spans from parent
                 var $spans = $tr.find('.tree-column > span').clone();
                 var $span = $spans.last();
                 if ($span.hasClass('views-tree-icon-last-folder') || $span.hasClass('views-tree-icon-last-leaf')) {
                   $span.removeClass('views-tree-icon-last-folder');
                   $span.removeClass('views-tree-icon-last-leaf');
                   $span.addClass('views-tree-icon-space');
                 }
                 else {
                   $span.removeClass('views-tree-icon-folder');
                   $span.removeClass('views-tree-icon-leaf');
                   $span.addClass('views-tree-icon-line');
                 }
                 $result.find('.tree-column').prepend($spans);

                 $tr.after($result);
                 Drupal.attachBehaviors($result, settings);
               },
             });
           }
         }
      }
      $(context).find('.views-tree-icon-folder').once('views-tree-icon-folder').on('click', function () {
        var $this = $(this);
        onChanageTree(this);
      });
      $(context).find('.views-tree-icon-last-folder').once('views-tree-icon-last-folder').on('click', function () {
        var $this = $(this);
        onChanageTree(this);
      });

    },
  };
})(jQuery, Drupal);
