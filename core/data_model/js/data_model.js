(function ($, Drupal, drupalSettings) {
  var nodes = [];
  var links = [];

  function fetchEntityType(entity_type_name, node_type) {
    $.ajax({
      async: false,
      type: 'GET',
      url: Drupal.url('entity_type/' + entity_type_name) + '?_format=json',
      dataType: 'json',
      success: function (data) {
        addEntityType(data, node_type);
      }
    });
  }

  function addEntityType(entity_type, node_type) {
    var source = entity_type.id;
    nodes[source] = entity_type;

    if (typeof node_type === 'undefined') {
      if (entity_type.bundle_of != null) {
        if (nodes[entity_type.bundle_of].node_type == 'master') {
          node_type = 'master_bundle';
        }
        else {
          node_type = 'bundle';
        }
      }
    }
    nodes[source].node_type = node_type;

    for (var name in entity_type.base_fields) {
      var field = entity_type.base_fields[name];
      if (field.type == "entity_reference") {
        var target = field.settings.target_type;
        if (typeof nodes[target] === 'undefined') {
          fetchEntityType(target);
        }
        var key = target + "_" + source;
        if (typeof links[key] !== 'undefined') {
          links[key].bidirectional = true;
        }
        else {
          key = source + "_" + target;
          links[key] = {'source': nodes[source], 'target': nodes[target]};
          if (nodes[target].bundle_of == source) {
            links[key].link_type = 'bundle';
          }
        }
      }
    }
  }

  function generateForceLayoutJsonDirectedGraph() {
    var width = $('#data-model').width(),
        height = 400;

    var svg = d3.select("#data-model")
        .append("svg")
        .attr("width",width)
        .attr("height",height);

    var force = d3.layout.force()//layout将json格式转化为力学图可用的格式
        .nodes(d3.values(nodes))//设定节点数组
        .links(d3.values(links))//设定连线数组
        .size([width, height])//作用域的大小
        .linkDistance(60)//连接线长度
        .charge(-1000); //顶点的电荷数。该参数决定是排斥还是吸引，数值越小越互相排斥

    force.start();//开始转换

    var marker= svg.selectAll("marker")
      .data(['normal-marker', 'bundle-marker'])
      .enter()
      .append('marker')
      .attr("id", function(d) {
        return d;
      })
      .attr("viewBox", "0 -5 10 10")//坐标系的区域
      .attr("refX",20)//箭头坐标
      .attr("markerWidth", 8)//标识的大小
      .attr("markerHeight", 8)
      .attr("orient", "auto")//绘制方向，可设定为：auto（自动确认方向）和 角度值
      .append("path")
      .attr("d", "M0,-5L10,0L0,5")//箭头的路径
      .attr('fill', function (d) {
        if (d == 'bundle-marker') {
          return '#d88';
        }
        return '#aaa';
      });

    var svg_edges = svg.selectAll("line")
      .data(force.links())
      .enter()
      .append("line")
      .style("stroke", function (d) {
        if (d.link_type == 'bundle') {
          return '#faa'
        }
        return "#ccc";
      })
      .attr("marker-end", function (d) {
        if (d.link_type == 'bundle') {
          return 'url(#bundle-marker)';
        }
        return "url(#normal-marker)";
      })
      .attr("marker-start", function (d) {
        if (d.bidirectional == true) {
          return "url(#normal-marker)";
        }
        else {
          return "none";
        }
      });

    var svg_nodes = svg.selectAll("circle")
        .data(force.nodes())
        .enter()
        .append("circle")
        .attr("r",8)
        .style("fill",function(d,i){
          if (d.node_type == 'master') {
            return "rgb(214,39,40)";
          }
          else if (d.node_type == 'master_bundle') {
            return 'rgb(255, 127, 14)';
          }
          else if (d.node_type == 'bundle') {
            return 'rgb(174, 199, 232)';
          }
          return 'rgb(31, 119, 180)';
        })
        .call(force.drag);	//使得节点能够拖动

    // TODO append "text" tag instead of "a" tag if d.links.collection does not exists.
    var svg_texts = svg.selectAll("a")
      .data(force.nodes())
      .enter()
      .append("a")
      .attr("xlink:href", function(d) {
        if (typeof d.links.collection === 'undefined') {
          return '#';
        }
        else {
          return Drupal.url(d.links.collection.substr(1));
        }
      })
      .append("text")
      .style("fill", "#333")
      .attr("dx", 12)
      .attr("dy", 4)
      .text(function(d){
        return d.label;
      });

    force.on("tick", function(){
      svg_edges.attr("x1",function(d){ return d.source.x; })
        .attr("y1",function(d){ return d.source.y; })
        .attr("x2",function(d){ return d.target.x; })
        .attr("y2",function(d){ return d.target.y; });
      svg_nodes.attr("cx",function(d){ return d.x; })
        .attr("cy",function(d){ return d.y; });
      svg_texts.attr("x", function(d){ return d.x; })
        .attr("y", function(d){ return d.y; });
    });

  }

  Drupal.behaviors.data_model = {
    attach: function attach(context, settings) {
      $(context).find('#data-model').once('data-model').each(function () {
        var entity_types = drupalSettings.data_model.entity_types;
        for (var i = 0; i < entity_types.length; i++) {
          fetchEntityType(entity_types[i], 'master');
        }
        generateForceLayoutJsonDirectedGraph();
      });
    }
  }
 
})(jQuery, Drupal, drupalSettings);
