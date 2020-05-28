(function ($, Drupal, drupalSettings) {

  Drupal.theme.report_filters_form = function (dataId) {
    var filter = drupalSettings.predefinedFilters[dataId];

    return Drupal.theme('add_filter', dataId, filter);
  };

  Drupal.theme.add_filter = function (dataId, filter) {
    var $row = $('<tr data-id="' + dataId + '">');
    var namePrefix = 'filters[' + dataId + ']';
    $row.append($('<td>').append($('<input name="' + namePrefix + '[admin_label]" />').val(filter.admin_label)))
        .append($('<td>').append($('<i class="fa fa-remove" />')))
        .append($('<td>').append($('<input type="hidden" name="' + namePrefix + '[filter]" />').val(JSON.stringify(filter))));

    return $row;
  };
})(jQuery, Drupal, drupalSettings);
