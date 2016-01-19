<?php

class messagesModel extends Staple_Model
{
    private $db;
    private $systemMessages;
    private $expiredSystemMessages;
    private $privateMessages;
    private $expiredPrivateMessages;
    private $allPrivateMessages;
    private $totalPrivateMessages;
    private $supervisorMessages;

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

    /**
     * @return int
     */
    public function getTotalPrivateMessages()
    {
        return $this->totalPrivateMessages;
    }/**

     * @return mixed
     */
    public function getAllPrivateMessages()
    {
        return $this->allPrivateMessages;
    }

    /**
     * @return mixed
     */
    public function getSupervisorMessages()
    {
        return $this->supervisorMessages;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
        $this->systemMessages = $this->loadSystemMessages();

        $this->privateMessages = $this->loadPrivateMessages();
        $this->allPrivateMessages = $this->loadAllPrivateMessages();
        $this->totalPrivateMessages = $this->countPrivateMessages();
        $this->supervisorMessages = $this->loadSupervisorMessages();

        $this->expiredSystemMessages = $this->loadExpiredSystemMessages();
        $this->expiredPrivateMessages = $this->loadExpiredPrivateMessages();
    }

    private function loadSystemMessages()
    {
        $date = new DateTime();
        $date->setTime(0,0,0);
        $timestamp = $date->format('U');

        $sql = "SELECT id FROM messages WHERE expireDate >= $timestamp ORDER BY postDate ASC";

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

        $sql = "SELECT id FROM messages WHERE expireDate < $timestamp ORDER BY postDate ASC";

        $query = $this->db->query($sql);
        $data = array();
        while($result = $query->fetch_assoc())
        {
            $message = new messageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }

    private function loadExpiredPrivateMessages()
    {
        $date = new DateTime();
        $date->setTime(23,59,59);
        $timestamp = $date->format('U');

        $sql = "SELECT id FROM privateMessages WHERE expireDate < $timestamp ORDER BY postDate ASC";

        $query = $this->db->query($sql);
        $data = array();
        while($result = $query->fetch_assoc())
        {
            $message = new privateMessageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }

    private function loadPrivateMessages()
    {
        $user = new userModel();
        $userId = $user->getId();

        $date = new DateTime();
        $date->setTime(0,0,0);

        $sql = "SELECT id FROM privateMessages WHERE userId = '".$userId."' AND expireDate >= '".$date->format('U')."' AND reviewed = '0' ORDER BY postDate ASC limit 1";
        $query = $this->db->query($sql);

        $data = array();

        while($result = $query->fetch_assoc())
        {
            $message = new privateMessageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }

    private function loadAllPrivateMessages()
    {
        $user = new userModel();
        $userId = $user->getId();

        $date = new DateTime();
        $date->setTime(0,0,0);

        $sql = "SELECT id FROM privateMessages WHERE userId = '".$userId."' AND expireDate >= '".$date->format('U')."' ORDER BY postDate ASC";
        $query = $this->db->query($sql);

        $data = array();

        while($result = $query->fetch_assoc())
        {
            $message = new privateMessageModel();
            $data[] = $message->load($result['id']);
        }

        return $data;
    }

    private function countPrivateMessages()
    {
        $user = new userModel();
        $userId = $user->getId();

        $sql = "SELECT id FROM privateMessages WHERE userId = '".$userId."' AND reviewed = '0'";
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    private function loadSupervisorMessages()
    {
        $user = new userModel();
        $userId = $user->getId();

        $date = new DateTime();
        $date->setTime(0,0,0);

        $sql = "SELECT id FROM privateMessages WHERE supervisorId = '".$userId."' AND expireDate >= '".$date->format('U')."' ORDER BY postDate ASC";
        $query = $this->db->query($sql);

        $data = array();

        while($result = $query->fetch_assoc())
        {
            $message = new privateMessageModel();
            $data[] = $message->supervisorLoad($result['id']);
        }

        return $data;
    }
}
