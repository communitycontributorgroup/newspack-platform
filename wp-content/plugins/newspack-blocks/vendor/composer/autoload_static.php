<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInited31aaab4ad399c15f9f84cc5fecbd40
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInited31aaab4ad399c15f9f84cc5fecbd40::$classMap;

        }, null, ClassLoader::class);
    }
}
