<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;

class CLASSifyConnect extends AbstractExternalModule {

    protected function includeJS($file) {
        // Use this function to use your JavaScript files in the frontend
        echo '<script src="' . $this->getUrl($file) . '"></script>';
    }

    protected function variable() {
        echo "<script>variable={}</script>";
        $this->includeJS('js/project_settings.js');
    }

    function redcap_every_page_top() {
        $this->includeJS('js/project_settings.js');
    }

}
