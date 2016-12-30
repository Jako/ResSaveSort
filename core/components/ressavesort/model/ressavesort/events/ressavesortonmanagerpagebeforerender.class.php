<?php

/**
 * @package ressavesort
 * @subpackage plugin
 */
class ResSaveSortOnManagerPageBeforeRender extends ResSaveSortPlugin
{
    public function run()
    {
        $this->modx->controller->addLexiconTopic('ressavesort:default');
        $this->ressavesort->includeScriptAssets();
    }
}