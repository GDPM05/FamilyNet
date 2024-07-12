
// Classe responsável pelo controlo do servidor NodeJs, usado na comunicação de 2 utilizadores

const io = require('socket.io')(9014, {
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
        var user;
        if(this.user_map[data.id] != null){ // Verifica se este utilizador já está no sistema (com um socket diferente)
            user = this.users[this.user_map[data.id]]; // Guarda na variavel user este utilizador já existente
            console.log("User already.");
        }else{
            var user_id = data.id; // Guarda em variável o id do user
            var user_name = data.name; // Guarda em variável o nome do user
            var user_conv = data.friend; // Guarda em variável o id do user que estabelecerá comunicação
            var conv_id = data.id_conv; 

            console.log("Infos ", user_id, user_name, user_conv, conv_id);
            // console.log("aaaa", user_conv);
            console.log("User conv é um array? ", (Array.isArray(user_conv)));
            console.log("Linha 64", this.user_conv_map);
            if(Array.isArray(user_conv)){
                console.log("OLÁA ");
                var user_friends = [];
                user_conv.forEach((us) => {
                    console.log("Linha 69", us);
                    console.log("this.users", this.users[this.user_map[us]]);
                    // console.log("this.user_map", this.user_map[this.users[us]]);
                    user_friends.push(this.users[this.user_map[us]]);
                    console.log("Linha 71", user_friends);
                })  

                console.log("user_friends ", user_friends);
            }else{
                var user_friends = this.users[this.user_map[user_conv]];
            }

            if(user_friends != undefined){
                socket.emit('friend_online', true);
                console.log('onlino');
            }

            console.log("User friends", user_friends);

            user = new User(user_id, user_name, conv_id ,user_conv); // Criar uma instância da class User
            
            console.log("New user: "+user.id, user.name, user.friend_id, user.conv_id); 

            do{
                user.generateUniqueId(); // Gera um id único identificativo para este user
            }while(user[user.uniqueId] != null);
            
            this.user_map[user_id] = user.uniqueId; // Guarda o id único deste user no mapa de users, usando o seu id como chave
            this.users[user.uniqueId] = user; // Guarda o user no array de users, usando o id único como chave
        }
        console.log(this.users);
        console.log("95", user_friends);
        // Esta verificação tem de ser feita com 1 array.
        var conversa = null;
        console.log("User friends: "+(Array.isArray(user_friends) ? (user_friends.filter(f => f === undefined).length < user_friends.length) : user_friends != undefined));
        if(user_friends != undefined && (Array.isArray(user_friends) ? user_friends.filter(f => f !== undefined).length < user_friends.length : true)){
            console.log("Entrei aqui.", user_friends);
            if(Array.isArray(user_friends)){
                console.log("User friends é um array.");
                console.log("linha 100");
                for(var i = 0; i < user_friends.length; i++){
                    console.log("Entrei no loop");
                    var friend = user_friends[i];
                    console.log("Amigo "+i+": ",friend);
                    if(friend != undefined){
                        if((user_friends.friend_id == user.id || friend.friend_id.includes(user_id)) && friend.conv_id == conv_id)
                            conversa = this.conversas[this.user_conv_map[friend.uniqueId]];
                    }else{
                        conversa = new Conversa(conv_id); // Cria uma conversa nov
                        conversa.generateUniqueId(); // Gera um id único para esta conversa
                        conversa.generateEncMethod(); // Gera um método de encriptação que será usado pelos utilizadores
                        this.conversas[conversa.uniqueId] = conversa; // Guarda a conversa no array de conversas, usando o id unico da conversa como chave
                    }
                }
            }else{
                console.log("Entrei no outro aqui!");
                var friend = user_friends;
                console.log(user_friends);
                console.log(friend);
                if(friend.conv_id == conv_id)
                    conversa = this.conversas[this.user_conv_map[friend.uniqueId]];
            }
        }else{
            console.log("Entrei no ultimo aqui!");
            conversa = new Conversa(conv_id); // Cria uma conversa nov
            conversa.generateUniqueId(); // Gera um id único para esta conversa
            conversa.generateEncMethod(); // Gera um método de encriptação que será usado pelos utilizadores
            this.conversas[conversa.uniqueId] = conversa; // Guarda a conversa no array de conversas, usando o id unico da conversa como chave
        }

        conversa.assoc_user(user.uniqueId); // Associa o utilizador em questão à conversa
        socket.emit('enc_method', conversa.get_method()); // Envia o método de encriptação para o utilizador
        var friendSocketId = this.socket_id_map[this.user_map[user_conv]];
        io.to(friendSocketId).emit('friend_online', friend_online);
        this.user_conv_map[user.uniqueId] = conversa.uniqueId; // Guarda o id unico da conversa no mapa de conversas, usando o id unico do utilizador como chave
        this.conversas[conversa.uniqueId] = conversa; // Guarda a conversa no array de conversas, usando o id único da conversa como chave
        this.socket_id_map[user.uniqueId] = socket.id; // Guarda o socket do utilizador no mapa de sockets usando o id unico do utilizador como chave
        this.sockets[socket.id] = user; // Guarda o utilizador no arrau de sockets usando o id do socket como chave
        console.log(this.user_map);
        console.log("Linha 143", this.user_conv_map);
    }
    
    handleFriendChange(socket, data){ // Método responsável pela mudança de utilizadores na conversa
        var user = this.sockets[socket.id]; // Busca o utilizador ao array de sockets
        console.log(socket.id);
        console.log(user);
        console.log(data);
        user.updateFriend(data.friends); // Atualiza o id do amigo no objeto do utilizador
        var user_uniqueId = user.uniqueId; // Guarda o id unico do user numa variavel

        var conversa_id = this.user_conv_map[user_uniqueId]; // Busca o id da conversa ao mapa de conversas, cujo usa o id unico do user como chave
        var conversa = this.conversas[conversa_id]; // Busca a conversa ao array de conversas usando o id da conversa

        conversa.remove_user(user); // Remove o utilizador da antiga conversa
        if(conversa.numUsers() < 1) // Verifica se já não há nenhum utilizador na conversa
            delete this.conversas[conversa]; // Apaga a conversa do array de conversas se esse for o caso

        conversa = null;

        if(Array.isArray(data.friends))
            for(var i = 0; i < data.friends.length; i++){
                var friend = this.users[this.user_map[data.friends[i].user_id]];
                
                if(friend != undefined)
                    if((friend.friend_id == user.id || friend.friend_id.includes(user.id)) && friend.conv_i == data.conv_id)
                        conversa = this.conversas[this.user_conv_map[friend.uniqueId]];
            }
        else{
            var friend = this.users[this.user_map[data.friends.user_id]];
            if(friend != null && friend.conv_id == data.conv_id)
                conversa = this.conversas[this.user_conv_map[friend.uniqueId]];
        }

        if(conversa == null){
            conversa = new Conversa(data.conv_id); // Cria uma conversa nov
            conversa.generateUniqueId(); // Gera um id único para esta conversa
            conversa.generateEncMethod(); // Gera um método de encriptação que será usado pelos utilizadores
            this.conversas[conversa.uniqueId] = conversa; // Guarda a conversa no array de conversas, usando o id unico da conversa como chave
        }

        user.updateConv(conversa.id)

        socket.emit('enc_method', conversa.get_method()); // Envia o novo método ao utilizador
        this.users[user.uniqueId] = user; // Guarda o utilizador no array de users
        this.user_conv_map[user.uniqueId] = conversa.uniqueId; // Atualiza o id da conversa no mapa de conversas
        this.conversas[conversa.uniqueId] = conversa; // Guarda a nova conversa no array de conversas
    }

    handleMessage(socket, data){ // Método responsável por tratar as mensagens enviadas
        console.log(data);
        var user = this.sockets[socket.id]; // Busca o utilizador ao array de sockets
        console.log("handleMessage user", user.friend_id);        
        console.log("User handleMessage:" + this.user_map[user.friend_id]);
        var users = [];
        if(Array.isArray(user.friend_id)){ // Verifica se os amigos são um array
            user.friend_id.forEach((id)=>{ // Para cada amigo
                if(this.user_map[id]){ // Verifica se o amigo está no servidor
                    users.push(this.user_map[id]); // Caso esteja, coloca-o no array users
                }
            });
        }else{ // Caso não seja um array
            if(!this.user_map[user.friend_id]){// Verifica se o utilizador amigo está no servidor
                console.log("Não está!");
                return; // Retorna se for o caso
            } 
        }

        console.log("linha 209", users);

        var conv_id = this.user_conv_map[user.uniqueId]; // Busca o id da conversa
        var conv = this.conversas[conv_id]; // Busca a conversa
        console.log("Conversas 212", this.conversas);
        console.log("Conversa 213", conv);
        console.log("Conv_Users: ", conv['conv_id']);
        return;
        if(Array.isArray(users)){
            users.forEach((user) => {
                if(user.uniqueId != user){ // Verifica se o id unico dos utilizadores é diferente do utilizador que enviou a mensagem
                    var index = conv['users'][i]; 
                    io.to(this.socket_id_map[index]).emit('new_msg', data); // Envia a mensagem para o utilizador
                }
            })
        }
        for(var i = 0; i < conv['users'].length; i++){ // Percorre todos os utilizadores
            if(user.uniqueId != conv['users'][i]){ // Verifica se o id unico dos utilizadores é diferente do utilizador que enviou a mensagem
                var index = conv['users'][i]; 
                io.to(this.socket_id_map[index]).emit('new_msg', data); // Envia a mensagem para o utilizador
            }
        }
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