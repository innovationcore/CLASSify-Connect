<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

$GLOBALS['classifyURL'] = 'https://data.ai.uky.edu/classify';
$GLOBALS['api_url'] = 'https://data.ai.uky.edu/classify/api';

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

    private static function isCLASSifyPage() {
    $page = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : "";
    if (preg_match("/ExternalModules\/\?prefix=CLASSify-Connect&page=pages%2FCLASSifyConnectPage/", $_SERVER['REQUEST_URI'])) {
        return TRUE;
    }
    return FALSE;
}


    function redcap_every_page_top($project_id) {
        if (self::isExternalModulePage() | self::isCLASSifyPage()) {
            $project_id = $_GET['pid']; // or however you're getting the project ID
            $instruments = REDCap::getInstrumentNames(); // Get instrument names

            // Initialize an empty array to store data by instrument
            $data_by_instrument = [];

            // Loop through each instrument and retrieve only its data
            foreach ($instruments as $instrument_name => $instrument_label) {
                // Get the list of fields for the instrument
                $data_dict = REDCap::getDataDictionary('array');
                $instrument_fields = [];

                foreach ($data_dict as $field_name => $field_info) {
                    if ($field_info['form_name'] === $instrument_name) {
                        $instrument_fields[] = $field_name;
                    }
                }

                // Retrieve data for only those fields
                if (!empty($instrument_fields)) {
                    $records = REDCap::getData([
                        'project_id' => $project_id,
                        'return_format' => 'json',
                        'fields' => $instrument_fields
                    ]);

                    $data_by_instrument[$instrument_name] = json_decode($records, true);
                }
            }
            $form = $this->getProjectSetting('form-id');
            $classifier = $this->getProjectSetting('class-field');
            $email = $this->getProjectSetting('classify-email');
            $data = REDCap::getData($project_id, 'csv');
            $project_title = REDCap::getProjectTitle();
            $filename = $this->getProjectSetting('filename');

            ?>

            <!-- Sets some global variables that are needed in JS code across the module. -->
            <script>
                const instruments = <?=json_encode($instruments)?>;
                console.log(instruments);
                const moduleData= <?= json_encode($data) ?>;
                const moduleCSV = <?= json_encode($data) ?>;
                const moduleByIns = <?= json_encode($data_by_instrument) ?>;
                console.log(moduleCSV);
                console.log(moduleByIns);
                const selectedForms = <?= json_encode($form) ?>;
                const classifier = <?= json_encode($classifier) ?>;
                const email = <?= json_encode($email) ?>;
                const project_title = <?= json_encode($project_title) ?>;
            </script>

            <!-- Includes the code needed to make the modal popups for CLASSify interaction work. -->
            <script src="<?= $this->getUrl('js/modals.js')?>"></script>

            <!-- adds the code used in the module configuration page -->
            <script src="<?= $this->getUrl('js/project_settings.js')?>"></script>

            <!-- Everything below imports the versions of bootstrap that were expected from the CLASSify UI which was ported in. -->
            <script src="<?= $this->getUrl('js/dataTables.bootstrap4.min.js')?>"></script>
            <script src="<?= $this->getUrl('js/dataTables.buttons.min.js')?>"></script>
            <script src="<?= $this->getUrl('js/jquery.dataTables.min.js')?>"></script>
            <script src="<?= $this->getUrl('js/toastify.min.js')?>"></script>
        <?php
        }
    }
}
