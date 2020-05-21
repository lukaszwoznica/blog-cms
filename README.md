# Blog CMS

[![Current Version](https://img.shields.io/badge/version-0.1-blue.svg)](https://github.com/lukaszwoznica/blog-cms)
[![Live Demo](https://img.shields.io/badge/demo-online-green.svg)](https://blogcms-php.herokuapp.com/)

## General info

Blog content management system created using a custom PHP MVC framework.

## Technologies
* PHP 7.4
* JavaScript (with jQuery)
* AJAX
* Materialize (with custom CSS)
* MySQL

## Setup
1. Clone this repository
1. Install the project dependencies with `Composer`:
```bash
$ composer install
```
3. Create new MySQL database and import [this database dump](database/blog_db.sql)
1. Set your database and SMTP credentials in [App/Config.php](App/Config.php) file

## Features

#### MVC framework:
* Routing with pretty URLs
* Views with Twig templates
* Flash messages
* Error handling with the ability to hide details from the user
* Action filters

#### Blog CMS application
* Authentication
	- User registration with email verification
	- Password reset using email
	- Remembering the login
* Authorization with admin and user roles
* Comments system
* Hierarchical post categories (categories with subcategories)
* Responsive layout
* Admin Panel
	- CRUD for posts, categories, users and comments
	- WYSIWYG editor for creating posts 
	- Google Charts in dashboard
	- Real-time notifications about new comments
	
## Demo
Working live demo of BlogCMS available here: https://blogcms-php.herokuapp.com/

:information_source: **Info**
> Login credentials are on the login page. You can also create a new account. 
  
>User management from the admin panel is disabled in this demo.