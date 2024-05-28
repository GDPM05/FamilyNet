const app = require('./processRequest');
const db = require('./database');

const io = require('socket.io')(8914, {
    cors: {
      origin: "*",
    }
});


class ServerNotifications {
    constructor(porta){
        this.db = db;
        this.app = app(porta);
    }

    start(){
        this.getNotifications();
        this.getTotalNotifications();
        this.loadNotifications();
        this.acceptInvite();
        this.refuseInvite();
        this.sendNotification();

        this.memory = {
            notifications: []
        };

        this.online = [];

        io.on('connection', (socket) => {
            console.log("Entrou: ", socket.id);

            socket.on('user', (userData) => {
                console.log(userData);
                this.online[userData] = socket.id; 
                console.log("Online: ", this.online);
            });

            socket.on('disconnect', ()=>{
                delete this.online[socket.id];
                console.log("Disconnect: ", this.online);
            });
        });

        setInterval(function(){
            cleanMemory();
        }, 1000*600*6);
    }

    cleanMemory(){
        for(const key in this.memory.notifications){
            if (this.memory.notifications.hasOwnProperty(key)) {
                const notification = this.memory.notifications[key];
                const noti_date = new Date(notification.time);
                const oneHourAgo = new Date();
                oneHourAgo.setHours(oneHourAgo.getHours() - 1);

                const differenceInMilliseconds = currentDate - oneHourAgo;

                const differenceInHours = differenceInMilliseconds / (1000 * 60 * 60);

                if(differenceInHours > 1){
                    delete this.memory.notifications[key];
                }
            }
        }
    }

    getNotifications(){
        this.app.post('/get_notifications', (req, res) => {
            req.on('data', async (info) => {
                res.setHeader('Content-Type', 'application/json');
                var return_data = {};
                const data = (info.toString().split('=')).pop();
                console.log("aa", data);
                if(data == '' || data == undefined){
                    return_data.error = true;
                    return_data.error_message = 'Invalid data!';
                    res.write(JSON.stringify(return_data));
                    res.end();
                    return false;
                }

                const notifications = await this.db.getUserNotifications(data);

                //console.log(notifications);

                res.end();
            });
        });
    }
    
    sendNotification(){
        this.app.post('/send_notification', (req, res) => {
            req.on('data', async (info) => {
                res.setHeader('Content-Type', 'application/json');
                const data = (info.toString().split('&'));
                var message = (decodeURIComponent(data[2]));
                message = message.substring(message.indexOf('=')+1, message.length).replaceAll('+', ' ');
                console.log(message);
                const id_receiver = Number((data[0].split('=')).pop());
                const id_sender = Number((data[1].split('=')).pop());
                const noti_type = Number((data[3].split('=')).pop());
                const post_id = Number((data[4].split('=')).pop());

                if(this.memory.notifications[post_id] != undefined && this.memory.notifications[post_id] == id_sender) {
                    res.end();
                    return;
                }

                this.memory.notifications[post_id] = id_sender;

                console.log(this.memory);

                const noti = await this.db.new_notification(id_sender, id_receiver, message, noti_type);
                
                const socket = this.online[id_receiver];
                console.log(socket);
                if(socket){
                    io.to(socket).emit('notification', {msg: "new notification"});
                }

                const return_data = {
                    success: noti 
                }

                res.write(JSON.stringify(return_data));
                res.end();
            })
        });
    }

    loadNotifications(){
        this.app.post('/load_notifications', (req, res) => {
            req.on('data', async (info) => {
                const data = (info.toString().split('&'));
                console.log("aaa", data);
                const get_data = {
                    user_id: Number((data[1].split('=')).pop()),
                    page: Number((data[0].split('=')).pop()),
                    limit: (data[2].split('=')).pop()
                };

                const notifications = await this.db.getUserNotifications(get_data);
                console.log(notifications);

                res.setHeader('Content-Type', 'application/json');
                res.write(JSON.stringify(notifications));
                res.end();
            });
        })
    }

    getTotalNotifications(){
        this.app.post('/get_total_notifications', (req, res) => {
            req.on('data', async (info) => {
                res.setHeader('Content-Type', 'application/json');
                const data = (info.toString().split('=')).pop();

                console.log(data);

                const num_noti = await this.db.getNotificationsCount(data);

                res.setHeader('Content-Type', 'application/json');
                res.write(JSON.stringify(num_noti));
                res.end();
            });
        });
    }

    acceptInvite(){
        this.app.post('/accept_invite', (req, res) => {
            req.on('data', async (info) => {
                console.log("aaaa");
                res.setHeader('Content-Type', 'application/json');
                const return_data = {};

                const data = (info.toString().split('=')).pop();

                if(data == '' || data == undefined){
                    return_data.error = true;
                    return_data.error_message = "There was an error. Try again later.";
                    res.write(JSON.stringify(return_data));
                    res.end();
                    return;
                }   

                console.log(data);

                const updated = await this.db.updateInvite(data, 1);

                if(!updated){
                    return_data.error = true;
                    return_data.error_message = "There was an error. Try again later.";
                    res.write(JSON.stringify(updated));
                    res.end();
                    return;
                }

                return_data.success = true;
                return_data.success_message = 'Friend Invite accepted successfully.';
                res.write(JSON.stringify(return_data));
                res.end();
            });
        });
    }

    refuseInvite(){
        this.app.post('/refuse_invite', (req, res) => {
            req.on('data', async (info) => {
                console.log("aaaa");
                res.setHeader('Content-Type', 'application/json');
                const return_data = {};

                const data = (info.toString().split('=')).pop();

                if(data == '' || data == undefined){
                    return_data.error = true;
                    return_data.error_message = "There was an error. Try again later.";
                    res.write(JSON.stringify(return_data));
                    res.end();
                    return;
                }   

                console.log(data);

                const updated = await this.db.updateInvite(data, 2);

                if(!updated){
                    return_data.error = true;
                    return_data.error_message = "There was an error. Try again later.";
                    res.write(JSON.stringify(updated));
                    res.end();
                    return;
                }

                return_data.success = true;
                return_data.success_message = 'Friend Invite refused successfully.';
                res.write(JSON.stringify(return_data));
                res.end();
            });
        });
    }

    teste(){

    }
}

const server = new ServerNotifications(5910);
server.start();
