# 🐦 BladeBreakIn

## The Next Generation of `S+E+L+F` Development

Are you still using the heavy CMS for non-eco-friendly?

The heavy CMS is definitely great, but aren't you tired of it?

More isn't always better, either for the customer or for the Web Coders and/or Artisans.
We should cut our coat according to our cloth.

BladeBreakIn is a WEB making system for the WEB Coders.
 
BladeBreakIn is based on the [Laravel](https://laravel.com/),
and aims at `S+E+L+F` development that means SIMPLE, EASY, LIGHT, and FAST.

Laravel is a very popular web application framework with expressive, elegant syntax, especialy [Blade Templating Engine](https://laravel.com/docs/master/blade) is unprecedented, and thus it makes Web Coders release from the many issues, and the black box of clasical codes.

## 🎼 Get started

1. Git-Clone or Download the latest version of BladeBreakIn.
1. Copy directries and files in the `src` directory to your web server's document root directory. Of course you can chose to copy these under a sub directory(e.g. `subdir`).

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

1. That's all if your Web Server is the Apache. 
`.htaccess` is suppose to defend your `bd` directory.
Otherwise, you must block access to `bd` directory from outgoing. For instance, set the directory's permission for 700.

1. You can just see the sample page when access to  `https:://your-domain/subdir/` with your browser.

## 👶 First steps

See sample codes put in `/bd/views/sample` and `/bd/scss/sample` directories.

Let's make your `/bd/views/index.blade.php`.

You can just see the new your page when access to `https:://your-domain/subdir/`.

If you have built websites with PHP langauge, you should be able to create new Web site soon.

These sample just use the Blade template(`.blade.php`), but you can use your favorite JS framework for front end - [ReactJS](https://reactjs.org/), [jQuery](https://jquery.com/), [AngularJS](https://angularjs.org/), [VueJS](https://vuejs.org/), [SolidJS](https://www.solidjs.com/), etc., or even just pure vanilla JavaScript.

`fd` directory is the storage box for html, css, etc. You can use it freelly.

`storage` directory is for uploaded files. Laravel can upload files into it.

Further documents are in the `/docs` directory.

## 🚀 Next steps

Rename `/bd/on.sample.php` to `/bd/on.php`.

Then you can write the routing and others to Web service procedures.

For example, show '/views/sample/test.blade.php' when `https:://your-uri/test/` is called from a browser,

``` on.php

  public static function onWeb($router)
  {
    \Route::get('test', function (Request $request) {
      return view('sample.test');
    });
  }

```

Laravel's routing has many functions.
See [Laravel's Routing Page](https://laravel.com/docs/master/routing).

## 🚀 Debug Mode

``` on.php

  public static function onBoot()
  {
    \HQ::setDebugbarPageSecret('your secret key');
  } 

```

Access `https:://your-uri/debugbar.php?secret=your secret key`.

Or, 

``` on.php

  public static function onBoot()
  {
    \HQ::setDebugMode(true);
  } 

```

But don't forget security issues, last example allow to see the debug results to all web visitors. 


## 🚀 Maintenance Mode

Coming soon...

## 🚀 Update BladeBreakIn

It's also easy.
Just overwrite new files belong to BladeBreakIn.

The files belong to BladeBreakIn(also sample files) will be overwritten, but your own directories and files are safe.

## 🎵 Code of conduct
This project is learning from [The Rust's Code of conduct](https://www.rust-lang.org/policies/code-of-conduct):
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
