=== Flash Form ===
Contributors: mahdiyazdani, mypreview, gookaani
Tags: contact, contact form, email, feedback, gutenberg
Donate link: https://www.buymeacoffee.com/mahdiyazdani
Requires at least: 5.5
Tested up to: 6.1
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create a form as easily as editing a block.

== Description ==
Remember you used to waste your time trying to come up with a solution to have a simple form or survey on your website? It takes a support ticket, an engineer, and a few hours to just “replace a text input with a dropdown menu instead!” Huh?

“Flash Form” offers an in-house form builder that’s fully integrated with the WordPress component library and design system. If you know WordPress and know a bit about forms, you already know “Flash Form”! Everybody on your team can create, edit, and publish forms and surveys on your website regardless of their technical background.

“Flash Form” takes care of the repetitive and annoying stuff—keeping track of values/errors, orchestrating basic validation, and handling submission—so you don’t have to. This means you spend less time navigating back and forth from one admin screen to another and more time focusing on your data collection requirements.

There is no fancy post-type registration or admin settings screen under the hood, just plain React-based WordPress Gutenberg block. “Flash Form” makes form creation a breeze by leveraging and staying within the core WordPress editor components and away from magic.

== Installation ==
= Minimum Requirements =

* PHP version 7.4 or greater.
* MySQL version 5.6 or greater or MariaDB version 10.0 or greater.
* WordPress version 5.5 or greater.

= Automatic installation =

Automatic installation is the easiest option — WordPress will handle the file transfer, and you won’t need to leave your web browser. To do an automatic install of the plugin, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “Flash Form”, then click “Search Plugins.” Once you’ve found the plugin, you can view details about it such as the point release, rating, and description. Click “Install Now,” and WordPress will take it from there.

= Manual installation =

The manual installation method requires downloading the plugin and uploading it to your webserver via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation "Manual plugin installation").

= Updating =

Automatic updates should work smoothly, but we still recommend you back up your site.

== Frequently Asked Questions ==
= What is “Flash Form”? =
“Flash Form” is a block-first, dependency-free form builder and automation tool designed for high-performance teams. In addition to creating forms and surveys using the native WordPress editor interface, developers can leverage the Gutenberg API and React.js to fully integrate their custom fields and customize the out-of-the-box offering of the block to meet their application or website needs using React components.

= How is “Flash Form” different than other plugins? =
“Flash Form” is purpose-built with the needs of developers in mind. The block interface decouples the traditional form builder into its independent offerings.
Most well-known form builder plugins would register a custom post type on your dashboard upon plugin activation and require you to publish your form or survey as an individual post with an interface usually cluttered and bloated with nested options often not used at all. As soon as you publish the form post, a unique shortcode will be generated, allowing you to place the form in your post or page content of your choosing.

With “Flash Form,” you create and customize simultaneously with a live preview of your form's appearance even before hitting the publish button.

This allows for limitless customization and control, native performance, air-tight security (honeypot, captcha, etc.), and better end-user experience while saving countless hours of manual, error-prone hand coding.

= How is spam protection being ensured? =
“Flash Form” has a built-in Honeypot feature and integrates with Google reCAPTCHA V2 service to offer spam protection for messages.

= The form block won't load! What to do? Help! =
That’s almost always a clash with another plugin. We always work hard to make “Flash Form” compatible with the whole plugin-verse. As it turns out, there are many possible combinations, and it’s not humanly achievable to test every one of them. We’re sorry!

To rule out those conflicts, try deactivating all plugins except “Flash Form” on the Plugins screen.

If that didn’t help, double-check if there’s an error printed out on your [browser console](https://wordpress.org/support/article/using-your-browser-to-diagnose-javascript-errors/ "Using Your Browser to Diagnose JavaScript Errors") while editing a page or post with “Gutenberg” editor support.

Feel free to start a new topic [here](https://wordpress.org/support/plugin/flash-form "Flash Form Support Forum") and share a screenshot or copy-pasted error message. We’ll be happy to help!

= Can I request a feature? =
Sure thing. We’re always open to hearing ways you think we can improve and evolve. To make a request, start a new support topic [here](https://wordpress.org/support/plugin/flash-form "Flash Form Support Forum").

Please include an example or specific use case so we can understand exactly what you’re after and whether others share your needs.

= How do I get help with the plugin? =
The easiest way to receive support is to “Create a new topic” by visiting the Community Forums page [here](https://wordpress.org/support/plugin/flash-form "Flash Form Support Forum").

Make sure to check the “Notify me of follow-up replies via email” checkbox to receive notifications as soon as a reply is posted to your question or inquiry.

*Please note that this is an open source 100% volunteer project, and it’s not unusual to get reply days or weeks later.*

= Can I help translate this plugin into a new language? =
The plugin is fully translation-ready and localized using the GNU framework, and translators are welcome to contribute to the plugin.

Here’s the the [WordPress translation website &#8594;](https://translate.wordpress.org/projects/wp-plugins/flash-form "WordPress translation website")

= How do I contribute to this plugin? =
We welcome contributions in any form, and you can help report, test, and detail bugs.

Here’s the [GitHub development repository &#8594;](https://github.com/mypreview/flash-form "GitHub development repository")

= Did you like the idea behind this plugin? =
If you or your company use any of my projects or like what I’m doing, please consider [making a donation](https://www.buymeacoffee.com/mahdiyazdani) so I can continue maintaining and evolving all my projects and new ones. I’m in this for the long run.

Share your experience by leaving this plugin [5 shining stars](https://wordpress.org/support/plugin/flash-form/reviews/ "Rate Flash Form 5 stars") if you like it.

= I need help customizing this plugin? =
Get free of charge advice on what could be done or how complex different approaches are.

[Start a consultation &#8594;](https://mahdiyazdani.com "Mahdi Yazdani’s personal website")

== Screenshots ==
1. Form submission via HTTP "GET" method.
2. Honeypot trap settings.
3. Supported form field types.

== Changelog ==
= 1.1.1 =
* Compatibility with WordPress 6.1

= 1.1.0 =
* Feature: Added basic integration with Google reCaptcha V2.
* Feature: Allow form field blocks to be transformed where applicable.

= 1.0.0 =
* Initial release.
