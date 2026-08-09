<?php

namespace App\Utils;

class UserType
{
    //access_type
    const INTERNAL_ACCESS_TYPE = 1;
    const EXTERNAL_ACCESS_TYPE = 2;

    // ১. সেন্ট্রাল আইডি ডেফিনিশন (ভবিষ্যতে আইডি চেঞ্জ হলে শুধু এখানে চেঞ্জ করবেন)
    const ADMIN              = 1;
    const STAFF              = 2;
    const VENDOR             = 3;
    const SR                 = 4; // Sales Representative
    const RETAILER           = 5;
    const SUPPLIER           = 6;
    const ECOMMERCE_CUSTOMER = 7;
    const POS_CUSTOMER       = 8;
    const RESELLER           = 9;
    const DELIVERY_MAN       = 10;
    const PLUMBER            = 11;
    const GUEST              = 12;
    const OTHERS             = 13;

    /**
     * সেন্ট্রাল লিস্ট (ড্রপডাউন বা UI এর নাম চেঞ্জ করতে হলে শুধু এখানে চেঞ্জ করবেন)
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
}
