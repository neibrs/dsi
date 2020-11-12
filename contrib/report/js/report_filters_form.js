(function ($, Drupal, drupalSettings) {
  
  Drupal.behaviors.report_filters_form = {
    attach: function attach(context) {
      var $context = $(context);
      var $form = $context;
      if (!$context.is('form[id^="filters-form"]')) {
        $form = $context.find('form[id^="filters-form"]');
      }
  
      $(context).find('#custom_condition_step4_cancel').once().each(function () {
        $(this).click(function () {
          $('#custom_condition_step4').addClass('hidden');
          $('#custom_condition_step3').addClass('hidden');
          $('#custom_condition_step2').addClass('hidden');
          $('#custom_condition_step1').removeClass('hidden');
      
          return false;
        });
      });
    }
  };

  Drupal.theme.report_filters_form = function (dataId) {
    var filter = drupalSettings.predefinedFilters[dataId];
  
    if ($('#filter-items tbody').length === 0) {
      $('#filter-items').append('<tbody />');
    }
    
    return $('#filter-items tbody').append(Drupal.theme('add_filter', filter));
  };

  Drupal.theme.add_filter = function (filter) {
    var id = filter.id;
    var namePrefix = 'filters[' + id + ']';
    while ($('input[name="' + namePrefix + '[filter]"]').length) {
      id = id + '_' + Math.round(Math.random()*1000);
      namePrefix = 'filters[' + id + ']';
    }
    filter.id = id;
  
    var admin_labels= [];
    admin_labels = filter.admin_label.split(":");
    var admin_label = $('<div class="js-form-item form-item js-form-type-item form-type-item form-no-label" >')
      .append($('<input class="form-text" type="text" name="' + namePrefix + '[admin_label][1]" />').val(admin_labels.pop()));
  
    var display = $('.tabledrag-hide').css('display');
    var tabledrag_handle = $('<a href="#" class="tabledrag-handle" title="拖放重新排序">').append($('<div class="handle">').html('&nbsp;'));
    if (display !== 'none') {
      tabledrag_handle.css('display', 'none');
    }
    var tabledrap = $('<td>');
    tabledrap.append(tabledrag_handle).append(admin_label);
    
    var filter_id = $('<td>');
    filter_id.append($('<input type="hidden" class="filter-id" name="' + namePrefix + '[id]">').val(id));
  
    var filter_parent = $('<td class="tabledrag-hide">');
    filter_parent.append($('<input type="hidden" class="filter-parent" name="' + namePrefix + '[parent]">'));
  
    var filter_weight = $('<td class="tabledrag-hide">');
    filter_weight.append($('<div class="js-form-item form-item js-form-type-item form-type-item form-no-label">')
      .append($('<input type="number" class="filter-wight" name="' + namePrefix + '[weight]">').val(0)));
  
    if (display === 'none') {
      filter_parent.css('display', display);
      filter_weight.css('display', display);
    }
  
    var update_url = '/entity_filter/handler_config/person_field_data/filter/' + JSON.stringify(filter);
    var operations = $('<div class="links-dropbutton-operations js-form-wrapper form-wrapper">');
    operations.append($('<a class="title use-ajax">').attr('href', update_url).text('编辑'))
      .append($('<a class="filter-delete text-red">').text('删除'));
    
    var $row = $('<tr class="draggable">');
    $row.append(tabledrap)
      .append(filter_id)
      .append(filter_parent)
      .append($('<td>').text(filter.admin_label))
      .append(filter_weight)
      .append($('<td>').append(operations))
      .append($('<td>').append($('<input type="hidden" name="' + namePrefix + '[filter]" />').val(JSON.stringify(filter))));
  
    return $row;
  };
})(jQuery, Drupal, drupalSettings);
