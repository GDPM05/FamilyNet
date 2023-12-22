
class User{

    constructor(id, name, id_user_conv){
        this.id = id;
        this.name = name;
        this.id_user_conv = id_user_conv;
        this.uniqueId = null;
    }

    generateUniqueId(){
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

    updateFriend(user){
        this.id_user_conv = user;
    }

}

module.exports = User;