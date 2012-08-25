jQuery(document).ready(function() {
  // Instantiate Create
  jQuery('body').midgardCreate({
    url: function() {
      return 'javascript:false;';
    }
  });
  
  // Fake Backbone.sync since there is no server to communicate with
  Backbone.sync = function(method, model, options)
  {
        $.ajax({
            url: "/plugin/CreateJS/admin/sync",
            async: true,
            data: {
                model: model.toJSONLD()
            },
            type: "post",
            success: function(data){
                if (data != undefined && data.success != undefined && data.success == true) {
                    //
                }
            },
            fail: function(data) {
                alert('API Call error [E892984845]');
            }
        });
        options.success(model);
    };
});
