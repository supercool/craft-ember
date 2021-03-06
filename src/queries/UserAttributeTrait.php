<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipboxfactory/craft-ember/blob/master/LICENSE
 * @link       https://github.com/flipboxfactory/craft-ember/
 */

namespace flipbox\craft\ember\queries;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\elements\User as UserElement;
use craft\records\User as UserRecord;
use flipbox\craft\ember\helpers\QueryHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
trait UserAttributeTrait
{
    /**
     * The user(s) that the resulting organizations’ users must have.
     *
     * @var string|string[]|int|int[]|UserElement|UserElement[]|null
     */
    public $user;

    /**
     * @param string|string[]|int|int[]|UserElement|UserElement[]|null $value
     * @return static The query object
     */
    public function setUser($value)
    {
        $this->user = $value;
        return $this;
    }

    /**
     * @param string|string[]|int|int[]|UserElement|UserElement[]|null $value
     * @return static The query object
     */
    public function user($value)
    {
        return $this->setUser($value);
    }

    /**
     * @param string|string[]|int|int[]|UserElement|UserElement[]|null $value
     * @return static The query object
     */
    public function setUserId($value)
    {
        return $this->setUser($value);
    }

    /**
     * @param string|string[]|int|int[]|UserElement|UserElement[]|null $value
     * @return static The query object
     */
    public function userId($value)
    {
        return $this->setUser($value);
    }

    /**
     * @param $value
     * @return int
     * @throws QueryAbortedException
     */
    protected function parseUserValue($value)
    {
        $return = QueryHelper::prepareParam(
            $value,
            function (string $identifier) {
                $value = (new Query())
                    ->select(['id'])
                    ->from([UserRecord::tableName()])
                    ->where(['email' => $identifier])
                    ->orWhere(['username' => $identifier])
                    ->scalar();
                return empty($value) ? false : $value;
            }
        );

        if ($return !== null && empty($return)) {
            throw new QueryAbortedException();
        }

        return $return;
    }
}
