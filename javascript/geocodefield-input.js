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
                },
                /**
                 * loads data via xhr
                 */
                buttonclick: function () {
                    var me = this,
                        references = $(me).data('references'),
                        $button = $('#' + me.data('button')),
                        $addressField = $('input[name="' + me.data('addressfield') + '"]'),
                        addressValidatorUrl = me.data('url');

                    $button.attr('disabled', 'true');

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
                        $addressField = $('input[name="' + me.data('addressfield') + '"]'),
                        $valueField = $('input[name="' + me.data('valuefield') + '"]'),
                        $lonField = $('input[name="' + me.data('lonfield') + '"]'),
                        $latField = $('input[name="' + me.data('latfield') + '"]');

                    $latField.val(response.lat);
                    $lonField.val(response.lon);
                    $addressField.val(response.formatted_address);
                    $valueField.val(JSON.stringify(response));
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
                }

            });
        });
    }); // ss namespace
}(jQuery));