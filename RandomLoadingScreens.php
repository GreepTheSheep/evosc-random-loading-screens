<?php


namespace EvoSC\Modules\RandomLoadingScreens;

use EvoSC\Classes\Hook;
use EvoSC\Classes\Log;
use EvoSC\Classes\Module;
use EvoSC\Interfaces\ModuleInterface;
use EvoSC\Controllers\MatchSettingsController;


class RandomLoadingScreens extends Module implements ModuleInterface
{
    public static array $images = array();
    public static string $scriptSettingName = "script_settings.S_LoadingScreenImageUrl";
    public static string $current = "";
    public static int $currentKey = 0;

    public static function start(string $mode, bool $isBoot = false)
    {
        self::$images = (array)config("RandomLoadingScreens.images");

        if (count(self::$images) < 1) {
            Log::write("ERROR: No images URL found in the config file");
            return;
        }

        self::$scriptSettingName = (string)config("RandomLoadingScreens.script_setting");

        Hook::add('Maniaplanet.Podium_Start', [self::class, 'endMap']);
    }

    public static function endMap(){
        self::$currentKey = (int)array_rand(self::$images);
        self::$current = self::$images[self::$currentKey];

        Log::write("Setting current loading image url to ". self::$current);

        MatchSettingsController::updateSetting(MatchSettingsController::getCurrentMatchSettingsFile(), self::$scriptSettingName, self::$current);
        MatchSettingsController::loadMatchSettings();
    }
}