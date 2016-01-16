<?php

class privateMessageModel extends messagesModel
{
    private $db;
    private $id;
    private $message;
    private $postDate;
    private $expireDate;
    private $userId;
    private $reviewDate;

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

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function load($id)
    {
        $sql = "SELECT * FROM privateMessages WHERE id = '".$this->db->real_escape_string($id)."' ";

        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        return $result;
    }
}
