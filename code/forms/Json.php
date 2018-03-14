<?php

namespace Nblum\Geocodefield\Forms;

use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\View\ArrayData;

class Json extends DBText
{

    public function Data()
    {
        $obj = json_decode($this->value);
        if (!is_array($obj) && !is_object($obj)) {
            return '';
        }
        return new ArrayData($obj);
    }

}