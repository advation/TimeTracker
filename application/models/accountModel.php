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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    /**
     * @return mixed
     */
    public function getTempPin()
    {
        return $this->tempPin;
    }

    /**
     * @param mixed $tempPin
     */
    public function setTempPin($tempPin)
    {
        $this->tempPin = $tempPin;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getAuthLevel()
    {
        return $this->authLevel;
    }

    /**
     * @param mixed $authLevel
     */
    public function setAuthLevel($authLevel)
    {
        $this->authLevel = $authLevel;
    }

    /**
     * @return mixed
     */
    public function getBatchId()
    {
        return $this->batchId;
    }

    /**
     * @param mixed $batchId
     */
    public function setBatchId($batchId)
    {
        $this->batchId = $batchId;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();

    }

    function load($id)
    {
        $sql = "SELECT id, username, firstName, lastName, authLevel, batchId, supervisorId, type, status FROM accounts WHERE id = '".$this->db->real_escape_string($id)."'";
        $query = $this->db->query($sql);
        $result = $query->fetch_assoc();

        $data = array();

        $data['id'] = $result['id'];
        $data['username'] = $result['username'];
        $data['firstName'] = $result['firstName'];
        $data['lastName'] = $result['lastName'];
        $data['level'] = $result['authLevel'];
        $data['supervisor'] = $result['supervisorId'];
        $data['type'] = $result['type'];
        $data['status'] = $result['status'];

        return $data;
    }

    function save()
    {
        if(isset($this->id))
        {
            //Check if username already exists
            $sql = "SELECT username FROM accounts WHERE username = '".$this->db->real_escape_string($this->username)."' AND id <> '".$this->db->real_escape_string($this->id)."'";
            $query = $this->db->query($sql);
            if($query->num_rows == 0)
            {
                $sql = "
                    UPDATE accounts SET
                    username = '".$this->db->real_escape_string($this->username)."',
                    firstName = '".$this->db->real_escape_string($this->firstName)."',
                    lastName = '".$this->db->real_escape_string($this->lastName)."',
                    authLevel = '".$this->db->real_escape_string($this->authLevel)."',
                    supervisorId = '".$this->db->real_escape_string($this->supervisorId)."',
                    type = '".$this->db->real_escape_string($this->type)."',
                    status = '".$this->db->real_escape_string($this->status)."'
                    WHERE id = '".$this->db->real_escape_string($this->id)."'
                ";

                if($this->db->query($sql))
                {
                    return true;
                }
            }
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
                    $id = $this->db->insert_id;

                    $this->tempPin = $pin;

                    $account = new userModel();
                    $userInfo = $account->userInfo($id);

                    $audit = new auditModel();
                    $audit->setUserId($userInfo['id']);
                    $audit->setAction('New Account Created');
                    $audit->setItem($account->getUsername()." created account.");
                    $audit->save();

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

    function resetPin($id)
    {
        $pin = $this->generatePin();
        $this->tempPin = $pin;

        $sql = "UPDATE accounts SET pin='".$this->db->real_escape_string(sha1($pin))."' WHERE id = '".$this->db->real_escape_string($id)."'";

        if($this->db->query($sql))
        {
            $account = new userModel();
            $userInfo = $account->userInfo($id);

            $audit = new auditModel();
            $audit->setUserId($userInfo['id']);
            $audit->setAction('PIN Reset');
            $audit->setItem($account->getUsername()." reset users PIN.");
            $audit->save();

            return true;
        }
    }
}
?>