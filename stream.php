<?php
defined('_JEXEC') or die('Restricted access');
global $_PROFILER;

include_once __DIR__ . '/loaders/generic.php';

JFactory::getDbo()->setQuery("SELECT 'com_stream::start'")->execute(); // Query progress mark


// During ajax calls, the following constant might not be called
defined('JPATH_COMPONENT') or define('JPATH_COMPONENT', dirname(__FILE__));

require_once JPATH_ROOT.'/components/com_community/defines.community.php';

// Require the base controller
require_once COMMUNITY_COM_PATH.'/libraries/error.php';
require_once COMMUNITY_COM_PATH.'/controllers/controller.php';
require_once COMMUNITY_COM_PATH.'/libraries/apps.php' ;
require_once COMMUNITY_COM_PATH.'/libraries/core.php';
require_once COMMUNITY_COM_PATH.'/libraries/template.php';
require_once COMMUNITY_COM_PATH.'/views/views.php';
require_once COMMUNITY_COM_PATH.'/helpers/url.php';
require_once COMMUNITY_COM_PATH.'/helpers/ajax.php';
require_once COMMUNITY_COM_PATH.'/helpers/time.php';
require_once COMMUNITY_COM_PATH.'/helpers/owner.php';
require_once COMMUNITY_COM_PATH.'/helpers/azrul.php';
require_once COMMUNITY_COM_PATH.'/helpers/string.php';
require_once COMMUNITY_COM_PATH.'/events/router.php';

JTable::addIncludePath(COMMUNITY_COM_PATH.'/tables');

jimport('joomla.utilities.date');


$provider  = new \Grisgris\Provider\Provider;
$provider->set('builder', CFactory::getBuilder());
$provider->set('database', JFactory::getDbo());
$provider->set('registry', new \Grisgris\Builder\Registry($provider->get('builder')));

/*
array
  'actid' => null
  'actor' => string '' (length=0)
  'target' => string '' (length=0)
  'date' => null
  'app' => null
  'cid' => null
  'groupid' => null
  'eventid' => null
  'maxList' => string '100' (length=3)
  'type' => string 'frontpage' (length=9)
  'exclusions' => null
  'displayArchived' => boolean false
*/

JFactory::getDbo()->setQuery("SELECT 'com_stream::jomsocial_loaded'")->execute(); // Query progress mark

$activitiesModel = CFactory::getModel('activities');
$appModel = CFactory::getModel('apps');

JFactory::getDbo()->setQuery("SELECT 'com_stream::activityModel'")->execute(); // Query progress mark

// This triggers 6 (!) queries to get the config.
$config = CFactory::getConfig();
$respectActivityPrivacy = $config->get('respectactivityprivacy');

JFactory::getDbo()->setQuery("SELECT 'com_stream::getConfig'")->execute(); // Query progress mark

$act = new CActivityStream();

JFactory::getDbo()->setQuery("SELECT 'com_stream::activityStream'")->execute(); // Query progress mark

// getHTML($actor, $target, $date = null, $maxEntry = 0, $type = '', $idprefix = '', $showActivityContent = true, $showMoreActivity = false, $exclusions = null, $displayArchived = false, $filter = 'all', $latestId = 0) {

//file_put_contents('/tmp/activitystream.html', $act->getHTML('', '', null, 0, $view, '', true, $showMore));

//var_dump(JTable::getInstance('Activity', 'CTable')->getFields());

$actid = null;
$view = '';
$showMore = true;

$date = null;
$maxList = 100;
$type = 'frontpage';
$exclusions = null;
$displayArchived = false;

$actor = '';
$target = '';


$rows = $activitiesModel->getActivities($actor, $target, $date, $maxList, $respectActivityPrivacy, $exclusions, $displayArchived, $actid);

JFactory::getDbo()->setQuery("SELECT 'com_stream::getActivities'")->execute(); // Query progress mark


echo '<ul>';
foreach ($rows as $row)
{
	$row->details = resolveLoader('jomsocial.' . $row->app, $provider, $row);
	// echo '<li>';
	// var_dump($result);
	// var_dump($row->app);
	// var_dump($row->params);
	// echo '</li>';
}

echo '</ul>';

$apps = array();
foreach ($rows as $row)
{
	if (method_exists($row->details, 'resolve'))
	{
		$row->details->resolve();
	}

	$apps[] = $row->app;

	//var_dump($row);
}

var_dump(array_unique($apps));

JFactory::getDbo()->setQuery("SELECT 'com_stream::postResolve'")->execute(); // Query progress mark

/*
$data = $act->_getData(
				array(
					'actid' => $actid,
					'actor' => $actor,
					'target' => $target,
					'date' => $date,
					'maxList' => $maxList,
					'type' => $type,
					'exclusions' => $exclusions,
					'displayArchived' => $displayArchived)
		);
var_dump($data);
//*/

function resolveLoader($app, &$provider, &$row)
{
	$parts = array_map('ucfirst', explode('.', $app));

	$path = __DIR__ . '/loaders/' . strtolower(implode('/', $parts)) . '.php';
	if (file_exists($path))
	{
		include_once $path;
		$class = '\Stream\Loader\\' . implode('', $parts);
		return new $class($provider, $row);
	}

	if (count($parts) == 1)
	{
		return new \Stream\Loader\Generic($provider, $row);
	}
	else
	{
		return resolveLoader(implode('.', array_slice($parts, 0, -1)), $provider, $row);
	}
}

