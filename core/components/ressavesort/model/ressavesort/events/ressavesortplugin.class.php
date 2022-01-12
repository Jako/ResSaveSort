<?php
/**
 * @package ressavesort
 * @subpackage plugin
 */

abstract class ResSaveSortPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var ResSaveSort $ressavesort */
    protected $ressavesort;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties = &$scriptProperties;
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('ressavesort.core_path', null, $this->modx->getOption('core_path') . 'components/ressavesort/');
        $this->ressavesort = $this->modx->getService('ressavesort', 'ResSaveSort', $corePath . 'model/ressavesort/', array(
            'core_path' => $corePath
        ));
    }

    abstract public function run();
}
