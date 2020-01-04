<?php

namespace ElBiniou\LivingSource;

class Event
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function getData()
    {
        return $this->data;
    }


    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function __get($name)
    {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

}
