<?php

namespace App\System\Core;

use App\System\Database\DB;
use App\System\Exceptions\DataNotFoundException;

abstract class Models extends DB
{
    /**
     * Database table name.
     * 
     * @var string
     */
    protected string $table;
    /**
     * Primary key field.
     * 
     * @var string
     */
    protected string $pk = 'id';
    /**
     * Sql query to be executed.
     * 
     * @var string|null
     */
    protected $query = null;
    /**
     * Columns to be selected.
     * @var string
     */

    protected string $columns = '*';

    /**
     * Conditions for the data to be retrieved.
     * 
     * @var string|null
     */

    protected ?string $conditions = null;

    /**
     * Order in which data  is to be ordered.
     * 
     * @var string|null
     */
    protected ?string $order = null;

      /**
       * Limit for the data.
       * 
       * @var string|null
       */
    protected ?string $limit = null;

    /** Check if data is loaded from database. @var bool */
    protected bool $loaded = false;

    /** Relationship configurations. @var string[] */
    protected array $related = [];

    public function __construct(?string $id = null) {
        parent::__construct();

        if(!is_null($id)) {
            $this->load($id);
        }
    }

    /**
     * Sets columns to be retrieved from
     * the database.
     * 
     * @param string[] $columns
     * @return Model
     */
    public function select(string...$columns): self
    {
        if (count($columns) > 0) {
            $this->columns = implode(', ', $columns);
        }

        return $this;
    }
    /**
     * Sets conditions for the data
     * to be retrieved.
     * 
     * @param string $column
     * @param string $operator
     * @param string|null $value
     * @return $this
     */
    public function where(string $column, string $operator, ?string $value = null): self
    {
        if ($operator === 'LIKE'){
            $cond = "{$column} {$operator} " . $this->quoteEscape('%' . $value . '%');
        } 
        elseif (is_null($value)) {
            $cond = "{$column} = " . $this->quoteEscape($operator);
        } else {
            $cond = "{$column} {$operator} " . $this->quoteEscape($value);
        }

        if (is_null($this->conditions)) {
            $this->conditions = $cond;
        } else {
            $this->conditions .= "AND {$cond}";
        }

        return $this;
    }
    /**
     * Sets conditions for the data
     * to be retrieved.
     * 
     * @param string $column
     * @param string $operator
     * @param string|null $value
     * @return void
     */
    public function orWhere(string $column, string $operator, ?string $value = null): self
    {
        if (is_null($value)) {
            $this->conditions .= " OR {$column} = " . $this->quoteEscape($operator);
        } else {
            $this->conditions .= " OR {$column} {$operator} " . $this->quoteEscape($value);
        }

        return $this;

    }
      /**
       * Sets the sorting order for the
       * data to be retrieved from database.
       * 
       * @param string $column
       * @param string $direction
       * @return Model
       */

    public function orderBy(string $column, string $direction = 'ASC'): self {
        if(is_null($this->order)) {
            $this->order = "{$column} {$direction}";
        } else {
            $this->order .= ", {$column} {$direction}";
        }

        return $this;
    }

      /**
       * Sets the limit for the number of 
       * data to be retrieved.
       * 
       * @param int $offset
       * @param int|null $limit
       * @return Model
       */

    public function limit(int $offset, ?int $limit = null): self {
        if(is_null($limit)) {
            $this->limit = "0, {$offset}";
        } else {
            $this->limit = "{$offset}, {$limit}";
        }

        return $this;
    }

      /**
       * Retrieves data from  the database.
       * 
       * @return array
       */

    public function get(): array {
        $this->buildSelect();

        $this->run($this->query);

        $class = !empty($this->related['class']) ? $this->related['class'] : get_class($this);

        $this->resetVars();

        if($this->count() > 0) {
            $data = $this->fetch();

            $collection = [];

            foreach($data as $item) {
                $obj = new $class;
                
                foreach($item as $k => $v) {
                    $obj->{$k} = $v;
                }
                $obj->setLoaded(true);

                $collection[] = $obj;
            }

            return $collection;
        } else {
            return [];
        }
    }

      /**
       * Builds select query.
       * 
       * @return void
       */

    private function buildSelect() {
        $table = !empty($this->related['table']) ? $this->related['table'] : $this->table;

        $this->query = "SELECT {$this->columns} FROM {$table}";

        if(!is_null($this->conditions)) {
            $this->query .= " WHERE {$this->conditions}";
        }

        if(!is_null($this->order)) {
            $this->query .= " ORDER BY {$this->order}";
        }

        if(!is_null($this->limit)) {
            $this->query .= " LIMIT {$this->limit}";
        }
    }

