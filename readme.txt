=== QMean - WordPress Did You Mean and Search Suggestion Like Google ===
Contributors: arashsafari
Tags: search,suggestion,optimization,better search
Requires at least: 6.0
Tested up to: 6.2.2
Stable tag: 2.0
Requires PHP: 7.0+
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ajax smart keyword suggestions by fixing typos for better results. Plus "Did You Mean" feature, Google style! Simple, Minimal, Fast and Smart. Plus an analytics dashboard for searched queries

== Description ==
QMean is a powerful, yet minimal, AJAX-driven auto-complete query and 'did you mean' suggestion plugin designed to enhance your WordPress search experience. With QMean, users can create relevant queries effortlessly, even if they contain typos or misspelled words, just like Google's smart suggestions.

== Auto-Complete Search Suggestions ==
QMean provides real-time suggestions while users type, making it easy for them to find the right keywords.

== Did You Mean ==
After the search, QMean shows a "Did You Mean" feature, suggesting the correct keywords based on similar words and typos.

== Two Engines ==
Choose between Google's suggestion engine and QMean to suit your specific needs.

== Block Enabled ==
Seamlessly integrate QMean into your WordPress Search block, and also add a "Did You Mean" block wherever you like.

== Offer Query Suggestions ==
QMean searches through your database, gathering relevant keywords and phrases related to the user's input, giving you control over where to look, including Titles, Excerpts, Contents, Taxonomy Terms, and PostMetas.

== Modes: Phrase or Word By Word ==
Customize QMean to interact with individual words or offer phrases based on the entire input, each with its own advantages.

== Keywords Analytics ==
QMean records searched queries and their results for better SEO optimization.

== CSS Selector ==
Aside being block base, utilize a CSS selector for your search input, allowing QMean to integrate and provide suggestions with ease using its QMean field recognizer tool.

== Simple Shortcode ==
If CSS selectors aren't your preference, you can use shortcodes to create search fields and position "Did you mean" suggestions anywhere you desire.

== Settings and Styling ==
Tailor the appearance and placement of the suggestion box to match your site's design, and switch between search engines with ease

== Custom and Defined Action ==
QMean's did you mean has an action so you can use it. It can also hook itself to any action you set in the settings and show up there.

== Advanced Settings ==
Fine-tune the plugin by adjusting keyword sensitivity, removing stop words, ignoring shortcodes in content, merging top searched keywords, limiting results, and more.

== How This Works!? ==
QMean intelligently interprets user input, even if it contains misspelled words, offering relevant suggestions based on context. The plugin supports both word-by-word and phrase-based suggestions, ensuring users find the content they seek effortlessly.

== Key Features: ==
* Auto complete search suggestion
* Smart prediction
* Two different suggestion engines: Google and QMean
* Word-by-word suggestions
* Phrase mode suggestions
* Support for Post types and Taxonomy terms
* Support for post title, excerpt, content, and meta
* Optimized performance
* Individual search areas for each form (e.g., posts, products, etc.)
* Minimal configuration required
* Searched keywords analytics and reports
* RTL direction and UTF-8 support

Upgrade your WordPress search experience with QMean and provide your users with intelligent and intuitive search suggestions!


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
= Is Google's Engine Free? =
Absolutely! QMean leverages Google's browser API URL to fetch suggestions, making it a completely free-to-use plugin.

= What are the differences between QMean and Google's suggestion engine? =
In general, QMean collects relevant keywords from your website's content and database, and then offers suggestions based on this data. On the other hand, Google's suggestion engine relies on the most relevant keywords determined by its ranking algorithms. The key distinctions between the two lie in speed and relevancy. Google suggests fast and QMean suggests relevant.

= What is the shortcode? =
There are two shortcodes. One for search form and the other to show suggestions. You can read more in [QMean's wiki](https://github.com/arashsafaridev/qmean/wiki/get-started)

= What is the difference of Phrase mode or Word by Word mode? =
Word by word mode will look for the most related keyword for each word. For example, if you are looking for "Hoodie With Logo",  it will search for suggestion based on active word. First for hoodie, then for with, and finally for logo.
But for the  Phrase mode, it will look for related keywords on the whole phrase. For example, when your finishing "Hoodie With Logo" It will provide the suggestions on completion, for the whole phrase.

= Which mode is better? =
Both of them are useful. It depends on your need. Phrase mode can have solid results at the end but fewer suggestions. But WBW can have more suggestions and fewer results because it will suggest the most related keyword from the entire database! it can be useful to optimize your SEO (if user created that phrase and you didn't predicted it, it is not QMeans's fault :) )


== Screenshots ==

1. Word By Word Suggestions
2. Phrase Mode Suggestions
3. Search Block Settings
4. Did You Mean
5. Did You Mean Block
6. Did You Mean Block Settings
7. Plugin Settings: Essential Setup
8. Plugin Settings: Suggestion Box Styles
9. Plugin Settings: Shortcodes
10. Plugin Settings: Advance Settings
11. Dashboard Analytics
12. User Eye - QMean Dashboard Analytics
13. QMean Field Recognizer

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
* Feature:   Add Did You Mean Block
* Feature:   Customize search block
* Refatored: Settings page
* Fixed:     MySQL compatibilty approach for regex
* Fixed:     PHP notice for uninstallation callback 
* Fixed:     PHP warning: Timeout exceeded in regular expression match
* Optimized: Early return when enough suggestions found
* Updated:   Readme file and plugin assets

= 1.9.1 =
* Add did you mean asset files 

== Upgrade Notice ==

= before 1.0.9 =
Please upgrade in order to fix issues and get more accurate results and tools specially if you are using version before 1.1.3
= after 1.2 =
Please make sure that the database table is created. If NOT please re-install QMean and it will fix the issue.
