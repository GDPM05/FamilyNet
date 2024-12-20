
class NotificationClient {

    socket;

    constructor(io){
        this.io = io;
    }

    connect(url){ // Método responsável por efetuar a conexão ao servidor.
        this.socket = this.io.connect(url);
        try{
            this.socket.on('notification', (socket) => {
                $('.notification-icon').append('<span class="notification-badge"></span>');
            });

            this.socket.on('connect', () => {
                try{
                    this.socket.emit('user', userdata.id);
                }catch(error){
                    throw error;
                } 
            });

        }catch(err){
            console.log(err);
        }
    }

    send_simple_notification(receiver_id){
        try{
            this.socket.emit('send_simple_notification', receiver_id);
        }catch(err){
            console.log(err);
        }
    }
}


const not_client = new NotificationClient(io);
not_client.connect('http://localhost:8914');

window.not_clinet = not_client;