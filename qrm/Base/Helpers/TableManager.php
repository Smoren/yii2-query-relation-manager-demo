<?php


namespace app\qrm\Base\Helpers;


use app\qrm\Base\QueryRelationManagerException;

class TableManager
{
    /**
     * @var Table
     */
    protected $mainTable;

    /**
     * @var Table[]
     */
    protected $mapByClassName = [];

    /**
     * @var Table[]
     */
    protected $mapByName = [];

    /**
     * @var Table[]
     */
    protected $mapByAlias = [];

    /**
     * @param Table $table
     * @return $this
     * @throws QueryRelationManagerException
     */
    public function add(Table $table): self
    {
        if($this->mainTable === null) {
            $this->mainTable = $table;
        }

        $this->addToMap('mapByClassName', 'className', $table);
        $this->addToMap('mapByName', 'name', $table);
        $this->addToMap('mapByAlias', 'alias', $table);

        return $this;
    }

    /**
     * @return Table
     * @throws QueryRelationManagerException
     */
    public function getMainTable(): Table
    {
        if($this->mainTable === null) {
            throw new QueryRelationManagerException('no main table found in TableManager');
        }

        return $this->mainTable;
    }

    /**
     * @param string $className
     * @return Table
     * @throws QueryRelationManagerException
     */
    public function byClassName(string $className): Table
    {
        return $this->getFromMap('mapByClassName', $className);
    }

    /**
     * @param string $name
     * @return Table
     * @throws QueryRelationManagerException
     */
    public function byName(string $name): Table
    {
        return $this->getFromMap('mapByName', $name);
    }

    /**
     * @param string $alias
     * @return Table
     * @throws QueryRelationManagerException
     */
    public function byAlias(string $alias): Table
    {
        return $this->getFromMap('mapByAlias', $alias);
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback): self
    {
        foreach($this->mapByAlias as $table) {
            $callback($table);
        }
        return $this;
    }

    /**
     * @param string $mapName
     * @param string $key
     * @param Table $table
     * @return $this
     * @throws QueryRelationManagerException
     */
    protected function addToMap(string $mapName, string $key, Table $table): self
    {
        if(isset($this->{$mapName}[$table->{$key}])) {
            throw new QueryRelationManagerException("duplicate key '{$key}' in map '{$mapName}' of TableManager");
        }
        $this->{$mapName}[$table->{$key}] = $table;

        return $this;
    }

    /**
     * @param string $mapName
     * @param string $key
     * @return Table
     * @throws QueryRelationManagerException
     */
    protected function getFromMap(string $mapName, string $key): Table
    {
        if(!isset($this->{$mapName}[$key])) {
            throw new QueryRelationManagerException("key '{$key}' not found in map '{$mapName}' of TableManager");
        }

        return $this->{$mapName}[$key];
    }
}