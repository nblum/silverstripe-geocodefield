(function ($) {
    $.entwine('ss', function ($) {
        $.entwine('geocodefield', function ($) {

            $('.geocodefield-input').entwine({
                /**
                 * initalizes geocode field
                 */
                onmatch: function () {
                    var me = this,
                        references = $(me).data('references'),
                        $valueField = $(me).find('[data-field="jsondata"]');

                    //load saved values
                    response = jQuery.parseJSON($valueField.val())
                    if (!!response && response.lon) {
                        me.showresponsedata(response);
                    }

                    $(me).find('button').on('click', function () {
                        me.buttonclick();
                    });

                    me.updateaddressfield(function () {
                        me.initChangeWatch();
                    });
                },
                /**
                 * initalizes the change watcher on the referenced fields
                 */
                initChangeWatch: function () {
                    var me = this,
                        references = $(me).data('references'),
                        referencedFields = references.split(',');

                    $.each(referencedFields, function (index, name) {
                        if (name.length > 0) {
                            $('input[name="' + name + '"]').on('change', function (e) {
                                me.updateaddressfield(function () {
                                    me.updateState();
                                });
                            });
                        }
                    });
                    me.updateState();
                },
                /**
                 * loads data via xhr
                 */
                buttonclick: function () {
                    var me = this,
                        references = $(me).data('references'),
                        $button = $(me).find('button'),
                        $geoAddress = $(me).find('[data-field="apiaddress"]'),
                        $addressField = $(me).find('[data-field="addressinput"]'),
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
                            me.updateState();
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
                        $geoAddress = $(me).find('[data-field="apiaddress"]'),
                        $valueField = $(me).find('[data-field="jsondata"]'),
                        $lonField = $(me).find('[data-field="lon"]'),
                        $latField = $(me).find('[data-field="lat"]');

                    $latField.val(response.lat);
                    $lonField.val(response.lon);
                    $geoAddress.html(response.formatted_address);
                    $valueField.val(JSON.stringify(response));
                },
                /**
                 * updates the address-field from the references (if set)
                 */
                updateaddressfield: function (cbFn) {
                    var me = this,
                        references = $(me).data('references'),
                        referencedFields = references.split(','),
                        $addressField = $(me).find('[data-field="addressinput"]'),
                        addressString = '';

                    $.each(referencedFields, function (index, name) {
                        if (name.length > 0) {
                            addressString += ' ' + $('input[name="' + name + '"]').val();
                        }
                    });

                    $addressField.val(addressString);

                    if (typeof(cbFn) === 'function') {
                        cbFn();
                    }
                },
                /**
                 * updates current address matching state
                 */
                updateState: function (cbFn) {
                    var me = this,
                        references = $(me).data('references'),
                        $lonField = $(me).find('[data-field="lon"]'),
                        $latField = $(me).find('[data-field="lat"]'),
                        $valueField = $(me).find('[data-field="jsondata"]'),
                        $addressField = $(me).find('[data-field="addressinput"]'),
                        values = $.parseJSON($valueField.val());

                    if (!references) {
                        return;
                    }

                    if (values == '' || values.search_address !== $addressField.val()) {
                        $(me).addClass('changed');
                        $lonField.val('');
                        $latField.val('');
                        $valueField.val('');
                    } else {
                        $(me).removeClass('changed');
                    }

                    if (typeof(cbFn) === 'function') {
                        cbFn();
                    }
                }

            });
        });
    }); // ss namespace
}(jQuery));