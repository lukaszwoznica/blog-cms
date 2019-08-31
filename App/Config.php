<?php

namespace App;

/**
 * App configuration
 */

class Config
{
   /*
    * Database settings
    */
    const DB_HOST = 'localhost';
    const DB_NAME = 'blog_db';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';

    /*
     * Show or hide error messages
     */
    const SHOW_ERRORS = true;

    /*
     * Secret key for hashing
     */
    const SECRET_KEY = '3xRKxebnuVfQhoMMZfyZbv9XU4VaICVP';

    /*
     * PHPMailer settings
     */
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_USERNAME = 'blogcms1@gmail.com';
    const SMTP_PASSWORD = 'Blog1234';
    const SENDER_ADDRESS = 'blogcms1@gmail.com';
    const SENDER_NAME = 'Blog.pl';
}