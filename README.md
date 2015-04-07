googlemap-tweets
======================
Google Map with Tweets

This project, is based on Laravel PHP Framework, uses for searching any locations by using Google Map API and showing tweets on the map by using Twitter PHP API.

Requirement
------------
PHP >= 5.4

Installation
------------

You need to run `php composer.phar update` for getting core libraries from Github such as Laravel PHP Framework, Twitter API PHP, etc.

How To Use
------
#### Set Database in config/database.php ####
	'mysql' => [
		'driver'    => 'mysql',
		'host'      => env('DB_HOST', 'XXX'),
		'database'  => env('DB_DATABASE', 'XXX'),
		'username'  => env('DB_USERNAME', 'XXX'),
		'password'  => env('DB_PASSWORD', 'XXX'),
		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
		'strict'    => false,
	],

#### Set Twitter access tokens in config/twitter.php ####

    $settings = array(
        'oauth_access_token' => "YOUR_OAUTH_ACCESS_TOKEN",
        'oauth_access_token_secret' => "YOUR_OAUTH_ACCESS_TOKEN_SECRET",
        'consumer_key' => "YOUR_CONSUMER_KEY",
        'consumer_secret' => "YOUR_CONSUMER_SECRET"
    );
	
#### Create Table in Database ####
	run url: {domain_name}/public/install
