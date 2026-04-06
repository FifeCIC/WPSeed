<?php

// autoload_real.php — WPSeed PSR-4 bootstrap.
//
// Multiple plugins on the same WordPress install each ship a Composer autoloader.
// Creating a second ClassLoader instance and registering it causes files already
// loaded by the first autoloader to be included again, breaking namespace
// declarations that must appear before any other statement.
//
// The correct approach: find the ClassLoader already registered via spl_autoload,
// and add WPSeed's PSR-4 prefix to it. If no ClassLoader exists yet (WPSeed is
// the first plugin to load), create one and register it.

class ComposerAutoloaderInitWPSeed {

    /** @var \Composer\Autoload\ClassLoader|null */
    private static $loader = null;

    /**
     * Register the WPSeed\ PSR-4 prefix with the active Composer ClassLoader.
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader() {
        if ( null !== self::$loader ) {
            return self::$loader;
        }

        $map = require __DIR__ . '/autoload_psr4.php';

        // Try to find an already-registered Composer ClassLoader so we can
        // add our prefix to it rather than registering a second autoloader.
        $existing = self::find_registered_loader();

        if ( $existing ) {
            foreach ( $map as $namespace => $path ) {
                $existing->addPsr4( $namespace, $path );
            }
            self::$loader = $existing;
            return self::$loader;
        }

        // No existing loader found — we are the first. Load ClassLoader and
        // register a new instance.
        if ( ! class_exists( 'Composer\Autoload\ClassLoader', false ) ) {
            require __DIR__ . '/ClassLoader.php';
        }

        $loader = new \Composer\Autoload\ClassLoader();
        foreach ( $map as $namespace => $path ) {
            $loader->addPsr4( $namespace, $path );
        }
        $loader->register( true );

        self::$loader = $loader;
        return self::$loader;
    }

    /**
     * Search the spl_autoload stack for a Composer ClassLoader instance.
     *
     * @return \Composer\Autoload\ClassLoader|null
     */
    private static function find_registered_loader() {
        foreach ( spl_autoload_functions() as $fn ) {
            if ( is_array( $fn ) && $fn[0] instanceof \Composer\Autoload\ClassLoader ) {
                return $fn[0];
            }
        }
        return null;
    }
}
