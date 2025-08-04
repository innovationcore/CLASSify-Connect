<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

class CLASSifyConnect extends AbstractExternalModule {
    public function proxyApiJsonPost($apiPath)
    {
        $apiUrl = rtrim($this->getSystemSetting('api_url') ?: 'https://classify.ai.uky.edu/api', '/') . $apiPath;
        $body = file_get_contents('php://input');

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($status);
        echo $response;
    }

    public function proxyRootJsonPost($apiPath)
    {
        $apiUrl = rtrim($this->getSystemSetting('api_url') ?: 'https://classify.ai.uky.edu', '/') . $apiPath;
        $body = file_get_contents('php://input');

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($status);
        echo $response;
    }

    public function proxyRootFormPost($apiPath)
    {
        $apiUrl = rtrim($this->getSystemSetting('api_url') ?: 'https://classify.ai.uky.edu', '/') . $apiPath;

        // Check the uploaded file
        if (empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
            return;
        }

        // Read file data
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileContents = file_get_contents($fileTmpPath);

        // User UUID field (adapt as needed)
        $userUuid = 'example-user-id'; // You can dynamically get this as appropriate

        // Boundary
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        // Build multipart body
        $data = '';
        $eol = "\r\n";

        // File part
        $data .= "--" . $delimiter . $eol;
        $data .= 'Content-Disposition: form-data; name="file"; filename="' . $fileName . '"' . $eol;
        $data .= 'Content-Type: text/csv' . $eol . $eol;
        $data .= $fileContents . $eol;

        // User UUID part
        $data .= "--" . $delimiter . $eol;
        $data .= 'Content-Disposition: form-data; name="user_uuid"' . $eol . $eol;
        $data .= $userUuid . $eol;

        // End boundary
        $data .= "--" . $delimiter . "--" . $eol;

        // Stream context for POST
        $options = [
            'http' => [
                'header'  => "Content-Type: multipart/form-data; boundary=" . $delimiter . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($apiUrl, false, $context);

        // Get response status code
        $status = $http_response_header[0] ?? 'HTTP/1.1 500 Internal Server Error';
        preg_match('/\d{3}/', $status, $matches);
        $statusCode = $matches[0] ?? 500;

        http_response_code((int)$statusCode);
        echo $result;
    }



    public function proxyFileUpload($apiPath)
    {
        $apiUrl = rtrim($this->getSystemSetting('api_url'), '/') . $apiPath;

        $postFields = [
            'user_uuid' => $_POST['user_uuid'] ?? '',
        ];

        if (isset($_FILES['file'])) {
            $postFields['file'] = new \CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']);
        }

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_POST, true);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($status);
        echo $response;
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
        $GLOBALS["classifyURL"] = $this->getUrl('proxy.php', true);
        //$GLOBALS['classifyURL'] = 'https://redcap.ai.uky.edu/api/?type=module&prefix=CLASSify-Connect&content=externalModule&action=reports-submit';
        $GLOBALS["api_url"] = $this->getUrl('api_proxy.php', true);

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
                console.log(moduleByIns)
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
