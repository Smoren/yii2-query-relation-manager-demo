<?php


namespace app\qrm\ActiveRecord;


use app\qrm\Base\QueryRelationManagerBase;
use app\qrm\Base\QueryRelationManagerException;
use app\qrm\Base\QueryWrapperInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class for making queries for getting data from database with relations and filters
 * @package Smoren\Yii2\QueryRelationManager
 * @author Smoren <ofigate@gmail.com>
 */
class QueryRelationManager extends QueryRelationManagerBase
{
    /**
     * @param string $relationName
     * @param string $relationAlias
     * @param string|null $parentAlias
     * @param string $joinType
     * @param string|null $extraJoinCondition
     * @param array|null $extraJoinParams
     * @return $this
     * @throws QueryRelationManagerException
     */
    public function with(
        string $relationName, string $relationAlias, ?string $parentAlias = null, string $joinType = 'left',
        ?string $extraJoinCondition = null, ?array $extraJoinParams = []
    ): self
    {
        $mainTable = $this->tableManager->getMainTable();

        $parentAlias = $parentAlias ?? $mainTable->alias;
        $parentClassName = $this->tableManager->byAlias($parentAlias)->className;

        if(!class_exists($parentClassName)) {
            throw new QueryRelationManagerException("class {$parentClassName} not exists");
        }

        /** @var ActiveRecord $inst */
        $inst = new $parentClassName;
        if(!($inst instanceof ActiveRecord)) {
            throw new QueryRelationManagerException("class {$parentClassName} is not an instance of ActiveRecord");
        }

        $methodName = 'get'.ucfirst($relationName);
        if(!method_exists($inst, $methodName)) {
            throw new QueryRelationManagerException("method {$parentClassName}::{$methodName}() not exists");
        }

        /** @var ActiveQuery $activeQuery */
        $activeQuery = $inst->$methodName();
        if(!($activeQuery instanceof ActiveQuery)) {
            throw new QueryRelationManagerException("method {$parentClassName}::{$methodName}() returned non-ActiveQuery instance");
        }

        if($activeQuery->via) {
            throw new QueryRelationManagerException('cannot use relations with "via" section yet');
        }
        if(!$activeQuery->link || !count($activeQuery->link)) {
            throw new QueryRelationManagerException('cannot use relations without "link" section');
        }

        if($activeQuery->multiple) {
            return $this->withMultiple(
                $relationName, $activeQuery->modelClass, $relationAlias,
                $parentAlias, $activeQuery->link, $joinType,
                $extraJoinCondition, $extraJoinParams
            );
        } else {
            return $this->withSingle(
                $relationName, $activeQuery->modelClass, $relationAlias,
                $parentAlias, $activeQuery->link, $joinType,
                $extraJoinCondition, $extraJoinParams
            );
        }
    }

    /**
     * Возвращает имя таблицы по классу сущности ActiveRecord
     * @param string $className имя класса
     * @return string имя таблицы
     * @throws QueryRelationManagerException
     */
    protected function getTableName(string $className): string
    {
        if(!method_exists($className, 'tableName')) {
            throw new QueryRelationManagerException("method {$className}::tableName() is not defined");
        }

        return $className::tableName();
    }

    /**
     * Создает объект запроса
     * @return QueryWrapperInterface
     */
    protected function createQuery(): QueryWrapperInterface
    {
        return new QueryWrapper();
    }

    /**
     * Возвращает список полей таблицы
     * @param string $className
     * @return array
     * @throws QueryRelationManagerException
     */
    protected function getTableFields(string $className): array
    {
        if(!class_exists($className)) {
            throw new QueryRelationManagerException("class {$className} is not defined");
        }

        if(!method_exists($className, 'getTableSchema')) {
            throw new QueryRelationManagerException("method {$className}::getTableSchema() is not defined");
        }

        return array_keys($className::getTableSchema()->columns);
    }

    /**
     * Возвращает поля первичного ключа таблицы
     * @param string $className
     * @return array
     * @throws QueryRelationManagerException
     */
    protected function getPrimaryKey(string $className): array
    {
        if(!class_exists($className)) {
            throw new QueryRelationManagerException("class {$className} is not defined");
        }

        if(!method_exists($className, 'primaryKey')) {
            throw new QueryRelationManagerException("method {$className}::primaryKey() is not defined");
        }

        return $className::primaryKey();
    }
}