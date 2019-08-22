# ResSaveSort

Sort MODX containers after saving resources.

- Author: Thomas Jakobi <thomas.jakobi@partout.info>
- License: GNU GPLv2

## Features

With this MODX Revolution plugin automatic sorting of MODX containers could be
triggered.

## Installation

MODX Package Management

## Usage

Fill the MODX system setting `ressavesort.sorts` with an JSON encoded array of
sort configurations. The JSON could be edited in a grid, when the system setting
is edited with a right click on the system setting.

## System Settings

The plugin uses the following system setting in the namespace `ressavesort`:

Key | Description | Default
----|-------------|--------
Sort Configurations | JSON encoded array of sort configurations | -

You could generate/edit the JSON only in a grid in the system setting edit
window (it can't be edited with a double click on the value).

### Sort Configuration

Each sort configuration could use the following settings:

Setting | Description | Default
--------|-------------| -------
sortby | Resource fields to sort the MODX container by (template variables have to be prefixed by `tv.`) | pagetitle
sortdir | Direction to sort the MODX container by (`asc` or `desc`) | asc
sortcontainer | ID of the MODX container that has to be sorted | -

## GitHub Repository

https://github.com/Jako/ResSaveSort
