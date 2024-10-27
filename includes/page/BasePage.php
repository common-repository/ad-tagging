<?php

namespace SYBAT\Page;

abstract class BasePage {

    private $pluginDir;
    private $pluginUrl;

    public function __construct($pluginDir) {
        $this->pluginDir = $pluginDir;
        $this->pluginUrl = plugin_dir_url($this->pluginDir . "/dummy");
    }

    protected function getPluginUrl($path) {
        return $this->pluginUrl . "/" . $path;
    }

    protected function getTranslationDirectory() {
        return $this->pluginDir . '/languages/';
    }

    abstract public function show();
}
