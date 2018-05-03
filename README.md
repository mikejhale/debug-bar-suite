# Debug Bar Suite
A WordPress Plugin that enables the Debug Bar and a suite of Debug Bar Add-ons in a single plugin. 

## Overview
When developing or troubleshooting a WordPress site, the Debug Bar plugin and related add-ons are a powerful way to see what's happening under the hood. However it can be a pain to have to install and activate multiple Debug Bar Add-ons. 

This plugin uses Composer to include ten popular Debug Bar Add-ons and Query Monitor, and let's you access all of what they provide through a single install. 

## Included Debug Bar Add-ons

### Debug Bar Console

This plugin provides a large textarea in which you can run arbitrary PHP.  This is excellent for testing the contents of variables etc.

### Debug Bar Shortcodes

Debug Bar Shortcodes adds a new panel to the Debug Bar that displays the registered shortcodes for the current request.

### Debug Bar Constants

Debug Bar Constants adds three new panels to the Debug Bar that display the defined constants available to you as a developer for the current request:

### Debug Bar Post Types

Debug Bar Post Types adds a new panel to the Debug Bar that displays detailed information about the registered post types for your site.

### Debug Bar Cron

Debug Bar Cron adds information about WP scheduled events to a new panel in the Debug Bar. This plugin is an extension for Debug Bar and thus is dependent upon Debug Bar being installed for it to work properly.

### Debug Bar Actions and Filters Addon

This plugin adds two more tabs in the Debug Bar to display hooks(Actions and Filters) attached to the current request. Actions tab displays the actions hooked to current request. Filters tab displays the filter tags along with the functions attached to it with respective priority.

### Debug Bar Transients

Debug Bar Transients adds information about WordPress Transients to a new panel in the Debug Bar. This plugin is an extension for Debug Bar and thus is dependent upon Debug Bar being installed for it to work properly.

### Debug Bar List Script & Style Dependencies

Lists scripts and styles that are loaded, in which order they're loaded, and what dependencies exist.

### Debug Bar Remote Requests

This will log and profile remote requests made through the HTTP API.

### Query Monitor
Query Monitor is a debugging plugin for anyone developing with WordPress. You can view debugging and performance information on database queries, hooks, conditionals, HTTP requests, redirects and more. It has some advanced features not available in other debugging plugins, including automatic AJAX debugging and the ability to narrow down things by plugin or theme.

## Features
* Allows for several Debug Bar Add-ons to be enabled at once
* Optionally disable Add-ons in Settings (Under `Tools`)

## Install
1. Download Zip Archive
2. Unzip to Plugin Directory of your wordpress site.
3. Activate Plugin at backend.
4. Check top right admin bar menu Debug.

## ToDo
* Test adding additional add-ons through Hooks/Filters
