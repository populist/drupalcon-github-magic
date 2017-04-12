function Site(data){

    var self = this;

    self.reset = function(){

        self._instances = {};

        self._servers = {
            'edgeserver'   :[],
            'appserver'    :[],
            'dbserver'     :[],
            'slavedbserver':[],
            'cacheserver'  :[],
            'fileserver'   :[],
            'newrelic'     :[],
            'indexserver'  :[],
            'codeserver'   :[]
        };

        self._links = {
            'edgeserver'   :['appserver'],
            'appserver'    :['dbserver', 'cacheserver','fileserver','newrelic','indexserver','codeserver'],
            'dbserver'     :['slavedbserver'],
            'slavedbserver':[],
            'cacheserver'  :[],
            'fileserver'   :[],
            'newrelic'     :[],
            'indexserver'  :[],
            'codeserver'   :[]
        };

        self._events = {
            'server.add'   :[],
            'server.delete':[]
        }
    }

    self.registerEvent = function(event, cb){
        self._events[event].push(cb);
    }

    self._triggerEvent = function(event, server){
        var e = {
            "name": event,
            "server": server
        };
        var listeners = self._events[event];
        $.each(listeners, function(i, cb){
            cb(e);
        });
    }

    self.load = function(servers){
        //Import servers into internal data store.
        var newbies = [];
        for(var i=0; i<servers.length; i++){
            var newbie = _addServer(servers[i]);
            if(newbie){
                newbies.push(newbie);
            }
        }

        //Kill oldies
        $.each(self._instances, function(id, existing){
            var keep = false;
            $.each(servers, function(i, incoming){
                if(incoming.id == id)
                    keep = true;
            });
            if(!keep){
                self._deleteServer(id);
            }
        });
        //Make sure our links are built.
        _buildLinks();
        $.each(newbies, function(i, newbie){
            self._triggerEvent("server.add", newbie);
        });
    }

    self._deleteServer = function(id){
        var toDelete = _lookup(id);
        delete self._instances[id];
        $.each(self._servers[toDelete.type], function(index, el){
            if(el.id == id){
                self._servers[toDelete.type].splice(index, 1);
                return false;
            }
        });

        self._triggerEvent("server.delete", toDelete);
    }

    self.get = function(spec){
        //Return array of types if string, or id if obj
        if(typeof spec == "string"){
            return self._servers[spec];
        }
        else if(spec instanceof Array){
            //We add to the array.
            for(serverType in self._servers){
                for(var i=0; i<self._servers[serverType].length; i++){
                    spec.push(self._servers[serverType][i]);
                }
            }
            return spec;
        }
        else if(_lookup(spec.id)){
            return _lookup(spec.id);
        }
        return [];
    }

    var _buildLinks = function(){
        for(id in self._instances){
            _addLinks(self._instances[id]);
        }
    }

    var _lookup = function(id){
        if(self._instances.hasOwnProperty(id)){
            return self._instances[id];
        }
        return false;
    }

    var _addServer = function(server){
        if(!_lookup(server.id)){
            self._instances[server.id] = server;
            self._servers[server.type].push(server);
            return server;
        }
        return false;
    }

    var _addLinks = function(server){
        //if(!server.hasOwnProperty("links")){
            server.links = [];
        //}

        var linkTypes = self._links[server.type];
        //for each link lookup all of that type and link it by id.
        for(var i=0; i<linkTypes.length; i++){
            var type = linkTypes[i];
            var servers = self._servers[type];
            for(var x=0; x<servers.length; x++){
                server.links.push(servers[x].id);
            }
        }
        return server;
    }

    //Do Initial Import
    self.reset();
    //self.load(data.servers);
}
