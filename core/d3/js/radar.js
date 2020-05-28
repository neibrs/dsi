(function ($, Drupal, drupalSettings, RadarChart) {

  Drupal.behaviors.d3Radar = {
    attach: function attach(context, settings) {
      $(context).find('.d3-radar').once('d3-radar').each(Drupal.d3Radar);
    }
  };

  Drupal.d3Radar = function() {
    var id = $(this).attr('id');
    //var data = $(this).attr('chart-data');
    var data = drupalSettings['d3-radar'][id];
    // scrollbar width is 11.24
    var options = {
      'w': $(this).width() - 40 - 6,
      'h': 440
    };
    //RadarChart('#' + id, JSON.parse(data), options);
    RadarChart('#' + id, data, options);
  };

})(jQuery, Drupal, drupalSettings, RadarChart);
