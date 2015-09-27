<?php
/**
 * ResSaveSort
 *
 * Copyright 2013 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ressavesort
 * @subpackage build
 *
 * Plugins for ResSaveSort package
 */
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id', 1);
$plugins[0]->set('name', 'ResSaveSort');
$plugins[0]->set('description', 'Sort MODX containers after saving resources.');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'ressavesort.plugin.php'));
$plugins[0]->set('category', 0);

$events = include $sources['events'] . 'ressavesort.events.php';
if (is_array($events) && !empty($events)) {
	$plugins[0]->addMany($events);
	$modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in ' . count($events) . ' Plugin events for ResSaveSort.');
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find plugin events for ResSaveSort!');
}
unset($events);

return $plugins;
