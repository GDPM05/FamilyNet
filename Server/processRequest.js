
var http = require('http');

var criarProcessRequest = function(porta){
    var entrada = {};
    var caminhos = {};
    var metodos = ['GET', 'POST'];

    metodos.forEach(function(metodo){
        caminhos[metodo] = {};
        entrada[metodo.toLowerCase()] = function(path, fn){
            caminhos[metodo][path] = fn;
        }
     }); 

     http.createServer(function(req, res){
        res.setHeader('Access-Control-Allow-Origin', '*');
        if(!caminhos[req.method][req.url]){
            res.statusCode = 404;
            return res.end();
        }
        caminhos[req.method][req.url](req, res);
    }).listen(porta);
    return entrada;
}

module.exports = criarProcessRequest; 