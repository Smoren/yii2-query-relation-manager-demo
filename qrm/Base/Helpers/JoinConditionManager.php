<?php


namespace app\qrm\Base\Helpers;


use app\qrm\Base\QueryRelationManagerException;

class JoinConditionManager
{
    /**
     * @var JoinCondition[]
     */
    protected $mapByJoinAs = [];

    /**
     * @var JoinCondition[][]
     */
    protected $matrixByJoinTo = [];

    /**
     * @param JoinCondition $condition
     * @return $this
     * @throws QueryRelationManagerException
     */
    public function add(JoinCondition $condition): self
    {
        if(isset($this->mapByJoinAs[$condition->table->alias])) {
            throw new QueryRelationManagerException("duplicate table alias '{$condition->table->alias}'");
        }
        $this->mapByJoinAs[$condition->table->alias] = $condition;

        if(!isset($this->matrixByJoinTo[$condition->joinTo->alias])) {
            $this->matrixByJoinTo[$condition->joinTo->alias] = [];
        }
        if(isset($this->matrixByJoinTo[$condition->joinTo->alias][$condition->table->alias])) {
            throw new QueryRelationManagerException("duplicate table alias '{$condition->table->alias}'");
        }
        $this->matrixByJoinTo[$condition->joinTo->alias][$condition->table->alias] = $condition;

        return $this;
    }

    /**
     * @param string $joinAs
     * @return JoinCondition
     * @throws QueryRelationManagerException
     */
    public function byJoinAs(string $joinAs): JoinCondition
    {
        if(!isset($this->mapByJoinAs[$joinAs])) {
            throw new QueryRelationManagerException("no condition found by table alias '{$joinAs}'");
        }
        return $this->mapByJoinAs[$joinAs];
    }

    /**
     * @param string $joinTo
     * @return JoinCondition[]
     */
    public function byJoinTo(string $joinTo): array
    {
        if(!isset($this->matrixByJoinTo[$joinTo])) {
            return [];
        }
        return $this->matrixByJoinTo[$joinTo];
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback): self
    {
        foreach($this->mapByJoinAs as $condition) {
            $callback($condition);
        }
        return $this;
    }
}