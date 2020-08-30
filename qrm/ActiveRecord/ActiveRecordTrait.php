<?php


namespace app\qrm\ActiveRecord;


use app\qrm\Base\QueryRelationManagerException;

/**
 * Trait ActiveRecordTrait
 * Trait для упрощения построения запросов с помощью QueryRelationManager
 * @package app\qrm\ActiveRecord
 */
trait ActiveRecordTrait
{
    /**
     * @param string|null $alias
     * @return QueryRelationManager
     * @throws QueryRelationManagerException
     */
    public static function select(?string $alias = null): QueryRelationManager
    {
        return QueryRelationManager::select(
            self::class, $alias ?? self::tableName()
        );
    }
}