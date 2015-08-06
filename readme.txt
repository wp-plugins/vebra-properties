=== Vebra Properties ===
Contributors: ultimatewebuk
Tags: Vebra, Solex, Alto, Property, Properties, RIghtmove, Zoopla
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T93YSNPS2MV22
Plugin URI: http://www.ultimateweb.co.uk/vebra_properties/
Requires at least: 3.5
Tested up to: 4.2.2
Stable tag: 1.20
License: GPL2

Quickly turn your Vebra API feed (for the Solex and Alto) into a property search on your own wordpress site.

== Description ==
Vebra Properties lets to turn your Vebra API feed into an interactive search and display listing on your own website with the minimum of fuss.  
Just signup for the Vebra API and once you have your credentials enter them into your Vebra Properties settings.  Add a few shortcodes and away you go.
For advanced users you can style your results by overriding our templates and stylesheets or if you're not so sure about this [contact us](http://www.ultimateweb.co.uk/contactus/) for our customisation services.

= Shortcodes =
We built the plugin using fleixible shortcodes for easy installation and customisation.  The shortcodes are;

* [vebra_quicksearch] : to display a concise search box 
* [vebra_search] : to show full search options
* [vebra_properties] : To display a list of properties or search results
* [vebra_details] : To display full property details including map and carousel of property images 

= Templates / Customisation =
We have provided templates corresponding to each of the shortcodes so you can create your own layouts and content.  Simply include your own version of the templates in your theme and these will override the standard set provided;

* vp_quicksearch.php : Change the layout of the quicksearch bar
* vp_search : modify the search layout
* vp_list.php : dictate how property summary details are shown
* vp_detail.php : style the detailed property view

== Installation ==
We have tried to make the installation and setup of the plugin as simple as possible whilst building in fleixibility for the more advanced user to be able to fully customise the style and content.

You can download and install Vebra Properties using the built in WordPress plugin installer. If you download Vebra Properties manually, make sure it is uploaded to "/wp-content/plugins/vebra-properties/".

Activate Vebra Properties in the "Plugins" admin panel using the "Activate" link.

= Basic setup =

Follow the simple steps below to get up and running in minutes; 

1. Create a search page on your site and add the [vebra_searh] shortcode to this page.
2. Create a new page on your site for the search results and add the [vebra_properties] shortcode to this page.
3. Create a new page on your site for the property details and add the [vebra_details] shortcode.
4. Enter your API details into the Verbra Properties "Settings" menu.
5. Also on the Verbra Properties "Settings" menu set the default property search results page that you created in step 2 and 3.

Once you click save on the settings page Vebra Properties will trigger a schedule to populate wordpress with the properties in your feed.  It will also schedule a daily update of properties.

= Scheduling =

As mentioned once set up wordpress will re-populate the property list daily.  This should happen automatically using WordPress's built in schedule service.  However, this can be un-reliable especially if your site uses lots of third party plugins so we recommend that you trigger the schedule using a server cron job.  Please see the FAQ for further details.

= Short codes =

This plugin has fleixble short codes to help with filtering and formatting of the content.  To add options simple include them in the shortcode as a name value pair e.g.  [vebra_list branchid="1" type="To Buy"].  See below for the full set of options available for each short code.  Only add attributes to the shortcode if you want to pre-filter results.  Any manually entered search criteria will override the attributes set in the shortcode.

**[vebra_quicksearch]**

* branchid
* area (To Buy, To Rent, Commercial)
* location (a string containing the location i.e. Leicester, UK)

**[vebra_search]**

* branchid
* area ("To Buy", "To Rent", "Commercial")
* featured ("yes" or "no")
* bedrooms (minimum number of bedrooms - default "0")
* minprice (number value)
* maxprice (number value)
* minrent (number value)
* maxrent (number value)
* type (text description of type i.e. "Detached")
* location (a string containing the location i.e. "Leicester, UK")
* radius (number of miles radius to search)

**[vebra_properties]**

* branchid
* vebraid (list of comma separated ids for select specific properties)
* area ("To Buy", "To Rent", "Commercial")
* soldlet ("show")
* featured ("yes" or "no")
* bedrooms (minimum number of bedrooms - default "0")
* maxbedrooms (number value)
* minprice (number value)
* maxprice (number value)
* minrent (number value)
* maxrent (number value)
* type (text description of type i.e. "Detached")
* location (a string containing the location i.e. "Leicester, UK")
* radius (number of miles radius to search)
* pagesize (number of properties per page, default "6")
* page (current page, default "1")
* orderby (ordering, default "price desc")
* view (default is list.  Optional map view to display on map - requires 1.5+, or add your own view type)

**[vebra_details]**

* vebraid (the VebraID for the property)
* agentref (the agents reference for the property)

== Frequently Asked Questions ==

= Can I edit the layout of the search and results? =
There are 4 templates, one for each of the shortcodes.  Simply create your own version of these templates in your themes directory to override the layout.  You can also create your own styles to override the default ones we have set.  The templates files are;
* vp_quicksearch.php : Change the layout of the quicksearch bar
* vp_search : modify the search layout
* vp_list.php : dictate how property summary details are shown
* vp_detail.php : style the detailed property view

