<?php

namespace ew_evelations;

class Template extends \Template
{
    /**
     * @param      $global_smarty
     * @param      $template
     * @param      $assignArray
     * @param bool $assign_to
     * @param bool $use_cache
     * @return string
     */
    public function getTemplate($global_smarty, $template, $assignArray, $assign_to = false, $use_cache = false)
    {
        global $xtPlugin, $language, $page, $currency;

        // cache fix for xt:C v6.1.2+
        $use_cache = plugin::isSystemCacheEnabled();

        // load default method for all other xt:C versions
        return parent::getTemplate($global_smarty, $template, $assignArray, $assign_to, $use_cache);
    }

    /**
     * Checks if template cache is available
     *
     * @param string $templateFile
     * @param bool $autoPluginPath
     * @return bool
     */
    public function isTemplateCache($templateFile, $autoPluginPath = true)
    {
        // load default method for all other xt:C versions
        return parent::isTemplateCache($templateFile);
    }

    /**
     * Get the cached html- little (blind) changes by Jens Albert to use in plugin folder
     *
     * @param $templateFile
     * @return string
     */
    public function getCachedTemplate($templateFile)
    {
        // load default method for all other xt:C versions
        return parent::getCachedTemplate($templateFile);
    }
}
