(function ($, Drupal) {
  Drupal.behaviors.collapsibleTree = {
    attach: function attach(context, settings) {
      var $context = $(context);
      var data = settings.organizations;
      $context.find('#tree-container').once('d3-tree').each(function () {
        var $this = $(this);
        var orientation = $this.attr('data-orientation');
        var show_manager = $this.attr('data-show-manager');
        var show_holder_count = $this.attr('data-show-holder-count');
        var hierarchy_expansion = $this.attr('data-hierarchy-expansion');
        if (orientation === 'vertical') {
          vertical_chart();
        }
        else {
          horizontal_chart();
        }

        // https://observablehq.com/@koljada/collapsible-tree
        function vertical_chart() {
          var margin = ({top: 20, right: 10, bottom: 10, left: 10});
          var width = $(document).width();
          var dx = width / data.children.length;
          var dy = 60;
          var tree = d3.tree().nodeSize([dx, dy]);
          var diagonal = d3.linkVertical().x(d => d.x).y(d => d.y);

          const root = d3.hierarchy(data);

          root.x0 = dx / 2;
          root.y0 = 0;
          root.descendants().forEach((d, i) => {
            d.id = i;
            d._children = d.children;
            if (d.depth) d.children = null;
          });

          const svg = d3.select('#tree-container').append("svg")
              .style("width", "100%")
              .style("height", "600px")
              .style("font", "10px sans-serif")
              .style("user-select", "none");

          const g = svg.append("g")
              .attr("transform", `translate(${width / 2}, ${margin.top})`);

          const gLink = g.append("g")
              .attr("fill", "none")
              .attr("stroke", "#555")
              .attr("stroke-opacity", 0.4)
              .attr("stroke-width", 1.5);

          const gNode = g.append("g")
              .attr("cursor", "pointer")
              .attr("pointer-events", "all");

          const zoomBehaviours = d3.zoom()
              .scaleExtent([0.05, 3])
              .on('zoom', () => g.attr('transform', d3.event.transform));

          svg.call(zoomBehaviours);

          setTimeout(() => zoomBehaviours.translateTo(svg, 0, 0), 100);

          function update(source) {
            const duration = d3.event && d3.event.altKey ? 2500 : 250;
            const nodes = root.descendants().reverse();
            const links = root.links();

            // Compute the new tree layout.
            tree(root);

            const transition = svg.transition()
                .duration(duration)
                .tween("resize", window.ResizeObserver ? null : () => () => svg.dispatch("toggle"));

            // Update the nodes…
            const node = gNode.selectAll("g")
                .data(nodes, d => d.id);

            // Enter any new nodes at the parent's previous position.
            const nodeEnter = node.enter().append("g")
                .attr("transform", d => `translate(${source.x0},${source.y0})`)
                .attr("fill-opacity", 0)
                .attr("stroke-opacity", 0)
                .on("click", d => {
                  d.children = d.children ? null : d._children;
                  update(d);
                  if (d3.event && d3.event.altKey) {
                    setTimeout(() => {
                      zoomToFit();
                    }, duration + 100);
                    //zoomToFit();
                  }
                });

            nodeEnter.append("circle")
                .attr("r", 4)
                .attr("fill", d => d._children ? "#555" : "#999")
                .attr("stroke-width", 10);

            nodeEnter.append("text")
                .attr("dy", "0.31em")
                .attr("x", d => d._children ? -6 : 6)
                .attr("text-anchor", d => d._children ? "end" : "start")
                .text(d => d.data.name)
                .clone(true)
                .lower()
                .attr("stroke-linejoin", "round")
                .attr("stroke-width", 3)
                .attr("stroke", "white");

            // Transition nodes to their new position.
            const nodeUpdate = node.merge(nodeEnter).transition(transition)
                .attr("transform", d => `translate(${d.x},${d.y})`)
                .attr("fill-opacity", 1)
                .attr("stroke-opacity", 1);

            // Transition exiting nodes to the parent's new position.
            const nodeExit = node.exit().transition(transition).remove()
                .attr("transform", d => `translate(${source.x},${source.y})`)
                .attr("fill-opacity", 0)
                .attr("stroke-opacity", 0);

            // Update the links…
            const link = gLink.selectAll("path")
                .data(links, d => d.target.id);

            // Enter any new links at the parent's previous position.
            const linkEnter = link.enter().append("path")
                .attr("d", d => {
                  const o = {x: source.x0, y: source.y0};
                  return diagonal({source: o, target: o});
                });

            // Transition links to their new position.
            link.merge(linkEnter).transition(transition)
                .attr("d", diagonal);

            // Transition exiting nodes to the parent's new position.
            link.exit().transition(transition).remove()
                .attr("d", d => {
                  const o = {x: source.x, y: source.y};
                  return diagonal({source: o, target: o});
                });

            // Stash the old positions for transition.
            root.eachBefore(d => {
              d.x0 = d.x;
              d.y0 = d.y;
            });

            // svg 的高度
          }

          function zoomToFit(paddingPercent) {
            const bounds = g.node().getBBox();
            const parent = svg.node().parentElement;
            const fullWidth = parent.clientWidth;
            const fullHeight = parent.clientHeight;

            const width = bounds.width;
            const height = bounds.height;

            const midX = bounds.x + (width / 2);
            const midY = bounds.y + (height / 2);

            if (width === 0 || height === 0) {
              return;
            } // nothing to fit

            const scale = (paddingPercent || 0.75) / Math.max(width / fullWidth, height / fullHeight);
            const translate = [fullWidth / 2 - scale * midX, fullHeight / 2 - scale * midY];
            const transform = d3.zoomIdentity
                .translate(translate[0], translate[1])
                .scale(scale);

            svg
                .transition()
                .duration(500)
                .call(zoomBehaviours.transform, transform);
          }
  
          function buildTree(node, current_depth) {
            current_depth += 1;
    
            node.children = node.children ? null : node._children;
            update(node);
    
            var children = node.children ? node.children : node._children;
            if (current_depth < hierarchy_expansion && children) {
              $.each(children, function (index,child) {
                buildTree(child, current_depth);
              });
            }
          }
  
          // 根据展开层级加载组织结构图.
          if (hierarchy_expansion > 0) {
            update(root);
            var children = root.children ? root.children : root._children;
            if (hierarchy_expansion > 1 && children) {
              $.each(children, function (index, child) {
                buildTree(child, 1);
              });
            }
          }
          else {
            var node = root;
            node.children = null;
            update(node);
          }

          //setTimeout(() => { zoomToFit();}, 5000);
        }

        // https://observablehq.com/@d3/collapsible-tree
        function horizontal_chart() {
          var width = $(document).width() > 768 ? $(document).width()/1.5 : $(document).width();
          var margin = ({top: 20, right: 120, bottom: 20, left: width/6});
          var dy = width/6;
          var dx = 30;
          var tree = d3.tree().nodeSize([dx, dy]);
          var diagonal = d3.linkHorizontal().x(d => d.y).y(d => d.x);

          const root = d3.hierarchy(data);

          root.x0 = dy / 2;
          root.y0 = 0;
          root.descendants().forEach((d, i) => {
            d.id = i;
            d._children = d.children;
            if (d.depth && d.data.name.length !== 7) {
              d.children = null;
            }
          });

          var svg = d3.select("#tree-container").append("svg")
              .attr("viewBox", [-margin.left, -margin.top, width, dx])
              .style("font", "10px sans-serif")
              .style("user-select", "none");

          const gLink = svg.append("g")
              .attr("fill", "none")
              .attr("stroke", "#555")
              .attr("stroke-opacity", 0.4)
              .attr("stroke-width", 1.5);

          const gNode = svg.append("g")
              .attr("cursor", "pointer")
              .attr("pointer-events", "all");

          function update(source) {
            const duration = d3.event && d3.event.altKey ? 2500 : 250;
            const nodes = root.descendants().reverse();
            const links = root.links();

            // Compute the new tree layout.
            tree(root);

            let left = root;
            let right = root;
            root.eachBefore(node => {
              if (node.x < left.x) left = node;
              if (node.x > right.x) right = node;
            });

            var height = right.x - left.x + margin.top + margin.bottom;

            // Update the nodes…
            const node = gNode.selectAll("g")
                .data(nodes, d => d.id);

            // Enter any new nodes at the parent's previous position.
            const nodeEnter = node.enter().append("g")
                .attr("transform", d => `translate(${source.y0},${source.x0})`)
                .attr("fill-opacity", 0)
                .attr("stroke-opacity", 0)
                .on("click", d => {
                  d.children = d.children ? null : d._children;
                  update(d);
                });

            nodeEnter.append("circle")
                .attr("r", 2.5)
                .attr("fill", d => d._children ? "#001f3f" : "#00c0ef")
                .attr("stroke-width", 10);

            nodeEnter.append("rect")
                .attr("x", d => d._children ? 16-dy : 4)
                .attr("y", "-10")
                .attr("rx", 4)
                .attr("ry", 4)
                .attr("width", dy - 20)
                .attr("height", function (d) {
                  if (show_manager === '1' || show_holder_count === '1') {
                    tree = d3.tree().nodeSize([dx + 10, dy]);
                    height = (right.x - left.x + margin.top + margin.bottom) * 2;
                    return 30;
                  }
                  else {
                    return 20;
                  }
                })
                .style("fill", "#f5f5f5")
                .style("stroke", "#00c0ef")
                .style("opacity", "0.7");

            tree(root); //重新计算布局

            const transition = svg.transition()
                .duration(duration)
                .attr("viewBox", [-margin.left, left.x - margin.top, width, height])
                .tween("resize", window.ResizeObserver ? null : () => () => svg.dispatch("toggle"));

            nodeEnter.append("text")
                .attr("dy", "0.31em")
                .attr("x", d => d._children ? -6 : 6)
                .attr("text-anchor", d => d._children ? "end" : "start")
                .text(d => d.data.name)
                .clone(true).lower()
                .attr("stroke-linejoin", "round")
                .attr("stroke-width", 3);

            //显示manager
            if (show_manager === '1') {
              nodeEnter.append("text")
                .attr("dy", "1.7em")
                .attr("x", d => d._children ? 18-dy : 8)
                .attr("text-anchor", "start")
                .style("fill", "#0073b7")
                .text(d => d.data.manager);
            }

            //显示holder_count
            if (show_holder_count === '1') {
              nodeEnter.append("text")
                .attr("dy", "1.7em")
                .attr("x", d => d._children ? -6 : dy-18)
                .attr("text-anchor", "end")
                .style("fill", "#3c8dbc")
                .text(d => d.data.holder_count);
            }

            // Transition nodes to their new position.
            const nodeUpdate = node.merge(nodeEnter).transition(transition)
                .attr("transform", d => `translate(${d.y},${d.x})`)
                .attr("fill-opacity", 1)
                .attr("stroke-opacity", 1);

            // Transition exiting nodes to the parent's new position.
            const nodeExit = node.exit().transition(transition).remove()
                .attr("transform", d => `translate(${source.y},${source.x})`)
                .attr("fill-opacity", 0)
                .attr("stroke-opacity", 0);

            // Update the links…
            const link = gLink.selectAll("path")
                .data(links, d => d.target.id);

            // Enter any new links at the parent's previous position.
            const linkEnter = link.enter().append("path")
                .attr("d", d => {
                  const o = {x: source.x0, y: source.y0};
                  return diagonal({source: o, target: o});
                })
                .attr("stroke", "#777");

            // Transition links to their new position.
            link.merge(linkEnter).transition(transition)
                .attr("d", diagonal);

            // Transition exiting nodes to the parent's new position.
            link.exit().transition(transition).remove()
                .attr("d", d => {
                  const o = {x: source.x, y: source.y};
                  return diagonal({source: o, target: o});
                });

            // Stash the old positions for transition.
            root.eachBefore(d => {
              d.x0 = d.x;
              d.y0 = d.y;
            });
          }
          
          function buildTree(node, current_depth) {
            current_depth += 1;
  
            node.children = node.children ? null : node._children;
            update(node);
  
            var children = node.children ? node.children : node._children;
            if (current_depth < hierarchy_expansion && children) {
              $.each(children, function (index,child) {
                buildTree(child, current_depth);
              });
            }
          }

          // 根据展开层级加载组织结构图.
          if (hierarchy_expansion > 0) {
            update(root);
            var children = root.children ? root.children : root._children;
            if (hierarchy_expansion > 1 && children) {
              $.each(children, function (index, child) {
                buildTree(child, 1);
              });
            }
          }
          else {
            var node = root;
            node.children = null;
            update(node);
          }
        }
      });
    }
  };
})(jQuery, Drupal);
