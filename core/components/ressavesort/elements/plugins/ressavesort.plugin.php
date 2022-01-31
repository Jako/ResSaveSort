<?php
/**
 * ResSaveSort Plugin
 *
 * @package ressavesort
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'TreehillStudio\ResSaveSort\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('ressavesort.core_path', null, $modx->getOption('core_path') . 'components/ressavesort/');
/** @var ResSaveSort $ressavesort */
$ressavesort = $modx->getService('ressavesort', 'ResSaveSort', $corePath . 'model/ressavesort/', [
    'core_path' => $corePath
]);

if ($ressavesort) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' could not be initialized!', '', 'ResSaveSort Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' was not found!', '', 'ResSaveSort Plugin');
    }
}

return;