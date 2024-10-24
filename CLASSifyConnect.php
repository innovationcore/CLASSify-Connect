<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

class CLASSifyConnect extends AbstractExternalModule {

    // provided courtesy of Scott J. Pearson
    private static function isExternalModulePage() {
		$page = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : "";
		if (preg_match("/ExternalModules\/manager\/project.php/", $page)) {
			return TRUE;
		}
		if (preg_match("/ExternalModules\/manager\/ajax\//", $page)) {
			return TRUE;
		}
		if (preg_match("/external_modules\/manager\/project.php/", $page)) {
			return TRUE;
		}
		if (preg_match("/external_modules\/manager\/ajax\//", $page)) {
			return TRUE;
		}
		return FALSE;
	}

    protected function includeJS($file) {
        // Use this function to use your JavaScript files in the frontend
        echo '<script src="' . $this->getUrl($file) . '"></script>';
    }

    protected function variable() {
        echo "<script>variable={}</script>";
        $this->includeJS('js/project_settings.js');
    }

    protected function dataToJavascript() {

    }

    function redcap_every_page_top($project_id) {
        if (self::isExternalModulePage()) {
            $project_id = $_GET['pid']; // or however you're getting the project ID
            $form = $this->getProjectSetting('form-id');
            $classifier = $this->getProjectSetting('class-field');
            $email = $this->getProjectSetting('classify-email');
            $data = REDCap::getData($project_id, 'csv');
            $project_title = REDCap::getProjectTitle();
            $filename = $this->getProjectSetting('filename');

            ?>
            <script>
                const moduleData = <?= json_encode($data) ?>;
                const selectedForms = <?= json_encode($form) ?>;
                const classifier = <?= json_encode($classifier) ?>;
                const email = <?= json_encode($email) ?>;
                const user_filename = <?= json_encode($filename) ?>;
                const project_title = <?= json_encode($project_title) ?>;
                var filename = null;
                // If they provide an alternative filename, we can use that. Otherwise, we can just use the project title.
                if (user_filename.length > 0) {
                    filename = user_filename[0]
                }
                else {
                    filename = project_title
                }
            </script>

            <script src="<?= $this->getUrl('js/project_settings.js') ?>"></script>
            <?php
        }
    }
}
