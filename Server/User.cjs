
class User{

    constructor(id, name, conv_id, friend_id){
        this.id = id;
        this.name = name;
        this.conv_id = conv_id;
        this.uniqueId = null;
        this.friend_id = friend_id;
    }

    generateUniqueId(){ // Gera id único para este utilizador
        var code = '';
        var char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var num = '1234567890';

        for(var i = 0; i<30; i++)
            if(Math.floor(Math.random() * 10) < 5)
                code += char[Math.floor(Math.random() * char.length)];
            else
                code += num[Math.floor(Math.random() * num.length)];

        this.uniqueId = code;
    }

    updateFriend(friend_id){
        this.friend_id = friend_id;
    }

    updateConv(conv_id){
        this.conv_id = conv_id;
    }

}

module.exports = User;