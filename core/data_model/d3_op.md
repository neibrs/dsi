#D3操作入门
##Drupal模块引入D3js
Drupal模块的`libraries.yml`文件中，依赖于 `charts_c3/d3`,这样可以在模块的JS文件中就进行D3相应的操作。
### Drupal的JS结构
```
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.data_model = {
    attach: function attach(context, settings) {
      // $(context).find('#data-model').once('data-model')......//自定义结构代码,以后D3的代码就可以放在这里面了。
    }
  };
})(jQuery, Drupal, drupalSettings);
```
### D3基础操作指南
* ForceLayout布局的基础结构(基础要素)
  * SVG画布
  * 图形(圆圈或其他形状)
  * 连接线
  * 节点文本
  * 动态刷新函数
* 常规Forcelayout数组结构如下:
```

```

  
说明: 
* 一般在ForceLayout数据结构包含`source`,`target`基础键值，其他字段根据实际需要进行扩展。
* 也可以直接把数据里面的第一个元素当源，第二个或第N个当目标，具体控制数据的转换写法如下:

```
links.forEach(function(link) {
  link.source = nodes[link.source] || (nodes[link.source] = {name: link.source});
  link.target = nodes[link.target] || (nodes[link.target] = {name: link.target});
});
```
  
画布

```
var links = items;
var nodes = {};
var colors=d3.scale.category20(); //选择20种颜色样式用来随机填充圆圈里面的颜色。
//重组D3结构里面连接线与节点的数组
links.forEach(function(link) {
  link.source = nodes[link.source] || (nodes[link.source] = {name: link.source});
  link.target = nodes[link.target] || (nodes[link.target] = {name: link.target});
});

var force = d3.layout.force()//layout将json格式转化为力学图可用的格式
  .nodes(d3.values(nodes))//设定节点数组
  .links(links)//设定连线数组
  .size([width, height])//作用域的大小
  .linkDistance(180)//连接线长度
  .charge(-1000)//顶点的电荷数。该参数决定是排斥还是吸引，数值越小越互相排斥
  .on("tick", tick)//指时间间隔，隔一段时间刷新一次画面
  .start();//开始转换
  
var width = 900,
  height = 800;  
// 选择ForceLayout生成图的位置及大小
var svg = d3.select("#data-model").append("svg")
  .attr("width", width)
  .attr("height", height);  
```

图形

```
//圆圈
var circle = svg.append("g").selectAll("circle")
  .data(force.nodes())//表示使用force.nodes数据
  .enter().append("circle")
  .attr("r", 28);//设置圆圈半径;
  .call(force.drag);//将当前选中的元素传到drag函数中，使顶点可以被拖动
```


连接线

```
//设置连接线
var edges_line = svg.selectAll(".edgepath")
  .data(force.links())
  .enter()
  .append("path")
  .style("pointer-events", "none")
  .style("stroke-width",0.5)//线条粗细
  .attr("marker-end", "url(#resolved)" );//根据箭头标记的id号标记箭头
```
  * 若需要连接线带有箭头，则需要设置以下代码(这段代码放在连接线代码之前即可，无固定位置。)
```
//箭头
var marker= svg.append("marker")
  //.attr("id", function(d) { return d; })
  .attr("id", "resolved")
  .attr("markerUnits","strokeWidth")//设置为strokeWidth箭头会随着线的粗细发生变化
  .attr("markerUnits","userSpaceOnUse")
  .attr("viewBox", "0 -5 18 18")//坐标系的区域
  .attr("refX",8)//箭头坐标
  .attr("refY", -1)
  .attr("markerWidth", 12)//标识的大小
  .attr("markerHeight", 12)
  .attr("orient", "auto")//绘制方向，可设定为：auto（自动确认方向）和 角度值
  .attr("stroke-width",2)//箭头宽度
  .append("path")
  .attr("d", function(d) {
    return "M0,-5L10,0L0,5";
    // return "M0,-5" + "L"+ d.source.x + "," + d.source.y + "L"+d.target.x +","+d.target.y;
  })//箭头的路径
  .attr('fill','#000');//箭头颜色
```

节点文本
```
var text = svg
  .append("g")
  .selectAll("text")
  .data(force.nodes())
  //返回缺失元素的占位对象（placeholder），指向绑定的数据中比选定元素集多出的一部分元素。
  .enter()
  .append("text")
  .attr("dy", ".35em")
  .attr("text-anchor", "middle")//在圆圈中加上数据
;
```

动态刷新函数
```
function tick() {
  circle.attr("transform", transform1);//圆圈
  text.attr("transform", transform1);//顶点文字
}

//设置圆圈和文字的坐标
function transform1(d) {
  return "translate(" + d.x + "," + d.y + ")";
}
```