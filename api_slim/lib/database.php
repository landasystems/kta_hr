<?php

class LandaDb {

    private $db;
    private $table;
    private $columns;
    private $where_clause;
    private $bind_param;
    private $join_table;
    private $limit;
    private $offset;
    private $orderBy;
    private $groupBy;
    private $grouped;

    function __construct() {
        $this->db = new PDO("mysql:host=" . config("hostname") . ";dbname=" . config('dbname'), config('dbuser'), config('dbpass'));
        $this->db->exec("set names utf8");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->db;
    }

    //=========== START CORE FUNCTION ===========//
    public function created() {

        $created = array();
        if (config('created.user') != null)
            $created[config('created.user')] = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

        if (config('created.time') != null)
            $created[config('created.time')] = (config('created.type') !== NULL && config('created.type') == "date") ? date("Y-m-d H:i:s") : strtotime(date("Y-m-d H:i:s"));

        return $created;
    }

    public function modified() {

        $created = array();
        if (config('modified.user') != null)
            $created[config('modified.user')] = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

        if (config('modified.time') != null)
            $created[config('modified.time')] = (config('modified.type') !== NULL && config('modified.type') == "date") ? date("Y-m-d H:i:s") : strtotime(date("Y-m-d H:i:s"));

        return $created;
    }

    public function escape($data) {
        return $this->db->quote(trim($data));
    }

    public function clearQuery() {
        $this->columns = NULL;
        $this->join_table = NULL;
        $this->bind_param = NULL;
        $this->limit = NULL;
        $this->offset = NULL;
        $this->orderBy = NULL;
        $this->where_clause = NULL;
        $this->table = NULL;
    }

