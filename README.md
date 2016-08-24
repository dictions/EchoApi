# Echo API plugin for Craft

> This plugin copies shamelessly from the great Pixel and Tonic [Plugin Element API](https://github.com/pixelandtonic/ElementAPI)

This plugin makes it easy to create a JSON API in [Craft](http://buildwithcraft.com). No fluff added, just simple routing.

## Why?

You may need access to Craft specific APIs, but may not need access to Elements saved to Craft's database. One great example use case, and the reason I created this, is to create an API wrapper around [Craft Shopify](https://github.com/davist11/craft-shopify/).

## Installation

To install Echo API, follow these steps:

1.  Upload the echoapi/ folder to your craft/plugins/ folder.
2.  Go to Settings > Plugins from your Craft control panel and enable the Echo API plugin.

## Setup

To define your API endpoints, create a new `echoapi.php` file within your craft/config/ folder. This file should return an array with an `endpoints` key, which defines your site’s API endpoints. Within the `endpoints` array, keys are URL patterns, and values are endpoint configurations.

```php
<?php
namespace Craft;

return [
    'endpoints' => [
        'news.json' => function() {
			$posts = fetchAllNews();
			return [
				posts: $posts
			]
		},
        'news/<entryId:\d+>.json' => function($entryId) {
			$posts = fetchNewsById($entryId);
            return [
                posts:
            ];
        },
    ]
];
```

### Configuration Settings

#### `endpoints` _(Required)_

The `endpoints` array is an array of pattern keys and pattern handlers. If the `endpoint` pattern is requested, the corresponding handler is run, and the response is returned as JSON.

#### `cacheEnabled` _(Default: `false`)_

Turns CraftCMS caching on and off. Echo API uses whatever caching mechanism is already set up for template caching.

#### `cacheDuration` _(Default: `3600`)_

Time Echo API should cache the response in seconds.

### Dynamic URL Patterns and Endpoint Configurations

URL patterns can contain dynamic subpatterns in the format of `<subpatternName:regex>`, where `subpatternName` is the name of the subpattern, and `regex` is a valid regular expression. For example, the URL pattern “`news/<entryId:\d+>.json`” will match URLs like `news/100.json`. You can also use the tokens `{handle}` and `{slug}` within your regular expression, which will be replaced with the appropriate regex patterns for matching handles and resources.

```php
'news/<entryId:\d+>.json' => function($entryId) {
	$posts = fetchNewsById($entryId);
	return [
		posts:
	];
}
```
