<?php

class requestForLeavePartTimeDaysForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('requestForLeavePartTimeDaysFormLayout');

        $this->setName('requestForLeaveDays')
            ->setAction($this->link(array('requests','days')));

        $startDate = $_SESSION['startDate'];
        $endDate = $_SESSION['endDate'];
        $numDays = $this->numOfDay($startDate,$endDate);

        $date = new DateTime();
        $date->setTimestamp($startDate);

        $user = new userModel();
        $this->staffType = $user->getType();

        for($i=0;$i<=$numDays;$i++)
        {
            //Ignore Sundays
            if($date->format("l") != 'Sunday')
            {
                $dateLabel = $date->format("l, F jS Y");
                $field = new Staple_Form_FoundationTextElement("day$i","$dateLabel");
                $field->setValue($dateLabel);
                $this->addField($field);

                $hours = new Staple_Form_FoundationTextElement("hours$i",'Total Hours for this date');
                $hours->setRequired()
                    ->addFilter(new Staple_Form_Filter_Trim())
                    ->addClass("hours")
                    ->addValidator(new Staple_Form_Validate_Numeric());
                    //->addValidator(new Staple_Form_Validate_Float());

                $this->addField($hours);

                $exclude = new Staple_Form_FoundationCheckboxElement("exclude$i",'Ignore this entry');
                $exclude->setRequired()
                    ->addAttrib("data-id",$i)
                    ->addAttrib("data-exclude","1");

                $this->addField($exclude);

            }
            $date->modify("+1 day");
        }

        $submit = new Staple_Form_FoundationSubmitElement('submit','Submit');
        $submit->addClass('button expand radius');

        $this->addField($submit);
    }

    private function numOfDay($start,$end)
    {
        $datediff = $end - $start;
        $count = floor($datediff / (60 * 60 * 24));
        if($count >= 1)
        {
            return $count;
        }
        else
        {
            return 0;
        }
    }
}
?>
