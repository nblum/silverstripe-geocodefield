<div class="geocodefield-input">
    <input type="text" class="text" />
    <div class="position-wrapper">
        <div class="part">
            <input $lon.AttributesHTML />
        </div>
        <div class="part">
            <input $lat.AttributesHTML />
        </div>
        <div class="part">
            <button data-url="$AjaxUrl" data-lat="$lat.name" data-lon="$lon.name" class="ss-ui-button ss-ui-button-small ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                <span class="ui-button-text">
                    aktualisieren
                </span>
            </button>
        </div>
    </div>
</div>
