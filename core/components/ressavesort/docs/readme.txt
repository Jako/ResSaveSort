ResSaveSort
===========

Sort MODX containers after saving resources.

Features
--------

With this MODX Revolution plugin automatic sorting of MODX containers could be
triggered.

Installation
------------

MODX Package Management

System Settings
---------------

The plugin uses the following system setting:

Key                 | Description                               | Default
------------------- | ----------------------------------------- | -------
Sort Configurations | JSON encoded array of sort configurations | -

Since version 1.0.2 you could generate the JSON with a grid in the system
setting edit window.

Sort Configuration
··················

Each sort configuration could use the following settings:

Setting       | Description                                  | Default
------------- | -------------------------------------------- | ----------------
sortby        | Resource fields to sort the MODX container   | pagetitle
              | by (template variables have to be prefixed   |
              | by 'tv.')                                    |
sortdir       | Direction to sort the MODX container by      | asc
              | ('asc' or 'desc')                            |
sortcontainer | ID of the MODX container that has to be      | parent of the
              | sorted                                       | current resource

Hotfixes and Bugreport on https://github.com/Jako/ResSaveSort