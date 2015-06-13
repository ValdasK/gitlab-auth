# gitlab-auth
Simple Gitlab Oauth wrapper to receive user data.

The main purpose of this package is to authenticate users in your application using Gitlab application. 

## Quick use

On your login page:
```php
$lib = new \GitlabAuth\Auth("http://mygitlab.com", "app id", "app secret", "http://myapp.com/do-login");
$authorizationUrl = $lib->getLoginRedirectUrl();
header('Location: ' . $authorizationUrl);
exit;
```

on "http://myapp.com/do-login" page:

```php
$lib = new \GitlabAuth\Auth("http://mygitlab.com", "app id", "app secret", "http://myapp.com/do-login");
$userDetais = $lib->getUserByCode($_GET['code']);
var_dump($userDetails); die;
```

## Full how-to guide

### 1. Initializing

To initialize this library, create new instance of GitlabAuth\Auth.

Guide how to create application ID and secret can be found here: http://doc.gitlab.com/ce/integration/oauth_provider.html

```php
$lib = new \GitlabAuth\Auth("http://mygitlab.com", "app id", "app secret", "http://myapp.com/do-login");
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

```php
$userDetais = $lib->getUserByCode($_GET['code']);
```

User details is an array, which contain email, name, surname, and other user details. Full documentation of Gitlab user API can be found here: http://doc.gitlab.com/ce/api/users.html#for-admin

### 4. (optional) Retrieving Gitlab Auth token

If you need extra Gitlab API features, which require auth token, after calling **getUserByCode** function, method **getTokenContainer** can be used which contains retrieved auth token.

```php
$authToken = $lib->getTokenContainer();
```
