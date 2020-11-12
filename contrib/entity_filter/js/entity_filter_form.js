(function ($, Drupal) {

  Drupal.theme.entity_filter_form = function (dataId) {
    var $row = Drupal.theme('entity_filter', dataId);

    var namePrefix = 'filters[' + dataId + ']';
    $row.prepend($('<td>').append($('<input type="checkbox">').attr('name', namePrefix + '[essential]')));

    return $row;
  };
})(jQuery, Drupal);
