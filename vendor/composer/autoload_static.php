<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite8c522127cafbb33886c7386d204a89b
{
    public static $files = array (
        '5d9c5be1aa1fbc12016e2c5bd16bbc70' => __DIR__ . '/..' . '/dusank/knapsack/src/collection_functions.php',
        'e5fde315a98ded36f9b25eb160f6c9fc' => __DIR__ . '/..' . '/dusank/knapsack/src/utility_functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPSteak\\' => 8,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Container\\' => 14,
            'PQAD\\' => 5,
        ),
        'L' => 
        array (
            'League\\Container\\' => 17,
        ),
        'D' => 
        array (
            'DusanKasan\\Knapsack\\' => 20,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
            'Cedaro\\WP\\Plugin\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPSteak\\' => 
        array (
            0 => __DIR__ . '/..' . '/apiki/wpsteak/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'PQAD\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'League\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/container/src',
        ),
        'DusanKasan\\Knapsack\\' => 
        array (
            0 => __DIR__ . '/..' . '/dusank/knapsack/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'Cedaro\\WP\\Plugin\\' => 
        array (
            0 => __DIR__ . '/..' . '/cedaro/wp-plugin/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite8c522127cafbb33886c7386d204a89b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite8c522127cafbb33886c7386d204a89b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite8c522127cafbb33886c7386d204a89b::$classMap;

        }, null, ClassLoader::class);
    }
}
