<?php
namespace Stream\Loader;

include_once dirname(__DIR__) . '/jomsocial.php';

class JomSocialPhotos extends JomSocial
{
	public function __construct($provider, $entry)
	{
		parent::__construct($provider, $entry);
		$registry = $this->provider->get('registry');
		$entry->album = $registry->register('CTableAlbum', $entry->cid);
	}

	public function resolve(array $keys = array())
	{
		return parent::resolve(array_merge(array('album'), $keys));
	}
}