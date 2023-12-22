
class Conversa{

    constructor(){
        this.users = [];
        this.cod_enc = '';
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
    

    numUsers(){
        return this.users.length;
    }

    get_method(){
        return this.cod_enc;
    }

    generateUniqueId(){
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

    generateEncMethod(){
        var char = ' abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZãõâôáéíóúàèìòùÃÕÂÔÁÉÍÓÚÀÈÌÒÙ1234567890,.?!-_ºª+*´`~^\'\"#$%&/()=@£§{[]}»«\\|';
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