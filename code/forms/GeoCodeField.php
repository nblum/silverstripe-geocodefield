<?php

/**
 * Class GeoCodeField
 */
class GeoCodeField extends FormField
{

    private static $allowed_actions = array(
        'validateAddress'
    );

    /**
     * @var TextField
     */
    protected $lon = null;

    /**
     * @var TextField
     */
    protected $lat = null;


    public function __construct($title = null)
    {
        parent::__construct('GeoCodeField', $title);
        $this->setTemplate('GeoCodeField');
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
        return $this;
    }

    public function Field($properties = array())
    {
        Requirements::css('silverstripe-geocodefield/css/geocodefield-input.css');
        Requirements::javascript('silverstripe-geocodefield/javascript/geocodefield-input.js');

        return parent::Field($properties);
    }

    public function AjaxUrl()
    {
        return $this->Link('validateAddress');
    }


    public function validateAddress($request)
    {
        $response = new SS_HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');

        $address = 'Heinigstraße 33, 67059 Ludwigshafen';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf('http://maps.google.com/maps/api/geocode/json?address=%s', urlencode($address))
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        $respObj = json_decode($resp);

        $result = [
            'lat' => $respObj->results[0]->geometry->location->lat,
            'lon' => $respObj->results[0]->geometry->location->lng
        ];

        $response->setBody(Convert::array2json($result));
        return $response;
    }

}

