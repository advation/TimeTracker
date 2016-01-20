<?php

class privateMessageModel extends messagesModel
{
    private $db;
    private $id;
    private $message;
    private $postDate;
    private $expireDate;
    private $userId;
    private $supervisorId;
    private $sentId;
    private $reviewDate;
    private $reviewed;

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

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    /**
     * @param mixed $reviewDate
     */
    public function setReviewDate($reviewDate)
    {
        $this->reviewDate = $reviewDate;
    }

    /**
     * @return mixed
     */
    public function getReviewed()
    {
        return $this->reviewed;
    }

    /**
     * @param mixed $reviewed
     */
    public function setReviewed($reviewed)
    {
        $this->reviewed = $reviewed;
    }

    /**
     * @return mixed
     */
    public function getSupervisorId()
    {
        return $this->supervisorId;
    }

    /**
     * @param mixed $supervisorId
     */
    public function setSupervisorId($supervisorId)
    {
        $this->supervisorId = $supervisorId;
    }

    /**
     * @return mixed
     */
    public function getSentId()
    {
        return $this->sentId;
    }

    /**
     * @param mixed $sentId
     */
    public function setSentId($sentId)
    {
        $this->sentId = $sentId;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function load($id)
    {
        $user = new userModel();
        $uid = $user->getId();

        $sql = "SELECT * FROM privateMessages WHERE id = '".$this->db->real_escape_string($id)."' AND userId = '".$this->db->real_escape_string($uid)."'";

        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        return $result;
    }

    function loadexpired($id)
    {
        $sql = "SELECT * FROM privateMessages WHERE id = '".$this->db->real_escape_string($id)."'";

        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        return $result;
    }

    function supervisorLoad($id)
    {
        $user = new userModel();
        $uid = $user->getId();

        $sql = "SELECT * FROM privateMessages WHERE id = '".$this->db->real_escape_string($id)."' AND supervisorId = '".$this->db->real_escape_string($uid)."'";

        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        return $result;
    }

    function save()
    {
        if(isset($this->id))
        {
            //update
            $sql = "UPDATE privateMessages SET message = '".$this->message."', expireDate ='".$this->expireDate."' WHERE id = '".$this->id."' ";

            if($this->db->query($sql))
            {
                return true;
            }
        }
        else
        {
            //save
            $date = new DateTime();
            $datetime = $date->format('U');
            $user = new userModel();
            $superId = $user->getId();
            $sentId = $user->getId();

            $sql = "INSERT INTO privateMessages (message,postDate,expireDate,userId,supervisorId,sentId) VALUES ('".$this->message."','".$datetime."','".$this->expireDate."','".$this->userId."','".$superId."','".$sentId."')";

            if($this->db->query($sql))
            {
                return true;
            }
        }
    }

    function delete($id)
    {
        $sql = "DELETE FROM privateMessages WHERE id = '".$this->db->real_escape_string($id)."'";

        if($this->db->query($sql))
        {
            return true;
        }
    }

    function markRead($id)
    {
        $sql = "UPDATE privateMessages SET reviewed = '".$this->db->real_escape_string(1)."' WHERE id = '".$this->db->real_escape_string($id)."' ";

        if($this->db->query($sql))
        {
            return true;
        }
    }
}
