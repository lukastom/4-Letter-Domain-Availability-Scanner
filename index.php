<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <?php

        // natáhnutí knihoven
        require './vendor/autoload.php';

        use Helge\Loader\JsonLoader;
        use Helge\Client\SimpleWhoisClient;
        use Helge\Service\DomainAvailability;

        $whoisClient = new SimpleWhoisClient();
        $dataLoader = new JsonLoader("src/data/servers.json");

        $service = new DomainAvailability($whoisClient, $dataLoader);
        
        $dotazy_na_registratora = 0;//reset proměnné
        echo "<b>Volné domény:</b><br />"; 
        ob_flush();
        flush();
        
        // přihlašovací údaje do databáze
        $servername = "localhost"; 
        $username="root";
        $password="databazka";
        $database="domeny_db";
        $table="domeny_tab";
        
        // spojení s databází
        $conn = mysqli_connect($servername, $username, $password, $database)
            or die('Nepodařilo se připojit k databázi.');
        
        //zjistíme id posledního row, ve kterém je vyplněn čas
        $sql = "SELECT * FROM domeny_tab ORDER BY cas_kontroly DESC LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $posledni_id = $row["id"];

        //hlavní loop, počet běhů určuje proměnná $behy
        
        //zjištění, kolik řádků má tabulka
        $query = "SELECT * FROM domeny_tab";
        $result = mysqli_query($conn,$query);
        $pocet_radku = mysqli_num_rows($result);
        
        $behy = $pocet_radku-$posledni_id;
        for($j=0; $j<$behy; $j++){
            //z následujícího row si přečteme jméno domény
            $sql = "SELECT jmeno FROM domeny_tab WHERE id=" . ($posledni_id+1+$j) . " LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $jmeno_domeny = $row["jmeno"];
            
            //TEST!!!
            //if (($posledni_id+1+$j)==7) {
            //    $jmeno_domeny = "hgtkgjzzu";
            //}

            //ověření com a net domény, zápis kontroly do řádku v databázi
            for($i=0; $i<2; $i++){

                //rozpoznání koncovky (0=com, 1=net)
                if ($i==0) {
                    $koncovka = "com";
                } else {
                    $koncovka = "net";
                }

                //přidání koncovky k názvu domény
                $domena = $jmeno_domeny . "." . $koncovka; 

                //Javascript měnící titulek okna na právě zpracovávanou doménu a procenta
                $procenta = round(($posledni_id+1+$j)/(475254/100));
                $titulek_okna = $domena . " (" . $procenta . " %, R=" . $dotazy_na_registratora . ")";
                ?>
                <script type="text/javascript">
                    document.title = "<?=$titulek_okna;?>"
                </script>
                <?php
                ob_flush();
                flush(); 
                
                //TEST!!!
                //if ($j==3) {
                //    $domena = "hhtzzkgju.com";
                //}

                // První dvě kontroly nejsou 100 % spolehlivé. Jsou tu kvůli zrychlení analýzy a ušetření dotazů na whois registrátorů
                // kontrola 1 - gethostbyname() - pokud je doména obsazená, vyhodí to IP adresu. Pokud je volná, vyhodí to původní adresu webu. Závisí to na lokání konfigraci PC.
                $dostupna = false; //resetování proměnné z minulého běhu na 0
                if (gethostbyname($domena) !== $domena) {
                    // Je obsazená
                 } else {
                    // kontrola 2 - checkdnsrr(), dns_get_record() - kouká se na DNS servery, jestli má doména DNS záznamy, je obsazená. Může být pomalejší.
                    $domena_com_tecka = $domena . "."; //přidání trailing period, zrychluje vyhledání
                    if (checkdnsrr($domena_com_tecka, 'ANY')) {
                        // Je obsazená
                    } else {
                        // kontrola 3 - Whois u registrátora - můžou mít různá omezení (např. denní limit)
                        if ($service->isAvailable($domena)) {
                            $dostupna = true; //pouze pokud prošlo všemi 3 kontrolami
                            $dotazy_na_registratora = $dotazy_na_registratora+1;
                            //randomizovaný sleep, abychom nepřehltili registrátora
                            sleep(rand(7, 13));
                            if ($dotazy_na_registratora==1000){
                                echo "<b>Končím, dosáhl jsem 1000 dotazů na registrátora. Pokud chceš pokračovat, spusť to znova.</b>";
                                mysqli_close($conn);
                                die();
                            }
                        } else {
                            // Je obsazená
                        }
                    }  
                }

                // co dělat, po kontrole dostupnosti
                if($dostupna == true) {
                    //Je dostupná - zápis kontroly do databáze
                    $cas = date('Y-m-d H:i:s');
                    $sql = "UPDATE domeny_tab SET dostupnost_" . $koncovka . "=true, cas_kontroly='" . $cas . "' WHERE id=". ($posledni_id+1+$j);
                    if (mysqli_query($conn, $sql)) {
                        //echo "Zápis domény do databáze proběhl na řádek ". ($posledni_id+1);
                    } else {
                         echo "Chyba: " . $sql . "<br>" . mysqli_error($conn);
                    } 
                    echo $domena . "<br />"; 
                    ob_flush();
                    flush(); 
                } else {
                    // Je obsazená - zápis kontroly do databáze
                    $cas = date('Y-m-d H:i:s');
                    $sql = "UPDATE domeny_tab SET dostupnost_" . $koncovka . "=false, cas_kontroly='" . $cas . "' WHERE id=". ($posledni_id+1+$j);
                    if (mysqli_query($conn, $sql)) {
                        //echo "Zápis domény do databáze proběhl na řádek ". ($posledni_id+1);
                    } else {
                         echo "Chyba: " . $sql . "<br>" . mysqli_error($conn);
                    } 
                    //echo "Doména " . $domena_com . " je obsazená:-(";
                }
            }
        }

        echo "<b>Končím, dostal jsem se úplně na konec programu.</b>";
        // odpojení od databáze
        mysqli_close($conn);
         
    ?>
</body>
</html>

