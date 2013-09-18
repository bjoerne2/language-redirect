# Language Redirect

WordPress plugin which redirects from the root site of a multisite project to a language specific network site. The decision which language should be used is based in the browser language of the user which is usually sent as 'Accept-Language' header (PHP: $_SERVER['HTTP_ACCEPT_LANGUAGE']).

The plugin provides a configuration page where supported languages, redirect targets and the default language can be configured.

The plugin should be activated only on the root site. Once redirected it is useless and shouldn't be active on the language specific network site.

The plugin is hosted on wordpress.org as well: [http://wordpress.org/plugins/language-redirect/](http://wordpress.org/plugins/language-redirect/)

*This plugin was originally developed for [monkkee](http://www.monkkee.com/) and is inspired by [oncleben31.cc](http://oncleben31.cc)*