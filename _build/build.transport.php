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
 * @subpackage build
 *
 * ResSaveSort build script
 */
ob_start();

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME', 'ResSaveSort');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '1.0.1');
define('PKG_RELEASE', 'pl');

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'events' => $root . '_build/data/events/',
    'resolvers' => $root . '_build/resolvers/',
    'properties' => $root . '_build/data/properties/',
    'permissions' => $root . '_build/data/permissions/',
    'chunks' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'snippets' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'plugins' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'pages' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/pages/',
    'templates' => $root . 'core/components/' . PKG_NAME_LOWER . '/templates/',
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER,
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
);
unset($root);
$download = false;

$hasAssets = is_dir($sources['source_assets']); /* Transfer the files in the assets dir. */
$hasCore = is_dir($sources['source_core']); /* Transfer the files in the core dir. */

$hasContexts = file_exists($sources['data'] . 'transport.contexts.php');
$hasResources = file_exists($sources['data'] . 'transport.resources.php');
$hasValidators = is_dir($sources['build'] . 'validators'); /* Run a validators before installing anything */
$hasFiles = file_exists($sources['data'] . 'transport.files.php');
$hasResolvers = file_exists($sources['data'] . 'transport.resolvers.php');
$hasSetupOptions = is_dir($sources['data'] . 'install.options'); /* HTML/PHP script to interact with user */
$hasMenu = file_exists($sources['data'] . 'transport.menus.php'); /* Add items to the MODx Top Menu */
$hasSettings = file_exists($sources['data'] . 'transport.settings.php'); /* Add new MODx System Settings */
$hasContextSettings = file_exists($sources['data'] . 'transport.contextsettings.php');

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx = new modX();
$modx->initialize('mgr');

echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);

$assetsPath = ($hasAssets) ? '{assets_path}components/' . PKG_NAME_LOWER . '/' : '';
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/', $assetsPath);

/* load contexts */
if ($hasContexts) {
    $contexts = include $sources['data'] . 'transport.contexts.php';
    if (!is_array($contexts)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No Contexts defined.');
    } else {
        $attributes = array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($contexts as $context) {
            $vehicle = $builder->createVehicle($context, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($contexts) . ' Contexts.');
        unset($contexts, $context, $attributes);
    }
}

/* load resources */
if ($hasResources) {
    $resources = include $sources['data'] . 'transport.resources.php';
    if (!is_array($resources)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No Resources defined.');
    } else {
        $attributes = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'pagetitle',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
                'ContentType' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                ),
            ),
        );
        foreach ($resources as $resource) {
            $vehicle = $builder->createVehicle($resource, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($resources) . ' Resources.');
        unset($resources, $resource, $attributes);
    }
}

/* load system settings */
if ($hasSettings) {
    $settings = include $sources['data'] . 'transport.settings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No System Settings defined.');
    } else {
        $attr = array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting, $attr);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings.');
        unset($settings, $setting, $attr);
    }
}

/* load context settings */
if ($hasContextSettings) {
    $settings = include $sources['data'] . 'transport.contextsettings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No Context Settings defined.');
    } else {
        $attributes = array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting, $attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' Context Settings.');
        unset($settings, $setting, $attributes);
    }
}

/* See what we have based on the files */
$hasSnippets = file_exists($sources['data'] . '/transport.snippets.php');
$hasChunks = file_exists($sources['data'] . '/transport.chunks.php');
$hasTemplates = file_exists($sources['data'] . '/transport.templates.php');
$hasTemplateVariables = file_exists($sources['data'] . '/transport.tvs.php');
$hasPlugins = file_exists($sources['data'] . '/transport.plugins.php');
$hasPropertySets = file_exists($sources['data'] . '/transport.propertysets.php');

/* create category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in category "' . PKG_NAME . '".');

/* add snippets */
if ($hasSnippets) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding snippets.');
    $snippets = include $sources['data'] . 'transport.snippets.php';
    if (is_array($snippets)) {
        if ($category->addMany($snippets, 'Snippets')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($snippets) . ' snippets.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in snippets failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No snippets defined in "transport.snippets.php".');
    }
    unset($snippets);
}

