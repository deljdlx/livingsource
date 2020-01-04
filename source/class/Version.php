<?php

namespace ElBiniou\LivingSource;

class Version implements \JsonSerializable
{

    protected $id;
    protected $datetime;
    protected $name;
    protected $description;
    protected $content;
    protected $checksum;



    public function loadFromFile($file)
    {
        $data = json_decode(
            file_get_contents($file),
            true
        );
        $this->loadFromArray($data);

        return $this;
    }

    public function loadFromArray($data)
    {
        foreach ($data as $key  => $value) {
            $this->$key = $value;
        }
    }



    public function getChecksum()
    {
        if(!$this->checksum) {
            $this->checksum = md5($this->content) . '-' . sha1($this->content);
        }
        return $this->checksum;
    }

    public function jsonSerialize()
    {
        return [
            'id' => uniqid(true),
            'datetime' => date('Y-m-d H:i:s'),
            'name' => $this->name,
            'description' => $this->description,
            'content' => $this->content,
            'checksum' => $this->getChecksum(),
        ];
    }


}

