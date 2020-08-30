<?php


namespace app\qrm\Base\Helpers;


class JoinCondition
{
    const TYPE_SINGLE = 1;
    const TYPE_MULTIPLE = 2;

    /**
     * @var int
     */
    public $type;

    /**
     * @var Table
     */
    public $table;

    /**
     * @var Table
     */
    public $joinTo;

    /**
     * @var array
     */
    public $joinCondition;

    /**
     * @var string
     */
    public $joinType;

    /**
     * @var string
     */
    public $extraJoinCondition;

    /**
     * @var array
     */
    public $extraJoinParams;

    public function __construct(
        int $type, Table $table, Table $joinTo, array $joinCondition,
        string $joinType = 'left', ?string $extraJoinCondition = null, array $extraJoinParams = []
    )
    {
        $this->type = $type;
        $this->table = $table;
        $this->joinTo = $joinTo;
        $this->joinCondition = $joinCondition;
        $this->joinType = $joinType;
        $this->extraJoinCondition = $extraJoinCondition;
        $this->extraJoinParams = $extraJoinParams;
    }

    public function stringify(): string
    {
        $joins = [];
        foreach($this->joinCondition as $linkBy => $linkTo) {
            $joins[] = "`{$this->table->alias}`.`{$linkBy}` = `{$this->joinTo->alias}`.`{$linkTo}`";
        }

        return implode(' AND ', $joins).' '.$this->extraJoinCondition;
    }
}