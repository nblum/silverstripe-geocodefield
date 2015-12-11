# silverstripe-geocodefield

Fetches the geo position (lon,lat) from google maps api.
Can be used as free address input field or referenced to other address fields in form

## Requirements
* Silverstripe 3.*

## Installation
### Composer
* `composer require "nblum/silverstripe-geocodefield"`

### Manual
* Download and copy module in SilverStripe root directory

## Usage

Basic Example:

```php

    private static $db = array(
        'Geodata' => 'Json'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        //create a GeoCodeField field
        $fields->addFieldToTab('Root.Main', new GeoCodeField('Geodata'));

        return $fields;
    }
    
```

Example with referenced address fields:

```php

    private static $db = array(
        'Street' => 'Varchar(64)',
        'City' => 'Varchar(64)',
        'Geodata' => 'Varchar(Json)'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', new TextField('Street'));
        $fields->addFieldToTab('Root.Main', new TextField('City'));

        //create a GeoCodeField field
        $field = new GeoCodeField('Geodata', 'Geo Position');
        $field->addAddressReference('Street');
        $field->addAddressReference('City');
        $field->setAddressNotEditable();
        $fields->addFieldToTab('Root.Main', $field);

        return $fields;
    }
    
```