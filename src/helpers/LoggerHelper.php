<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-ember/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-ember/
 */

namespace flipbox\craft\ember\helpers;

use Craft;
use craft\log\FileTarget;
use yii\log\Logger;
use yii\web\Request;
use yii\web\User;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
class LoggerHelper
{
    public static $requireSession = true;

    /**
     * Takes an array of log categories and creates log target configs
     *
     * @param array $categories
     * @param array $targetConfig
     * @return array
     */
    public static function targetConfigs(array $categories, array $targetConfig = []): array
    {
        $configs = [];

        foreach ($categories as $category) {
            $configs[$category] = static::targetConfig($category, $targetConfig);
        }

        return array_filter($configs);
    }

    /**
     * Takes a log category and creates a log target config
     *
     * @param string $category
     * @param array $targetConfig
     * @return array
     */
    public static function targetConfig(string $category, array $targetConfig = []): array
    {
        // When empty, assume file target
        if (empty($targetConfig) || !isset($targetConfig['class'])) {
            return static::fileTargetConfig($category, $targetConfig);
        }

        return static::bootstrapConfig($category, $targetConfig);
    }

    /**
     * Takes a log category and creates a log target config
     *
     * @param string $category
     * @param array $targetConfig
     * @return array
     * @since 2.3.3
     */
    public static function fileTargetConfig(string $category, array $targetConfig = []): array
    {
        $generalConfig = Craft::$app->getConfig()->getGeneral();

        return static::targetConfig(
            $category,
            array_merge(
                [
                    'class' => FileTarget::class,
                    'logFile' => '@storage/logs/' . $category . '.log',
                    'fileMode' => $generalConfig->defaultFileMode,
                    'dirMode' => $generalConfig->defaultDirMode
                ],
                $targetConfig
            )
        );
    }

    /**
     * @param string $category
     * @param array $config
     * @return array
     * @since 2.6.2
     */
    public static function bootstrapConfig(string $category, array $config = []): array
    {
        $request = Craft::$app->getRequest();
        $user = Craft::$app->getUser();

        // Only log console requests and web requests that aren't getAuthTimeout requests
        $isConsoleRequest = $request instanceof Request && $request->getIsConsoleRequest();
        if (!$isConsoleRequest &&
            (static::$requireSession && ($user instanceof User && !Craft::$app->getUser()->enableSession))
        ) {
            return [];
        }

        $target = [
            'logVars' => [],
            'categories' => [$category, $category . ':*']
        ];

        if (!$isConsoleRequest) {
            // Only log errors and warnings, unless Craft is running in Dev Mode or it's being installed/updated
            if (!YII_DEBUG
                && Craft::$app->getIsInstalled()
                && !Craft::$app->getUpdates()->getIsCraftDbMigrationNeeded()
            ) {
                $target['levels'] = Logger::LEVEL_ERROR | Logger::LEVEL_WARNING;
            }
        }

        return array_merge($target, $config);
    }

    /**
     * @return callable
     *
     * @deprecated
     */
    public static function config(): callable
    {
        return function (string $category, array $config = []) {
            return static::bootstrapConfig($category, $config);
        };
    }
}
