
class Conversa{

    constructor(conv_id){
        this.users = [];
        this.cod_enc = '';
        this.conv_id = conv_id;
        this.uniqueId = null;
    }

    assoc_user(user){
        this.users.push(user);
    }

    remove_user(user){
        var index = this.users.indexOf(user);
        if (index !== -1) {
            this.users.splice(index, 1);
        }
    }
    
    get_friends(user_id){
        var users = [];
        for(var i = 0; i < this.users.length; i++){
            if(this.users[i] != user_id)
                users.push(this.users[i]);
        }
        return users
    }

    friends_online(user_id){
        for(var i = 0; i < this.users.length; i++)
            if(this.users[i].uniqueId != user_id)
                return true
            else
                return false;
    }

    numUsers(){
        return this.users.length;
    }

    get_method(){
        return this.cod_enc;
    }

    generateUniqueId(){ // Gera um id único para a conversa
        var code = '';
        var char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var num = '1234567890';

        for(var i = 0; i<20; i++)
            if(Math.floor(Math.random() * 10) < 5)
                code += char[Math.floor(Math.random() * char.length)];
            else
                code += num[Math.floor(Math.random() * num.length)];

        this.uniqueId = code;
    }

    generateEncMethod(){ // Gera o cóigo que vai ser utilizado pelos utilizadores para a encriptação das mensagens
        var char = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZãõâôáéíóúàèìòùÃÕÂÔÁÉÍÓÚÀÈÌÒÙ1234567890,.?!-_ºª+*´`~^\'\"#$%&/()=@£§{[]}»«\|";
        var code = {}; // Inicialize code como um objeto
    
        for(var i = 0; i < char.length; i++){
            do{
                var random_char = char[Math.floor(Math.random() * char.length)];
            }while(char[i] == random_char || Object.values(code).includes(random_char))
            
            code[char[i]] = random_char;
        }
    
        this.cod_enc = code;
    }

    hasUser(user){
        var index = this.users.indexOf(user);

        if(index === -1)
            return false;

        return true;
    }

}

module.exports = Conversa;