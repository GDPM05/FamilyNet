
/**
 * Funções assincronas - em conjunto com await delimitados como os objetos interagem
 * 
 */

async function connect(){
    if(global.connection && global.connection.status !=='disconnected')
        return global.connection;
    

    // mysql2 -> createConnection ->  Ligação à base de dados, este método é async, logo tenho de implementar um callback que transforma a nossa função async/await deste modo a operação apenas executa após criar a ligação

    const mysql = require('mysql2/promise');

    const connection = await mysql.createConnection("mysql://root:mysql@localhost:3306/familinet");
    console.log("Ligado...");
    global.connection = connection;

    return connection;
}

async function new_notification(id_sender, id_receiver, message, type){
    const conn = await connect();

    try{
        var currentDate = new Date();
        var sqlFormattedDate = currentDate.toISOString().slice(0, 19).replace('T', ' ');
    
        await conn.query("INSERT INTO notifications (type_id, sent_date, receiver_id, sender_id, message_text) VALUES (?, ?, ?, ?, ?);",[Number(type), sqlFormattedDate, id_receiver, id_sender, message]);    

        return true;
    }catch(err) {
        console.error(err);
        return false;
    }
}

async function getUserNotifications(data){
    const conn = await connect();
    console.log(data.page);
    console.log(data.limit);
    const [rows] = await conn.query("SELECT * FROM notifications WHERE receiver_id = ? LIMIT ? OFFSET ?;", [Number(data.user_id), Number(data.limit), Number(data.page-1)]);

    return rows;
}


async function getNotificationsCount(data){
    const conn = await connect();

    const [rows] = await conn.query("SELECT count(*) as num_noti FROM notifications WHERE receiver_id = ?;", [data]);

    return rows;
}

async function updateInvite(notification_id, status){
    const conn = await connect();

    try{
        const [rows] = await conn.query("SELECT receiver_id, sender_id FROM notifications WHERE id = ?", [notification_id]);

        const users_ids = await JSON.parse(JSON.stringify(rows))[0];
    
        const invite_id = await conn.query("UPDATE friends SET status=? WHERE id_user1 = ? AND id_user2 = ? OR id_user1 = ? AND id_user2 = ?", [Number(status), users_ids.sender_id, users_ids.receiver_id, users_ids.receiver_id, users_ids.sender_id])['rows'];
    
        await conn.query("DELETE FROM notifications WHERE id = ?", [notification_id]);
    
        const user = await conn.query("SELECT * FROM user WHERE id = ?;", [users_ids.receiver_id]);
        //console.log(await conn.query("SELECT * FROM user WHERE id = ?;", [users_ids.receiver_id]));
        const username = JSON.parse(JSON.stringify(await user))[0][0].username;
        //console.log("User: "+JSON.stringify(JSON.parse(JSON.stringify(await user))[0][0].username));
    
        var currentDate = new Date();
        var sqlFormattedDate = currentDate.toISOString().slice(0, 19).replace('T', ' ');
        console.log(sqlFormattedDate); // Outputs: "2024-04-30 10:43:30"
        await conn.query("INSERT INTO notifications (type_id, sent_date, receiver_id, sender_id, message_text) VALUES (?, ?, ?, ?, ?);",[2, sqlFormattedDate, users_ids.sender_id, users_ids.receiver_id, username+' '+((status == 1) ? 'accpeted' : 'refused')+' your friend invitation.']);    
        return true;
    }catch(err){
        console.log(err);
        return false;
    }
}

module.exports = {getUserNotifications, getNotificationsCount, updateInvite, new_notification};


