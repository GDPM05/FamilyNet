
class AjaxHandler{

    get(url, callback){
        $.ajax({
            url: url,
            type: 'GET',
            success: function(res){
                callback(res);
            },
            error: function(error){
                console.log(error);
            }
        });
    }

    post(url, data, callback, c_type){
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            contentType: c_type,
            success: function(res) {
                callback(res);
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

}