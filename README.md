# Dailymotion Provider for OAuth 2.0 Client

This package provides Dailymotion OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Requirements

The following versions of PHP are supported.

- PHP 5.6
- PHP 7.0
- PHP 7.1
- PHP 8.0
- PHP 8.1

## Installation

Add the following to your `composer.json` file.

```json
{
    "require": {
        "wolftotem4/oauth2-dailymotion": "^0.9.1"
    }
}
```

## Usage

### Authorization Code Flow

```php
session_start();

$provider = new \WTotem4\OAuth2\Client\Provider\Dailymotion([
    'clientId'          => '{dailymotion-app-key}',
    'clientSecret'      => '{dailymotion-app-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => ['email', '...', '...'],
    ]);
    $_SESSION['oauth2state'] = $provider->getState();
    
    echo '<a href="'.$authUrl.'">Log in with Dailymotion!</a>';
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    echo 'Invalid state.';
    exit;

}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Optional: Now you have a token you can look up a users profile data
try {

    // We got an access token, let's now get the user's details
    $user = $provider->getResourceOwner($token);

    // Use these details to create a new profile
    printf('Hello %s!', $user->getScreenname());
    
    echo '<pre>';
    var_dump($user);
    # object(WTotem4\OAuth2\Client\Provider\DailymotionUser)#10 (1) { ...
    echo '</pre>';

} catch (\Exception $e) {

    // Failed to get user details
    exit('Oh dear...');
}

echo '<pre>';
// Use this to interact with an API on the users behalf
var_dump($token->getToken());
# string(217) "EAABzGQwfmiMBAPxoLpTL...

// The time (in epoch time) when an access token will expire
var_dump($token->getExpires());
# int(1515911929)
echo '</pre>';
```
