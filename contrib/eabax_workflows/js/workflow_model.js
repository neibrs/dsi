(function ($, Drupal, drupalSettings) {
  var nodes = [];
  var links = [];
  function setupGraph(data) {
    nodes = data['type_settings']['states'];
    for (var state_id in nodes) {
      nodes[state_id].id = state_id;
      nodes[state_id].type = 'state';
    }
    for (var transition_id in data.type_settings.transitions) {
      var transition = data.type_settings.transitions[transition_id];
      transition.id = transition_id;
      var to_state = transition.to;
      for (var i in transition.from) {
        var from_state = transition.from[i];
        transition.type = 'action';
        nodes[from_state + "_" + to_state] = transition;
  
        links[transition.from[i] + '_' + transition_id] = {'source': nodes[from_state], 'target': transition};
        links[transition_id + '_' + to_state] = {'source': transition, 'target': nodes[to_state]};
      }
    }
  }

  function generateForceLayoutJsonDirectedGraph(workflow_id) {
    var width = $('.workflow-model').width(),
      height = 400;

    var svg = d3.select(".workflow-model")
      .append("svg")
      .attr("width",width)
      .attr("height",height);

    var force = d3.layout.force()//layout将json格式转化为力学图可用的格式
      .nodes(d3.values(nodes))//设定节点数组
      .links(d3.values(links))//设定连线数组
      .size([width, height])//作用域的大小
      .linkDistance( function () {
        var distance = 90;
        distance += distance * Math.random() * 0.5;
        return distance;
      })//连接线长度
      .charge(-500); //顶点的电荷数。该参数决定是排斥还是吸引，数值越小越互相排斥

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
        if (d === 'bundle-marker') {
          return '#d88';
        }
        return '#aaa';
      });
    var svg_edges = svg.selectAll("line")
      .data(force.links())
      .enter()
      .append("line")
      .style("stroke", function (d) {
        if (d.type === 'action') {
          return '#faa'
        }
        return "#ccc";
      })
      .attr("marker-end", function (d) {
        if (d.type === 'action') {
          // return 'url(#bundle-marker)';
          return "rgb(214,39,40)";
        }
        return "url(#normal-marker)";
      });
    var svg_nodes = svg.selectAll("circle")
      .data(force.nodes())
      .enter()
      .append("circle")
      .attr("r",function (d) {
        if (d.type === 'action') {
          return 10;
        }
        return 8;
      })
      .style("fill",function(d){
        if (d.type === 'action') {
          return "rgb(31, 119, 180)";
        }
        return 'rgb(174, 199, 232)';
      })
      .call(force.drag);	//使得节点能够拖动

    // TODO append "text" tag instead of "a" tag if d.links.collection does not exists.
    var svg_texts = svg.selectAll("a")
      .data(force.nodes())
      .enter()
      .append("a")
      .attr("href", function (d) {
        if (d.type === 'action') {
          return Drupal.url("admin/config/workflow/workflows/manage/" + workflow_id + "/transition/" + d.id );
        }
        else if (d.type === 'state') {
          return Drupal.url("admin/config/workflow/workflows/manage/" + workflow_id + "/state/" + d.id );
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
  Drupal.behaviors.worlflowModel = {
    attach: function attach(context, settings) {
      $(context).find('.workflow-model').once('workflow-model').each(function () {
        var workflow_id = $(this).attr('data-workflow-id');

        $.ajax({
          async: false,
          type: 'GET',
          url: Drupal.url('entity/workflow/' + workflow_id + '?_format=json'),
          dataType: 'json',
          success: function (data) {
            setupGraph(data);
          }
        });
        generateForceLayoutJsonDirectedGraph(workflow_id);
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
