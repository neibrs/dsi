(function ($, Drupal, drupalSettings) {
  Drupal.filtersForm = {};

  Drupal.behaviors.filtersForm = {
    attach: function attach(context) {
      var $context = $(context);
      var $form = $context;
      if (!$context.is('form[id^="filters-form"]')) {
        $form = $context.find('form[id^="filters-form"]');
      }

      $(context).find('.menu-item-folder > span').once().each(function () {
        var $menu_item = $(this).parent();
        if ($menu_item.hasClass('menu-item--expanded')) {
          $menu_item.removeClass('menu-item--expanded');
          $menu_item.addClass('menu-item--collapsed');
        }
      });
      var theme = $('#filter-items').attr('data-theme');
      $(context).find('#predefined-filters [data-id] span').once('predefined-filters').each(function () {
        $(this).on('click', function (e) {

          var dataId = $(this).parent().attr('data-id');
          if ($('#filter-items tbody').length === 0) {
            $('#filter-items').append('<tbody />');
          }
          $('#filter-items tbody').append(Drupal.theme(theme, dataId));
          Drupal.attachBehaviors($('#filter-items tbody'));
        });
      });

      $(context).find('.filter-delete').once('delete').each(function () {
        $(this).on('click', function (e) {
          $(this).parent().parent().parent().remove();
        });
      });

      //buildCustomConditionBuilder
      $(context).find('#add_custom_condition').once().each(function () {
        $(this).click(function () {
          if ($('#custom_condition_step2').hasClass('hidden')) {
            $('#custom_condition_step2').removeClass('hidden');
          }
          else {
            $('#custom_condition_step2').addClass('hidden');
          }
      
          return false;
        });
      });

      $(context).find('#custom_condition_step3_return').once().each(function () {
        $(this).click(function () {
          $('#custom_condition_step1').removeClass('hidden');
          $('#custom_condition_step2').removeClass('hidden');
          $('#custom_condition_step3').addClass('hidden');

          return false;
        });
      });

      $(context).find('#custom_condition_step3_cancel').once().each(function () {
        $(this).click(function () {
          $('#custom_condition_step3').addClass('hidden');
          $('#custom_condition_step2').addClass('hidden');
          $('#custom_condition_step1').removeClass('hidden');

          return false;
        });
      });
      
      // Filter config select all.
      //@see core/views_ui/js/views-admin.js:509
      $(context).find('.js-form-item-options-value-all').once('filterConfigSelectAll').each(function () {
        var $selectAllCheckbox = $(this).find('input[type=checkbox]');
        var $checkboxes = $(this).closest('.form-checkboxes').find('.js-form-type-checkbox:not(.js-form-item-options-value-all) input[type="checkbox"]');
  
        $selectAllCheckbox.on('click', function () {
          $checkboxes.prop('checked', $(this).is(':checked'));
        });
  
        $checkboxes.on('click', function () {
          if ($(this).is('checked') === false) {
            $selectAllCheckbox.prop('checked', false);
          }
        });
      });

      // 字段搜索和过滤.
      if ($form.once('views-ui-filter-options').length) {
        new Drupal.filtersForm.OptionsSearch($form);
      }
    }
  };

  Drupal.theme.entity_filter = function (dataId) {
    var filter = drupalSettings.predefinedFilters[dataId];

    return Drupal.theme('add_filter', filter);
  };

  Drupal.theme.add_filter = function (filter) {
    var id = filter.id;
    var namePrefix = 'filters[' + id + ']';
    while ($('input[name="' + namePrefix + '[filter]"]').length) {
      id = id + '_' + Math.round(Math.random()*1000);
      namePrefix = 'filters[' + id + ']';
    }
    filter.id = id;
    
    var update_url = '/entity_filter/handler_config/person_field_data/filter/' + JSON.stringify(filter);
    var operations = $('<div class="links-dropbutton-operations js-form-wrapper form-wrapper">');
    operations.append($('<a class="title use-ajax">').attr('href', update_url).text('编辑'))
              .append($('<a class="filter-delete text-red">').text('删除'));
    
    var $row = $('<tr>');
    $row.append($('<td>').text(filter.admin_label))
        .append($('<td>').append(operations))
        .append($('<td>').append($('<input type="hidden" name="' + namePrefix + '[filter]" />').val(JSON.stringify(filter))));

    return $row;
  };

  Drupal.filtersForm.OptionsSearch = function ($form) {
    this.$form = $form;

    var searchBoxSelector = '[data-drupal-selector="edit-options-search"]';
    var controlGroupSelector = '[data-drupal-selector="edit-group"]';
    this.$form.on('formUpdated', searchBoxSelector + ',' + controlGroupSelector, $.proxy(this.handleFilter, this));

    this.$searchBox = this.$form.find(searchBoxSelector);
    this.$controlGroup = this.$form.find(controlGroupSelector);

    this.options = this.getOptions(this.$form.find('.filterable-option'));

    this.$searchBox.on('keypress', function (event) {
      if (event.which === 13) {
        event.preventDefault();
      }
    });
  };

  $.extend(Drupal.filtersForm.OptionsSearch.prototype, {
    getOptions: function getOptions($allOptions) {
      var $title = void 0;
      var $option = void 0;
      var options = [];
      var length = $allOptions.length;
      for (var i = 0; i < length; i++) {
        $option = $($allOptions[i]);
        $title = $option.find('.title');
        options[i] = {
          searchText: $title.text().toLowerCase(),

          $div: $option
        };
      }
      return options;
    },
    handleFilter: function handleFilter(event) {
      var search = this.$searchBox.val().toLowerCase();
      var words = search.split(' ');

      var group = this.$controlGroup.val();

      this.options.forEach(function (option) {
        function hasWord(word) {
          return option.searchText.indexOf(word) !== -1;
        }

        var found = true;

        if (search) {
          found = words.every(hasWord);
        }
        if (found && group !== 'all') {
          found = option.$div.hasClass(group);
        }

        option.$div.toggle(found);
      });
    }
  });

})(jQuery, Drupal, drupalSettings);
