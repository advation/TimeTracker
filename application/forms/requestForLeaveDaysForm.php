<?php

class requestForLeaveDaysForm extends Staple_Form
{
    public function _start()
    {
        $this->setLayout('requestForLeaveDaysFormLayout');

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

                $inTime = new Staple_Form_FoundationTextElement("inTimeDay$i",'Start Time');
                $inTime->setRequired()
                    ->addFilter(new Staple_Form_Filter_Trim())
                    ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
                    ->addAttrib('placeholder','h:mm am/pm');

                if($this->staffType == 'full')
                {
                    $inTime->setValue("9:00 AM");
                }

                $this->addField($inTime);

                $outTime = new Staple_Form_FoundationTextElement("outTimeDay$i",'End Time');
                $outTime->setRequired()
                    ->addFilter(new Staple_Form_Filter_Trim())
                    ->addValidator(new Staple_Form_Validate_Regex('/^(0|[0-9]|1[012]):[0-5][0-9] ?((a|p)m|(A|P)M)$/','Invalid time format. Expected format: h:mm am/pm.'))
                    ->addAttrib('placeholder','h:mm am/pm');
                if($this->staffType == 'full')
                {
                    $outTime->setValue("6:00 PM");
                }

                $this->addField($outTime);

                $exclude = new Staple_Form_FoundationCheckboxElement("exclude$i",'Remove this entry');
                $exclude->setRequired();

                $this->addField($exclude);

            }
            else
            {

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