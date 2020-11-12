(function ($, Drupal, drupalSettings) {
  Drupal.filtersForm = {};

  Drupal.behaviors.filtersForm = {
    attach: function attach(context) {
      var $context = $(context);
      var $form = $context;
      if (!$context.is('form[id^="views-fields-override-form"]')) {
        $form = $context.find('form[id^="views-fields-override-form"]');
      }

      $(context).find('#field_option_cancel').once().each(function () {
        $(this).click(function () {
          $('#field_option_content').addClass('hidden');
          $('#override-wrapper').removeClass('hidden');
        
          return false;
        });
      });
    
      Drupal.theme('delete_field', context);

      // 字段搜索和过滤.
      if ($form.once('views-ui-filter-options').length) {
        new Drupal.filtersForm.OptionsSearch($form);
      }
    }
  };
  
  Drupal.theme.add_field = function (field_options) {
    var nameField = 'fields[' + field_options.id + '][options]';
    
    var $row = $('<tr class="draggable">');
    var tabledrap = $('<td>');
    tabledrap.append($('<a href="#" class="tabledrag-handle" title="拖放重新排序">').append($('<div class="handle">').html('&nbsp;')))
      .append($('<div class="js-form-item form-item js-form-type-item form-type-item form-no-label" >').text(field_options.label));
    $row.append(tabledrap)
        .append($('<td>').append($('<a href field_item_delete="delete" > ').text('删除')))
        .append($('<td>').append($('<input type="hidden" name="' + nameField + '"/>').val(JSON.stringify(field_options))));
    return $row;
  };
  
  Drupal.theme.delete_field = function (context) {
    
    $(context).find('[field_item_delete]').once('delete').each(function () {
      $(this).on('click', function () {
        $(this).parent().parent().remove();
        return false;
      });
    });
    
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
