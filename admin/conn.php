 <?php
 class DB{
     //主机
    private $host="localhost";
    //数据库的username
    private $name="root";
    //数据库的password
     private $pass="root";
    //数据库名称
     private $table="mapback";
     //编码形式
    private $ut="utf-8";
 
	private $conn='';
     //构造函数
    function __construct(){
        //$this->ut=$ut;
        
		
		$this->conn=$this->connect();
     }
 
     //数据库的链接
    function connect(){
         $link=mysqli_connect($this->host,$this->name,$this->pass) or die ($this->error());
        mysqli_select_db($link,$this->table) or die("没该数据库：".$this->table);
        mysqli_query($link,"SET NAMES '$this->ut'");
		return $link;
   }
 
    function query($sql, $type = '') {
       if(!($query = mysqli_query($this->conn,$sql))) $this->show('Say:', $sql);
        return $query;
     }

     function show($message = '', $sql = '') {
         if(!$sql) echo $message;
        else echo $message.'<br>'.$sql;
    }

     function affected_rows() {
        return mysqli_affected_rows();
     }

     function result($query, $row) {
         return mysqli_result($query, $row);
     }
 
     function num_rows($query) {
        return @mysqli_num_rows($query);
   }
 
    function num_fields($query) {
         return mysqli_num_fields($query);
    }
 
    function free_result($query) {
        return mysqli_free_result($query);
     }

    function insert_id() {
        return mysqli_insert_id();
     }

     function fetch_row($query) {
       return mysqli_fetch_row($query);
     }
 
   function version() {
         return mysqli_get_server_info();
    }

     function close() {
       return mysqli_close();
    }
 
    //向$table表中插入值
    function insert($table,$name,$value){
        $this->query("insert into $table ($name) value ($value)");
   }
     //根据$id值删除表$table中的一条记录
    function delete($table,$id,$value){
        $this->query("delete from $table where $id=$value");
         echo "id为". $id." 的记录被成功删除!";
    }
	
	  function getOne($sql, $limited = false)
    {
        if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false)
        {
            $row = mysqli_fetch_row($res);

            if ($row !== false)
            {
                return $row[0];
            }
            else
            {
                return '';
            }
        }
        else
        {
            return false;
        }
    }
	
	    function getAll($sql)
    {
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysqli_fetch_assoc($res))
            {
                $arr[] = $row;
            }

            return $arr;
        }
        else
        {
            return false;
        }
    }
	
	
	    function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $querymode = '')
    {
        $field_names = $this->getCol('DESC ' . $table);

        $sql = '';
        if ($mode == 'INSERT')
        {
            $fields = $values = array();
            foreach ($field_names AS $value)
            {
                if (array_key_exists($value, $field_values) == true)
                {
                    $fields[] = $value;
                    $values[] = "'" . $field_values[$value] . "'";
                }
            }

            if (!empty($fields))
            {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        }
        else
        {
            $sets = array();
            foreach ($field_names AS $value)
            {
                if (array_key_exists($value, $field_values) == true)
                {
                    $sets[] = $value . " = '" . $field_values[$value] . "'";
                }
            }

            if (!empty($sets))
            {
                $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
            }
        }

        if ($sql)
        {
            return $this->query($sql, $querymode);
        }
        else
        {
            return false;
        }
    }
	    function getCol($sql)
    {
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysqli_fetch_row($res))
            {
                $arr[] = $row[0];
            }

            return $arr;
        }
        else
        {
            return false;
        }
    }
	
 }
 

 ?>