<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

<<<<<<< HEAD
class ComposerStaticInit90bbf95869b4e88bf0dc5022e708a458
=======
class ComposerStaticInit80fa5acfa7add4a320748b43bbcd35be
>>>>>>> origin/dance_final_system
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
<<<<<<< HEAD
            $loader->prefixLengthsPsr4 = ComposerStaticInit90bbf95869b4e88bf0dc5022e708a458::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit90bbf95869b4e88bf0dc5022e708a458::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit90bbf95869b4e88bf0dc5022e708a458::$classMap;
=======
            $loader->prefixLengthsPsr4 = ComposerStaticInit80fa5acfa7add4a320748b43bbcd35be::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit80fa5acfa7add4a320748b43bbcd35be::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit80fa5acfa7add4a320748b43bbcd35be::$classMap;
>>>>>>> origin/dance_final_system

        }, null, ClassLoader::class);
    }
}
