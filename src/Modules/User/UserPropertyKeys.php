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
        self::$EMAIL = PropertyKey::string('user.email');
        self::$AGE = PropertyKey::int('user.age');
        self::$IS_PREMIUM = PropertyKey::bool('user.is_premium');
        self::$PHONE = PropertyKey::string('user.phone');
        self::$REGISTRATION_DATE = PropertyKey::string('user.registration_date');
    }
}

UserPropertyKeys::initialize();
