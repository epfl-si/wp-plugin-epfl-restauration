# epfl-restauration
---

**epfl-restauration** is a WordPress plugin used to display on the  web site the EPFL's daily offers and opening hours 
of all the restaurants on the campus. This plugin calls an API from nutriMenu where the restaurant staff enters all menus 
and other data daily on their web platform. PHP and JavaScript (jQuery) are the main languages used to code the plugin.

## Required environment
To get the epfl-restauration plugin, then access the nutriMenu API and obtain restaurant data in the application, we 
need to set identification data as the **username**, **password** and the **URL** to nutriMenu.
First we need to deploy the application in a container and it is better to have a Linux kernel machine.
Here's the environment we need to install the plugin, configure it and deploy it:
- A Linux OS virtual machine (e.g. Debian) or WSL (a Windows Subsystem for Linux if your OS is not a Linux kernel) ;
- Docker (to deploy the app inside a container). Follow the steps to install Docker for Debian: *https://docs.docker.com/engine/install/debian/* ;

However, we encountered a problem when installing the plugin with WSL on a Windows 11 operating system. 
We therefore recommend that you install a Linux VM instead of using WSL if you have a Windows OS machine.

## Get epfl-restauration plugin
To get the plugin :
- Download **wp-dev** that you will find at the following url: *https://github.com/epfl-si/wp-dev* ;
- Find the **epfl-restauration** EPFL plugin at the following path: *wp-dev\volumes\wp\X.Y.Z\wp-content\plugins\epfl-restauration* (X.Y.Z representing the version number) ;
- Import **epfl-restauration** in your favorite IDE ;
- ✅

## Configuration of the plugin
Here are a few steps before storing the connection data to the API in the WordPress database:
- At the root of the directory **wp-dev**, open your CLI and type the command *make exec* (to enter in the *mgmt* docker container) ;
- Go into the directory of your web site with the shell command *cd /srv/test/wp-httpd/htdocs* ;
- Type *wp plugin list* to be sure that your are in the correct place and activate the **epfl-restauration** plugin with the following command : `wp plugin activate epfl-restauration` ;
- You can now set your connection data to nutriMenu API ✅

To set the **username**, **password** and **URL** (you'll find these data in our Password Safe password manager) to access at the nutriMenu API, type these commands :
```sh
wp option set epfl_restauration_api_username 'nutriMenu_username'
wp option set epfl_restauration_api_password 'nutriMenu_password'
wp option set epfl_restauration_api_url 'nutriMenu_url'
```

## Environment settings before development
Here are a few adjustments to be made before you can start developing the new feature.

### Wordpress
- Open a browser, then go to *https://wp-httpd/* ;
- Create a new web page from the Wordpress Dashboard.

### Shortcode
In the web page created from the Wordpress Dashboard, add a *Shortcode* and enter one of the Shortcodes from the table 
below depending on the page requiring the new functionality.

In the table, here is the structure of the WordPress shortcodes for the four menu and timetable pages (FR/EN).
As you can see, there's no need to specify the language as a parameter.

| Page                                                        | Shortcode                                      |
|-------------------------------------------------------------|------------------------------------------------|
| Offre du jour dans tous les points de restauration (FR)     | [epfl_restauration type="menu"]                |
| Daily offers in all restaurants (EN)                        | [epfl_restauration type="menu"]                |
| Horaires points de restauration (FR)                        | [epfl_restauration type="schedule"]            |
| Restaurants opening hours (EN)                              | [epfl_restauration type="schedule"]            |

For each restaurant page, you need to add a shortcode for displaying the menus and a shortcode for displaying the opening times. 
In order for the menus and opening times to apply to the desired restaurant, you need to enter its corresponding ID.

Here's an example for the *Arcadie* :
- Shortcode for menus ➔ [epfl_restauration type=‘menu’ params=‘resto_id=103’]
- Shortcode for schedules ➔ [epfl_restauration type=‘schedule’ params=‘resto_id=103’]

In the event of a problem with the shortcode (shortcode "not working" or error displayed on the page),
check the configuration of **username**, **password** and **URL**).

## Development in the IDE and pull request
With the *Shortcode Wordpress* configured in the page, every modification made in the code will be directly visible 
on the page created on https://wp-httpd/ !

Once the new feature has been completed in the new branch (e.g. *epfl-si/feature/allergens*), perform a pull request.

## epfl-restauration file organization
The files of the plugin are organised as below:

_epfl-restauration.php_ :
Contains all the configuration of the restauration web app as the connection data to the nutriMenu API, the parameters settings, language management, inclusions of _schedule_ and _menus_ pages and functions.
You will note the connection data to nutriMenu set in your terminal :
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
This is the file that contains all the JavaScript (especially JS library jQuery) code.

_menus.ini_ :
In _menus.ini_, you will find the configuration of the URL associated to the restaurants ID and the FR/EN words's dictionnary of the app.
