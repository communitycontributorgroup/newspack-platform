<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit91e9b8af0bb7cd68dc5be50c347d4eff
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

        spl_autoload_register(array('ComposerAutoloaderInit91e9b8af0bb7cd68dc5be50c347d4eff', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit91e9b8af0bb7cd68dc5be50c347d4eff', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit91e9b8af0bb7cd68dc5be50c347d4eff::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
