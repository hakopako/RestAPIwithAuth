<?php

namespace App\Models;

use App\Config;

abstract class Model {

    static protected $table;

    /**
     * @param Array $properties table fields
     */
    function __construct($properties=[])
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }
    /**
     * Create connection
     */
    private function connect()
    {
        $config   = Config::get();
        $schema   = $config['DB_CONNECTION'];
        $host     = $config['DB_HOST'];
        $user     = $config['DB_USERNAME'];
        $password = $config['DB_PASSWORD'];
        $dbname   = $config['DB_DATABASE'];
        $port     = $config['DB_PORT'];
        if(!isset($this->database)){
            try {
                $this->database = new \PDO("$schema:host=$host;port=$port;dbname=$dbname;", $user, $password);
                if(!$this->database){
                    throw new \Exception('db connect error.');
                }
            } catch(\Exception $e) {
                throw new \Exception('db connect error.');
            }
        }
    }

    /**
     * Close connection
     */
    private function close()
    {
        if(isset($this->database)){
            $this->database = null;
        }
    }

    /**
     * Extract records from database.
     * @param String $sql
     * @return Array table records
     */
    private function select(string $sql)
    {
        $klass = get_called_class();
        try {
            $this->connect();
            $statement = $this->database->prepare($sql);
            $statement->execute();
            $records = [];
            while($row = $statement->fetch(\PDO::FETCH_ASSOC)){
                $records[] = new $klass($row);
            }
            $statement = null;
            $this->close();
            return $records;
        } catch(\Exception $e) {
            $this->close();
            return [];
        }
    }

    /**
     * Execute sql.
     * @param String $sql
     * @return Bool true=success, false=failed
     */
    private function execute(string $sql)
    {
        try {
            $this->connect();
            $statement = $this->database->prepare($sql);
            $result = $statement->execute();
            $this->close();
            return $result;
        } catch(\Exception $e) {
            $this->close();
            return false;
        }
    }

    /**
     * Find a record with id column.
     * @param Int $id
     * @param Array $config db config
     * @return Object/null
     */
    static public function find(int $id)
    {
        $klass = get_called_class();
        $record = (new $klass)->select("SELECT * FROM ".$klass::$table." WHERE id = $id");
        return (count($record) == 1) ? $record[0] : null;
    }

    /**
     * Find all record in a table.
     * @param Array $config db config
     * @return Array<Object>
     */
    static public function all()
    {
        $klass = get_called_class();
        return (new $klass)->select("SELECT * FROM ".$klass::$table);
    }

    /**
     * Find records with conditions in a table.
     * @param String $sql condition which fllows "WHERE".
     * @param Array $config db config
     * @return Array<Object>
     */
    static public function where(string $sql)
    {
        $klass = get_called_class();
        return (new $klass)->select("SELECT * FROM ".$klass::$table." WHERE ".$sql);
    }

    /**
     * Insert a record into a table.
     * @return Array<Object>
     */
    public function save()
    {
        $klass = get_called_class();
        $properties = (array)$this;
        if(!isset($this->id)){
            $keys = implode(', ', array_keys($properties));
            $values = "'".implode("', '", array_values($properties))."'";
            return $this->execute("INSERT INTO ".$klass::$table." ($keys) VALUES ($values)");
        } else {
            $data = [];
            unset($properties['id']);
            foreach ($properties as $key => $value) {
                $data[] = $key." = '".$value."'";
            }
            $newData = implode(" , ", $data);
            return $this->execute("UPDATE ".$klass::$table." SET $newData WHERE id = $this->id");
        }
    }

    /**
     * Delete a record from a table.
     * @return Bool true=success,false=failed
     */
    public function delete()
    {
        if(!isset($this->id)){
            return false;
        }
        return $this->execute("DELETE FROM ".$this::$table." WHERE id = $this->id");
    }

    /**
     * Object to Array
     * @return Array
     */
    public function toArray()
    {
        return (array)$this;
    }
}
