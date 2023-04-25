<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4fb148c6bad0939015fbdfff313d7dd9
{
    public static $files = array (
        'd49adb5b6dd045f81650256f926ee7da' => __DIR__ . '/../..' . '/src/BladeView.php',
    );

    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Aidid\\BladeView\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Aidid\\BladeView\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4fb148c6bad0939015fbdfff313d7dd9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4fb148c6bad0939015fbdfff313d7dd9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4fb148c6bad0939015fbdfff313d7dd9::$classMap;

        }, null, ClassLoader::class);
    }
}