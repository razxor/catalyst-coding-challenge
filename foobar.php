<?php
/*
Raz Ahamed | raz.abcoder@gmail.com

2. Logic Test
-----------------------------------------------------------------------------
Create a PHP script that is executed form the command line. The script should: 
• Output the numbers from 1 to 100 • Where the number is divisible by three (3) output the word “foo” 
• Where the number is divisible by five (5) output the word “bar” 
• Where the number is divisible by three (3) and (5) output the word “foobar” 
• Only be a single PHP file 
*/
for ($counter = 1; $counter<=100; $counter++) {
    #Where the number is divisible by only three (3) OR three (3) and (5) output the word “foobar” 
    if (($counter % 3 == 0)){
        echo "foo";
        if (($counter % 5 == 0)){
            echo "bar";
        }
        echo "\n";
    }

    #Where the number is divisible by only five (5) output the word “bar”  
    elseif (($counter % 5 == 0)){
        echo "bar"."\n";
    }

    # For rest of the numbers
    else {
        echo $counter."\n";      
    }
}
?>