DocSaveSort
================================================================================

Sort MODX containers after saving resources.

Features
--------------------------------------------------------------------------------
With this MODX Revolution plugin automatic sorting of MODX containers could be triggered. 

Installation
--------------------------------------------------------------------------------
MODX Package Management

System Settings
--------------------------------------------------------------------------------
The plugin uses the following system setting:

Key | Description | Default
--- | ----------- | -------
ressavesort.sorts | JSON encoded array of sort configurations | -

### Example

`[{"sortby":"pagetitle","sortdir":"asc","sortcontainer":""}]`

### Sort Configuration

Each sort configuration could use the following settings:

Setting | Description | Default
------- | ----------- | -------
sortby | Resource fields to sort the MODX container by (template variables have to be prefixed by `tv.`) | pagetitle
sortdir | Direction to sort the MODX container by (`asc` or `desc`) | asc
sortcontainer | ID of the MODX container that has to be sorted | parent of the current document