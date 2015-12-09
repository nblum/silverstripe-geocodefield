(function ($) {
    $.entwine('ss', function ($) {
        $.entwine('geocodefield', function ($) {
            var me = this;

            $('.geocodefield-input button').entwine({
                onmatch: function () {
                },
                onclick: function () {
                    var me = this;

                    $('.geocodefield-input input').attr('disabled', 'true');

                    $.ajax(this.data('url'))
                        .done(function (response) {
                            $('#' + me.data('lat')).val(response.lat);
                            $('#' + me.data('lon')).val(response.lon);
                        })
                        .fail(function () {
                            alert("Failed to update geodata");
                        })
                        .always(function () {
                            $('.geocodefield-input input').removeAttr('disabled');
                        });
                }
            });
        });
    }); // ss namespace
}(jQuery));