<?php


class Json extends Text
{

    public function Values()
    {
        $obj = json_decode($this->value);
        var_dump($obj);
        if(!is_array($obj) && !is_object($obj)) {
            return '';
        }
        return new ArrayData($obj);
    }

}