<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit887d5082acd72cb471bf61a6ad16f980
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit887d5082acd72cb471bf61a6ad16f980::$classMap;

        }, null, ClassLoader::class);
    }
}
