Displeger web
=============


Breton verb conjugator - web interface

This project is an interface for the data in the displeger-verbou project : https://github.com/Drouizig/displeger-verbou

It uses Symfony 4, with yarn, bootstrap and jquery as front technologies.

Set up the project
==================

Clone the repo and execute :

 `composer install`
 `yarn encore dev`

Set up you databse info in the .env file and execute :

 `bin/console doc:da:cr`
 `bin/console doc:sc:cr`
 
 
displeger_format.csv is the file containing all the verb data in the displeger-verbou project. You need to download that file. You can do so by executing this command :
 `wget https://raw.githubusercontent.com/Drouizig/displeger-verbou/master/data/displeger_format.csv`
 
Then import the data with this command :
 `bin/console app:import-verbs displeger_format.csv`
