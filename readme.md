# epfl-restauration
---

**epfl-restauration** is a WordPress plugin used to display on the  web site the EPFL's daily offers and opening hours of all the catering outlets on the campus. This plugin calls an API from nutriMenu where the restaurant staff enters all menus and other data daily on their web platform. PHP and JavaScript (jQuery) are the main languages used to code the app. 

## Get epfl-restauration plugin
To get the plugin :
- Download **wp-dev** that you will find at the following url: _https://github.com/epfl-si/wp-dev_ ;
- Find the **epfl-restauration** EPFL plugin at the following path: _wp-dev\volumes\wp\5.5.6\wp-content\plugins\epfl-restauration_ ;
- Open your favorite IDE ;
- Import **epfl-restauration** ;
- ✅

## Required environment
To access to the nutriMenu API and get the catering outlets data in the app, we need to set some indentifcication data as the **username**, **password** and the **URL** to nutriMenu.
For this, it is required to have an environment as following:
- Docker (to deploy the app inside an container);
- WSL (a Windows Subsystem for Linux if your OS is not a Linux kernel).

## Configuration of the plugin
Here are a few steps before storing the connection data to the API in the WordPress database:
- At the root of the directory **wp-dev**, open your WSL2 terminal and type the command _make exec_ (to enter in the _mgmt_ docker container) ;
- Go into the directory of your web site with the shell command _cd /srv/test/wp-httpd/_ ;
- Type _wp plugin list_ to be sure that your are in the correct place : you'd note **epfl-restauration** active ;
- You can now set your connection data to nutriMenu API ✅

To set the **username**, **password** and **URL** to access at the nutriMenu API, type these commands on your WSL terminal:
```sh
wp option set epfl_restauration_api_username 'nutriMenu_username'
wp option set epfl_restauration_api_password 'nutriMenu_password'
wp option set epfl_restauration_api_url 'nutriMenu_url'
```

## epfl-restauration file organization
The files of the plugin are organised by only five main **files**:

_epfl-restauration.php_ :
Contains all the configuration of the restauration web app as the connection data to the nutriMenu API, the parameters settings, language management, inclusions of _schedule_ and _menus_ pages and functions.
You will note the connection data to nutriMenu set in you WSL terminal :
```sh
$username = get_option("epfl_restauration_api_username");
$password = get_option("epfl_restauration_api_password");
$url = get_option("epfl_restauration_api_url");
```

_menus.php_ :
This file contains nutriMenu dynamic data managed by PHP code. _menus.php_ represents the **Daily offers in all restaurants** EPFL web page.

_schedule.php_ :
This file contains nutriMenu dynamic data managed by PHP code. _schedule.php_ represents the **Restaurants opening hours** EPFL web page.

_script.js_ :
This is the file who contains all the JavaScript (especially JS library jQuery) code.

_menus.ini_ :
In _menus.ini_, you will find the configuration of the URL associated to the restaurants ID and the FR/EN words's dictionnary of the app.





