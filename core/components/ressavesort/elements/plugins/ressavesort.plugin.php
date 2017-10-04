<?php
/**
 * ResSaveSort Plugin
 *
 * @package ressavesort
 * @subpackage pluginfile
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'ResSaveSort' . $modx->event->name;

$corePath = $modx->getOption('ressavesort.core_path', null, $modx->getOption('core_path') . 'components/ressavesort/');
/** @var ResSaveSort $ressavesort */
$ressavesort = $modx->getService('ressavesort', 'ResSaveSort', $corePath . 'model/ressavesort/', array(
    'core_path' => $corePath
));

$modx->loadClass('ResSaveSortPlugin', $ressavesort->getOption('modelPath') . 'ressavesort/events/', true, true);
$modx->loadClass($className, $ressavesort->getOption('modelPath') . 'ressavesort/events/', true, true);
if (class_exists($className)) {
    /** @var ResSaveSortPlugin $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}

return;
