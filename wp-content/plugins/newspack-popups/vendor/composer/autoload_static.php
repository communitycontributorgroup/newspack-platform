<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67b5b90f2fa7051628f1c7cb8746e686
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DrewM\\MailChimp\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DrewM\\MailChimp\\' => 
        array (
            0 => __DIR__ . '/..' . '/drewm/mailchimp-api/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Newspack\\Campaigns\\CLI\\Export' => __DIR__ . '/../..' . '/includes/cli/class-export.php',
        'Newspack\\Campaigns\\CLI\\Import' => __DIR__ . '/../..' . '/includes/cli/class-import.php',
        'Newspack\\Campaigns\\CLI\\Prune_Data' => __DIR__ . '/../..' . '/includes/cli/class-prune-data.php',
        'Newspack\\Campaigns\\Schema' => __DIR__ . '/../..' . '/includes/schemas/class-schema.php',
        'Newspack\\Campaigns\\Schemas\\Campaigns' => __DIR__ . '/../..' . '/includes/schemas/class-campaigns.php',
        'Newspack\\Campaigns\\Schemas\\Package' => __DIR__ . '/../..' . '/includes/schemas/class-package.php',
        'Newspack\\Campaigns\\Schemas\\Prompts' => __DIR__ . '/../..' . '/includes/schemas/class-prompts.php',
        'Newspack\\Campaigns\\Schemas\\Segments' => __DIR__ . '/../..' . '/includes/schemas/class-segments.php',
        'Newspack_Popups' => __DIR__ . '/../..' . '/includes/class-newspack-popups.php',
        'Newspack_Popups_API' => __DIR__ . '/../..' . '/includes/class-newspack-popups-api.php',
        'Newspack_Popups_Criteria' => __DIR__ . '/../..' . '/includes/class-newspack-popups-criteria.php',
        'Newspack_Popups_Custom_Placements' => __DIR__ . '/../..' . '/includes/class-newspack-popups-custom-placements.php',
        'Newspack_Popups_Data_Api' => __DIR__ . '/../..' . '/includes/class-newspack-popups-data-api.php',
        'Newspack_Popups_Exporter' => __DIR__ . '/../..' . '/includes/class-newspack-popups-exporter.php',
        'Newspack_Popups_Importer' => __DIR__ . '/../..' . '/includes/class-newspack-popups-importer.php',
        'Newspack_Popups_Inserter' => __DIR__ . '/../..' . '/includes/class-newspack-popups-inserter.php',
        'Newspack_Popups_Logger' => __DIR__ . '/../..' . '/includes/class-newspack-popups-logger.php',
        'Newspack_Popups_Model' => __DIR__ . '/../..' . '/includes/class-newspack-popups-model.php',
        'Newspack_Popups_Presets' => __DIR__ . '/../..' . '/includes/class-newspack-popups-presets.php',
        'Newspack_Popups_Segmentation' => __DIR__ . '/../..' . '/includes/class-newspack-popups-segmentation.php',
        'Newspack_Popups_Settings' => __DIR__ . '/../..' . '/includes/class-newspack-popups-settings.php',
        'Newspack_Popups_View_As' => __DIR__ . '/../..' . '/includes/class-newspack-popups-view-as.php',
        'Newspack_Segments_Migration' => __DIR__ . '/../..' . '/includes/class-newspack-segments-migration.php',
        'Newspack_Segments_Model' => __DIR__ . '/../..' . '/includes/class-newspack-segments-model.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67b5b90f2fa7051628f1c7cb8746e686::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67b5b90f2fa7051628f1c7cb8746e686::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit67b5b90f2fa7051628f1c7cb8746e686::$classMap;

        }, null, ClassLoader::class);
    }
}