= Can you help customise the plugin =
Of course, yes.  We are a web development agency that specialises in WordPress integrations and customisation.  Just [contact us](http://www.ultimateweb.co.uk/contactus/) with your requirements.

= No maps are appearing =
Some themes include google maps api in order to display maps and may be adding the javascript link as well as our own plugin link.  Google maps will crash if more than one refernce to the javscript library exists causing no maps to display at all on the site.  Either remove the api link from your theme, or make sure it is enqued with the name "google-maps".

= My properties are not appearing =
First of all check that the API details you entered are correct.  If any of these are not correct then the schedule to grab properties will fail.  There may also be a problem with the WordPress scheduler as this can fail particularly if you have other 3rd party plugins installed.  We therefore recommend that you set up a system cron job to run the WordPress schedule manually as we found this to be much more reliable.

= How do I set up scheduling as a cron job? =
You can set up your server’s cron to hit wp-cron.php at a regular interval by following the instructions outlined in [Harish Chouhan’s article on Wptuts+](http://wp.tutsplus.com/articles/insights-into-wp-cron-an-introduction-to-scheduling-tasks-in-wordpress/). If this seems overly complicated, you could use a tool such as Pingdom to trigger an HTTP request directly to wp-cron.php.

= I have no properties listed and the API Authentication Status shows errors =
Any API connection error when talking to the Vebra API is recorded to help identify the problem.  The most common issues are listed below;

* The API details entered in the Vebra Properties settings page are incorrect.  Please be careful in entereing the correct values in the correct fields and make suer the casing is correct as the details are case sensitive.  Also confirm with Vebra that they have sent you the correct details (when we first built the system it would not work and it turned out Vebra had given us some duff credentials)
* If you are running both a test and live environment (i.e. two sites using the same Vebra credentials) one will work and the other will not.  This is because Vebra issue an access token that is valid for several hours and once issued will lock access to the token.  A second site will not know this token and when requesting a new one will be refused because there is still an active token.  So you must turn off any “competing” sites and wait a few hours to release the token.


== Screenshots ==

1. Settings screen.  Enter the API details and select the property list and details pages.
2. Sample property listing page, including search shortcode
3. Sample property details page  

== Upgrade Notice ==

= 1.0 =

First version release October 2014.

= 1.1 =

Released November 2014

= 1.3 =

Fixed problem with daily schedule and finding templates in child themes

= 1.4 = 

Citical update.  Fixed wp cron issue

= 1.5 = 

Added show results on map functionality

= 1.6 = 

Move geolocation to try client side first to avoid API quota limits

= 1.7 = 

Added and api authentication status option

= 1.8 = 

Fix to update (make sure properties are deleted)
Exclude Let and Sold properties from listing
Minor update to Google geolocate to make it more reliable

= 1.9 =

Added rent filter

= 1.10 =

Added branch details to templates and feed import

= 1.11 =

Updated the location search queries to produce better results

= 1.12 =

Additional tweeking to search results based on google maps api logic.  Added file type into database so templates can pull specific types of files separately (images, floorplans, EPC)

= 1.13 =

Added max bedrooms, order by most recent and higher values in max rent

= 1.14 =

Fix for multiple room dimensions

= 1.15 =

Update the map marker pop ups to use InfoBubble for more fleixible layout

= 1.16 =

Added specific file selector to codex

= 1.16 =

Updated the basic styling and improved map listing and search results - updated to use Vebra API v9

= 1.18 = 

Critical Fix: Fixed data format issue with upgrade to v9

= 1.19 = 

Added page title code for property details page and fixed some issues with google maps javascript clash with themes

= 1.20 = 

Minor fix to dimension to stop repeating.  Also added short code soldlet option

== Changelog ==

* First version released 01st October 2014
* 2014-11-06 : Removed php error reporting
* 2014-11-19 : Fixed referencing of scripts and stylesheets and changed the way that property details are referenced to use VebraID instead of Agent Ref as the later is optional
* 2014-12-02 : Fixed problem with daily schedule and finding templates in child themes
* 2014-12-04 : Fixed further WP Cron issue
* 2014-12-05 : Added show on map functionality
* 2014-12-09 : Move google geolocation to client side to avoid usage quota restrictions
* 2014-12-23 : Fix to update (make sure properties are deleted), Exclude Let and Sold properties from listing, Minor update to Google geolocate to make it more reliable
* 2015-02-17 : Added Rent Filter
* 2015-03-02 : Added branch details to default templates and feed
* 2015-03-18 : Improved location matching search
* 2015-04-14 : Search tweeks and filetypes added
* 2015-04-30 : Added max bedrooms, order by most recent and higher values in max rent
* 2015-06-01 : Fix for problem with multiple room dimensions
* 2015-06-26 : Update the map marker pop ups to use InfoBubble for more fleixible layout, also added file type seletor to codex
* 2015-07-09 : Updated the basic styling and improved map listing and search results - updated to use Vebra API v9
* 2015-07-14 : Critical fix - sorted issue with formating of numbers in new feed causing data to populate incorrectly
* 2015-08-03 : Added page title code for property details page and fixed some issues with google maps javascript clash with themes
