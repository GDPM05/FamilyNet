
// Classe responsável pelo controlo do servidor NodeJs, usado na comunicação de 2 utilizadores

const io = require('socket.io')(3000, {
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
        //this.showInfo(); // Método para mostrar as informações do servidor
    }

    showInfo(){ // Método para mostrar as informações do servidor (apenas em fase de testes)
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
            console.log('Joined'); // Mostra na consola que um utilizador se conectou
            this.checkConnection(socket); // Método para verificar se este utilizador já se conectou e desconecta se for o caso
            socket.on('user_data', this.handleUserData.bind(this, socket)); // Trata os dados enviados pelo utilizador
            socket.on('message', this.handleMessage.bind(this, socket)); // Trata as mensagens enviadas pelos utilizadores
            socket.on('change_friend', this.handleFriendChange.bind(this, socket)); // Administra a troca de utilizadora de conversa
            socket.on('disconnect', this.handleDisconnect.bind(this, socket)); // Trata dos dados quando um utilizador se desconecta
        });
    }    

    
    handleUserData(socket, data){ // Método que trata das informações enviadas pelos utilizadores
        console.log(data); 
        var friend_online = false;
        if(this.user_map[data.id] != null){ // Verifica se este utilizador já está no sistema (com um socket diferente)
            var user = this.users[this.user_map[data.id]]; // Guarda na variavel user este utilizador já existente
            console.log("User already.");
        }else{
            var user_id = data.id; // Guarda em variável o id do user
            var user_name = data.name; // Guarda em variável o nome do user
            var conv_id = data.id_conv; // Guarda em variável o id da conversa associada ao user
            var id_friend = data.friend; // Guarda o id do amigo de forma a ser possível o identificar.
            
            var user = new User(user_id, user_name, conv_id, id_friend); // Criar uma instância da class User
            
            console.log("New user: "+user.id, user.name, user.conv_id); 

            do{
                user.generateUniqueId(); // Gera um id único identificativo para este user
            }while(user[user.uniqueId] != null);
            
            this.user_map[user_id] = user.uniqueId; // Guarda o id único deste user no mapa de users, usando o seu id como chave
            this.users[user.uniqueId] = user; // Guarda o user no array de users, usando o id único como chave
        }

        if(this.conversas[conv_id] != null){ // Verifica se o outro utilizador já está conectado ao servidor e se ele está no servidor para falar connosco
            var conversa = this.conversas[conv_id]; // Busca a conversa já existente
            console.log("Estamos juntos");
            friend_online = true;
        }else{
            console.log("Estamos separados");
            var conversa = new Conversa(conv_id); // Cria uma conversa nov
            conversa.generateUniqueId(); // Gera um id único para esta conversa
            conversa.generateEncMethod(); // Gera um método de encriptação que será usado pelos utilizadores
            this.conversas[conversa.uniqueId] = conversa; // Guarda a conversa no array de conversas, usando o id unico da conversa como chave
        }
        
        conversa.assoc_user(user.uniqueId); // Associa o utilizador em questão à conversa
        socket.emit('enc_method', conversa.get_method()); // Envia o método de encriptação para o utilizador
        var friendSocketId = this.socket_id_map[this.user_map[id_friend]];
        io.to(friendSocketId).emit('friend_online', friend_online);
        this.user_conv_map[user.uniqueId] = conversa.uniqueId; // Guarda o id unico da conversa no mapa de conversas, usando o id unico do utilizador como chave
        this.conversas[conversa.conv_id] = conversa; // Guarda a conversa no array de conversas, usando o id único da conversa como chave
        this.socket_id_map[user.uniqueId] = socket.id; // Guarda o socket do utilizador no mapa de sockets usando o id unico do utilizador como chave
        this.sockets[socket.id] = user; // Guarda o utilizador no arrau de sockets usando o id do socket como chave
        console.log("92: "+this.user_map.toString());
    }
    
    handleFriendChange(socket, data){ // Método responsável pela mudança de utilizadores na conversa
        var user = this.sockets[socket.id]; // Busca o utilizador ao array de sockets
        user.updateFriend(data.friend_id); // Atualiza o id do amigo no objeto do utilizador
        var user_uniqueId = user.uniqueId; // Guarda o id unico do user numa variavel

        var conversa_id = this.user_conv_map[user_uniqueId]; // Busca o id da conversa ao mapa de conversas, cujo usa o id unico do user como chave
        var conversa = this.conversas[conversa_id]; // Busca a conversa ao array de conversas usando o id da conversa

        conversa.remove_user(user); // Remove o utilizador da antiga conversa
        if(conversa.numUsers() < 1) // Verifica se já não há nenhum utilizador na conversa
            delete this.conversas[conversa]; // Apaga a conversa do array de conversas se esse for o caso

        conversa = new Conversa(); // Cria uma conversa nova 
        conversa.generateUniqueId(); // Gera um id único 
        conversa.generateEncMethod(); // Gera um método de encriptação

        socket.emit('enc_method', conversa.get_method()); // Envia o novo método ao utilizador
        this.users[user.uniqueId] = user; // Guarda o utilizador no array de users
        this.user_conv_map[user.uniqueId] = conversa.uniqueId; // Atualiza o id da conversa no mapa de conversas
        this.conversas[conversa.uniqueId] = conversa; // Guarda a nova conversa no array de conversas
    }

    handleMessage(socket, data){ // Método responsável por tratar as mensagens enviadas
        console.log("Olá");
        var user = this.sockets[socket.id]; // Busca o utilizador ao array de sockets
        console.log(user);
        console.log("User:" + user.conv_id);

        if(!this.conversas[user.conv_id].friends_online(user.uniqueId)) // Verifica se a conversa tem algum membro online
            return; // Caso não tenha retorna.
        
        var conv = this.conversas[user.conv_id]; // Busca a conversa
        //console.log(conv);

        var users = conv.get_friends(user.uniqueId);
        console.log(users.length);
        console.log("babado "+users);
        if(users.length < 2){
            var socket = this.socket_id_map[users];
            console.log("socket: "+socket); 
            io.to(socket).emit('new_msg', data);
        }else{
            for(var i = 0; i < users.length; i++){
                console.log("uasd " + user);
                var socket = this.socket_id_map[users[i]];
                console.log("socket: "+socket); 
                io.to(socket).emit('new_msg', data);
            }
        }/*
            for(var i = 0; i < conv.users.length; i++){ // Percorre todos os utilizadores
            console.log("unique1: "+user.uniqueId);
            console.log("unique2: "+conv.users[i]);
            
            if(user.uniqueId != conv.users[i].uniqueId){ // Verifica se o id unico dos utilizadores é diferente do utilizador que enviou a mensagem
                var index = conv.users[i]; 
                console.log("index " + index);
                io.to(this.socket_id_map[index]).emit('new_msg', data); // Envia a mensagem para o utilizador
            }
        }*/
    }

    handleDisconnect(socket, data){ // Método responsável por tratar as informações quando um user se disconecta
        var user = this.sockets[socket.id]; // Busca o user ao array de users
        if(user){ // Verifica se não está vazio
            var conversa = this.conversas[this.user_conv_map[user.uniqueId]]; // Busca a conversa ao array de conversas
            if(conversa){ // Verifica se não está vazio
                conversa.remove_user(user.uniqueId); // Remove o utilizador em questão da conversa
                if(this.users[this.user_map[user.id_user_conv]]) {
                    var friendSocketId = this.socket_id_map[this.user_map[user.id_user_conv]];
                    io.to(friendSocketId).emit('friend_online', false);
                }
            }

            if(conversa.numUsers() < 1){ // Verifica se a conversa não tem mais nenhum utilizador
                var index = Object.keys(this.user_conv_map).find(key => this.user_conv_map[key] === conversa.uniqueId); 
                delete this.user_conv_map[index]; // Apaga a conversa do mapa de conversas
                delete this.conversas[conversa.uniqueId]; // Apaga a conversa do array de conversas
            }

            delete this.user_conv_map[user.uniqueId]; // Apaga o id unico do utilizador do mapa de conversas
            delete this.sockets[this.socket_id_map[user.uniqueId]]; // Apaga o utilizador do array de sockets
            delete this.socket_id_map[user.uniqueId]; // Apaga o utilizador do mapa do sockets
            delete this.user_map[user.id]; // Apaga o utilizador do mapa de users
            delete this.users[user.uniqueId]; // Apaga o utilizador do array de users
            

            console.log('Disconnected'); // Informa que um utilizador se disconectou
        }
    }

    checkConnection(socket){ // Método responsável por evitar que um utilizador se conecte mais que 1 vez com o mesmo socket
        var index = this.sockets.indexOf(socket.id);

        if(index !== -1){
            socket.disconnect(true);
            console.log('aa');
        }
            
    }
}

const sv = new Server();
sv.start();