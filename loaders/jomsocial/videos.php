<?php
namespace Stream\Loader;

include_once dirname(__DIR__) . '/jomsocial.php';

class JomSocialVideos extends JomSocial
{
	public function __construct($provider, $entry)
	{
		parent::__construct($provider, $entry);
		$registry = $this->provider->get('registry');
		$entry->video = $registry->register('CTableVideo', $entry->cid);
	}

	public function resolve(array $keys = array())
	{
		return parent::resolve(array_merge(array('video'), $keys));
	}
}