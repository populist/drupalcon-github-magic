function myGraph(el) {
    var self = this;
    var classMap = {
        'edgeserver'   :"server",
        'appserver'    :"server",
        'dbserver'     :"server",
        'slavedbserver':"server",
        'cacheserver'  :"server",
        'fileserver'   :"server",
        'newrelic'     :"server",
        'indexserver'  :"server",
        'codeserver'   :"server"
    };

    var _types = ['edgeserver', 'appserver', 'dbserver', 'slavedbserver', 'cacheserver', 'fileserver', 'newrelic', 'indexserver', 'codeserver'];

    self._events = {
        'node.selected'  :[],
        'node.unselected':[]
    }

    this.registerEvent = function(event, cb){
        self._events[event].push(cb);
    }

    this._triggerEvent = function(event, target){
        var e = {
            "name": event,
            "node": target
        };
        var listeners = self._events[event];
        $.each(listeners, function(i, cb){
            cb(e);
        });
    }

    // Add and remove elements on the graph object
    this.addNode = function (node) {
        if(node.type == "edgeserver"){
            node.fixed = true;
            node.x = w/2;
            node.y = 50;
        }
        else if(node.type == "newrelic"){
            node.fixed = true;
            node.x=300;
            node.y=150;
        }
        else if(node.type == "codeserver"){
            node.fixed = true;
            node.x=300;
            node.y=275;
        }
        nodes.push(node);
        update();
    }

    this.removeNode = function (id) {
        var i = 0;
        var n = findNode(id);
        while (i < links.length){
            if ((links[i]['source'] == n)||(links[i]['target'] == n)){
                links.splice(i,1);
            }
            else{
                i++;
            }
        }
        nodes.splice(findNodeIndex(id),1);
        update();
    }

    this.addLink = function (source, target){
        // if(source.type == "newrelic" || target.type == "newrelic"){
        //     // Don't factor these links in, they screw things up!
        //     return;
        // }
        if(findNode(source) && findNode(target)){
            links.push({"source":findNode(source),"target":findNode(target)});

            update();
        }
    }

    var findNode = function(id){
        var toReturn = false;
        $.each(nodes, function(i, obj){
            if(obj.id == id){
                toReturn = obj;
            }
        });
        return toReturn;
    }

    var findNodeIndex = function(id){
        for (var i in nodes) {if (nodes[i]["id"] === id) return i};
    }

    var bg = [
        {"id":"edgeserver", "pos":50},
        {"id":"appserver", "pos":200}
    ];

    var color = d3.scale.category20();

    // set up the D3 visualisation in the specified element
    var w = $(el).innerWidth(),
        h = $(el).innerHeight(),
        r = 36, //Circle radius
        st = 0,
        bgcolor = 0,
        sw = 0,
        fill = "#313945";
        //sc = "";

    var vis = this.vis = d3.select(el).append("svg:svg")
        .attr("width", w)
        .attr("height", h)
        .attr("stroke", st)
        .attr("background", bgcolor)
        .attr("fill", fill)
        .attr("stroke-width", sw);
        //.attr("class", sc);

    var bgGroup = vis.insert("g").attr("class", "bgGroup");
    var linkGroup = vis.insert("g").attr("class", "linkGroup");
    var nodeGroup = vis.insert("g").attr("class", "nodeGroup");

    // Remember the node that is selected.
    var selectedNode = false;

    var force = d3.layout.force()
        //.chargeDistance(.10000)
        //.alpha(0)
        .gravity(1)
        .friction(.7)
        .linkStrength(function(d){
            if(d.target.type == "newrelic" || d.target.type == "codeserver"){
                return 0;
            }
            return 1;
        })
        .distance(function(d){
            //var dist = 10 + (10*d.source.links.length)
            //return dist;
            if(d.target.type == "codeserver" || d.target.type == "newrelic" || d.source.type == "codeserver" || d.source.type == "newrelic"){
                return w/2;
            }
            if(d.source.links.length > 5 || d.target.type == "appserver"){
                return 150;
            }
            return 150;
        })//Could be a function
        .charge(function(d){
            if(d.type == "appserver"){
                return -20000;
            }
            else if(d.type=="newrelic" || d.type=="codeserver"){
                return 0;
            }
            return -20000;
        })//Could be a function
        .size([w, h]);

    var nodes = force.nodes(),
        links = force.links();

    vis.on("click", function(){
        if(d3.event.defaultPrevented) return;
        if(selectedNode){
            self._triggerEvent("node.unselected",selectedNode);
            selectedNode = false;
            activate();
        }
        d3.event.stopPropagation();
    });
    var activate;
    var update = function(){

        //Draw the bg
        var bgLine = bgGroup.selectAll("g.bg")
            .data(bg, function(d){return d.id;});

        bgLine.enter().insert("g")
            .attr("class", "bg link")
            .attr("x1", function(d) { return 0; })
            .attr("x2", function(d) { return w; })
            .attr("y1", function(d) { return d.pos; })
            .attr("y2", function(d) { return d.pos; });

        bgLine.exit().remove();

        //Draw the nodes
        var node = nodeGroup.selectAll("g.node")
            .data(nodes, function(d) { return d.id;})
            .on("click", function(d) {
                if(d3.event.defaultPrevented) return; // ignore drag
                nodeGroup.selectAll("g.node").attr("stroke", "#EFD01B");
                nodeGroup.selectAll("g.node").attr("background", "#313945");
                nodeGroup.selectAll("g.node").attr("fill", "#313945");
                nodeGroup.selectAll("g.node").attr("stroke-width", 0);
                //nodeGroup.selectAll("g.node").attr("class", "");
                if(selectedNode.id == d.id){
                    //Unselect if selected node is already selected, duh!
                    selectedNode = false;
                    self._triggerEvent("node.unselected",d);
                }
                else{
                    selectedNode = d;
                    d3.select(this).attr("stroke", "#EFD01B");
                    d3.select(this).attr("stroke-width", 4);
                    d3.select(this).attr("background", "#00A9E0");
                    d3.select(this).attr("fill", "#00A9E0");
                    //d3.select(this).attr("class", "svgClass");
                    self._triggerEvent("node.selected", d);
                }

                activate();//force.resume();//update();
                d3.event.stopPropagation();
            });

        var nodeEnter = node.enter().insert("g")
            .attr("class", "node")
            //.call(force.drag);

        nodeEnter.append("circle")
            .attr("class", "bgCircle")
            .attr("r", r)

        nodeEnter.append("circle")
            .attr("class", function(d){ return "server "+d.type; })
            .attr("r", r)

        nodeEnter.append("image")
            .attr("xlink:href", function(d){ return "img/" + d.type + "-white-72x72.png"; })
            .attr("class", "icon")
            .attr("x", -22).attr("y", -22)
            .attr("width", 44).attr("height", 44)

        node.exit().remove();


        //Draw the links
        var link = linkGroup.selectAll("line.link")
            .data(links, function(d) { return d.source.id + "-" + d.target.id; });

        link.enter().insert("line")
            .attr("class", function(l){
                classes = ["link"];
                if(l.target.type == "newrelic" || l.target.type=="codeserver"){
                    classes.push("dashed");
                }
                return classes.join(" ");
            });

        link.exit().remove();


        //Activate Links and Nodes based on node selection - called be event handlerss
        activate = function(){
            //Highlight pertinent links
            //Sorry about the verbosity of this... I guess I am too tired to get this right without typing it out.
            var targetsOrSources = [];
            link.classed("active",function(d){
                var active = false;
                if(d.target.id == selectedNode.id){active = true; }
                if(d.source.id == selectedNode.id){active = true; }
                if(active){
                    if(d.target.id != selectedNode.id){targetsOrSources.push(d.target); }
                    if(d.source.id != selectedNode.id){targetsOrSources.push(d.source); }
                }
                return active;
            });
            node.classed("inactive",function(n){
                //No selection, bail.
                if(!selectedNode){return false;}
                if(targetsOrSources.indexOf(n) > -1){return false; }
                if(n.id == selectedNode.id){return false; }
                return true;
            });
        }

        //Updating on ticks
        force.on("tick", function(e) {
            // var desiredWidth = 1200;
            // var maxX = desiredWidth/2 + desiredWidth/4;
            // var minX = desiredWidth/2 - desiredWidth/4;

            node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

            // node.attr("cx", function(d) { return d.x = Math.max(minX+r, Math.min(maxX - r, d.x)); })
            //     .attr("cy", function(d) { return d.y = Math.max(r, Math.min(h - r, d.y)); });
            node.attr("cx", function(d) { return d.x = Math.max(r, Math.min(w - r, d.x)); })
                .attr("cy", function(d) { return d.y = Math.max(r, Math.min(h - r, d.y)); });

            nodes.forEach(function(d, i){
                //if(d.type == "edgeserver"){d.y = 50; d.x = w/2;}
                /*else*/ if(d.type == "appserver"){d.y = 200;}
                else if(d.type == "dbserver" || d.type == "fileserver" || d.type == "cacheserver" || d.type == "indexserver"){d.y = 350;}
                else if(d.type == "slavedbserver"){d.y = 500;}
                //else if(d.type == "codeserver"){d.x=w-200; d.y=275}
                //else if(d.type == "newrelic"){d.x=w-100; d.y=50}
                //else{d.y = 500;}
            });

              // var q = d3.geom.quadtree(nodes),
              //     i = 0,
              //     n = nodes.length;

              // while (++i < n) {
              //   q.visit(collide(nodes[i]));
              // }

            var k = 20 * e.alpha;
            // links.forEach(function(d, i) {
            //     //Push up the source of the link a little.
            //     //If it is the source of many it will be forced to the top of the vis.
            //     //d.source.y -= k;
            //     //d.target.y += k;

            //     // //Make sure the edge servers float to the top.
            //     // if(d.source.type == "edgeserver"){
            //     //     d.source.y -= (1*k);
            //     // }

            // });

            link.attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });
        });

        var collide = function(node){
          var r = node.radius + 30,
              nx1 = node.x - r,
              nx2 = node.x + r,
              ny1 = node.y - r,
              ny2 = node.y + r;
          return function(quad, x1, y1, x2, y2) {
            if (quad.point && (quad.point !== node)) {
              var x = node.x - quad.point.x,
                  y = node.y - quad.point.y,
                  l = Math.sqrt(x * x + y * y),
                  r = node.radius + quad.point.radius;
              if (l < r) {
                l = (l - r) / l * .5;
                node.x -= x *= l;
                node.y -= y *= l;
                quad.point.x += x;
                quad.point.y += y;
              }
            }
            return x1 > nx2
                || x2 < nx1
                || y1 > ny2
                || y2 < ny1;
          };
        }

        // Restart the force layout.
        force.start();
    }

    // Make it all go
    update();
}
