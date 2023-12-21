
// Classe responsável pelo controlo do servidor NodeJs, usado na comunicação de 2 utilizadores

class Server{

    constructor(){
        this.conversas = []; // Array responsável por guardar todas as conversas
        this.user_conv_map = {}; // Mapa de todas as conversas associadas a um utilizador
        this.socket_id_map = {}; // Mapa de todos os sockets associados a utilizadores
        this.sockets = []; // Array que guarda os sockets
        this.user_map = {}; // Mapa responsável por identificar os utilizadores baseado no seu id único
        this.users = []; // Arary de utilizadores
    }

    start(){
        
    }    

}