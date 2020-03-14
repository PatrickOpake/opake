<?php

/**
 * Доступ к наблюдателям приложения
 */
namespace Opake;

class EventDispatcher
{
	protected $pixie;
	protected $events = array();
	protected $instances = array();
	protected $isEventsInitialized = false;

	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * Returns listener instance by name
	 *
	 * @param $name
	 * @return string
	 *
	 */
	public function get($name)
	{
		if (isset($this->instances[$name])) {
			return $this->instances[$name];
		} else {
			$service = $this->pixie->app_namespace . 'Events\\' . implode('\\', array_map('ucfirst', explode('_', $name)));
			if (!class_exists($service)) {
				$service .= '\\' . ucfirst($name);
			}
			$service = new $service($this->pixie);
			$this->instances[$name] = $service;
			return $service;
		}
	}

	/**
	 * Создаёт событие
	 *
	 * @param string $event Событие, может быть любой строкой
	 * @param mixed $args Объекты, тексты или что-то ещё, что поступит на листенер
	 */
	public function fireEvent($event)
	{
		$this->checkEventsInitialized();
		$args = func_get_args();
		array_shift($args);
		if (isset($this->events[$event])) {
			foreach ($this->events[$event] as $listener) {

				try {
					if (is_callable($listener)) {
						call_user_func_array($listener, $args);
					} elseif ($listener instanceof \Opake\Events\AbstractListener) {
						call_user_func_array(array($listener, 'dispatch'), $args);
					}
				} catch (\Exception $e) {
					$this->pixie->logger->exception($e);
				}

			}
		}
	}

	/**
	 * Регистрирует событие и листенер для него
	 *
	 * @param string $event Событие, может быть любой строкой
	 * @param \Opake\Events\AbstractListener|callable $listener Листенер, который подхватит событие
	 */
	public function register($event, $listener)
	{
		$this->events[$event][] = $listener;
	}

	protected function checkEventsInitialized()
	{
		if (!$this->isEventsInitialized) {
			$this->initEvents();
			$this->isEventsInitialized = true;
		}
	}

	protected function initEvents()
	{

	}
}
