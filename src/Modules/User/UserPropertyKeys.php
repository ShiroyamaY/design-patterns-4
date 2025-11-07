<?php

namespace DynamicProperties\Modules\User;

use DynamicProperties\Core\PropertyKey;

class UserPropertyKeys
{

    public static PropertyKey $EMAIL;

    public static PropertyKey $AGE;

    public static PropertyKey $IS_PREMIUM;

    public static PropertyKey $PHONE;

    public static PropertyKey $REGISTRATION_DATE;

    public static function initialize(): void
    {
        self::$EMAIL = PropertyKey::create('user.email');
        self::$AGE = PropertyKey::create('user.age');
        self::$IS_PREMIUM = PropertyKey::create('user.is_premium');
        self::$PHONE = PropertyKey::create('user.phone');
        self::$REGISTRATION_DATE = PropertyKey::create('user.registration_date');
    }
}

UserPropertyKeys::initialize();