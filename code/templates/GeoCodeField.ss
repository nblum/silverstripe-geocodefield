<div class="geocodefield-input" data-url="$AjaxUrl"
     data-references="<% loop $ReferencedFields %>$Me.value,<% end_loop %>">
    <input $AttributesHTML />
    <input $address.AttributesHTML />

    <div class="position-wrapper">
        <div class="part">
            <input $lon.AttributesHTML />
        </div>
        <div class="part">
            <input $lat.AttributesHTML />
        </div>
        <div class="part">
            <button class="ss-ui-button ss-ui-button-small ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                    role="button" aria-disabled="false">
                <span class="ui-button-text">
                    <%t GeoCodeField.Refresh 'refresh' %>
                </span>
            </button>
        </div>
    </div>
    <% if $apiAddressVisible %>
        <div class="geo-address">
            <span><%t GeoCodeField.ApiAddress 'API Address:' %></span>
            <span data-field="apiaddress"
                  data-searching="<%t GeoCodeField.Searching 'searching...' %>"></span>
        </div>
    <% end_if %>
</div>
