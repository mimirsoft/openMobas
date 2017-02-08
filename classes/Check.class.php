<?php
class checks{
    
    public static function check_amount($net)
    {
        $check_amount = '';
        $thousands = floor($net/1000); // get the number of thousands
        if($thousands != 0)
        {
            $thousand_tens = floor($thousands/10); //convert it to an integer.
            $thousand_ones = ($thousands%10);
            $thousands_as_word = Framework::tens_to_word($thousand_tens, $thousand_ones);
            if($thousand_tens != 1)
            {
                $thousands_as_word .= Framework::numeral_to_word($thousand_ones);
            }
    
            $check_amount .= $thousands_as_word; //convert it to a string
            $check_amount .= "THOUSAND "; //add the word THOUSAND to it
        }
        $hundreds = ($net%1000); // get the number of hundreds
        $hundred = floor($hundreds/100); // get the number of hundreds
        if($hundred != 0)
        {
                $check_amount .= Framework::numeral_to_word($hundred); //convert it to a string
                $check_amount .= "HUNDRED "; //add the word HUNDRED to it
        }
        $tens = ($hundreds%100); //get the number of tens
        $ten = floor($tens/10); //convert it to an integer.
        $ones = ($tens%10);
        $tens_as_word = Framework::tens_to_word($ten, $ones);
        if($ten != 1)
        {
                $tens_as_word .= Framework::numeral_to_word($ones);
        }
        $check_amount .= $tens_as_word;
        $cents = bcsub($net, floor($net), 2);
        $cents = $cents * 100;
        if($cents == '')
        {
        $cents = 0;
        }
        $check_amount = $check_amount . "AND " . $cents . "/100";

        return $check_amount;
    }
}

?>
