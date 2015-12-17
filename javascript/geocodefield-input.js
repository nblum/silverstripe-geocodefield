(function ($) {
    $.entwine('ss', function ($) {
        $.entwine('geocodefield', function ($) {

            $('.geocodefield-input').entwine({
                /**
                 * initalizes geocode field
                 */
                onmatch: function () {
                    var me = this,
                        response = {},
                        references = $(me).data('references'),
                        referencedFields = references.split(','),
                        $valueField = $('input[name="' + me.data('valuefield') + '"]');


                    $.each(referencedFields, function (index, name) {
                        if (name.length > 0) {
                            $('input[name="' + name + '"]').on('change', function (e) {
                                me.updateaddressfield();
                            });
                        }
                    });

                    //load saved values
                    response = jQuery.parseJSON($valueField.val())
                    if (!!response && response.lon) {
                        me.showresponsedata(response);
                    }

                    $('#' + me.data('button')).on('click', function () {
                        me.buttonclick();
                    });

                    me.updateaddressfield();
                },
                /**
                 * loads data via xhr
                 */
                buttonclick: function () {
                    var me = this,
                        references = $(me).data('references'),
                        $button = $('#' + me.data('button')),
                        $geoAddress = $('#' + me.data('geoaddress')),
                        $addressField = $('input[name="' + me.data('addressfield') + '"]'),
                        addressValidatorUrl = me.data('url');

                    $button.attr('disabled', 'true');
                    $geoAddress.html($geoAddress.data('searching'));

                    $.ajax({
                            url: addressValidatorUrl,
                            data: {
                                address: $addressField.val()
                            }
                        })
                        .done(function (response) {
                            me.showresponsedata(response);
                        })
                        .fail(function () {
                            alert("Failed to update geodata");
                        })
                        .always(function () {
                            $button.removeAttr('disabled', 'true');
                        });
                },
                /**
                 * adds the given respons into the fields
                 * @param response
                 */
                showresponsedata: function (response) {
                    var me = this,
                        $geoAddress = $('#' + me.data('geoaddress')),
                        $valueField = $('input[name="' + me.data('valuefield') + '"]'),
                        $lonField = $('input[name="' + me.data('lonfield') + '"]'),
                        $latField = $('input[name="' + me.data('latfield') + '"]');

                    $latField.val(response.lat);
                    $lonField.val(response.lon);
                    $geoAddress.html(response.formatted_address);
                    $valueField.val(JSON.stringify(response));
                    me.updateState();
                },
                /**
                 * updates the address-field from the references (if set)
                 */
                updateaddressfield: function () {
                    var me = this,
                        references = $(me).data('references'),
                        referencedFields = references.split(','),
                        $addressField = $('input[name="' + me.data('addressfield') + '"]'),
                        addressString = '';

                    $.each(referencedFields, function (index, name) {
                        if (name.length > 0) {
                            addressString += ' ' + $('input[name="' + name + '"]').val();
                        }
                    });

                    $addressField.val(addressString);
                    me.updateState();
                },
                /**
                 * updates current address matching state
                 */
                updateState: function () {
                    var me = this,
                        references = $(me).data('references'),
                        referencedFields = references.split(','),
                        $valueField = $('input[name="' + me.data('valuefield') + '"]'),
                        values = $.parseJSON($valueField.val()),
                        $addressField = $('input[name="' + me.data('addressfield') + '"]'),
                        addressString = '';

                    if(values.search_address !== $addressField.val()) {
                        $(me).addClass('changed');
                    } else {
                        $(me).removeClass('changed');
                    }
                }

            });
        });
    }); // ss namespace
}(jQuery));