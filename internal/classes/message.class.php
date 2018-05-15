<?php 

include "../properties.inc.php";

class Message {
    private $id = 0;
    private $sender = "";
    private $chat = 0;
    private $time = "";
    private $text = "";
    
    public function __construct($id) {
        $this->id = $id;
        
        refresh_values();
    }
    
    public function refresh_values() {
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
        mysqli_select_db($connection, DB_NAME);
        
        $sql = "SELECT * FROM nachricht WHERE id = ".$this->id;
        $db_result = mysqli_query($connection, $sql);
        
        $row = mysqli_fetch_assoc($db_result);
        $this->sender = $row["sender"];
        $this->chat = $row["chat"];
        $this->date = $row["zeit"];
        $this->text = $row["text"];
        
        mysqli_close($connection);
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
    
}

?>