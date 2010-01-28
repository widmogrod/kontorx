<?php
require_once 'Prana/Notification.php';

class Prana_Notification_Center {

	/**
	 * @var Prana_Notification_Center
	 */
	protected static $_instance;
	
	/**
	 * @return Prana_Notification_Center
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function __construct() {}

	/**
	 * @var array
	 */
	protected $_notifications = array();

	/**
	 * @param array $observer - callback
	 * @param string $notificationName
	 * @param object $object
	 * @throws Exception
	 * @return void
	 */
	public function addObserver($observer, $notificationName, $object = null) {
		if (is_callable($observer, true, $callable_name)) {
			// czy to jest metoda
			if (is_array($observer)) {
				if (!method_exists($observer[0], $observer[1])) {
					throw new Exception('observer is not callable');
				}
			} else
			// czyfunkcja istnieje
			if (!function_exists($observer)) {
				throw new Exception('observer is not callable');
			}
		}

		$notificationName = (string) $notificationName;

		if (!isset($this->_notifications[$notificationName])) {
			$this->_notifications[$notificationName] = array();
		}

		if (!isset($this->_notifications[$notificationName][$callable_name])) {
			$this->_notifications[$notificationName][$callable_name] = array(
				'observer' => $observer,
				'object' => $object);
		}
		
		return true;
	}

	/**
	 * @param mixed $observer
	 * @throws Exception
	 * @return bool
	 */
	public function removeObserver($observer) {
		if (!is_callable($observer, true, $callable_name)) {
			throw new Exception('observer is not callable');
		}
		
		$result = false;
		foreach ($this->_notifications as $notification) {
			if (isset($notification[$callable_name]))  {
				unset($notification[$callable_name]);
				$result = true;
			}
		}

		return $result;
	}
	
	/**
	 * @param Prana_Notification $notification
	 * @return bool
	 */
	public function postNotification(Prana_Notification $notification) {
		$notificationName = $notification->getName();

		// nie istnieje żaden obiekt zarejestrowany do powiadamiania
		if (!isset($this->_notifications[$notificationName])) {
			return false;
		}

		$object = $notification->getObject();

		$result = false;
		foreach ($this->_notifications[$notificationName] as $observer) {
			// powiadom obserwatorów tylko tego samego typu
			if ($observer['object'] === null || $object === $observer['object']) {
				// powiadamiam..
				call_user_func($observer['observer'], $notification);
				$result = true;
			}			
		}

		return $result;
	}
	
	/**
	 * @param string $name
	 * @param object $object
	 * @param array $userInfo
	 * @return bool
	 */
	public static function postNotificationName($name, $object, array $userInfo = array()) {
		$notification = new Prana_Notification($name, $object, $userInfo);

		return self::getInstance()
						->postNotification($notification);
	}
}