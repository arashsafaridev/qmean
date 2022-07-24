=== QMean - WordPress Did You Mean and Search Suggestion Like Google ===
Contributors: arashsafari
Tags: search,suggestion,optimization,better search
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.8
Requires PHP: 7.0+
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ajax smart keyword suggestions by fixing typos for better results. Plus "Did You Mean" feature, Google style! Simple, Minimal, Fast and Smart. Plus an analytics dashboard for searched queries

== Description ==
QMean is a minimal, ajax, auto-complete query and 'did you mean' suggestion plugin. It searches for relative keywords and fix typos in words. Its goal is to help users to create relevant queries before sending it to WP search engine.

= Like Google =
QMean can detect similar words even if they contain typos! This feature will work both on AJAX suggestions and after searching the query. By showing "Did You Mean: Right Keywords"

= Offer Query Suggestions =
QMean will search through the database. It collects keywords and phrases related to the given input. You are the one who decides where to look. Titles, Excerpts, Contents, Taxonomy Terms or PostMetas and, on which post types.

= Modes: Phrase or Word By Word =
QMean can interact by any word user enters,  and complete the query or offer them phrases base on the whole input. Each one has its own advantages so you can set the one suits you.

= Keywords Analytics =
QMean will record searched queries and the result for better SEO!

= Only "CSS Selector" To Hook =
QMean only needs the selector of your search input. Then it can add itself to it and provides the suggestions. It has a special tool, QMean field recognizer, to find the selector on you theme

== Simple Shortcode ==
If you don't want to add QMean to any field using CSS selectors, you can use the shortcode instead. It will create your search field anywhere you want! There is another shortcode to position `Did you mean` suggestions too.

== Settings and Styling ==
You can position and style the box as you wish via settings and change the search engine from there too.

== Custom and defined action  ==
QMean's did you mean has an action so you can use it. It can also hook itself to any action you set in the settings and show up there.

= How This Works!? =
QMean doesn't care if you typed "hodei wth", "hdie wih" or "hode whi"! It is smart enough to determine that you are looking for "Hoodie with". In most cases even the wrong order of words can't fail the suggestion. Depending on your selected mode. It can help users to complete the phrase, word by word or show them complete phrased suggestions at the beginning!

== Key Features: ==
* Auto complete search suggestion
* Smart prediction
* Word by word suggestions
* Phrase mode suggestions
* Support Post types, Taxonomies
* Support post title, excerpt, content and meta
* Minimal configuration
* Optimized performance
* Individual search areas for each form e.g. posts, products and ...
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

[qmean-dym wrapper_class=""]
If you want to show did you mean on search page. wrapper_class is optional

= What is the difference of Phrase mode or Word by Word mode? =
Word by word mode will look for the most related keyword for each word. For example, if you are looking for "Hoodie With Logo",  it will search for suggestion based on active word. First for hoodie, then for with, and finally for logo.
But for the  Phrase mode, it will look for related keywords on the whole phrase. For example, when your finishing "Hoodie With Logo" It will provide the suggestions on completion, for the whole phrase.

= Which mode is better? =
Both of them are useful. It depends on your need. Phrase mode can have solid results at the end but fewer suggestions. But WBW can have more suggestions and fewer results because it will suggest the most related keyword from the entire database! it can be useful to optimize your SEO (if user created that phrase and you didn't predicted it, it is not QMeans's fault :) )


== Screenshots ==

1. Phrase Mode Suggestions
2. Word By Word Suggestions
3. Did You Mean
4. Essential Settings
5. Advance Settings
6. Dashboard Analytics
7. User Eye - QMean Dashboard Analytics
8. QMean Field Recognizer

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
* Feature: added Auto Field Recognizer on front-end
* Feature: keyboard arrow selection for results
* Feature: merge previous searched queries higher than db queries by hit and found_post values
* Fixed: duplicate queries
* Fixed: more relevant queries by characters length
* Fixed: show loading... before suggestions appear
* Option: submit search after selection [UX]
* Option: disable/enable merging previous searched queries

= 1.6 =
* Feature: remove button to remove keywords from report to clean the merged suggestions database
* Feature: User's Eye: see what keywords the user has seen on QMean's report
* Fixed: Auto Recognizer class selector on single class name
* Fixed: PHP MySQL errors, PHP notices and warnings
* Fixed: Phrase mode keyword relevancy for more than one word
* UX: Live search suggestion wrapper visibility delay reduced to 200 miliseconds from 500 miliseconds
* Optimization: On creating patterns, finding phrases in a text
* Option: Search efficiency option (on/off)
* Option: Ignore shortcode texts in contents (yes/no)
* Option: Number of words to trim the phrase and retrieve after main keyword (number)
* Removed: Option of phrase length

= 1.7 =
* Feature: Total search hits and keyword diversity
* Feature: Dashboard keyword search
* Feature: [qmean-dym wrapper_class=""] shortcode added
* Fixed: Related keywords on WBW
* Fixed: White spaces before selected query
* Fixed: Move up / down suggestion cursor point to the end

= 1.8 =
* Feature: Remove stop words
* Feature: Custom search areas and post types for each form individually
* Feature: Button text attribute to the shortcode
* Fixed: qmean-dym shortcode variable
* Fixed: MySQL compatibilty issue on regex 
* Fixed: Code syntaxing and applied PSR-2
* Fixed: Word by word mode suggestion start after 3 letters on each word
* Fixed: Remove stop words on the query
* Fixed: Selector option and changed default selector
* Improved: Settings page
* Removed: Sensivity option, QMean now sets sensivity automatically

= 1.9 =
* Fixed: MySQL compatibilty approach for regex
* Fixed: PHP notice for uninstallation callback 
== Upgrade Notice ==

= before 1.0.9 =
Please upgrade in order to fix issues and get more accurate results and tools specially if you are using version before 1.1.3
= after 1.2 =
Please make sure that the database table is created. If NOT please re-install QMean and it will fix the issue.
