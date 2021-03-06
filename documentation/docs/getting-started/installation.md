# Installation

In this section we will see how to make a full installation and initialization of a project based on `Fux Framework`.
In the rest of the documentation we will refer to the whole framework, and it's ecosystem, simply with `Fux`

## How it works

The main purpose of Fux is to keep things simple and easy to use. No huge packages or dependencies. 
In the fact Fux consists of a group of folders representing the base elements of an MVC project. Into one of these 
folders is present the whole framework core which is the _brain_ of any project based on Fux.
More information about the project structure can be found in the [project structure](../todo.md) section.

## Download skeleton project

The simplest way to install Fux and getting started with your project is to download the skeleton project from 
the following link: [download skeleton](https://todo.md).
You can now rename the folder with your project name and put it into your local development server (such as XAMPP or MAMP).

You can see that there are a bunch of folders in this skeleton project; basically you will use all folders except for 
the one called `php` which contain the entire Fux core. In the other sections of this documentation you will learn how 
to populate these folders, but now you have to check if all is up to work correctly.

## Initial configuration and setup

Now we have to make a simple configuration of the project to be able to run our test application.

### Config folder
All the project configuration files are present in the `config` folder. 
You can rename (or make a copy) the _enviroment.example.php_ file to _enviroment.php_, here we are going to make our changes.

#### Server port
First of all you have to set the server port. For example if you are using the default XAMPP configuration you can leave
it to `false` but if you are using MAMP you will probably need to change it to `8888` which is the default MAMP port.

#### Domain name
You should not need to change it; in the fact by default it store the current server name 
(which is the domain name or IP address, or the "localhost" string if you are using XAMPP or MAMP). Anyway there are some
cases where you cannot use the PHP `$_SERVER` global variable keys or you want to make this value depend on some other config files.

**For a basic configuration you don't have to change it**

#### Project dir
You have to set the `PROJECT_DIR` constant the directory where your project is placed relative to the root of your 
server. If this is not an exhaustive explanation of how you have to set this value here we have a bunch of example.

Let's suppose you are using a common XAMPP or MAMP configuration. You probably have moved your project folder inside the
htdocs folder. The htdocs folder represent the server root. 
At this time you have these possibilites:
1) If your project folder is named "test-project" you have to write `define("PROJECT_DIR", "/test-project");`
2) If you placed your project folder named "test-project" inside the "subdir" folder `define("PROJECT_DIR", "/subdir/test-project");`
3) If you placed ALL the skeleton files and folders directly inside the htdocs you must use an empty string `define("PROJECT_DIR", "");`

In the case of a real hosting you have to think at your web hosting root as the htdocs folder and follow one of the 
steps you read before. 

This project folder organization allow you and your team to manager a single server environment where are present multiple 
projects based on Fux which are placed on different sub directories.

### The .htaccess file
The last step to complete our first configuration is to change the .htaccess file present in your project.
You have to modify the `line 8` as follow:
`RewriteBase {PROJECT_DIR here}` where {PROJECT_DIR here} is the value of the constants `PROJECT_DIR` defined in the `/config/environment.php` file


## Test the project
Now you can open your browser and navigate to http://{SERVER_NAME}{PROJECT_DIR}/home. 
For example if you are using XAMPP you can navigate to `http://localhost/test-project/home`.
If you are using MAMP navigate to `http://localhost:8888/test-project/home`.

If everything has been configured correctly you will see a classic _hello world_ example!






