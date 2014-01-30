=== Language Redirect ===
Contributors: bjoerne
Tags: language, redirect, header, location
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XS98Y5ASSH5S4
Requires at least: 3.4
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html


Redirects from the root site of a multisite project to a language specific network site.


== Description ==
WordPress plugin which redirects from the root site of a multisite project to a language specific network site. The decision which language should be used is based in the browser language of the user which is usually sent as 'Accept-Language' header (PHP: $_SERVER['HTTP_ACCEPT_LANGUAGE']).

The plugin provides a configuration page where supported languages, redirect targets and the default language can be configured.

The plugin should be activated only on the root site. Once redirected it is useless and shouldn't be active on the language specific network site.

The plugin is developed on [Github](https://github.com/bjoerne2/language-redirect). Feel free to fork the project or create pull requests. 

*This plugin was originally developed for [monkkee](http://www.monkkee.com/) and is inspired by [oncleben31.cc](http://oncleben31.cc)*


== Installation ==

1. Upload plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure plugin (Settings -> Language Redirect)


== Screenshots ==

1. Options
