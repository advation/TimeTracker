<?php

class messagesModel extends Staple_Model
{
    private $db;
    private $systemMessages;
    private $expiredSystemMessages;
    private $privateMessages;
    private $expiredPrivateMessages;

    /**
     * @return mixed
     */
    public function getSystemMessages()
    {
        return $this->systemMessages;
    }

    /**
     * @return mixed
     */
    public function getPrivateMessages()
    {
        return $this->privateMessages;
    }

    /**
     * @return mixed
     */
    public function getExpiredSystemMessages()
    {
        return $this->expiredSystemMessages;
    }

    /**
     * @return mixed
     */
    public function getExpiredPrivateMessages()
    {
        return $this->expiredPrivateMessages;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
        $this->systemMessages = $this->loadSystemMessages();
        $this->expiredSystemMessages = $this->loadExpiredSystemMessages();
        $this->privateMessages = $this->loadPrivateMessages();
    }

    private function loadSystemMessages()
    {
        $date = new DateTime();
        $date->setTime(0,0,0);
        $timestamp = $date->format('U');

        $sql = "SELECT id FROM messages WHERE expireDate >= $timestamp ORDER BY postDate DESC";

        $query = $this->db->query($sql);
        $data = array();
        while($result = $query->fetch_assoc())
        {
            $message = new messageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }

    private function loadExpiredSystemMessages()
    {
        $date = new DateTime();
        $date->setTime(23,59,59);
        $timestamp = $date->format('U');

        $sql = "SELECT id FROM messages WHERE expireDate <= $timestamp ORDER BY postDate DESC";

        $query = $this->db->query($sql);
        $data = array();
        while($result = $query->fetch_assoc())
        {
            $message = new messageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }

    private function loadPrivateMessages()
    {
        $date = new DateTime();
        $date->setTime(0,0,0);

        $sql = "SELECT id FROM privateMessages ORDER BY postDate DESC";
        $query = $this->db->query($sql);

        $data = array();

        while($result = $query->fetch_assoc())
        {
            $message = new privateMessageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }
}
