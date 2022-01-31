<?php
/**
 * Abstract plugin
 *
 * @package ressavesort
 * @subpackage plugin
 */

namespace TreehillStudio\ResSaveSort\Plugins;

use modX;
use ResSaveSort;

/**
 * Class Plugin
 */
abstract class Plugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var ResSaveSort $ressavesort */
    protected $ressavesort;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    /**
     * Plugin constructor.
     *
     * @param $modx
     * @param $scriptProperties
     */
    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties = &$scriptProperties;
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('ressavesort.core_path', null, $this->modx->getOption('core_path') . 'components/ressavesort/');
        $this->ressavesort = $this->modx->getService('ressavesort', 'ResSaveSort', $corePath . 'model/ressavesort/', [
            'core_path' => $corePath
        ]);
    }

    /**
     * Run the plugin event.
     */
    public function run()
    {
        $init = $this->init();
        if ($init !== true) {
            return;
        }

        $this->process();
    }

    /**
     * Initialize the plugin event.
     *
     * @return bool
     */
    public function init()
    {
        return true;
    }

    /**
     * Process the plugin event code.
     *
     * @return mixed
     */
    abstract public function process();
}