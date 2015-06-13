# gitlab-auth
Simple Gitlab Oauth wrapper to receive user information, such as email.

The main purpose of this package is to retrieve user info from Gitlab, simplifying user login and registration proccess.

## Quick use

On your login page:
```php
$lib = new \GitlabAuth\Auth(new GuzzleHttp\Client(), "http://mygitlab.com", "app id", "app secret", "http://myapp.com/do-login");
header('Location: ' . $lib->getLoginRedirectUrl()); exit;
```

on "http://myapp.com/do-login" page:

```php
$lib = new \GitlabAuth\Auth(new GuzzleHttp\Client(), "http://mygitlab.com", "app id", "app secret", "http://myapp.com/do-login");
var_dump($lib->getUserByCode($_GET['code'])); exit;
```

## Full how-to guide

### 1. Initializing

To initialize this library, create new instance of GitlabAuth\Auth.

Guide how to create application ID and secret can be found here: http://doc.gitlab.com/ce/integration/oauth_provider.html

```php
$lib = new \GitlabAuth\Auth(new GuzzleHttp\Client(), "http://mygitlab.com", "app id", "app secret", "http://myapp.com/do-login");
```

### 2. Retrieving authorization URL

First step in login proccess is to redirect user to Gitlab authorization page, where he or she accepts of denies access using their Gitlab account.
This library provides simple method to create proper redirect URL. 

```php
$authorizationUrl = $lib->getLoginRedirectUrl();
```

You can use this URL in link and present it to user or just use proper redirect header to skip user input.

### 3. Retrieving user data

When user visits authorization page, he can choose whether to accept or deny authorization. After action is chosen, he is redirected back to redirect url (set in library initialization step), with GET parameter named "code".
This is used to retrieve auth token from Gitlab server, and then using that auth token user details are retrievied from Gitlab API.

Tip: do not forget to check if *$_GET['error']* exists, and if it does that means user declined the request and should not be authorized.

```php
$userDetais = $lib->getUserByCode($_GET['code']);
```

User details is an array, which contain email, name, surname, and other user details. Full documentation of Gitlab user API can be found here: http://doc.gitlab.com/ce/api/users.html#for-admin

### 4. (optional) Retrieving Gitlab Auth token

If you need extra Gitlab API features, which require auth token, after calling **getUserByCode** function, method **getTokenContainer** can be used which contains retrieved auth token.

```php
$authToken = $lib->getTokenContainer();
```
