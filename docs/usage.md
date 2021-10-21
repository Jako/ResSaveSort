After the package is installed, the MODX system setting `ressavesort.sorts` has
to be filled with a JSON encoded array of sort configurations.

## System Settings

The following MODX system settings are available:

Key | Description | Default
----|-------------|--------
Sort Configurations | JSON encoded array of sort configurations [^1] | -

### Sort Configuration

Each sort configuration could use the following settings:

Setting | Description | Default
--------|-------------| -------
sortby | Resource fields to sort the MODX container by (template variables have to be prefixed by `tv.`) | pagetitle
sortdir | Direction to sort the MODX container by (`asc` or `desc`) | asc
sortcontainer | ID of the MODX container that has to be sorted | -

[^1]: You could generate/edit the JSON only in a grid in the system setting edit window (it can't be edited with a double click on the value).
