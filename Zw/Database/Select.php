<?php
class ZwDatabaseSelect
{

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function __toString() {
        $str = 'SELECT ';
        if (is_string($this->_cols)) {
            if ($this->_cols == '*')
                $str .= $this->_table.'.';
            $str .= $this->_cols;
        } else if (is_array($this->_cols)) {
            $str .= implode(',',$this->_cols);
        }
        foreach ($this->_joins as $join) {
            $str .= ',';
            $joincols = array();
            foreach($join['cols'] as $joinalias => $joincol)
                $joincols[] = $joincol.' AS '.$joinalias;
            $str .= implode(',',$joincols);
        }
        $str .= ' FROM '.$this->_table;
        foreach ($this->_joins as $join) {
            $str.= ' '.$join['type'].' JOIN '.$join['table'];
            $str.=' ON '.$join['cond'];
        }

        if (count($this->_where) > 0) {
            $str .= ' WHERE ';
            $str .= implode(' AND ',$this->_where);
        }
        if ($this->_group != null) {
            $str .= ' GROUP BY ';
            if (is_string($this->_group)) {
                $str .= $this->_group;
            } else if (is_array($this->_group)) {
                $str .= implode(',',$this->_group);
            }
        }
        if ($this->_order != null) {
            $str .= ' ORDER BY ';
            if (is_string($this->_order)) {
                $str .= $this->_order;
            } else if (is_array($this->_order)) {
                $str .= implode(',',$this->_order);
            }
        }
        if ($this->_limit != null) {
            $str .= ' LIMIT '.$this->_limit;
        }
//        echo '<pre>';
//        print_r($this);
//        echo $str;
//        echo '</pre>';
        return $str;
    }


    private $_table = null;
    private $_cols = null;
    /**
     * @param string $table
     * @param null|string|array $cols
     * @return ZwDatabaseSelect
     */
    public function from($table,$cols = '*')
    {
        $this->_table = $table;
        $this->_cols = $cols;
        return $this;
    }

    private $_where = array();
    /**
     * @param string $cond
     * @return ZwDatabaseSelect
     */
    public function where($cond)
    {
        $this->_where[] = $cond;
        return $this;
    }

    private $_order = null;
    /**
     * @param string|array $order
     * @return ZwDatabaseSelect
     */
    public function order($order)
    {
        $this->_order = $order;
        return $this;
    }

    private $_joins = array();
    /**
     * @param string $table
     * @param string $cond
     * @param null|string|array $cols
     * @return $this
     */
    public function joinInner($table,$cond,$cols=null)
    {
        $elem = array();
        $elem['table'] = $table;
        $elem['cond'] = $cond;
        $elem['cols'] = $cols;
        $elem['type'] = 'INNER';
        $this->_joins[] = $elem;
        return $this;
    }

    private $_group = null;
    /**
     * @param string|array $group
     * @return ZwDatabaseSelect
     */
    public function group($group)
    {
        $this->_group = $group;
        return $this;
    }
    private $_limit = null;
    /**
     * @param string|array $group
     * @return ZwDatabaseSelect
     */
    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

}