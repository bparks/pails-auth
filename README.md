pails-auth
==========

An authentication and authorization plugin for pails loosely based
on UserPie ([userpie.com][userpie]). At this point it's a near-complete rewrite.

Dependencies
------------

* pails
* pails/activerecord
* pails/actionmailer

We recommend using composer to get the dependencies. Furthermore, we
recommend not installing the dependencies for pails-auth directly, but listing
pails-auth as a dependency in your pails application's composer.json file.

Installation
------------

In the root of a pails app, run

    composer require pails/auth

Configuration
-------------

Inside any controller where you want to make use of the authentication/authorization
methods, `use` the `PailsAuthentication` [trait][trait].

```php
class DefaultController extends Pails\Controller
{
	use PailsAuthentication;
}
```

You can then use the before actions `require_login` or `require_anonymous`:

```php
$before_actions = array(
	'require_login' => array('except' => array('index', 'about', 'contact'))
);
```

Two utility methods, `is_logged_in` and `current_user`, are also provided.

Support
-------

pails-auth is a core plugin maintained and supported by Brian Parks.

[userpie]: http://userpie.com
[trait]: http://php.net/manual/en/language.oop5.traits.php
