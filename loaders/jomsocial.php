<?php
namespace Stream\Loader;

include_once __DIR__ . '/generic.php';

class Jomsocial extends Generic
{
	public function __construct($provider, $entry)
	{
		parent::__construct($provider, $entry);

		$registry = $this->provider->get('registry');

		if ($entry->actor)
		{
			$entry->actor = $registry->register('JSUser', $entry->actor);
		}

		$entry->actors = json_decode($entry->actors);
		$entry->userActors = array();

		if (isset($entry->actors->userid))
		{
			foreach ($entry->actors->userid as $userId)
			{
				$entry->userActors[] = $registry->register('JSUser', $userId->id);
			}
		}

		if ($entry->groupid)
		{
			$entry->group = $registry->register('CTableGroup', $entry->groupid);
		}

		if ($entry->eventid)
		{
			$entry->event = $registry->register('CTableEvent', $entry->eventid);
		}

		$entry->app_title = $registry->register('JomSocialAppTitle', $entry->app);
	}

	public function resolve(array $keys = array())
	{
		return parent::resolve(array_merge(array('app_title', 'actor', 'group', 'event', 'userActors'), $keys));
	}
}