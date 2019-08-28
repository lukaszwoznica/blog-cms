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
}