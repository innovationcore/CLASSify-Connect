<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;

class CLASSifyConnect extends AbstractExternalModule {

    // This is generally where your module's hooks will live
    function redcap_every_page_top($project_id) {
        print_r('Hello world! I am a message produced by a hook!');
    }

    
}
