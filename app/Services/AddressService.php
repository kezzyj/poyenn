<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Customer;

class AddressService
{
    public function getAddresses(Customer $customer)
    {
        return $customer->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
    }

    public function getDefaultAddress(Customer $customer): ?Address
    {
        return $customer->addresses()->where('is_default', true)->first()
            ?? $customer->addresses()->latest()->first();
    }

    public function create(Customer $customer, array $data): Address
    {
        $data['customer_id'] = $customer->id;
        $data['is_default'] = $data['is_default'] ?? false;

        // If this is the first address, make it default automatically
        if ($customer->addresses()->doesntExist()) {
            $data['is_default'] = true;
        }

        return Address::create($data);
    }
}