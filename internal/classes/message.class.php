<?php 

class Message {
    private $id = 0;
    private $sender = "";
    private $chat = 0;
    private $time = "";
    private $text = "";
    private $read_by_at = array();
    private $connection = null;
    
    public function __construct($id)
{
    $this->id = $id;
    
    $this->refresh_values();
}
    
    public function refresh_values() {
        $this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
        mysqli_select_db($this->connection, DB_NAME);
        
        $sql = "SELECT * FROM nachricht WHERE id = ".$this->id;
        $db_result = mysqli_query($this->connection, $sql);
        
        $row = mysqli_fetch_assoc($db_result);
        $this->sender = $row["sender"];
        $this->chat = $row["chat"];
        $this->time = $row["zeit"];
        $this->text = $row["text"];
        
        $read_by_sql = "SELECT * FROM gelesen WHERE nachricht = ".$this->id;
        $read_by_result = mysqli_query($this->connection, $read_by_sql);
        
        while ($row = mysqli_fetch_assoc($read_by_result)) {
            $this->read_by_at[$row["benutzer"]] = $row["zeit"];
        }
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_sender() {
        return $this->sender;
    }
    
    public function get_chat() {
        return $this->chat;
    }
    
    public function get_time() {
        return $this->time;
    }
    
    public function get_text() {
        return $this->text;
    }
    
    public function read_at($user) {
        return $this->read_by_at[$user];
    }
    
    public function read_by() {
        return array_keys($this->read_by_at);
    }
    
    public function set_read($user) {
        if(!isset($this->read_by_at[$user]) and $this->sender != $user) {
            $this->read_by_at[$user] = date("Y:m:d H:i:s", time());
        
            $sql = "INSERT INTO gelesen (nachricht, benutzer, zeit) VALUES (".$this->id.", '".$user."', '".$this->read_by_at[$user]."')";
            mysqli_query($this->connection, $sql);
        }
    }
    
}

?>