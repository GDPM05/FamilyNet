
class NotificationClient {

    socket;

    constructor(io){
        this.io = io;
    }

    connect(url){ // Método responsável por efetuar a conexão ao servidor.
        console.log("Connecting");
        this.socket = this.io.connect(url);
        try{
            this.socket.on('notification', (socket) => {
                console.log(socket);
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
}


const client = new NotificationClient(io);
client.connect('http://localhost:8914');