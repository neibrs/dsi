(function ($, Drupal, drupalSettings, d3) {

  Drupal.behaviors.d3Scatter = {
    attach: function attach(context, settings) {
      $(context).find('.d3-scatter').once('d3-scatter').each(Drupal.d3Scatter);
    }
  };

  Drupal.d3Scatter = function() {
    var id = $(this).attr('id');
    var data = drupalSettings['d3-scatter'][id];

    var margin = {top: 20, right: 20, bottom: 30, left: 40};
    // scrollbar width is 11.24
    var width = $(this).width() - margin.left - margin.right - 6;
    var height = $(this).height();
    if (height < 480) {
      height = 480;
    }
    height = height - margin.top - margin.bottom;

    var svg = d3.select('#' + id).append('svg')
      .attr('class', 'd3-chart')
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
      .append('g')
      .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

    var xScale = d3.scale.linear().range([0, width]);
    var xAxis = d3.svg.axis().scale(xScale).orient('bottom');
    var yScale = d3.scale.linear().range([height, 0]);
    var yAxis = d3.svg.axis().scale(yScale).orient('left');
    xScale.domain(d3.extent(data.columns, function(d) { return d.x; }));
    yScale.domain([0, d3.max(d3.extent(data.columns, function(d) { return d.y; }))]);

    var rScale = d3.scale.linear(). range([5, 20]);
    rScale.domain(d3.extent(data.columns, function(d) { return d.weight; })).nice();

    var colorScale = d3.scale.category10();

    svg.append('g')
      .attr('class', 'axis')
      .attr('transform', 'translate(0,' + height + ')')
      .call(xAxis)
      .append('text')
      .attr('class', 'label')
      .attr('x', width)
      .attr('y', -6)
      .style('text-anchor', 'end')
      .text(data.axis[0]);
    svg.append('g')
      .attr('class', 'axis')
      .call(yAxis)
      .append('text')
      .attr('class', 'label')
      .attr('transform', 'rotate(-90)')
      .attr('y', 6)
      .attr('dy', '.71em')
      .style('text-anchor', 'end')
      .text(data.axis[1]);

    svg.selectAll('.point')
      .data(data.columns)
      .enter()
      .append('circle')
      .attr('class', 'point')
      .attr('cx', function(d) {
        return xScale(d.x);
      })
      .attr('cy', function(d) {
        return yScale(d.y);
      })
      .attr('r', function(d) {
        return rScale(d.weight);
      })
      .attr('fill', function(d) {
        return colorScale(d.weight);
      });
  };

})(jQuery, Drupal, drupalSettings, d3);