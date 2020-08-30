<?php


namespace app\qrm\Base\Helpers;


use app\qrm\Base\QueryRelationManagerException;

class Table
{
    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $alias;

    /**
     * @var string[]
     */
    public $primaryKey;

    /**
     * @var string
     */
    public $containerFieldAlias;

    /**
     * @var array
     */
    protected $fieldMap = [];

    /**
     * @var array
     */
    protected $fieldMapReverse = [];

    /**
     * @var array
     */
    protected $pkFieldMapReverse = [];

    /**
     * Table constructor.
     * @param string $className
     * @param string $name
     * @param string $alias
     * @param array $fields
     * @param array $primaryKey
     * @param string $containerFieldAlias
     * @throws QueryRelationManagerException
     */
    public function __construct(
        string $className, string $name, string $alias, array $fields, array $primaryKey, ?string $containerFieldAlias = null
    )
    {
        $this->className = $className;
        $this->name = $name;
        $this->alias = $alias;
        $this->primaryKey = $primaryKey;
        $this->containerFieldAlias = $containerFieldAlias;

        $bufMap = [];
        foreach($fields as $field) {
            $bufMap[$field] = "{$this->alias}_{$field}";
            $this->fieldMap["`{$this->alias}`.`{$field}`"] = "{$this->alias}_{$field}";
            $this->fieldMapReverse["{$this->alias}_{$field}"] = $field;
        }

        foreach($this->primaryKey as $field) {
            if(!isset($bufMap[$field])) {
                throw new QueryRelationManagerException("pk field {$field} not found in field list");
            }
            $this->pkFieldMapReverse[$bufMap[$field]] = $field;
        }
    }

    /**
     * @return string[]
     */
    public function getFieldMap(): array
    {
        return $this->fieldMap;
    }

    /**
     * @param string $fieldPrefixed
     * @return string
     */
    public function getField(string $fieldPrefixed): string
    {
        return $this->fieldMapReverse[$fieldPrefixed];
    }

    /**
     * @return string
     */
    public function stringifyPrimaryKey(): string
    {
        return implode('-', $this->primaryKey);
    }

    /**
     * @param array $row
     * @param JoinConditionManager $conditionManager
     * @return array
     * @throws QueryRelationManagerException
     */
    public function getDataFromRow(array $row, JoinConditionManager $conditionManager): array
    {
        $item = [];

        foreach($row as $key => $val) {
            if(isset($this->fieldMapReverse[$key])) {
                $item[$this->fieldMapReverse[$key]] = $val;
            }
        }

        /** @var JoinCondition $cond */
        foreach($conditionManager->byJoinTo($this->alias) as $cond) {
            switch($cond->type) {
                case JoinCondition::TYPE_MULTIPLE:
                    $item[$cond->table->containerFieldAlias] = [];
                    break;
                case JoinCondition::TYPE_SINGLE:
                    $item[$cond->table->containerFieldAlias] = null;
                    break;
                default:
                    throw new QueryRelationManagerException("unknown condition type '{$cond->type}'");
            }
        }

        $primaryKeyValue = $this->stringifyPrimaryKeyValue($row);

        try {
            $cond = $conditionManager->byJoinAs($this->alias);
            $joinTo = $cond->joinTo;
            $aliasTo = $joinTo->alias;
            $foreignKeyValue = $joinTo->stringifyPrimaryKeyValue($row);
            $type = $cond->type;
        } catch(QueryRelationManagerException $e) {
            $aliasTo = null;
            $foreignKeyValue = null;
            $containerFieldAlias = null;
            $type = null;
        }

        return [$item, $primaryKeyValue, $this->alias, $aliasTo, $foreignKeyValue, $this->containerFieldAlias, $type];
    }

    /**
     * @return array
     */
    public function getPrimaryKeyForSelect(): array
    {
        $result = [];
        foreach($this->primaryKey as $field) {
            $result[] = "`{$this->alias}`.`{$field}`";
        }

        return $result;
    }

    /**
     * @param array $row
     * @return string
     * @throws QueryRelationManagerException
     */
    protected function stringifyPrimaryKeyValue(array $row): string
    {
        $primaryKeyValues = [];

        foreach($this->pkFieldMapReverse as $fieldPrefixed => $field) {
            if(!isset($row[$fieldPrefixed])) {
                throw new QueryRelationManagerException("no primary key field '{$field}' found in row");
            }
            $primaryKeyValues[] = $row[$fieldPrefixed];
        }

        return implode('-', $primaryKeyValues);
    }
}