
// Classe responsável pelo controlo do servidor NodeJs, usado na comunicação de 2 utilizadores

const io = require('socket.io')(6969, {
    cors: {
      origin: "*",
    }
});

const User = require('./User.cjs');
const Conversa = require('./Conversa.cjs');

class Server{

    constructor(){
        this.conversas = []; // Array responsável por guardar todas as conversas
        this.user_conv_map = {}; // Mapa de todas as conversas associadas a um utilizador
        this.socket_id_map = {}; // Mapa de todos os sockets associados a utilizadores
        this.sockets = []; // Array que guarda os sockets
        this.user_map = {}; // Mapa responsável por identificar os utilizadores baseado no seu id único
        this.users = []; // Arary de utilizadores
        this.showInfo();
    }

    showInfo(){
        setInterval(()=>{
            console.log("Conversas: ", this.conversas);
            console.log("User Conv Map: ", this.user_conv_map);
            console.log("Socket id map: ", this.socket_id_map);
            console.log("Sockets: ", this.sockets);
            console.log("Map de users: ", this.user_map);
            console.log("users: ", this.users);
        }, 10000)
    }

    start(){
        io.on('connection', socket =>{
            console.log('Joined');
            this.checkConnection(socket);
            socket.on('user_data', this.handleUserData.bind(this, socket));
            socket.on('message', this.handleMessage.bind(this, socket));
            socket.on('change_friend', this.handleFriendChange.bind(this, socket)); 
            socket.on('disconnect', this.handleDisconnect.bind(this, socket));
        });
    }    

    
    handleUserData(socket, data){
        console.log(data);
        if(this.user_map[data.id] != null){
            var user = this.users[this.user_map[data.id]];
        }else{
            var user_id = data.id; // Guarda em variável o id do user
            var user_name = data.name; // Guarda em variável o nome do user
            var user_conv = data.id_user_conv; // Guarda em variável o id do user que estabelecerá comunicação
            
            var user = new User(user_id, user_name, user_conv); // Criar uma instância da class User
            
            do{
                user.generateUniqueId(); // Gera um id único identificativo para este user
            }while(user[user.uniqueId] != null);
            
            this.user_map[user_id] = user.uniqueId; 
            this.users[user.uniqueId] = user;    
        }
        console.log(this.users);
        if(this.users[this.user_map[user_conv]] && this.users[this.user_map[user_conv]].id_user_conv == user_id){ // Verifica se o outro utilizador já está conectado ao servidor e se ele está no servidor para falar connosco
            console.log('Ai 1');
            var conversa = this.conversas[this.user_conv_map[this.user_map[user_conv]]];
        }else{
            console.log('Ai 2');
            var conversa = new Conversa();
            conversa.generateUniqueId();
            conversa.generateEncMethod();
            this.conversas[conversa.uniqueId] = conversa;
        }
        
        conversa.assoc_user(user.uniqueId);
        socket.emit('enc_method', conversa.get_method());
        this.user_conv_map[user.uniqueId] = conversa.uniqueId;
        this.conversas[conversa.uniqueId] = conversa;
        this.socket_id_map[user.uniqueId] = socket.id;
        this.sockets[socket.id] = user;
        console.log(this.user_map);
    }
    
    handleFriendChange(socket, data){
        console.log('Olha eu aqui');
        var user = this.sockets[socket.id];
        user.updateFriend(data.friend_id);
        var user_uniqueId = user.uniqueId;

        var conversa_id = this.user_conv_map[user_uniqueId];
        var conversa = this.conversas[conversa_id];

        conversa.remove_user(user);
        if(conversa.numUsers() < 1)
            delete this.conversas[conversa];

        conversa = new Conversa();
        conversa.generateUniqueId();
        conversa.generateEncMethod();

        socket.emit('enc_method', conversa.get_method());
        this.users[user.uniqueId] = user;
        this.user_conv_map[user.uniqueId] = conversa.uniqueId;
        this.conversas[conversa.uniqueId] = conversa;
    }

    handleMessage(socket, data){
        var user = sockets[socket.id];
        if(!user_map[user.id_user_conv])
            return;
        
        conv_id = this.user_conv_map[user.uniqueId];
        var conv = this.conversas[conv_id];

        for(var i = 0; i < conv['users'].length; i++){
            if(user.uniqueId != conv['users'][i]){
                var index = conv['users'][i];
                io.to(this.socket_id_map[index]).emit('new_msg', data);
            }
        }
    }

    handleDisconnect(socket, data){
        var user = this.sockets[socket.id];
        if(user){
            var conversa = this.conversas[this.user_conv_map[user.uniqueId]];
            if(conversa)
                conversa.remove_user(user.uniqueId);

            if(conversa.numUsers() < 1){
                var index = Object.keys(this.user_conv_map).find(key => this.user_conv_map[key] === conversa.uniqueId);
                delete this.user_conv_map[index];
                delete this.conversas[conversa.uniqueId];
            }

            delete this.user_conv_map[user.uniqueId];
            delete this.sockets[this.socket_id_map[user.uniqueId]];
            delete this.socket_id_map[user.uniqueId];
            delete this.user_map[user.id];
            delete this.users[user.uniqueId];
            

            console.log('Disconnected');
        }
    }

    checkConnection(socket){
        var index = this.sockets.indexOf(socket.id);

        if(index !== -1){
            socket.disconnect(true);
            console.log('aa');
        }
            
    }
}

const sv = new Server();
sv.start();