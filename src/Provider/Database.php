<?php

namespace Emil\PactTests\Provider;

use Emil\PactTests\Domain\Model\Address;

final class Database
{
    const PATH = '/tmp/provider_database';

    /**
     * @readonly
     * @var array<string, Address>
     */
    public array $addresses = [];

    public function __construct()
    {
        $content = @file_get_contents(self::PATH);

        if ($content && $addresses = @unserialize($content)) {
            $this->addresses = $addresses;
        }
    }

    public function addAddress(string $id, Address $address): void
    {
        $this->addresses[$id] = $address;
        $this->save();
    }

    public function getAddress(string $id): ?Address
    {
        if (!array_key_exists($id, $this->addresses)) {
            return null;
        }

        return $this->addresses[$id];
    }

    public function removeAddress(string $id): bool
    {
        if (array_key_exists($id, $this->addresses)) {
            unset($this->addresses[$id]);
            $this->save();

            return true;
        }

        return false;
    }

    public function clear(): void
    {
        $this->addresses = [];
        $this->save();
    }

    private function save(): void
    {
        file_put_contents(self::PATH, serialize($this->addresses));
    }
}