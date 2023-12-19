# 🐦 BladeBreakIn

## The Next Generation of `S+E+L+F` Development

Are you still using a heavy CMS that's not eco-friendly?

While heavy CMSs have their merits, do you ever feel exhausted by them?

More isn't always better, whether for the customer or for web coders and artisans. We should tailor our approach to our resources.

Introducing BladeBreakIn, a web-making system designed for web coders.

BladeBreakIn is built on [Laravel](https://laravel.com/) and focuses on `S+E+L+F` development—Simple, Easy, Light, and Fast.
 
Laravel, a highly popular web application framework, boasts expressive, elegant syntax. In particular, the [Blade Templating Engine](https://laravel.com/docs/master/blade) is unparalleled, liberating web coders from numerous issues and the black box of classical code.

## 🎼 Get started

1. Git-Clone or Download the latest version of BladeBreakIn.
1. Copy the directories and files to your web server's document root directory. Alternatively, you can choose to copy them under a subdirectory (e.g., `subdir`).

```
<Your Web Server's DocumentRoot>
   └ <subdir>
       ├ <bd>
       │   ┊
       │   ├ .htaccess
       │   └ on.sample.php 
       ├ <fd>
       ├ <storage>
       ├ .htaccess
       ├ debugbar.php
       ├ index.php
       ┊
```

1. That's all if your web server is Apache. The `.htaccess` file is supposed to secure your `/bd` directory. Otherwise, you must block access to the `/bd` directory from the outside. For instance, set the directory's permissions to 700.

1. You can view the sample page by accessing `https://your-domain/subdir/` in your browser.

## 👶 First steps

### Sample codes

See sample codes put in `/bd/views/sample`, `/bd/scss/sample`, and `/bd/markdowns/sample` directories.
As you can see, these are using Blade Template, SCSS, and Markdown.

#### SCSS

You can see a sample of using SCSS in `/bd/views/sample/index.blade.php`:

``` html
  <link rel="stylesheet" href="?css_route=sample.style">
```

Here, `css_route=sample.style` means loading `/bd/scss/sample/style.scss` after it has been compiled into regular CSS.
The result is cached, similar to Blade Templates.


#### Markdown

You can see a sample of using Markdown with Mustache in `/bd/views/sample/index.blade.php`:

``` html
  {!! Compilers::markdown('sample.test', ['test' => 2]) !!}
```

Here, `sample.test` means loading `/bd/markdowns/sample/test.md` after it has been compiled into HTML.
The result is cached, similar to Blade Templates.

And `['test' => 2]` means providing the hash to [Mustache Template](https://mustache.github.io/).


### Start Your Coding

Let's make your `/bd/views/index.blade.php`.

You can view your new page by accessing `https://your-domain/subdir/`.

If you have built websites with PHP langauge, you should be able to create new Web site soon.

This sample utilizes the Blade template (`.blade.php`), but you have the flexibility to employ your preferred front-end JS framework such as [ReactJS](https://reactjs.org/), [jQuery](https://jquery.com/), [AngularJS](https://angularjs.org/), [VueJS](https://vuejs.org/), [SolidJS](https://www.solidjs.com/), or even opt for pure vanilla JavaScript.

The `/fd` directory serves as the storage container for front-end assets such as HTML, CSS, JS, etc. Feel free to use it.

The `/storage` directory designated for publicly uploaded files, and the framework facilitates easy file uploads into this directory.

~~Further documents are in the `/docs` directory.~~(Coming soon...)

## 🚀 Next steps

### Settings

Copy `/bd/config.sample.php` to `/bd/config.php`.

Afterward, you can modify the environment settings.


### Routings

Copy `/bd/on.sample.php` to `/bd/on.php`.

Afterward, you can proceed to write the routing and other necessary procedures for the web service.

For instance, to display `/views/sample/test.blade.php` when `https://your-uri/test/` is accessed from a browser, you can configure the appropriate routing,

``` php: on.php

  public static function onWeb($router)
  {
    \Route::get('test', function (Request $request) {
      return view('sample.test');
    });
  }

```

Laravel's routing system offers a variety of functions. For detailed information and examples, refer to [Laravel's Routing Page](https://laravel.com/docs/master/routing).

## 🐞 Debug Mode

``` php: on.php

  public static function onBoot()
  {
    \HQ::setDebugbarPageSecret('your-secret-key');
  } 

```

Access `https://your-uri/debugbar.php?secret=your-secret-key`.

Or, 

``` php: on.php

  public static function onStart()
  {
    \HQ::setDebugMode(true);
    \HQ::setDebugbarShowAlways(true); // <====== For security, the default value is false.
  } 

```

However, it's crucial not to overlook security considerations. In the last example, it's worth noting that allowing all web visitors to see the debug results could pose a security risk.

## 🔧 Maintenance Mode

Coming soon...

## ✔ Update BladeBreakIn

Making updates is also a straightforward process.

Simply overwrite the new files that belong to BladeBreakIn.

While files belonging to BladeBreakIn, including sample files, will be overwritten, your custom directories and files remain unaffected.

## 🎵 Code of conduct

This project draws inspiration from [The Rust's Code of conduct](https://www.rust-lang.org/policies/code-of-conduct):
* We are committed to providing a friendly, safe and welcoming environment for all, regardless of level of experience, gender identity and expression, sexual orientation, disability, personal appearance, body size, race, ethnicity, age, religion, nationality, or other similar characteristic.
* Please avoid using overtly sexual aliases or other nicknames that might detract from a friendly, safe and welcoming environment for all.
* Please be kind and courteous. There’s no need to be mean or rude.
* Respect that people have differences of opinion and that every design or implementation choice carries a trade-off and numerous costs. There is seldom a right answer.
* Please keep unstructured critique to a minimum. If you have solid ideas you want to experiment with, make a fork and see how it works.
* We will exclude you from interaction if you insult, demean or harass anyone. That is not welcome behavior. We interpret the term “harassment” as including the definition in the Citizen Code of Conduct; if you have any lack of clarity about what might be included in that concept, please read their definition. In particular, we don’t tolerate behavior that excludes people in socially marginalized groups.
* Private harassment is also unacceptable. No matter who you are, if you feel you have been or are being harassed or made uncomfortable by a community member, please contact one of the channel ops or any of the Rust moderation team immediately. Whether you’re a regular contributor or a newcomer, we care about making this community a safe place for you and we’ve got your back.
* Likewise any spamming, trolling, flaming, baiting or other attention-stealing behavior is not welcome.

## 👏 Contribution

### ♪ By PR (Pull Request)

Feel free to open a pull-request.

### ♪ As a Coraborater

Coraboraters are welcome!

### ♪ As a Supporter

😻We like beers🍺

## 📝 Licence

The MIT License.
