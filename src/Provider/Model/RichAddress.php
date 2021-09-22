<?php

namespace Emil\PactTests\Provider\Model;

use Emil\PactTests\Domain\Model\Address;

class RichAddress extends Address
{
    public ?string $firstname = null;
    public ?string $lastname = null;

    public static function createFromParent(Address $parent, ?string $firstname, ?string $lastname): self
    {
        $address = new self($parent->street, $parent->city, $parent->postcode);
        $address->firstname = $firstname;
        $address->lastname = $lastname;

        return $address;
    }
}