<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

$GLOBALS['classifyURL'] = 'https://classify.ai.uky.edu';
$GLOBALS['api_url'] = 'https://classify.ai.uky.edu/api';

class CLASSifyConnect extends AbstractExternalModule {

    public function handleApiProxyRequest()
    {
        // --------------------------------------------------------------------
        // IMPORTANT SECURITY NOTE:
        // Before making the external API call, always validate and sanitize
        // any input received from the frontend (e.g., query parameters, POST data).
        // Never blindly pass user-supplied data to the external API.
        // --------------------------------------------------------------------

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Set content type header for the response
        header('Content-Type: application/json');

        // --- Configuration for the External API ---
        $externalUrl = $_SERVER['HTTP_X_PROXY_TARGET_URL']; // Replace with your actual external API endpoint
        $apiKey = $this->getProjectSetting('api_key'); // If the external API requires an API key

        // --- Initialize cURL session ---
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $externalUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_HEADER, false);      // Don't include the response header
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);        // Timeout after 60 seconds

        // Forward all incoming headers from the client to the external API
        /*$incomingHeaders = getallheaders();
        $forwardHeaders = [];
        foreach ($incomingHeaders as $name => $value) {
            // Exclude headers that cURL manages automatically or that are specific to the proxy call
            // We want to forward 'Content-Type', 'Authorization', etc.
            if (!in_array(strtolower($name), ['host', 'content-length', 'expect', 'x-proxy-target-url'])) {
                $forwardHeaders[] = "$name: $value";
            }
        }*/

        // If the external API requires an API key in headers
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey, // Example for Bearer Token
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // If the frontend makes a POST request to this proxy,
        // you might want to forward the POST data to the external API.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = file_get_contents('php://input'); // Get raw POST data
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        // --- Execute cURL request ---
        $response = curl_exec($ch);

        // --- Error Handling ---
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            $this->log('CURL Error: ' . $error_msg, [
                'api_url' => $externalUrl,
                'request_method' => $_SERVER['REQUEST_METHOD']
            ]);
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to connect to external API: ' . $error_msg]);
        } else {
            // Get HTTP status code from the API response
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Forward the original HTTP status code from the external API
            http_response_code($httpCode);

            // Directly echo the response from the external API to the frontend
            echo $response;
        }

        // --- Close cURL session ---
        curl_close($ch);

        // Important: Exit after handling the request to prevent REDCap from
        // trying to render a full page or further processing.
        exit();
    }

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
            $GLOBALS['api_key'] = $this->getProjectSetting('api_key');
            $GLOBALS['proxy'] = $this->getURL('classify_proxy');

            ?>

            <!-- Sets some global variables that are needed in JS code across the module. -->
            <script>
                const instruments = <?=json_encode($instruments)?>;
                const moduleData= <?= json_encode($data) ?>;
                const moduleCSV = <?= json_encode($data) ?>;
                const moduleByIns = <?= json_encode($data_by_instrument) ?>;
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
