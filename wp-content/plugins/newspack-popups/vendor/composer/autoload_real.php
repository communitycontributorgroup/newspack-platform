<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitacba02f7e21b9d7f1a1e9727ddcaae78
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitacba02f7e21b9d7f1a1e9727ddcaae78', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitacba02f7e21b9d7f1a1e9727ddcaae78', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitacba02f7e21b9d7f1a1e9727ddcaae78::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
