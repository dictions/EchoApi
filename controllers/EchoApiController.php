<?php
namespace Craft;

class EchoApiController extends BaseController {
	public function actionSendResponse($handler) {
		JsonHelper::sendJsonHeaders();

		// Default disabled
		// Default duration 1 hr
		$CACHE_DURATION = craft()->config->get('cacheDuration', 'echoapi') ?: 3600;
		$CACHE_ENABLED = craft()->config->get('cacheEnabled', 'echoapi') ?: false;

		if ($CACHE_ENABLED) {
			$CACHE_KEY = 'ECHO_API_' . $_SERVER['REQUEST_URI'];
			$cachedJsonResponse = craft()->cache->get($CACHE_KEY);

			if ($cachedJsonResponse) {
				header("X-EchoApi-Cache: hit");
				echo $cachedJsonResponse;
			} else {
				header("X-EchoApi-Cache: not-found");
				$response = $this->_callHandler($handler);
				echo json_encode($response);
				craft()->cache->set($CACHE_KEY, json_encode($response), $CACHE_DURATION);
			}
		} else {
			header("X-EchoApi-Cache: disabled");
			$response = $this->_callHandler($handler);
			echo json_encode($response);
			craft()->cache->set($CACHE_KEY, json_encode($response), $CACHE_DURATION);
		}

		craft()->end();
	}

	private function _callHandler($handler) {
		$params = craft()->urlManager->getRouteParams();
		$variables = (isset($params['variables']) ? $params['variables'] : null);
		$response = $this->_callWithParams($handler, $variables);

		return $response;
	}

	// Taken from ElementAPI plugin
	private function _callWithParams($func, $params) {
		if (!$params) {
			return call_user_func($func);
		}

		$ref = new \ReflectionFunction($func);
		$args = [];

		foreach ($ref->getParameters() as $param) {
			$name = $param->getName();

			if (isset($params[$name])) {
				if ($param->isArray()) {
					$args[] = is_array($params[$name]) ? $params[$name] : [$params[$name]];
				} else if (!is_array($params[$name])) {
					$args[] = $params[$name];
				} else {
					return false;
				}
			} else if ($param->isDefaultValueAvailable()) {
				$args[] = $param->getDefaultValue();
			} else {
				return false;
			}
		}

		return $ref->invokeArgs($args);
	}
}
