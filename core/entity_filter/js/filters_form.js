(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.filtersForm = {
    attach: function attach(context) {
      $(context).find('.menu-item-folder > span').once().each(function () {
        var $menu_item = $(this).parent();
        if ($menu_item.hasClass('menu-item--expanded')) {
          $menu_item.removeClass('menu-item--expanded');
          $menu_item.addClass('menu-item--collapsed');
        }
      });
      var theme = $('#predefined_filters_item').attr('data-theme');
      $(context).find('#predefined-filters [data-id] span').once('predefined-filters').each(function () {
        $(this).on('click', function (e) {

          var dataId = $(this).parent().attr('data-id');
          if ($('#filters tr[data-id="' + dataId + '"]').length === 0) {
            if ($('#predefined_filters_item tbody').length === 0) {
              $('#predefined_filters_item').append('<tbody />');
            }
            $('#predefined_filters_item tbody').append(Drupal.theme(theme, dataId));
            Drupal.attachBehaviors($('#predefined_filters_item tbody'));
          }
        });
      });

      $(context).find('.fa-remove').once('delete').each(function () {
        $(this).on('click', function (e) {
          $(this).parent().parent().remove();
        });
      });

      //buildCustomConditionBuilder
      $(context).find('#add_custom_condition').once().each(function () {
        $(this).on('click', function () {
          $('#custom_condition_step2').toggle();

          return false;
        });
      });

      // Submit the filter options form.
      $(context).find('#custom_condition_step3_save').once().each(function () {
        $(this).click(function () {
          var plugin_id = $('div[data-plugin-id]').attr('data-plugin-id');

          // Get operator from input.
          var operator;
          if ($('select[name="options[operator]"]').length) {
            operator = $('select[name="options[operator]"]').val();
          }
          else {
            operator = $('input[name="options[operator]"]:checked').val();
          }

          // Get value from input.
          var value;
          var value_label = null;
          switch (plugin_id) {
            case 'date':
            case 'datetime':
              value = {};
              value.type = $('input[name="options[value][type]"]:checked').val();
              value.value = $('input[name="options[value][value]"]').val();
              value_label = value.value;
              break;
            case 'bundle':
            case 'in_operator':
            case 'entity_status':
            case 'entity_reference_in_operator':
            case 'language':
              value = {};
              $.each($('input[name^="options[value]"]:checked'),function(){
                // Get value.
                var val = $(this).val();
                if (val != null) {
                  value[val] = val;
                }
                // Get value label.
                var id = $(this).attr('id');
                value_label = (value_label != null ? value_label + ',' : '') + $('label[for="' + id + '"]').text();
              });
              // Get person value and label.
              if ($.isEmptyObject(value)) {
                value_label = $('input[name="options[value]"]').val();
                var arr = value_label.split(",");
                for (var i = 0; i < arr.length; i++) {
                  var name = arr[i].substring(0, arr[i].length-1);
                  arr[i] = name.substr(name.lastIndexOf('(') + 1);
                }
                value = arr;
              }
              break;
            case 'boolean':
            case 'current_person':
              var $radio = $('input[name="options[value]"]:checked');
              value = $radio.val();
              var id = $radio.attr('id');
              value_label = $('label[for="' + id + '"]').text();
              break;
            case 'equality':
            case 'has_qualification':
              value = {};
              value.qualification_category = $('input[name="options[qualification_category]"]:checked').val();
              value.value = $('input[name="options[value]"]').val();
              break;
            case 'many_to_one':
            case 'list_field':
              value = $('select[name="options[value][]"]').val();
              break;
            case 'numeric':
            case 'person_age':
            case 'expiration_days':
            case 'month':
              value = $('input[name="options[value][value]"]').val();
              // TODO: min max
              break;
            default:
              value = $('input[name="options[value]"]').val();
              break;
          }

          var plugin_options = drupalSettings.custom_conditions.plugin_options;
          var admin_label = plugin_options.field_title + ':' + (value_label != null ? value_label : value);
          var filter = {
            'table': plugin_options.table,
            'field': plugin_options.field,
            'admin_label': admin_label,
            'plugin_id': plugin_options.plugin_id,
            'operator': operator,
            'value': value,
          };

          var $relationship = $('select[name="options[relationship]"]');

          var relationship = $relationship.val();
          if (relationship !== undefined) {
            filter.relationship = relationship;
            var relationship_title = $relationship.children('option[value="' + relationship + '"]').text();
            filter.admin_label = relationship_title + ':' + filter.admin_label;
          }

          if (operator !== undefined || value != null) {
            var dataId = $('#custom_condition_step3_title').text();
            if ($.isPlainObject(value)) {
              $.each(value, function (i) {
                dataId += value[i];
              });
            }
            else {
              dataId += value;
            }
            if ($('#filters tr[data-id="' + dataId + '"]').length === 0) {
              // Append the tbody html element if it does not exists.
              if ($('#predefined_filters_item tbody').length === 0) {
                $('#predefined_filters_item').append('<tbody />');
              }

              $('#predefined_filters_item tbody').append(Drupal.theme('add_filter', dataId, filter));
              Drupal.attachBehaviors($('#predefined_filters_item tbody'));
            }
          }

          $('#custom_condition_step1').show();
          $('#custom_condition_step2').show();
          $('#custom_condition_step3').hide();

          return false;
        });
      });

      $(context).find('#custom_condition_step3_return').once().each(function () {
        $(this).click(function () {
          $('#custom_condition_step1').show();
          $('#custom_condition_step2').show();
          $('#custom_condition_step3').hide();

          return false;
        });
      });

      $(context).find('#custom_condition_step3_cancel').once().each(function () {
        $(this).click(function () {
          $('#custom_condition_step3').hide();
          $('#custom_condition_step2').hide();
          $('#custom_condition_step1').show();

          return false;
        });
      });

    }
  };

  Drupal.theme.entity_filter = function (dataId) {
    var filter = drupalSettings.predefinedFilters[dataId];

    return Drupal.theme('add_filter', dataId, filter);
  };

  Drupal.theme.add_filter = function (dataId, filter) {
    var $row = $('<tr data-id="' + dataId + '">');
    var namePrefix = 'filters[' + dataId + ']';
    $row.append($('<td>').text(filter.admin_label))
        .append($('<td>').append($('<i class="fa fa-remove" />')))
        .append($('<td>').append($('<input type="hidden" name="' + namePrefix + '[filter]" />').val(JSON.stringify(filter))));

    return $row;
  };

})(jQuery, Drupal, drupalSettings);
