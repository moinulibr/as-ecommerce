<?php

namespace App\Utils;

use InvalidArgumentException;

class UserType
{
    /*
    |--------------------------------------------------------------------------
    | Access Type
    |--------------------------------------------------------------------------
    */

    public const INTERNAL_ACCESS_TYPE = 1;
    public const EXTERNAL_ACCESS_TYPE = 2;


    /*
    |--------------------------------------------------------------------------
    | User Type
    |--------------------------------------------------------------------------
    */

    public const ADMIN              = 1;
    public const STAFF              = 2;
    public const VENDOR             = 3;
    public const SR                 = 4;
    public const RETAILER           = 5;
    public const SUPPLIER           = 6;
    public const ECOMMERCE_CUSTOMER = 7;
    public const POS_CUSTOMER       = 8;
    public const RESELLER           = 9;
    public const DELIVERY_MAN       = 10;
    public const PLUMBER            = 11;
    public const GUEST              = 12;
    public const OTHERS             = 13;


    /*
    |--------------------------------------------------------------------------
    | User Type List
    |--------------------------------------------------------------------------
    */

    public static function list(): array
    {
        return [
            self::ADMIN              => 'Admin',
            self::STAFF              => 'Staff',
            self::VENDOR             => 'Vendor',
            self::SR                 => 'Sales Representative (SR)',
            self::RETAILER           => 'Retailer',
            self::SUPPLIER           => 'Supplier',
            self::ECOMMERCE_CUSTOMER => 'Ecommerce Customer',
            self::POS_CUSTOMER       => 'POS Customer',
            self::RESELLER           => 'Reseller',
            self::DELIVERY_MAN       => 'Delivery Man',
            self::PLUMBER            => 'Plumber',
            self::GUEST              => 'Guest',
            self::OTHERS             => 'Others',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve User Type
    |--------------------------------------------------------------------------
    |
    | Supports:
    |
    |   UserType::resolve('Admin');
    |   UserType::resolve('Office Staff');
    |   UserType::resolve(['Office Staff']);
    |
    | If no match is found:
    |
    |   UserType::resolve('Unknown');
    |       -> throws InvalidArgumentException
    |
    | With default:
    |
    |   UserType::resolve('Unknown', self::STAFF);
    |       -> returns 2
    |
    */

    public static function resolve(mixed $roles, ?int $default = null): int
    {
        /*
        |--------------------------------------------------------------------------
        | Convert Single Value To Array
        |--------------------------------------------------------------------------
        */

        if (!is_array($roles)) {
            $roles = [$roles];
        }


        /*
        |--------------------------------------------------------------------------
        | Remove Empty / Invalid Values
        |--------------------------------------------------------------------------
        */

        $roles = array_values(array_filter(
            $roles,
            fn($role) => is_string($role) && trim($role) !== ''
        ));


        /*
        |--------------------------------------------------------------------------
        | Normalize Role Names
        |--------------------------------------------------------------------------
        */

        foreach ($roles as $role) {

            $role = self::normalize($role);

            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'admin',
                'administrator',
                'admin user',
            ])) {
                return self::ADMIN;
            }


            /*
            |--------------------------------------------------------------------------
            | Sales Representative
            |--------------------------------------------------------------------------
            */

            if (
                self::matches($role, [
                    'sr',
                    'sales representative',
                    'sales rep',
                    'sales representative sr',
                ])
            ) {
                return self::SR;
            }


            /*
            |--------------------------------------------------------------------------
            | Vendor
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'vendor',
                'vendor manager',
                'vendor user',
            ])) {
                return self::VENDOR;
            }


            /*
            |--------------------------------------------------------------------------
            | Retailer
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'retailer',
                'retailer user',
            ])) {
                return self::RETAILER;
            }


            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'supplier',
                'supplier user',
            ])) {
                return self::SUPPLIER;
            }


            /*
            |--------------------------------------------------------------------------
            | Ecommerce Customer
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'ecommerce customer',
                'e commerce customer',
                'e-commerce customer',
            ])) {
                return self::ECOMMERCE_CUSTOMER;
            }


            /*
            |--------------------------------------------------------------------------
            | POS Customer
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'pos customer',
                'pos-customer',
                'pos user',
            ])) {
                return self::POS_CUSTOMER;
            }


            /*
            |--------------------------------------------------------------------------
            | Reseller
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'reseller',
                'reseller user',
            ])) {
                return self::RESELLER;
            }


            /*
            |--------------------------------------------------------------------------
            | Delivery Man
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'delivery man',
                'delivery-man',
                'delivery person',
                'delivery user',
            ])) {
                return self::DELIVERY_MAN;
            }


            /*
            |--------------------------------------------------------------------------
            | Plumber
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'plumber',
                'plumber user',
            ])) {
                return self::PLUMBER;
            }


            /*
            |--------------------------------------------------------------------------
            | Guest
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'guest',
                'guest user',
            ])) {
                return self::GUEST;
            }


            /*
            |--------------------------------------------------------------------------
            | Staff
            |--------------------------------------------------------------------------
            |
            | "Office Staff" -> STAFF
            |
            */

            if (self::matches($role, [
                'staff',
                'office staff',
                'office-staff',
                'staff user',
            ])) {
                return self::STAFF;
            }


            /*
            |--------------------------------------------------------------------------
            | Others
            |--------------------------------------------------------------------------
            */

            if (self::matches($role, [
                'other',
                'others',
            ])) {
                return self::OTHERS;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        if ($default !== null) {
            return $default;
        }


        /*
        |--------------------------------------------------------------------------
        | No Match
        |--------------------------------------------------------------------------
        */

        throw new InvalidArgumentException(
            'User type does not match any supported user type.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Role
    |--------------------------------------------------------------------------
    */

    private static function normalize(string $role): string
    {
        $role = trim($role);

        $role = preg_replace('/\s+/', ' ', $role);

        return strtolower($role);
    }


    /*
    |--------------------------------------------------------------------------
    | Match Role
    |--------------------------------------------------------------------------
    |
    | Exact match অথবা meaningful phrase match.
    |
    */

    private static function matches(string $role, array $aliases): bool
    {
        foreach ($aliases as $alias) {

            $alias = self::normalize($alias);

            /*
            |--------------------------------------------------------------------------
            | Exact Match
            |--------------------------------------------------------------------------
            */

            if ($role === $alias) {
                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | Phrase Match
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | "Office Staff Member"
            | contains "office staff"
            |
            */

            if (
                strlen($alias) >= 4 &&
                preg_match(
                    '/(^|\s)' . preg_quote($alias, '/') . '($|\s)/',
                    $role
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * ইউজার টাইপ আইডি থেকে নাম পাওয়ার সেন্ট্রাল লজিক
     */
    public static function getLabel(?int $userTypeId): string
    {
        if (is_null($userTypeId)) {
            return 'Guest';
        }
        return self::list()[$userTypeId] ?? 'Unknown Type';
    }

    /**
     * কাস্টমার গ্রুপ চেকিং (ভবিষ্যতে নতুন কোনো কাস্টমার টাইপ আসলে শুধু এই অ্যারেতে বসাবেন)
     */
    public static function isCustomer(?int $userTypeId): bool
    {
        return in_array($userTypeId, [
            self::ECOMMERCE_CUSTOMER,
            self::POS_CUSTOMER,
            self::RESELLER
        ]);
    }

    /**
     * ইন্টারনাল স্টাফ চেকিং (কারা ব্যাকঅফিস বা সিস্টেম কন্ট্রোল করতে পারে)
     */
    public static function isInternalStaff(?int $userTypeId): bool
    {
        return in_array($userTypeId, [
            self::ADMIN,
            self::STAFF
        ]);
    }


    /*views/pos/create.blade.php
    <option value="1"> Ecommerce Register </option>
    <option value="2"> Socialite Add  </option>
    <option value="3"> Admin Panel </option>
    <option value="4"> SR Panel </option>*/
    //customer added from

    public const CUSTOMER_ADDED_FROM_ECOMMERCE = 1;
    public const CUSTOMER_ADDED_FROM_ECOMMERCE_SOCIALITE = 2;
    public const CUSTOMER_ADDED_FROM_ADMIN = 3;
    public const CUSTOMER_ADDED_FROM_SR = 4;
    public const CUSTOMER_ADDED_FROM_RESELLER = 5;
}
