<?php

namespace Emil\PactTests\Provider\Model;

use Emil\PactTests\Domain\Model\Address;

class RichAddress
{
    public string $street;
    public string $city;
    public string $postalCode;
    public ?string $firstname = null;
    public ?string $lastname = null;

    public function __construct(string $street, string $city, string $postalCode, ?string $firstname = null, ?string $lastname = null)
    {
        $this->street = $street;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public static function createFromAddress(Address $address, ?string $firstname, ?string $lastname): self
    {
        $address = new self($address->street, $address->city, $address->postalCode);
        $address->firstname = $firstname;
        $address->lastname = $lastname;

        return $address;
    }
}