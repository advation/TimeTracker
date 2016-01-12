<?php

class messagesModel extends Staple_Model
{
    private $db;
    private $id;
    private $message;
    private $postDate;
    private $expireDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getExpireDate()
    {
        $date = new DateTime();

        $date->setTimestamp($this->expireDate);

        return $date->format('m/d/Y');
    }

    /**
     * @param mixed $expireDate
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = strtotime($expireDate);
    }

    /**
     * @return mixed
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function load($id)
    {
        $sql = "SELECT * FROM messages WHERE id = '".$this->db->real_escape_string($id)."' ";

        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        $this->id = $result['id'];
        $this->expireDate = $result['expireDate'];
        $this->message = $result['message'];
        $this->postDate = $result['postDate'];
    }

    function getMessages()
    {
        $date = new DateTime();
        $date->setTime(0,0,0);

        $sql = "
        SELECT * FROM messages WHERE expireDate >= '".$this->db->real_escape_string($date->format('U'))."' ORDER BY postDate DESC;
        ";

        $data = array();

        $query = $this->db->query($sql);

        while($result = $query->fetch_assoc())
        {
            $data[] = $result;
        }

        return $data;
    }

    function getExpiredMessages()
    {
        $date = new DateTime();
        $date->setTime(0,0,0);

        $sql = "
            SELECT * FROM messages WHERE expireDate < '".$this->db->real_escape_string($date->format('U'))."' ORDER BY postDate DESC;
        ";

        $data = array();

        $query = $this->db->query($sql);
        while($result = $query->fetch_assoc())
        {
        $data[] = $result;
        }

        return $data;
    }

    function save()
    {
        if(isset($this->id))
        {
            //Edit
            $sql = "UPDATE messages SET expireDate = '".$this->expireDate."', message = '".$this->message."' WHERE id = '".$this->id."';";

            if($this->db->query($sql))
            {
                return true;
            }
        }
        else
        {
            //Save
            $sql = "INSERT INTO messages (message,expireDate) VALUES ('".$this->db->real_escape_string($this->message)."','".$this->db->real_escape_string($this->expireDate)."')";

            if($this->db->query($sql))
            {
                return true;
            }
        }
    }

    function delete($id)
    {
        $sql = "DELETE FROM messages WHERE id = '".$this->db->real_escape_string($id)."';";

        if($this->db->query($sql))
        {
            return true;
        }
    }



}
