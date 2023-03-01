<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6499d2e6d85facb0e2a4a98dc37b260f
{
    public static $files = array (
        'eccc0347283a01e62f5536bcf76b6e62' => __DIR__ . '/..' . '/wikimedia/at-ease/src/Wikimedia/Functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wikimedia\\CSS\\' => 14,
            'Wikimedia\\AtEase\\' => 17,
            'Wikimedia\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wikimedia\\CSS\\' => 
        array (
            0 => __DIR__ . '/..' . '/wikimedia/css-sanitizer/src',
        ),
        'Wikimedia\\AtEase\\' => 
        array (
            0 => __DIR__ . '/..' . '/wikimedia/at-ease/src/Wikimedia/AtEase',
        ),
        'Wikimedia\\' => 
        array (
            0 => __DIR__ . '/..' . '/wikimedia/scoped-callback/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'UtfNormal\\Constants' => __DIR__ . '/..' . '/wikimedia/utfnormal/src/Constants.php',
        'UtfNormal\\Utils' => __DIR__ . '/..' . '/wikimedia/utfnormal/src/Utils.php',
        'UtfNormal\\Validator' => __DIR__ . '/..' . '/wikimedia/utfnormal/src/Validator.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6499d2e6d85facb0e2a4a98dc37b260f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6499d2e6d85facb0e2a4a98dc37b260f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6499d2e6d85facb0e2a4a98dc37b260f::$classMap;

        }, null, ClassLoader::class);
    }
}