      /**
       * Returns first matching data.
       * 
       * @return Model|null
       */

    public function  first(): ?Models {
        $this->limit(1);

        $result = $this->get();

        if (count($result) == 1) {
            return $result[0];
        } else {
            return null;
        }

    }

      /**
       * Loads data matching the given
       * primary key value.
       * 
       * @param string $id
       * @return void
       */

    public function load(string $id) {
        $this->where($this->pk, $id);
        $this->buildSelect();
        $this->run($this->query);
        $this->resetVars();

        if($this->count() > 0 ) {
            $data = $this->fetch();

            foreach($data[0] as $k => $v) {
                $this->{$k} = $v;
            }

            $this->setLoaded(true);
        } else {
            throw new DataNotFoundException("Data with condition '{$this->pk} = {$id}' not found in the table '{$this->table}'.");
        }
    }

    /**
     * Resets query builder variables.
     */

    private function resetVars() {
        $this->columns = '*';
        $this->conditions = null;
        $this->order = null;
        $this->limit = null;
        $this->query = null;
        $this->related = [];

    }

    /**
     * Sets value of loaded variable.
     * 
     * @param bool $value
     * @return void
     * 
     */

    public function setLoaded(bool $value) {
        $this->loaded = $value;
    }
    
    /**
     * Inserts/updates data in the database.
     * 
     * @return void
     */
    public function save() {
        if($this->loaded) {
            $this->buildUpdate();
        } else {
            $this->buildInsert();
        }

        $this->run($this->query);
        $this->resetVars();

        if($this->loaded) {
            $this->load($this->{$this->pk});
        } else {
            $this->load($this->insertId());
        }
    }

    /**
     * Builds insert query.
     * 
     * @return void
     */

    private function buildInsert() {
        $dataVars = $this->getDataVars();
        $set = [];

        foreach($dataVars as $k => $v) {
            if($k != $this->pk) {
                if(!is_null($v)) {
                  $set[] = "{$k} = ".$this->quoteEscape($v);
                } else {
                    $set[] = "{$k} = NULL";
                }
            }
        }
        // echo "INSERT INTO {$this->table} SET ".implode(',', $set) ;
        $this->query = "INSERT INTO {$this->table} SET ".implode(',', $set);
    }

    /**
     * Builds update query.
     * 
     * @return void
     */

    private function buildUpdate() {
        $dataVars = $this->getDataVars();
        $set = [];

        foreach($dataVars as $k => $v) {
            if($k != $this->pk) {
                if(!is_null($v)) {
                  $set[] = "{$k} = ".$this->quoteEscape($v);
                } else {
                    $set[] = "{$k} = NULL";
                }
            }
        }

        $this->query = "UPDATE {$this->table} SET ".implode(',', $set) . " WHERE {$this->pk} = '{$this->{$this->pk}}'";
    }

    /**
     * Returns list of data variables with value.
     * 
     * @return array
     */

    private function getDataVars(): array {
        $predefined =  get_class_vars(get_class($this));
        $all = get_object_vars($this);

        return array_diff_key($all, $predefined);

    }
    /**
     * Deletes data from the database.
     * 
     * @return void
     */
    public function delete() {
        $this->query = "DELETE FROM {$this->table} WHERE {$this->pk} = '{$this->{$this->pk}}'";
        $this->run($this->query);
        $this->resetVars();
        $this->setLoaded(false);

        $dataVars = $this->getDataVars();

        foreach($dataVars as $k => $v) {
            unset($this->{$k});
        }
    }

    protected function relation(string $class, string $table, string $fk, string $relation = 'child', string $pk = 'id'): self {
        if($relation == 'child') {
            $this->where($fk, $this->{$pk});
        } elseif ($relation == 'parent') {
            $this->where($pk, $this->{$fk});
        }

        $this->related = compact('class', 'table');

        return $this;
    }

    public function paginate(int $perPage = 10): array {
        $pageNo = $_GET['page']  ?? 1;
        $offset = ($pageNo - 1) * $perPage;

        $table = !empty($this->related['table']) ? $this->related['table'] : $this->table;

        $query = "SELECT COUNT(id) AS total FROM {$table}";

        if(!is_null($this->conditions)) {
            $query .= " WHERE {$this->conditions}";
        }

        $this->run($query);
        $result = $this->fetch();
        
        $total = $result[0]['total'];

        $pages = ceil($total / $perPage);

        $this->limit($offset, $perPage);

        $data = $this->get();

        return compact('data', 'pageNo', 'pages');
    }
}