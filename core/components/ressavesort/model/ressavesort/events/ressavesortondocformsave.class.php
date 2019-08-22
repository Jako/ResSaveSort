<?php

/**
 * @package ressavesort
 * @subpackage plugin
 */
class ResSaveSortOnDocFormSave extends ResSaveSortPlugin
{
    public function run()
    {
        /** @var modResource $resource */
        $resource = $this->scriptProperties['resource'];
        $parent = $resource->get('parent');

        $sorts = json_decode($this->modx->getOption('ressavesort.sorts', null, ''), true);
        if ($sorts) {
            foreach ($sorts as $sort) {
                $sortBy = $this->modx->getOption('sortby', $sort, 'pagetitle', true);
                $sortDir = $this->modx->getOption('sortdir', $sort, 'asc', true);
                $sortContainer = $this->modx->getOption('sortcontainer', $sort, $parent, true);

                $sortContainer = explode(',', $sortContainer);
                foreach ($sortContainer as $key => $value) {
                    $sortContainer[$key] = intval($value);
                }

                if (in_array($parent, $sortContainer, true)) {
                    $c = $this->modx->newQuery('modResource');
                    if (substr($sortBy, 0, 3) != 'tv.') {
                        // sortby resource field
                        $c->select('modResource.*');
                        $c->where(array('parent:=' => $parent));
                        $c->sortby($sortBy, $sortDir);
                    } else {
                        // sortby template variable
                        $c->select('modResource.*, tvc.value, tv.name');
                        $c->where(array(
                            'parent:=' => $parent,
                            array(
                                'AND:tv.name:=' => substr($sortBy, 3),
                                'OR:tv.name:=' => null
                            )
                        ));
                        $c->sortby('value', $sortDir);
                        $c->leftJoin('modTemplateVarResource', 'tvc', array('tvc.contentid = modResource.id'));
                        $c->leftJoin('modTemplateVar', 'tv', array('tv.id = tvc.tmplvarid'));
                    }
                    $siblings = $this->modx->getIterator('modResource', $c);
                    if ($this->modx->getCount('modResource', $c) > 0) {
                        $menuindex = 0;
                        foreach ($siblings as $sibling) {
                            $sibling->set('menuindex', $menuindex);
                            $sibling->save();
                            $menuindex++;
                        }
                    }
                }
            }
        }
    }
}
