<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            //tento skript vygeneruje tabulku se všemi existujícími 1,2,3,4-písmennými názvy a uloží to do databáze
        
            // přihlašovací údaje do databáze
            $servername = "localhost"; 
            $username="root";
            $password="databazka";
            $database="domeny_db";
            
            // název tabulky, která se vytvoří - OKDKOMENTUJ PŘI POUŽITÍ
            //$table="domeny_tab";
         
            // spojení s databází
            $conn = mysqli_connect($servername, $username, $password, $database)
                or die('Nepodařilo se připojit k databázi.');
            
            // vytvoření tabulky
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `jmeno` varchar(4) NOT NULL,
                `dostupnost_com` tinyint(1) NOT NULL,
                `dostupnost_net` tinyint(1) NOT NULL,
                PRIMARY KEY (`id`)
              ) DEFAULT CHARSET=utf8";
            
            if (mysqli_query($conn, $sql)) {
                echo "Vytvoření tabulky se podařilo. Pokračuji generováním a zapisováním možných názvů domén do tabulky.<br /><br />";
            } else {
                echo "Při vytváření tabulky nastala chyba: " . mysqli_error($conn);
            }
            
            // Všechny písmena abecedy (a=0, b=1 atd.)    
            $alphabet = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
        
            // Generátor 1-písmenných názvů   
            echo "Generování 1-písmenných názvů: ";
            for($k=0; $k<26; $k++){
                $sql = "INSERT INTO " . $table. " (jmeno) VALUES ('" . $alphabet[$k] . "')";  
                if (mysqli_query($conn, $sql)) {
                    $procenta = round(($k+1)/(26/100));
                    echo $alphabet[$k] . " (" . $procenta . " %), ";
                } else {
                    echo "Chyba: " . $sql . "<br>" . mysqli_error($conn);
                }
                ob_flush();
                flush();
            }        
           
            // Generátor 2-písmenných názvů
            echo "<br /><br />Generování 2-písmenných názvů: ";
            for($j=0; $j<26; $j++){
                for($k=0; $k<26; $k++){
                    $sql = "INSERT INTO " . $table. " (jmeno) VALUES ('" . $alphabet[$j].$alphabet[$k] . "')";  
                     if (mysqli_query($conn, $sql)) {
                        $procenta = round(($j+1)*($k+1)/(26*26/100));
                        echo $alphabet[$j].$alphabet[$k] . " (" . $procenta . " %), ";
                     } else {
                         echo "Chyba: " . $sql . "<br>" . mysqli_error($conn);
                     } 
                    ob_flush();
                    flush();
                }
            }
            
            // Generátor 3-písmenných názvů
            echo "<br /><br />Generování 3-písmenných názvů: ";
            for($i=0; $i<26; $i++){
                for($j=0; $j<26; $j++){
                    for($k=0; $k<26; $k++){
                        $sql = "INSERT INTO " . $table. " (jmeno) VALUES ('" . $alphabet[$i].$alphabet[$j].$alphabet[$k] . "')";  
                         if (mysqli_query($conn, $sql)) {
                            $procenta = round(($i+1)*($j+1)*($k+1)/(26*26*26/100));
                            echo $alphabet[$i].$alphabet[$j].$alphabet[$k] . " (" . $procenta . " %), ";
                         } else {
                             echo "Chyba: " . $sql . "<br>" . mysqli_error($conn);
                         } 
                        ob_flush();
                        flush();                       
                    }
                }
            } 
            
            // Generátor 4-písmenných názvů
            echo "<br /><br />Generování 4-písmenných názvů: ";
            for($h=0; $h<26; $h++){
                for($i=0; $i<26; $i++){
                    for($j=0; $j<26; $j++){
                        for($k=0; $k<26; $k++){
                            
                            //pokud se to zasekne, tímto kódem lze navázat
                            
                            /*
                            if (($h==0) AND ($i==0) AND ($j==0) AND ($k==0)) {
                             echo "Stav proměnných na začátku smyček:" . $h . $i . $j . $k . ". Nastavíme je jinak, ale jen pokud jsou 0000.";
                             //zadej čísla písmen, kde to skončilo (poslední číslo o jedno větší), a=0, b=1 atd.
                             $h=12;
                             $i=22;
                             $j=8;
                             $k=18;
                             echo " Stav proměnných po změně:" . $h . $i . $j . $k . ". Pokračuji v doplňování tabulky.";
                            }
                            */
                                                        
                            $sql = "INSERT INTO " . $table. " (jmeno) VALUES ('" . $alphabet[$h].$alphabet[$i].$alphabet[$j].$alphabet[$k] . "')";  
                             if (mysqli_query($conn, $sql)) {
                                echo ". ";
                             } else {
                                 echo "Chyba: " . $sql . "<br>" . mysqli_error($conn);
                                 exit("Končím program.");
                             } 
                            ob_flush();
                            flush();  
                                                 
                        }
                    }
                } 
            }    
                      
            // odpojení od databáze
            mysqli_close($conn);
        ?>
    </body>
</html>
