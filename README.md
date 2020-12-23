# FeedReader

Web Application to read and manage your RSS/Atom-Feeds.

## Features

* Create, modify and delete feeds and categories
* Read Atom and RSS Feeds with Thumbnail preview
* OPML-Import/Export of your Feeds used in other Applications

## Used Tech Stack

* [Symfony Framework](https://symfony.com/)
* [Docker](https://www.docker.com/) with the following [Template](https://github.com/tmxs/DockerTemplates/tree/master/Symfony4)
* [Doctrine ORM](https://www.doctrine-project.org/)
* [Bootstrap CSS](https://getbootstrap.com/)

## Screenshots

### Notes Overview:

![feed-overview](https://raw.githubusercontent.com/tmxs/FeedReader/master/assets/imgs/feedreader.png)

### Manage Feeds:

![manage-feeds](https://raw.githubusercontent.com/tmxs/FeedReader/master/assets/imgs/managefeeds.png)

## Installation

* Create a specific folder for the Application
* Download the following [Docker Template](https://github.com/tmxs/DockerTemplates/tree/master/Symfony4) into that folder
    * Obviously, if Docker is not installed, install it now
* Create a folder named `app` and clone this repository into it with `git clone https://github.com/tmxs/FeedReader.git`

* Then go into the root directory of your application (where the docker-compose.yml file is) and run the following commands:

    docker-compose exec web bash

    php bin/console doctrine:schema:update

* You can then open the RSS-Reader on [Localhost](http://127.0.0.1)

## ToDo's

* Filter Feeds by Category and Feed Source
