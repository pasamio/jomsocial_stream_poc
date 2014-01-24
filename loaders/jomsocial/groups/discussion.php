<?php
namespace Stream\Loader;

include_once dirname(dirname(__DIR__)) . '/jomsocial.php';

class JomSocialGroupsDiscussion extends JomSocial
{
	public function __construct($provider, $entry)
	{
		parent::__construct($provider, $entry);
		$registry = $this->provider->get('registry');
		$entry->groupDiscussion = $registry->register('CTableDiscussion', $entry->cid);
	}

	public function resolve(array $keys = array())
	{
		return parent::resolve(array_merge(array('groupDiscussion'), $keys));
	}
}

