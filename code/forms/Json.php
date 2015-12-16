<?php


class Json extends Text
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