<?php
/**
 * @package ressavesort
 * @subpackage plugin
 */

namespace TreehillStudio\ResSaveSort\Plugins\Events;

use TreehillStudio\ResSaveSort\Plugins\Plugin;

class OnManagerPageBeforeRender extends Plugin
{
    public function process()
    {
        $this->modx->controller->addLexiconTopic('ressavesort:default');
        $this->ressavesort->includeScriptAssets();
    }
}
