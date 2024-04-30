const app = require('./processRequest');
const db = require('./database');

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
    }

    getNotifications(){
        this.app.post('/get_notifications', (req, res) => {
            req.on('data', async (info) => {
                res.setHeader('Content-Type', 'application/json');
                var return_data = {};
                const data = (info.toString().split('=')).pop();

                if(data == '' || data == undefined){
                    return_data.error = true;
                    return_data.error_message = 'Invalid data!';
                    res.write(JSON.stringify(return_data));
                    res.end();
                    return false;
                }

                const notifications = await this.db.getUserNotifications(data);

                console.log(notifications);

                res.end();
            });
        });
    }
    
    loadNotifications(){
        this.app.post('/load_notifications', (req, res) => {
            req.on('data', async (info) => {
                const data = (info.toString().split('&'));

                const get_data = {
                    'user_id': Number((data[1].split('=')).pop()),
                    'page': Number((data[0].split('=')).pop())
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

}

const server = new ServerNotifications(5000);
server.start();
