<div class="geocodefield-input" data-url="$AjaxUrl" data-button="geocode-button-$Identifier" data-valuefield="$name" data-addressfield="$address.name" data-latfield="$lat.name" data-lonfield="$lon.name" data-references="<% loop $ReferencedFields %>$Me.value,<% end_loop %>">
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
            <button id="geocode-button-$Identifier" class="ss-ui-button ss-ui-button-small ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                <span class="ui-button-text">
                    <%t GeoCodeField.Refresh "refresh" %>
                </span>
            </button>
        </div>
    </div>
</div>
