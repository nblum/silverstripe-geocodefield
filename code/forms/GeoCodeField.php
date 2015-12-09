<?php

/**
 * Class GeoCodeField
 */
class GeoCodeField extends FormField
{
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

}

