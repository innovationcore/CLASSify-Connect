<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;

class CLASSifyConnect extends AbstractExternalModule {
    public function redcap_module_project_settings_page($project_id) {
        $this->includeJS('js/project_settings.js');
    }
}
