<?php

namespace Emil\PactTests\Domain\Model;

class Address
{
    /** @readonly */
    public string $street;
    /** @readonly */
    public string $city;
    /** @readonly */
    public string $postalCode;

    public function __construct(string $street, string $city, string $postalCode)
    {
        $this->street = $street;
        $this->city = $city;
        $this->postalCode = $postalCode;
    }
}