
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

async function getUserNotifications(data){
    const conn = await connect();
    console.log(data.page);
    const [rows] = await conn.query("SELECT * FROM notifications WHERE receiver_id = ? LIMIT ? OFFSET ?;", [data.user_id, 20, data.page-1]);

    return rows;
}


async function getNotificationsCount(data){
    const conn = await connect();

    const [rows] = await conn.query("SELECT count(*) as num_noti FROM notifications WHERE receiver_id = ?;", [data]);

    return rows;
}

module.exports = {getUserNotifications, getNotificationsCount};


