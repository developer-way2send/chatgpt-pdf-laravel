<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd37033681354c4930ae541f4229e41d4
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Process\\' => 26,
            'Spatie\\PdfToText\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Spatie\\PdfToText\\' => 
        array (
            0 => __DIR__ . '/..' . '/spatie/pdf-to-text/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd37033681354c4930ae541f4229e41d4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd37033681354c4930ae541f4229e41d4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd37033681354c4930ae541f4229e41d4::$classMap;

        }, null, ClassLoader::class);
    }
}
