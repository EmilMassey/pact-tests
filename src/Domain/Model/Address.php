<?php

namespace Emil\PactTests\Domain\Model;

class Address
{
    /** @readonly */
    public string $street;
    /** @readonly */
    public string $city;
    /** @readonly */
    public string $postcode;

    public function __construct(string $street, string $city, string $postcode)
    {
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
    }
}