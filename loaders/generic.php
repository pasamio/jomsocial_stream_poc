<?php

namespace Stream\Loader;

class Generic {
	protected $provider;
	protected $entry;
	protected $registry = array();

	public function __construct($provider, $entry)
	{
		$this->provider = $provider;
		$this->entry = $entry;
	}

	public function resolve(array $keys = array())
	{
		$keys = array_unique($keys);

		foreach ($keys as $key)
		{
			if (isset($this->entry->$key))
			{
				if (is_array($this->entry->$key))
				{
					foreach ($this->entry->$key as $index => $subvalue)
					{
						if (method_exists($subvalue, 'resolve'))
						{
							$this->entry->{$key}[$index] = $subvalue->resolve();
						}
					}
				}
				else
				{
					if (method_exists($this->entry->$key, 'resolve'))
					{
						$this->entry->$key = $this->entry->$key->resolve();
					}
				}
			}
		}

		return $this->entry;
	}
}