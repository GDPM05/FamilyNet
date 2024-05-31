
class AjaxHandler{

    get(url, callback){
        $.ajax({
            url: url,
            type: 'GET',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
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
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function(res) {
                callback(res);
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

}