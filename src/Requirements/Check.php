<?php

namespace App\Requirements;

class Check
{
    const STATE_OK = 'OK';
    const STATE_WARNING = 'WARNING';
    const STATE_ERROR = 'ERROR';

    /**
     * @var array
     */
    private $data;

    /**
     * Check constructor.
     * @param  array  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  array  $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


}