/* add chunks */
if ($hasChunks) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding chunks.');
    $chunks = include $sources['data'] . 'transport.chunks.php';
    if (is_array($chunks)) {
        if ($category->addMany($chunks, 'Chunks')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($chunks) . ' chunks.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in chunks failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No chunks defined in "transport.chunks.php".');
    }
    unset($chunks);
}

/* add templates  */
if ($hasTemplates) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding templates.');
    $templates = include $sources['data'] . '/transport.templates.php';
    if (is_array($templates)) {
        if ($category->addMany($templates, 'Templates')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($templates) . ' templates.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in templates failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No templates defined in "transport.templates.php".');
    }
    unset($templates);
}

/* add template variables  */
if ($hasTemplateVariables) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding template variables.');
    $tvs = include $sources['data'] . '/transport.tvs.php';
    if (is_array($tvs)) {
        if ($category->addMany($tvs, 'TemplateVars')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($tvs) . ' template variables.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in template variables failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No template variables defined in "transport.tvs.php".');
    }
    unset($tvs);
}

/* add plugins */
if ($hasPlugins) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding plugins.');
    $plugins = include $sources['data'] . 'transport.plugins.php';
    if (is_array($plugins)) {
        if ($category->addMany($plugins, 'Plugins')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' plugins.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in plugins failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No plugins defined in "transport.plugins.php".');
    }
    unset($plugins);
}

/* add property sets */
if ($hasPropertySets) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding property sets.');
    $propertySets = include $sources['data'] . '/transport.propertysets.php';
    if (is_array($propertySets)) {
        if ($category->addMany($propertySets, 'PropertySets')) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' property sets.');
        } else {
            $modx->log(modX::LOG_LEVEL_FATAL, 'Packing in property sets failed');
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No property sets defined in "transport.propertysets.php".');
    }
}

/* create category vehicle */

$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
);

if ($hasValidators) {
    $attr[xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL] = true;
}

if ($hasSnippets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

if ($hasChunks) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

if ($hasPlugins) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

if ($hasTemplates) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Templates'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'templatename',
    );
}

if ($hasTemplateVariables) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['TemplateVars'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

if ($hasPropertySets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['PropertySets'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}

$vehicle = $builder->createVehicle($category, $attr);
unset($category, $attr);

if ($hasValidators) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding validators ...');
    $validators = include $sources['data'] . 'transport.files.php';
    if (!is_array($validators)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No validators defined.');
    } else {
        foreach ($validators as $key => $validator) {
            if (file_exists($validator['source'])) {
                $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . $key . ' validator.');
                $vehicle->validate('php', $validator);
            } else {
                $modx->log(modX::LOG_LEVEL_ERROR, 'Could not find validator ' . $key . ' file.');
            }
        }
    }
}

if ($hasFiles) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers ...');
    $files = include $sources['data'] . 'transport.files.php';
    if (!is_array($files)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No resolvers defined.');
    } else {
        foreach ($files as $file) {
            $vehicle->resolve('file', $file);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($files) . ' file resolvers.');
    }
}

if ($hasResolvers) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding PHP resolvers ...');
    $resolvers = include $sources['data'] . 'transport.resolvers.php';
    if (!is_array($resolvers)) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'No resolvers defined.');
    } else {
        foreach ($resolvers as $resolver) {
            $vehicle->resolve('php', $resolver);
        }
        $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($files) . ' PHP resolvers.');
    }
}
$builder->putVehicle($vehicle);
unset($vehicle, $resolvers, $resolver);

/* now pack in the license file, readme and changelog */
$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip ...');
$built = $builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

if ($built) {
    if (!$download) {
        $modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");
        ob_end_flush();
        exit();
    } else {
        ob_end_clean();

        $filename = $builder->filename;
        $directory = $builder->directory;

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($directory . $filename));
        @readfile($directory . $filename);
    }
} else {
    $modx->log(modX::LOG_LEVEL_FATAL, "\n<br />Error: No Package Built.<br />\nExecution time: {$totalTime}\n");
    ob_end_flush();
}

exit();
