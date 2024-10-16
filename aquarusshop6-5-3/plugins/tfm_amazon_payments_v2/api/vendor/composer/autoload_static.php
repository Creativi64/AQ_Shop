<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4b52d2748ef705c413e4b0cc26cffe81
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib3\\' => 11,
        ),
        'P' => 
        array (
            'ParagonIE\\ConstantTime\\' => 23,
        ),
        'A' => 
        array (
            'Amazon\\Pay\\API\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib3\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'ParagonIE\\ConstantTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/paragonie/constant_time_encoding/src',
        ),
        'Amazon\\Pay\\API\\' => 
        array (
            0 => __DIR__ . '/..' . '/amzn/amazon-pay-api-sdk-php/Amazon/Pay/API',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4b52d2748ef705c413e4b0cc26cffe81::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4b52d2748ef705c413e4b0cc26cffe81::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
