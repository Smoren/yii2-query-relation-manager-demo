<?php


namespace app\qrm\Pdo;



use app\qrm\Base\QueryRelationManagerBase;
use app\qrm\Base\QueryRelationManagerException;
use app\qrm\Base\QueryWrapperInterface;

/**
 * Class for making queries for getting data from database with relations and filters
 * @package Smoren\Yii2\QueryRelationManager
 * @author Smoren <ofigate@gmail.com>
 */
class QueryRelationManager extends QueryRelationManagerBase
{

    /**
     * Возвращает имя таблицы по классу сущности ActiveRecord
     * @param string $className имя класса
     * @return string имя таблицы
     */
    protected function getTableName(string $className): string
    {
        return $className;
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
        $qw = new QueryWrapper();
        $qw->setRawSql('SHOW COLUMNS FROM '.addslashes($className));
        $rows = $qw->all();

        $result = [];
        foreach($rows as $row) {
            $result[] = $row['Field'];
        }

        return $result;
    }

    /**
     * Возвращает поля первичного ключа таблицы
     * @param string $className
     * @return array
     * @throws QueryRelationManagerException
     */
    protected function getPrimaryKey(string $className): array
    {
        $qw = new QueryWrapper();
        $qw->setRawSql("SHOW COLUMNS FROM ".addslashes($className)." WHERE `Key` = 'PRI'");
        $rows = $qw->all();

        $result = [];
        foreach($rows as $row) {
            $result[] = $row['Field'];
        }

        return $result;
    }
}