<?php
class ZwDatabase
{
    /**
     *  @var ZwApplication
     */
    private $theApp;

    /**
     *  @var mysqli
     */
    private $dbconn = null;
    /**
     * @param ZwApplication $app
     */
    public function __construct($app)
    {
        $this->theApp = $app;
        $this->theApp->log('Establishing database','dbinit.start');
        $conns = $app->ini['database-'.$app->environment()];
        $this->dbconn = new mysqli($conns['host'],$conns['user'],$conns['password'],$conns['database']);
        if ($this->dbconn->connect_error) {
            die('Connect Error (' . $this->dbconn->connect_errno . ') '. $this->dbconn->connect_error);
        }
        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
        }
        if (!$this->dbconn->set_charset("utf8")) {
            die("Error loading character set utf8: %s\n". $this->dbconn->error);
        } 

        $this->theApp->log('Database established : '.$this->dbconn->host_info,'dbinit.end');
    }

    public function close()
    {
        $this->dbconn->close();
    }

    /**
     * @return ZwDatabaseSelect
     */
    public function select()
    {
        return new ZwDatabaseSelect();
    }

    /**
     * @param string $q
     * @return array|null
     */
    public function fetchRow($q)
    {
        return $this->fetch($q,1);
    }

    /**
     * @param string $q
     * @return array|null
     */
    public function fetchAll($q)
    {
        return $this->fetch($q,2);
    }

    /**
     * @param string $q
     * @return array|null
     */
    public function fetchCol($q)
    {
        return $this->fetch($q,3);
    }

    /**
     * @param string|object $q
     * @param int $type .. 1=ROW, 2=ALL, 3 = COL
     * @return array|null
     */
    protected function fetch($q,$type)
    {
        $this->theApp->log('ZwDatabase::fetch - start','dbfetch.start');
        if (is_string($q) == false) {
            if (get_class($q) == "ZwDatabaseSelect")
                $q = (string)$q;
        }
        $this->theApp->log('ZwDatabase::fetch - '.$q,'dbfetch.info');
        if (is_string($q)) {
            $result = $this->dbconn->query($q);
            if ($this->dbconn->errno != 0) {
                echo $q.'<br />';
                die('ZwDatabase::fetch error ('.$this->dbconn->errno.') - '.$this->dbconn->error);
            }
            if ($result==null && $result->num_rows == 0)
                return null;

            $res = array();
            if ($result != null) {
                $var = null;
                while($row = $result->fetch_array(MYSQL_ASSOC)) {
                    if ($type == 1) {
                        $res = $row;
                        break;
                    } else if ($type == 2) {
                        $res[] = $row;
                    } else if ($type == 3) {
//                        if ($var == null) {
//                            $var = array_keys($row)[0];
//                        }
                        $res[] = $row[$var];
                    }
                }
                $result->free();
            }
            $this->theApp->log('ZwDatabase::fetch - done','dbfetch.end');
            return $res;
        }
    }

    /**
     * @param string $table
     * @param array $vals
     * @param string|null $cond
     * @return int
     */
    public function update($table,$vals,$cond = null)
    {
        $this->theApp->log('ZwDatabase::update - start','dbupdate.start');
        $str = 'UPDATE '.$table.' SET ';
        $realvals = array();
        foreach($vals as $valcol => $valval)
            $realvals[] = $valcol.' = \''.$valval.'\'';
        $str .= implode(',',$realvals);
        if ($cond != null) {
            $str .= ' WHERE '.$cond;
        }
        $this->theApp->log('ZwDatabase::update - '.$str,'dbupdate.info');
        $result = $this->dbconn->query($str);
        if ($this->dbconn->errno != 0) {
            echo $str.'<br />';
            die('ZwDatabase::update error ('.$this->dbconn->errno.') - '.$this->dbconn->error);
        }
        $this->theApp->log('ZwDatabase::update - done','dbupdate.end');
        return $this->dbconn->affected_rows;
    }
    /**
     * @param string $table
     * @param array $vals
     * @param null|string $onduplicate
     * @return int
     */
    public function insert($table,$vals,$onduplicate=null)
    {
        $this->theApp->log('ZwDatabase::insert - start','dbinsert.start');
        $str = 'INSERT INTO '.$table.' SET ';
        $realvals = array();
        foreach($vals as $valcol => $valval)
            $realvals[] = $valcol.' = \''.$valval.'\'';
        $str .= implode(',',$realvals);
        if ($onduplicate != null) {
            $str .=' ON DUPLICATE KEY UPDATE '.$onduplicate;
        }
        $this->theApp->log('ZwDatabase::insert - '.$str,'dbinsert.info');
        $result = $this->dbconn->query($str);
        if ($this->dbconn->errno != 0) {
            echo $str.'<br />';
            die('ZwDatabase::insert error ('.$this->dbconn->errno.') - '.$this->dbconn->error);
        }
        $this->theApp->log('ZwDatabase::insert - done','dbinsert.end');
        return $this->dbconn->affected_rows;
    }
    public function lastInsertId()
    {
        return $this->dbconn->insert_id;
    }
    /**
     * @param string $table
     * @param string|null $cond
     * @return int
     */
    public function deleteFrom($table,$cond = null)
    {
        $this->theApp->log('ZwDatabase::deleteFrom - start','dbremove.start');
        $str = 'DELETE FROM '.$table;
        if ($cond != null) {
            $str .=' WHERE '.$cond;
        }
        $this->theApp->log('ZwDatabase::deleteFrom - '.$str,'dbremove.info');
        $result = $this->dbconn->query($str);
        if ($this->dbconn->errno != 0) {
            echo $str.'<br />';
            die('ZwDatabase::update error ('.$this->dbconn->errno.') - '.$this->dbconn->error);
        }
        $this->theApp->log('ZwDatabase::deleteFrom - done','dbremove.end');
        return $this->dbconn->affected_rows;
    }


}