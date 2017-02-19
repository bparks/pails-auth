<?php

namespace Pails\Authentication;

/*

In order to use this method, you need to put the following in your <head>:

<script src="https://apis.google.com/js/platform.js" async defer></script>
<meta name="google-signin-client_id" content="YOUR_CLIENT_ID.apps.googleusercontent.com">

And optionally:

<meta name="google-signin-hosted_domain" content="YOUR_HOSTED_DOMAIN" />

Make sure to replace YOUR_CLIENT_ID with your actual client ID from the Google Developers
Console (https://developers.google.com/identity/sign-in/web/devconsole-project)

*/

class GoogleAuthenticationProvider implements IAuthenticationProvider
{
    public $client_id;
    public $hosted_domain;

    function __construct($options)
    {
		if (!isset($options['client_id']))
			throw new \Exception("Option 'client_id' must be specified for a GoogleAuthenticationProvider");

        $this->client_id = $options['client_id'];
        if (isset($options['hosted_domain']))
            $this->hosted_domain = $options['hosted_domain'];

        if (!class_exists("\Google_Client"))
            throw new Exception("The Google PHP client (google/apiclient) is required to use GoogleAuthenticationProvider.");
    }

    function getSession($session_key)
    {
        $client = new \Google_Client(['client_id' => $this->client_id]);
        $payload = $client->verifyIdToken($session_key);
        if ($payload) {
            $userid = $payload['sub'];
            if ($this->hosted_domain != '' && (!isset($payload['hd']) || strtolower($payload['hd']) != strtolower($this->hosted_domain)))
                return null;
            return (object)[
                'username' => $payload['email'],
                'email' => $payload['email'],
                'user_id' => $payload['sub'],
                'password' => '',
                'picture' => $payload['picture'],
            ];
        } else {
            return null;
        }
    }

    function validate($user_id, $hash)
    {
        return true;
    }

    function getLoginUrl()
    {
        return 'https://google.com';
    }

    function redirectToLoginPage()
    {
        return false;
    }
}