    public function run($query, $bind = array()) {
        $query = trim($query);
        try {
            $result = $this->db->prepare($query);
            $result->execute($bind);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

    public function get_last_id() {
        $this->db->lastInsertId();
    }

    public function field_filter($table, $data) {
        $stmt = $this->db->query("DESCRIBE $table");
        $list = $stmt->fetchAll(PDO::FETCH_OBJ);
        $table_fields = array();
        foreach ($list as $val) {
            $table_fields[] = $val->Field;
        }
        return array_values(array_intersect($table_fields, array_keys($data)));
    }

    public function get_data($table, $id) {
        $sql = "select * from $table where id = $id";
        try {
            $exec = $this->db->query($sql);
            $r = $exec->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            $r = $e->getMessage();
        }
        return $r;
    }

    public function clean($string) {
        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    //========== END CORE FUNCTION ============//
    //========== CRUD ==========//
    public function insert($table, $data) {
        //$data = array_merge($this->created(), $data);
        //$data = array_merge($this->modified(), $data);
        $fields = $this->field_filter($table, $data);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
        $bind = array();
        foreach ($fields as $field)
            $bind[":$field"] = $data[$field];

        try {
            $this->run($sql, $bind);
            $lastId = $this->db->lastInsertId();
            try {
                $r = $this->get_data($table, $lastId);
            } catch (Exception $ex) {
                $r = $data;
            }
        } catch (Exception $e) {
            $r = $e->getMessage();
        }

        return $r;
    }

    public function update($table, $data, $where = array()) {
        //update modified
        //$data = $this->modified() + $data;
        //$created = array_keys($this->created());
        //$created_field = isset($created[1]) ? $created[1] : '';
        //if (isset($data[$created_field]))
        //    unset($data[$created_field]);
        //end update modified

        $fields = $this->field_filter($table, $data);
        foreach ($fields as $key => $val) {
            $set[] = "$val = :update_" . $val;
            $bind[":update_$val"] = $data[$val];
        }

        $param = '';
        foreach ($where as $k => $vals) {
            if (empty($param))
                $param .= " where $k = :where_$k";
            else
                $param .= " and $k =  :wher e_$k";

            $bind[":where_$k"] = $vals;
        }
        $sql = "UPDATE " . $table . " SET " . implode(', ', $set) . " $param ";
        try {
            $this->run($sql, $bind);
            try {
                if (isset($data['id']))
                    $r = $this->get_data($table, $data['id']);
                else
                    $r = $data;
            } catch (Exception $ex) {
                $r = $data;
            }
        } catch (Exception $e) {
            $r = $e->getMessage();
        }

        return $r;
    }

    public function delete($table, $where = array()) {
        $param = '';
        foreach ($where as $k => $vals) {
            if (empty($param))
                $param .= " WHERE $k = :where_$k";
            else
                $param .= " AND $k = :where_$k";

            $bind[":where_$k"] = $vals;
        }

        $sql = "DELETE FROM " . $table . " $param ";

        try {
            $r = $this->run($sql, $bind);
        } catch (Exception $e) {
            $r = $e->getMessage();
        }

        return $r;
    }

    //======= END CRUD =======//
    //======= SELECTING DATA =======//
    public function select($columns = '*') {
        $this->clearQuery();

        if (is_array($columns))
            $this->columns = implode(",", $columns);
        else
            $this->columns = $columns;
        return $this;
    }

    public function from($table) {
        if (is_array($table)) {
            $this->table = implode(",", $columns);
        } else
            $this->table = $table;

        $this->table = trim($table);
        return $this;
    }

    public function join($join_type, $table, $clause) {
        $this->join_table .= " $join_type " . $table . " ON " . $clause;
        return $this;
    }

    public function innerJoin($table, $clause) {
        $this->join('INNER JOIN', $table, $clause);
    }

    public function leftJoin($table, $clause) {
        $this->join('LEFT JOIN', $table, $clause);
    }

    public function rightJoin($table, $clause) {
        $this->join('RIGHT JOIN', $table, $clause);
    }

    public function customWhere($where) {
        $this->where_clause .= " (" . $where . ")";
        return $this;
    }

    public function where($filter, $column, $value, $nParam = 'AND') {
        if (is_array($value)) {
            $_keys = [];
            foreach ($value as $k => $v)
                $_keys[] = (is_numeric($v) ? $v : $this->escape($v));

            $value = "(" . implode(', ', $_keys) . ")";
        }

        if ($filter == "like" or $filter == "LIKE")
            $value = '%' . $value . '%';
        else
            $value = $value;

        $where = $this->where_clause;
        if (empty($this->where_clause))
            $where = trim($column) . " $filter :where_" . $this->clean($column);
        else
            $where .= " $nParam " . trim($column) . " $filter :where_" . $this->clean($column);

        $this->bind_param[":where_" . $this->clean($column)] = $value;

        if ($this->grouped) {
            $this->where_clause .= '(' . $where;
            $this->grouped = false;
        } else {
            $this->where_clause = $where;
        }

        return $this;
    }

    public function andWhere($filter, $column, $value) {
        $this->where($filter, $column, $value, 'AND');
        return $this;
    }

    public function orWhere($filter, $column, $value) {
        $this->where($filter, $column, $value, 'OR');
    }

    public function limit($limit) {
        $this->limit = trim($limit);
        return $this;
    }

    public function offset($offset) {
        $this->offset = trim($offset);
        return $this;
    }

    public function orderBy($order) {
        $this->orderBy = trim($order);
        return $this;
    }

    public function groupBy($group) {
        $this->groupBy = trim($group);
        return $this;
    }

    public function count() {
        $sql = 'SELECT COUNT(*) as jumlah FROM ' . $this->table . ' ' . $this->join_table;
        if (!is_null($this->where_clause))
            $sql .= ' WHERE ' . $this->where_clause;

        try {
            $exec = $this->run(trim($sql), $this->bind_param);
            $count = $exec->fetch(PDO::FETCH_OBJ);
            return $count->jumlah;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function grouped($obj) {
        $this->grouped = true;
        call_user_func($obj);
        $this->where_clause .= ')';
        return $this;
    }

    public function prepareQuery() {
        $query = 'SELECT ' . $this->columns . ' FROM ' . $this->table;

        if (!is_null($this->join_table))
            $query .= $this->join_table;

        if (!is_null($this->where_clause))
            $query .= ' WHERE ' . $this->where_clause;

        if (!is_null($this->groupBy))
            $query .= ' GROUP BY ' . $this->groupBy;

        if (!is_null($this->orderBy))
            $query .= ' ORDER BY ' . $this->orderBy;

        if (!is_null($this->limit))
            $query .= ' LIMIT ' . $this->limit;

        if (!is_null($this->offset))
            $query .= ' OFFSET ' . $this->offset;

        return array('query' => $query, 'bind' => $this->bind_param);
    }

    public function find($sql = null) {
        if (empty($sql)) {
            $query = $this->prepareQuery();
        } else {
            $query['query'] = $sql;
            $query['bind'] = array();
        }
//        print_r($query);
        try {
            $exec = $this->run(trim($query['query']), $query['bind']);
            return $exec->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function findAll($sql = null) {
        if (empty($sql)) {
            $query = $this->prepareQuery();
        } else {
            $query['query'] = $sql;
            $query['bind'] = array();
        }

        try {
            $exec = $this->run(trim($query['query']), $query['bind']);
            return $exec->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    function sql_debug($sql_string, array $params = null) {
        if (!empty($params)) {
            $indexed = $params == array_values($params);
            foreach ($params as $k => $v) {
                if (is_object($v)) {
                    if ($v instanceof \DateTime)
                        $v = $v->format('Y-m-d H:i:s');
                    else
                        continue;
                }
                elseif (is_string($v))
                    $v = "'$v'";
                elseif ($v === null)
                    $v = 'NULL';
                elseif (is_array($v))
                    $v = implode(',', $v);

                if ($indexed) {
                    $sql_string = preg_replace('/\?/', $v, $sql_string, 1);
                } else {
                    if ($k[0] != ':')
                        $k = ':' . $k; //add leading colon if it was left out
                    $sql_string = str_replace($k, $v, $sql_string);
                }
            }
        }
        return $sql_string;
    }

    public function log($sql = null) {
        if (empty($sql)) {
            $query = $this->prepareQuery();
        } else {
            $query['query'] = $sql;
            $query['bind'] = array();
        }
        echo "<div class='well'>";
        echo $this->sql_debug($query['query'], $query['bind']);
        echo "</div>";
    }

    //========= END SELECTING DATA ========//
}
