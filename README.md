Displeger web
=============


Breton verb conjugator - web interface

This project is an interface for the data in the displeger-verbou project : https://github.com/Drouizig/displeger-verbou

It uses Symfony 4, with yarn, bootstrap and jquery as front technologies. It has a backoffice for easy editing.

Set up the project with Docker
==============================

Simply execute the following commands:

`make start`
`make install`

To compile the assets when you change them, execute :
 `make assets`

 The website is then accessible at `http://localhost:8000`


Set up the project without docker
=================================

Clone the repo and execute :

 `composer install`
 
 `yarn encore dev`

Set up you databse info in the .env file and execute :

 `bin/console doc:da:cr`
 
 `bin/console doc:sc:cr`
 
You can then import the database dump that is located at `docker/database/displeger_dump.sql.gz`

Translation
===========

Translation is managed by Weblate https://hosted.weblate.org/projects/displeger-verbou/
Please contact the developers to add a language.

Discord
===========

The project has a Discord server, if you want to help or come to talk about the project: https://discord.gg/ZkrZwJw
