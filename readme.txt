=== QMean - WordPress Did You Mean and Search Suggestion Like Google ===
Contributors: arashsafari
Tags: search,suggestion,optimization,better search
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.4.0
Requires PHP: 7.0+
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ajax smart keyword suggestions and fix typos for better results by showing "Did You Mean", Google style! Simple, Minimal and Fast. Plus an analytics dashboard for searched queries

== Description ==
QMean is a minimal auto-complete query and 'did you mean' suggestion plugin which searches for relative keywords and fix typos in words. Its goal is to help users to create relevant queries before sending it to WP search engine.

= Like Google =
QMean can detect similar words even if they contain typos! This feature will work on both AJAX suggestions and after the query is searched by showing "Did You Mean: Right Keywords"

= Offer Query Suggestions =
QMean will search through database and collect keywords and phrases related to the given input. You are the one who decides where to look: Titles, Excerpts, Contents, Taxonomy Terms or PostMetas and on which post types.

= Modes: Phrase or Word By Word =
QMean can interact by any word user enters individually and complete the query or offer them phrases base on the whole input. Each one has its own advantages so you can set the one suits you.

= Keywords Analytics =
QMean will record searched queries and the result for better SEO!

= How This Works!? =
QMean doesn't care if you typed "hodei wth", "hdie wih" or "hode whi" it is smart enough to determine that you are looking for "Hoodie with". In most cases even the wrong order of words can't fail the suggestion. Depending on your selected mode it can help user to complete the phrase, word by word or show them complete phrased suggestions at the beginning!

= Just "CSS Selector" To Hook =
QMean just needs the selector of your search input which can hook itself to it and provides the suggestions.

== Simple Shortcode ==
If you don't want to hook QMean to any field using CSS selectors, you can use the shortcode instead to create your search field anywhere you want!

== Settings and Styling ==
You can position and style the box as you wish via settings and change the search engine from there too.

== Key Features: ==
* Live query suggestions
* Phrase Mode or Word By Word Searching
* Search through titles, excerpts, contents, taxonomy term or even none-hidden post metas
* Minimal configuration
* Optimized performance
* Searched keywords analytics and report
* Minimal implementation
* RTL direction and UTF-8 support

== Installation ==
= Automatic installation =
Automatic installation is the easiest option -- WordPress will handles the file transfer, and you won’t need to leave your web browser. To do an automatic installation of QMean, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”
In the search field type “QMean” then click “Search Plugins.” Once you’ve found us,  you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by! Click “Install Now,” and WordPress will take it from there.

= Manual installation =
 * Place the repository in wp-content/plugins/`
 * Activate the plugin in WordPress > Plugins > QMean > Activate
 * Configure your settings via new dashboard menu item "QMean" > Settings

PLEASE make sure to configure QMean first (Navigation: WP Dashboard / QMean / Settings)
By default live suggestions won't work if it doesn't know the selector of the input

== Frequently Asked Questions ==

= What is the shortcode? =
[qmean icon="yes" placeholder="Type to search" button_bg="#1a1a1a" button_height="40px" button_width="40px" form_class="" input_class="form-control" button_class=""  form_style="padding:0 0 15px 0" input_style="" button_style=""]
Best practices are
1- Use specific named attribute if you don't know how to use CSS like button_height, button_bg and ...
2- Use in set! Either use style attributes for all of them or use class attribute for all of them like form_class, input_class, button_class OR form_style, input_style and ...
* You can use all of them but it won't make sense in general and it is confusing :)

= What is difference of Phrase mode or Word by Word mode? =
Word by word will look for most related keyword on each word, for example if you are looking for "Hoodie With Logo" it will complete the phrase by looking for every word separately; First for hoodie, then for with, and finally for logo
But Phrase mode will look for related keywords on the whole phrase; For example when your finishing "Hoodie With Logo" It will provide the suggestions on completion to the whole phrase.

= Which mode is better? =
Both of them are useful, it depends on your need. Phrase mode can have solid results at the end but fewer suggestions. But WBW can have more suggestions and fewer results because it will suggest the most related keyword from the entire database! it can be useful to optimize your SEO (if user created that phrase and you didn't predicted it, it is not QMeans's fault :) )


== Screenshots ==

1. Phrase Mode Suggestions
2. Word By Word Suggestions
3. Did You Mean
4. QMean Settings
5. QMean Dashboard Analytics

== Changelog ==

= 1.0 =
* First Launch

= 1.1 =
* Limit number of suggested keywords
* Smarter suggestions! Most related and matched keywords first
* Styling the suggestion wrapper

= 1.2 =
* Search Field Shortcode Added
* Now QMean Can Search Terms Too
* Settings Field Arranged & Documented

= 1.3 =
* Searched Queries Analytics Dashboard Page
* Fixed variant character case for suggested words
* Settings Documentation
* Fixed some minor bugs

= 1.4 =
* new files commit

= 1.5 =
* Features: added Auto Field Recognizer on front-end


== Upgrade Notice ==

= before 1.0.9 =
Please upgrade in order to fix issues and get more accurate results and tools specially if you are using version before 1.1.3
= after 1.2 =
Please make sure that the database table is created. If NOT please re-install QMean and it will fix the issue.
