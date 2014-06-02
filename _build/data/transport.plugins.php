<?php
/**
 * ResSaveSort
 *
 * Copyright 2013 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * ResSaveSort is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ResSaveSort is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ResSaveSort; if not, write to the Free Software Foundation, Inc., 59
 * Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package ressavesort
 * @subpackage build
 *
 * Package in plugins.
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
