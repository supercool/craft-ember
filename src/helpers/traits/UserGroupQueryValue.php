<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-ember/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-ember
 */

namespace flipbox\ember\helpers\traits;

use craft\db\Query;
use craft\helpers\Db;
use craft\models\UserGroup;
use craft\helpers\ArrayHelper;
use flipbox\ember\helpers\QueryHelper;
use craft\records\UserGroup as UserGroupRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UserGroupQueryValue
{
    /**
     * @param $value
     * @param string $join
     * @return array
     */
    public static function parseUserGroupValue($value, string $join = 'and'): array
    {
        if (false === QueryHelper::parseBaseParam($value, $join)) {
            foreach ($value as $operator => &$v) {
                self::resolveUserGroupValue($operator, $v);
            }
        }

        // parse param to allow for mixed variables
        return array_merge([$join], ArrayHelper::filterEmptyStringsFromArray($value));
    }

    /**
     * @param $operator
     * @param $value
     */
    private static function resolveUserGroupValue($operator, &$value)
    {
        if (false === QueryHelper::findParamValue($value, $operator)) {
            if (is_string($value)) {
                $value = (new Query())
                    ->select(['id'])
                    ->from([UserGroupRecord::tableName()])
                    ->where(Db::parseParam('handle', $value))
                    ->scalar();
            }

            if ($value instanceof UserGroup) {
                $value = $value->id;
            }

            if ($value) {
                $value = QueryHelper::assembleParamValue($value, $operator);
            }
        }
    }
}
