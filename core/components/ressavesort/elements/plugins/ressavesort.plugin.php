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
 * ResSaveSort is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ResSaveSort; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package ressavesort
 * @subpackage plugin
 *
 * Plugin Code
 */
switch ($modx->event->name) {
    case 'OnDocFormSave':
        $parent = $resource->get('parent');

        // get ressavesort system settings
        $sorts = $modx->fromJSON($modx->getOption('ressavesort.sorts', null, ''));
        if ($sorts) {
            // work each configuration
            foreach ($sorts as $sort) {
                // get each configuration setting
                $sortBy = $modx->getOption('sortby', $sort, 'pagetitle', true);
                $sortDir = $modx->getOption('sortdir', $sort, 'asc', true);
                $sortContainer = $modx->getOption('sortcontainer', $sort, $parent, true);

                $sortContainer = explode(',', $sortContainer);
                foreach ($sortContainer as $key => $value) {
                    $sortContainer[$key] = intval($value);
                }

                // if resource lasts in one sorted container
                if (in_array($parent, $sortContainer, true)) {
                    $c = $modx->newQuery('modResource');

                    if (substr($sortBy, 0, 3) != 'tv.') {
                        // sortby resource field
                        $c->select('modResource.*');
                        $c->where(array('parent:=' => $parent));
                        $c->sortby($sortBy, $sortDir);
                    } else {
                        // sortby template variable
                        $c->select('modResource.*, tvc.value, tv.name');
                        $c->where(array('parent:=' => $parent, array('AND:tv.name:=' => substr($sortBy, 3), 'OR:tv.name:=' => null)));
                        $c->sortby('value', $sortDir);
                        $c->leftJoin('modTemplateVarResource', 'tvc', array('tvc.contentid = modResource.id'));
                        $c->leftJoin('modTemplateVar', 'tv', array('tv.id = tvc.tmplvarid'));
                    }
                    // get sorted resources
                    $siblings = $modx->getCollection('modResource', $c);
                    if (count($siblings) > 0) {
                        $menuindex = 0;
                        // replace the menuindex
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