<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-ember/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-ember
 */

namespace flipbox\ember\db\traits;

use craft\models\UserGroup;
use craft\db\Query;
use craft\helpers\Db;
use flipbox\ember\helpers\ArrayHelper;
use flipbox\ember\helpers\QueryHelper;
use craft\records\UserGroup as UserGroupRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UserGroupAttribute
{
    /**
     * The user group(s) that the resulting organizations’ users must be in.
     *
     * @var string|string[]|int|int[]|UserGroup|UserGroup[]|null
     */
    public $userGroup;

    /**
     * @param string|string[]|int|int[]|UserGroup|UserGroup[]|null $value
     * @return static The query object
     */
    public function setUserGroup($value)
    {
        $this->userGroup = $value;
        return $this;
    }

    /**
     * @param string|string[]|int|int[]|UserGroup|UserGroup[]|null $value
     * @return static The query object
     */
    public function userGroup($value)
    {
        return $this->setUserGroup($value);
    }

    /**
     * @param string|string[]|int|int[]|UserGroup|UserGroup[]|null $value
     * @return static The query object
     */
    public function setUserGroupId($value)
    {
        return $this->setUserGroup($value);
    }

    /**
     * @param string|string[]|int|int[]|UserGroup|UserGroup[]|null $value
     * @return static The query object
     */
    public function userGroupId($value)
    {
        return $this->setUserGroup($value);
    }

    /**
     * @param $value
     * @param string $join
     * @return array
     */
    public function parseUserGroupValue($value, string $join = 'or'): array
    {
        if (false === QueryHelper::parseBaseParam($value, $join)) {
            foreach ($value as $operator => &$v) {
                $this->resolveUserGroupValue($operator, $v);
            }
        }

        $value = ArrayHelper::filterEmptyAndNullValuesFromArray($value);

        if (empty($value)) {
            return [];
        }

        // parse param to allow for mixed variables
        return array_merge([$join], $value);
    }

    /**
     * @param $operator
     * @param $value
     */
    private function resolveUserGroupValue($operator, &$value)
    {
        if (false === QueryHelper::findParamValue($value, $operator)) {
            if (is_string($value)) {
                $value = $this->resolveUserGroupStringValue($value);
            }

            if ($value instanceof UserGroup) {
                $value = $value->id;
            }

            if ($value) {
                $value = QueryHelper::assembleParamValue($value, $operator);
            }
        }
    }

    /**
     * @param string $value
     * @return string|null
     */
    protected function resolveUserGroupStringValue(string $value)
    {
        $value = (new Query())
            ->select(['id'])
            ->from([UserGroupRecord::tableName()])
            ->where(Db::parseParam('handle', $value))
            ->scalar();
        return empty($value) ? false : $value;
    }
}
