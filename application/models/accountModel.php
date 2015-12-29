<?php

class accountModel extends Staple_Model
{
    private $db;

    private $id;
    private $username;
    private $password;
    private $pin;
    private $tempPin;
    private $firstName;
    private $lastName;
    private $authLevel;
    private $batchId;
    private $supervisorId;
    private $type;
    private $status;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param mixed $authLevel
     */
    public function setAuthLevel($authLevel)
    {
        $this->authLevel = $authLevel;
    }

    /**
     * @param mixed $batchId
     */
    public function setBatchId($batchId)
    {
        $this->batchId = $batchId;
    }

    /**
     * @param mixed $supervisorId
     */
    public function setSupervisorId($supervisorId)
    {
        $this->supervisorId = $supervisorId;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTempPin()
    {
        return $this->tempPin;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();

    }

    function save()
    {
        if(isset($this->id))
        {
            //Edit user

        }
        else
        {
            //Build username
            $username = strtolower(substr($this->firstName,0,1).$this->lastName);

            //Check if username already exists
            $sql = "SELECT username FROM accounts WHERE username = '".$this->db->real_escape_string($username)."'";
            $query = $this->db->query($sql);
            if($query->num_rows == 0)
            {
                //Check if PIN already exists
                $sql = "SELECT pin FROM accounts WHERE pin = '".$this->db->real_escape_string(sha1($this->pin))."'";
                $query = $this->db->query($sql);

                if($query->num_rows > 0)
                {
                    $pin = $this->generatePin();
                }
                else
                {
                    $pin = $this->pin;
                }

                $sql = "

                    INSERT INTO accounts (username,password,pin,firstName,lastName,authLevel,batchId,supervisorId,type,status)
                    VALUES (
                    '".$this->db->real_escape_string($username)."',
                    '".$this->db->real_escape_string(sha1('taketime'))."',
                    '".$this->db->real_escape_string(sha1($pin))."',
                    '".$this->db->real_escape_string($this->firstName)."',
                    '".$this->db->real_escape_string($this->lastName)."',
                    '".$this->db->real_escape_string($this->authLevel)."',
                    '".$this->db->real_escape_string('0')."',
                    '".$this->db->real_escape_string($this->supervisorId)."',
                    '".$this->db->real_escape_string($this->type)."',
                    '".$this->db->real_escape_string('1')."'
                    );
                ";

                if($this->db->query($sql))
                {
                    $this->tempPin = $pin;
                    return true;
                }
            }
        }

    }

    function generatePin()
    {
        $pin = array();

        for($i=0;$i<4;$i++)
        {
            $pin[$i] = rand(0,9);
        }

        $pin = implode("",$pin);

        $sql = "SELECT pin FROM accounts WHERE pin = '".$this->db->real_escape_string(sha1($pin))."'";
        $query = $this->db->query($sql);
        if($query->num_rows == 0)
        {
            return $pin;
        }
        else
        {
            $this->generatePin();
        }
    }
}
?>