<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5bd886cce1a2cef64b2d43e5961f3197
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5bd886cce1a2cef64b2d43e5961f3197::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5bd886cce1a2cef64b2d43e5961f3197::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}