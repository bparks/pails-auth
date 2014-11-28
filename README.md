pails-auth
==========

An authentication (and eventually authorization) plugin for pails loosely based
on UserPie ([userpie.com][http://userpie.com]).

Dependencies
------------

* pails-activerecord

      pails install activerecord

Installation
------------

In the root of a pails app, run

    pails install auth

If you haven't already installed the `activerecord` plugin, run the command above

Configuration
-------------

Inside any controller where you want to make use of the authentication/authorization
methods, `use` the `PailsAuthentication` [trait][http://php.net/manual/en/language.oop5.traits.php].

    class DefaultController extends Pails\Controller
    {
    	use PailsAuthentication;
    }

You can then use the before actions `require_login` or `require_anonymous`:

    $before_actions = array(
    	'require_login' => array('except' => array('index', 'about', 'contact'))
    );

Two utility methods, `is_logged_in` and `current_user`, are also provided.

Support
-------

pails-auth is a core plugin supported by Synapse Software. Contact us at
support@synapsesoftware.com.
