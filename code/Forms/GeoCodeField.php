<?php

namespace Nblum\Geocodefield\Forms;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

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
     * @var bool
     */
    protected $apiAddressVisible = true;

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
        $this->setAttribute('data-field', 'jsondata');
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
     * address input field will be not editable
     */
    public function setApiAddressInvisible()
    {
        $this->apiAddressVisible = false;
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
        $this->address->setAttribute('data-field', 'addressinput');
        $this->address->setAttribute('placeholder', _t('GeoCodeField.Address', 'Enter address (street, postcode city, country'));
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
        $this->lon->setAttribute('data-field', 'lon');
        $this->lon->setAttribute('placeholder', _t('GeoCodeField.Lon', 'Longitude'));
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
        $this->lat->setAttribute('data-field', 'lat');
        $this->lat->setAttribute('placeholder', _t('GeoCodeField.Lat', 'Latitude'));
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
        Requirements::css('nblum/silverstripe-geocodefield:css/geocodefield-input.css');
        Requirements::javascript('nblum/silverstripe-geocodefield:javascript/geocodefield-input.js');

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
     * @param HTTPRequest $request
     * @return HTTPResponse
     */
    public function validateAddress(HTTPRequest $request)
    {
        $response = new HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');

        $address = filter_var($request->requestVar('address'), FILTER_SANITIZE_STRING);

        // use nominatim.openstreetmap.org
        if ($this->config()->get('custom_geocoder') == 'osm') {
            $result = $this->validateAddressWithOsm($address);
            $response->setBody(Convert::array2json($result));
            return $response;
        }

        // default: use google geocoder
        $result = $this->validateAddressWithGoogle($address);

        $response->setBody(Convert::array2json($result));
        return $response;
    }

    /**
     * Get Geodata from nominatim.openstreetmap.org
     * Note: we provide a referer to not get blocked (so quickly)
     * @param $address
     * @return array
     */
    public function validateAddressWithGoogle($address) {

        $google_api_key = $this->config()->get('google_api_key');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf('https://maps.google.com/maps/api/geocode/json?address=%s&key=%s', urlencode($address), $google_api_key)
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        $respObj = json_decode($resp);
        //        var_dump($respObj);die();
        if (property_exists($respObj, 'error_message')) {
            return [
                'search_address' => $address,
                'formatted_address' => 'API ERROR: ' . $respObj->error_message,
                'lat' => 0,
                'lon' => 0
            ];
        }

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

        return $result;
    }

    /**
     * Get Geodata from nominatim.openstreetmap.org
     * Note: we provide a referer to not get blocked (so quickly)
     * @param $address
     * @return array
     */
    public function validateAddressWithOsm($address) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf('https://nominatim.openstreetmap.org/search?format=json&q=%s', urlencode($address)),
            CURLOPT_REFERER => Director::protocolAndHost()
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        $respObj = json_decode($resp);

        if (count($respObj) > 0) {
            $result = array(
                'search_address' => $address,
                'formatted_address' => $respObj[0]->display_name,
                'lat' => $respObj[0]->lat,
                'lon' => $respObj[0]->lon
            );
        } else {
            $result = array(
                'search_address' => $address,
                'formatted_address' => _t('GeoCodeField.NoResults', '- No Data Found - '),
                'lat' => 0,
                'lon' => 0
            );
        }

        return $result;
    }

}

