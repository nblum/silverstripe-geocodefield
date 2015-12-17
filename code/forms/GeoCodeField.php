<?php

/**
 * Class GeoCodeField
 */
class GeoCodeField extends TextField
{

    private static $allowed_actions = array(
        'validateAddress'
    );

    /**
     * @var string
     */
    protected $identifier = '';

    /**
     * @var bool
     */
    protected $editableAddress = true;

    /**
     * @var TextField
     */
    protected $address = null;

    /**
     * @var TextField
     */
    protected $lon = null;

    /**
     * @var TextField
     */
    protected $lat = null;

    /**
     * @var string[]
     */
    protected $referencedFields = array();

    /**
     * GeoCodeField constructor.
     * @param string $name
     * @param null $title
     */
    public function __construct($name, $title = null)
    {
        parent::__construct($name, $title);
        $this->identifier = uniqid();

        $this->setTemplate('GeoCodeField');
        $this->setLon('Lon' . $this->Identifier());
        $this->setLat('Lat' . $this->Identifier());
        $this->setAddress('Address' . $this->Identifier());
        $this->setAttribute('type', 'hidden');
        $this->setAttribute('data-valuefield', 'true');
    }

    /**
     * returns a unique identifier
     * @return string
     */
    public function Identifier()
    {
        return $this->identifier;
    }

    /**
     * address input field will be not editable
     */
    public function setAddressNotEditable()
    {
        $this->address->setAttribute('disabled', 'disabled');
    }

    /**
     * @return TextField
     */
    public function getAddress()
    {
        return $this->lon;
    }

    /**
     * @param TextField $name
     * @return GeoCodeField
     */
    public function setAddress($name)
    {
        $this->address = new TextField($name);
        $this->address->setAttribute('placeholder', _t('GeoCodeField.Address', ''));
        return $this;
    }

    /**
     * @return TextField
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @param TextField $lon
     * @return GeoCodeField
     */
    public function setLon($lon)
    {
        $this->lon = new TextField($lon);
        $this->lon->setAttribute('disabled', 'disabled');
        $this->lon->setAttribute('placeholder', _t('GeoCodeField.Lon', ''));
        return $this;
    }

    /**
     * @return TextField
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param TextField $lat
     * @return GeoCodeField
     */
    public function setLat($lat)
    {
        $this->lat = new TextField($lat);
        $this->lat->setAttribute('disabled', 'disabled');
        $this->lat->setAttribute('placeholder', _t('GeoCodeField.Lat', ''));
        return $this;
    }

    /**
     * @return string[]
     */
    public function getReferencedFields()
    {
        return new ArrayList($this->referencedFields);
    }

    /**
     * @param string $fieldName
     */
    public function addAddressReference($fieldName)
    {
        $this->referencedFields[] = ArrayData::create(array('value' => $fieldName));
    }

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = array())
    {
        Requirements::css('silverstripe-geocodefield/css/geocodefield-input.css');
        Requirements::javascript('silverstripe-geocodefield/javascript/geocodefield-input.js');

        return parent::Field($properties);
    }

    /**
     * returns the xhr request url for address validation
     * @return string
     */
    public function AjaxUrl()
    {
        return $this->Link('validateAddress');
    }

    /**
     * validates the given address against the google maps address api
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function validateAddress(SS_HTTPRequest $request)
    {
        $response = new SS_HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');

        $address = filter_var($request->requestVar('address'), FILTER_SANITIZE_STRING);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf('http://maps.google.com/maps/api/geocode/json?address=%s', urlencode($address))
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        $respObj = json_decode($resp);

        if (count($respObj->results) > 0) {
            $result = array(
                'search_address' => $address,
                'formatted_address' => $respObj->results[0]->formatted_address,
                'lat' => $respObj->results[0]->geometry->location->lat,
                'lon' => $respObj->results[0]->geometry->location->lng
            );
        } else {
            $result = array(
                'search_address' => $address,
                'formatted_address' => _t('GeoCodeField.NoResults', '- No Data Found - '),
                'lat' => 0,
                'lon' => 0
            );
        }

        $response->setBody(Convert::array2json($result));
        return $response;
    }

}

