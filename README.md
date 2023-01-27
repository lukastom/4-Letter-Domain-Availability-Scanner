# 4 Letter Domain Availability Scanner

This PHP script was built by me in 2015 as an experiment. It scans all 4 letter com and net domains. First, it creates a MySQL database, then scans and writes results to the database and continuously on the screen. In the case of crash, it is able to pick up where it left off. It uses https://github.com/HelgeSverre/Domain-Availability for scanning. Richli documented in the Czech language.

How to install on Ubuntu Linux machine (Czech, note: this worked in 2015!):

1) nainstalovat LAMP
2) nainstalovat nové NetBeans 8.1 (ne z repozitory, tam jsou staré)
3) vytvořit v Apache2 nový virtualhost:

    ServerAdmin webmaster@localhost
    ServerName localhost
    DocumentRoot /home/doma/NetBeansProjects

    <Directory /home/doma/NetBeansProjects>
        Require all granted
    </Directory>

4) nainstalovat Composer
5) nainstalovat do php extension curl, intl
6) upgradovat na PHP 5.6
7) Composerem se do projektu nainstalují chybějící knihovny: composer install (vygeneruje se to nainstalovaným enginem: composer.phar na základě seznamu knihoven: composer.json a na základě seznamu verzí knihoven: composer.lock - to vše dát do jedné složky. Tím se vytvoří složka vendor a do té se stáhnou všechny knihovny, na kterých projekt závisí.)
8) nainstalovat phpMyAdmin: sudo apt-get install phpmyadmin, sudo php5enmod mcrypt
