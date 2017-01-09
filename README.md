# Socialite JbHub.
Jetbrains hub socialite provider.

### Installation
Require socialite and add the `SocialiteServiceProvider` and `Manager\ServiceProvider` to the providers array in the `app.php` file:

```php
'providers' => [
    Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
]
```

If wanted, you can add the Socialite facade to the alias class (I prefer and recommend this):

```php
'aliases' => [
    'Socialite' => Laravel\Socialite\Facades\Socialite::class
]
```

Download the source or require the package with composer or something.  
Add the provider to the 'services' config array:

```php
return [
    'jetbrains-hub' => [
        'client_id'     => env('JB_HUB_CLIENT_ID', ''),
        'client_secret' => env('JB_HUB_CLIENT_SECRET', ''),
        'base_url'      => env('JB_HUB_URL', 'https://hub.yourpage.tld'),
        'redirect'      => env('APP_URL', 'http://localhost') . "/your/callback"
    ],
];
```

Add the socialite extension to the events provider:

```php
protected $listen = [
    SocialiteWasCalled::class => [
        JetbrainsHubExtendSocialite::class . "@handle"
    ]
];
```

Add your callback routes:

```php
$router->get('/your/redirect', function() {        
    // Redirect!
    return Socialite::driver('jetbrains-hub')->redirect();
});

$router->get('/your/callback', function() {
    $user = Socialite::driver('jetbrains-hub')->user();
    // Yay you got your user (I hope!)    
});
```

Create some kind of login form and redirect a "log in with jb-hub" button to the `your/redirect`-endpoint!... 
This you can hopefully figure out yourself...

### License
```
MIT License
  
  Copyright (c) 2017 JiteSoft
  
  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:
  
  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
  
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
```
