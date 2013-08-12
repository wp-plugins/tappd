=== Tappd ===
Contributors: rustyfelty, martinbowling, digitalrelativity
Tags: untappd plugin, tappd, untappd, craft beer, beer feed, untappd feed
Website: http://digitalrelativity.com/untappd-wordpress-plugin
Donate link: http://digitalrelativity.com/
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tappd is a Wordpress plugin to display different types of feeds from the Untappd API. The plugin will show checkins either in a widget or shortcode.

== Description ==

Tappd is a Wordpress plugin dedicated to displaying different types of feeds from the Untappd API. These feeds include beer, brewery, venue and user. The plugin will show checkins either in a widget, or by the use of a shortcode. The output is classed so that you may style the plugin however you wish.

== Installation ==

1. Upload the `tappd` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Sign Up for an API key. (http://untappd.com/api/register)
1. Enter API settings on Tappd's Settings Page (only from admin account).
1. Use Widgets and Shortcodes!

= GETTING STARTED =

When you first install the plugin, be sure to visit the **Tappd** *Settings Page*. There you will find 2 fields asking for a Client ID and a Client Secret. You must register with Untappd for an API key to use this plugin. You can do so at http://untappd.com/api/register after you have registered a normal account.

After you enter your Client ID and Client Secret, fill in the default values for the feeds you will be using. These are not required, but they do make a nice fallback if you forget to enter an ID later.

= THE WIDGETS =

To use the widgets, it’s no different than any other widget. After you install the plugin, the widgets will be in Appearances -> Widgets under Available Widgets. You then just drag the feed widget you wish to use over to the sidebar widget area. Enter the ID of the beer/venue/brewery/user you wish to display. You can also set a limit to the number of checkins displayed in the widget.

= THE SHORTCODES =

`[beer id=”2034” limit=”10”]`
`[brewery id=”3176” limit=”10”]`
`[venue id=”99432” limit=”10”]`
`[untappduser id=”patstrader” limit=”10”]`

This version of Tappd's shortcodes only support the id="" and limit="" parameters.

== Screenshots ==

1. http://cl.ly/image/2a0A1M3S1n1L
2. http://cl.ly/image/3B2Y3V121B2i