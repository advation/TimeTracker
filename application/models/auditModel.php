<?php
class auditModel extends Staple_Model
{
    private $db;

    private $timestamp;
    private $action;
    private $userId;
    private $group;
    private $item;
    private $pager;

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
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
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * @param mixed $pager
     */
    public function setPager($pager)
    {
        $this->pager = $pager;
    }

    function __construct()
    {
        $this->db = Staple_DB::get();
    }

    function save()
    {
        if(isset($this->userId) && isset($this->action) && isset($this->item))
        {
            $sql = "
                INSERT INTO audit (action, userId, item) VALUES ('".$this->db->real_escape_string($this->getAction())."','".$this->db->real_escape_string($this->getUserId())."','".$this->db->real_escape_string($this->getItem())."');
            ";

            if($this->db->query($sql))
            {
                return true;
            }
        }
    }

    function getAll($page,$items)
    {
        $pager = new Staple_Pager();

        //Get total rows
        $sql = "SELECT COUNT(id) as count FROM audit";
        $result = $this->db->query($sql)->fetch_assoc();
        $total = $result['count'];

        $pager->setTotal($total);
        $pager->setItemsPerPage($items);
        $pager->setPage($page);

        $sql = "
            SELECT * FROM audit WHERE 1 ORDER BY timestamp ASC LIMIT ".$pager->getStartingItem().", ".$pager->getItemsPerPage()."
        ";

        $this->pager = $pager;

        if($this->db->query($sql)->num_rows > 0)
        {
            $query = $this->db->query($sql);

            $data = array();
            $i = 0;
            while($result = $query->fetch_assoc())
            {
                $data[$i]['timestamp'] = $result['timestamp'];
                $account = new userModel();
                $data[$i]['account'] = $account->userInfo($result['userId']);
                $data[$i]['action'] = $result['action'];
                $data[$i]['item'] = $result['item'];
                $i++;
            }

            return $data;
        }
        else
        {
            return array();
        }
    }
}
?>