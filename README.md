# silverstripe-geocodefield

Fetches the geo position (lon,lat) from google maps api.
Can be used as free address input field or referenced to other address fields in form

## Requirements
* Silverstripe 4.* or Silverstripe 3.*(use tag 0.3.0 for v3.x support)

## Installation
### Composer
* `composer require "nblum/silverstripe-geocodefield"`

### Manual
* Download/Clone module in SilverStripe root directory

## Usage

Configuration:

You may need to provide an api key from google

```yml
Nblum\Geocodefield\Forms\GeoCodeField:
  google_api_key: 'your_google_maps_api_ke'
```

...or try to geocode with Nominatim from OpenStreetMap https://wiki.openstreetmap.org/wiki/Nominatim

```yml
Nblum\Geocodefield\Forms\GeoCodeField:
  custom_geocoder: 'osm'
```


Basic Example:

```php

    class MyPage extends Page {
    
        private static $db = array(
            'Geodata' => \Nblum\Geocodefield\Forms\Json::class
        );
    
        public function getCMSFields() {
            $fields = parent::getCMSFields();
    
            //creates a GeoCodeField field
            $fields->addFieldToTab('Root.Main', new \Nblum\Geocodefield\Forms\GeoCodeField('Geodata'));
    
            return $fields;
        }
    }
    
```

Example with referenced address fields:

```php

    class MyPage extends Page {
    
        private static $db = array(
            'Street' => 'Varchar',
            'City' => 'Varchar',
            'Geodata' => 'Json'
        );
    
        public function getCMSFields() {
            $fields = parent::getCMSFields();
    
            $fields->addFieldToTab('Root.Main', new TextField('Street'));
            $fields->addFieldToTab('Root.Main', new TextField('City'));
    
            //creates a GeoCodeField field
            $field = new \Nblum\Geocodefield\Forms\GeoCodeField('Geodata', 'Geo Position');
            $field->addAddressReference('Street');
            $field->addAddressReference('City');
            $field->setAddressNotEditable();
            $fields->addFieldToTab('Root.Main', $field);
    
            return $fields;
        }
    }
    
```

Write lon/lat values to separate db columns

```php

    class MyPage extends Page {
    
        private static $db = array(
            'Lat' => 'Varchar',
            'Lon' => 'Varchar',
            'GeoData' => 'Json'
        );
        
        public function getCMSFields() {
            //...
        }
        
        protected function onBeforeWrite()
        {
            parent::onBeforeWrite();
            
            //get current values and update some custom fields
            $parts = json_decode($this->getField('GeoData'));
            $this->setField('Lat', $parts->lat);
            $this->setField('Lon', $parts->lon);
        }
    }

```
