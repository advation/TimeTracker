<?php

class changeYearForm extends Staple_Form
{
    public function _start()
    {
        //$this->setLayout('');

        $this->setName('changeYearForm')
         ->setAction($this->link(array('timesheet','changeyear')));

        $year = new Staple_Form_FoundationSelectElement('year','Year');
        $year->setRequired()
            ->addOptionsArray($this->getYears())
            ->addValidator(new Staple_Form_Validate_InArray($this->getYears()));

        if(count($this->getYears()) == 0)
        {
            $year->addOption(date('Y'),date('Y'));
        }

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($year,$submit);
    }

    function getYears()
    {
        $db = Staple_DB::get();

        //Get user ID from Auth
        $user = new userModel();
        $userId = $user->getId();

        $sql = "SELECT YEAR(FROM_UNIXTIME(inTime)) AS 'year' FROM timeEntries WHERE userId = $userId GROUP BY year ORDER by year ASC";

        if($db->query($sql)->num_rows > 0)
        {
            $query = $db->query($sql);
            $data = array();
            while($result = $query->fetch_assoc())
            {
                $data[$result['year']] = $result['year'];
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