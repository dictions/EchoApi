<?php
namespace Craft;

class EchoApiPlugin extends BasePlugin {
	public function getName() {
		return Craft::t('Echo API');
	}

	public function getVersion() {
		return '1.0.0';
	}

	public function getSchemaVersion() {
		return '1.0.0';
	}

	public function getDeveloper() {
		return 'Mono Mono';
	}

	public function getDeveloperUrl() {
		return 'http://monomono.studio';
	}

	public function getPluginUrl() {
		return 'https://github.com/dictions/EchoApi';
	}

	public function registerSiteRoutes() {
		$routes = [];
		$endpoints = craft()->config->get('endpoints', 'echoapi');

		foreach ($endpoints as $pattern => $handler) {
			// Convert Yii 2-style route subpatterns to normal regex subpatterns
			$pattern = preg_replace('/<(\w+):([^>]+)>/', '(?P<\1>\2)', $pattern);
			$params = ['handler' => $handler];

			$routes[$pattern] = [
				'action' => 'echoApi/sendResponse',
				'params' => $params,
			];
		}

		return $routes;
	}
}
