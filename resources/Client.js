
class Client{

    socket;
    bin_hexa_map;
    constructor(io){
        this.io = io;
        this.bin_hexa_map = { // Mapa do valor em hexadecimal correspondente a cada valor binário
            '0000': '0',
            '0001': '1',
            '0010': '2',
            '0011': '3',
            '0100': '4',
            '0101': '5',
            '0110': '6',
            '0111': '7',
            '1000': '8',
            '1001': '9',
            '1010': 'A',
            '1011': 'B',
            '1100': 'C',
            '1101': 'D',
            '1110': 'E',
            '1111': 'F'
        };
    }

    connect(url){
        try{
            this.socket = this.io.connect(url);

            this.socket.on('connect_error', (error) => {
                console.log('connect_error event fired', error);
                window.location.href = 'http://localhost/FamilyNet/main';
            });

            this.socket.on('connect', () => {
                this.socket.on('new_msg', this.receive_message.bind(this, this.socket));
                this.socket.on('enc_method', (socket)=>{
                    console.log('ai');
                    this.enc_method = socket;
                });
            });
        }catch(error){
            window.location.href = 'http://localhost/FamilyNet/main';
            throw error;
        }
    }

    emit_userdata(data){
        console.log(data);
        try{
            this.socket.emit('user_data', {name: data.user_name, id: data.user_id, id_user_conv: data.friend_id});
        }catch(error){
            throw error;
        }    
    }

    getMethod(){
        return this.enc_method;
    }

    change_friend(friend_id){
        try{
            this.socket.emit('change_friend', {friend_id: friend_id});
        }catch(error){
            throw error;
        }
    }

    send_message(str){
        var encrypted_message = this.encrypt_message(str);
        
        try{
            this.socket.emit('message', {msg: encrypted_message});
            return encrypted_message;
        }catch(error){
            throw error;
        }
    }

    receive_message(socket, str){
        console.log(str);
        var decrypted_msg = this.decrypt_message(str.msg, this.enc_method);
        console.log(toString(decrypted_msg));
        $(".messages").prepend('<p class="message friend_msg">'+decrypted_msg+'</p>');
    }

    encrypt_message(str){
        console.log(str);
        console.log(this.enc_method[str[0]]);
        var new_str = '';
        for(let i = 0; i < str.length; i++){ // Percorre a string passada
            new_str += this.enc_method[str[i]]; // E para cada letra, substitui pela correspondente no método de encriptação gerado no servidor
        }
    
        var str_bin = '';
        for (let i = 0; i < new_str.length; i++) { // Percorre a string "baralhada"
            var binario = new_str[i].charCodeAt().toString(2); // Substitui cada letra pelo correspondete em binário
            str_bin += Array(8 - binario.length + 1).join('0') + binario; // Garante que cada "caracter" corresponde a 1 byte em binário
        } 
    
        var str_hexa = '';
        for(let i = 0; i < str_bin.length; i += 4){ // Percorre a string em binário
            var hexa = this.bin_hexa_map[str_bin.substring(i, i+4)]; // Usa o mapa de hexa para encontrar o caracter correspondente a cada grupo de 4 caracteres binários
            str_hexa += hexa; // Adiciona cada caracter à nova string encriptada
        }
        return str_hexa;
    }

    decrypt_message(str, enc_method) {     
        var str_bin = '';
        for(let i = 0; i < str.length; i++){ // Percorre a string encriptada em hexa
            let byte = Object.keys(this.bin_hexa_map).find(key => this.bin_hexa_map[key] === str[i]); // A cada caracter, substitui pelo correspondete em binário 
            str_bin += byte;
        }
    
        var str_cod = '';
        for (let i = 0; i < str_bin.length; i += 8) { // Percorre a string em binário
            let byte = str_bin.substring(i, i+8); // Separa o respetivo byte da string em binário
            let caractere = String.fromCharCode(parseInt(byte, 2)); // Substitui esse byte pelo caractere correspondente
            str_cod += caractere;
        }

        var new_str = '';
        for(var i = 0; i < str_cod.length; i++){ // Percorre a string baralhada
            var original_char = Object.keys(enc_method).find(key => enc_method[key] === str_cod[i]); // Substitui cada caractere pelo caractere correto, seguindo o método de encriptação criado no servidor
            new_str += original_char;

        }

        return new_str;
    }
}