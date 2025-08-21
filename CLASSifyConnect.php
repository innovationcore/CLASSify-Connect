<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

// Include these guzzle pieces to make more concise and orderly curls
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use REDCap;



class CLASSifyConnect extends AbstractExternalModule {
    public function proxyJsonPost($apiPath) {
        // Set up Monolog logger
        //$logFile = __DIR__ . '/logs/guzzle.log';
        //$logger = new Logger('guzzle');
        //$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

        // Guzzle logging
        $stack = HandlerStack::create();
        //$stack->push(Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG)));

        $guzzleClient = new Client(['handler' => $stack]);

        $apiUrl = rtrim($this->getSystemSetting('api_url') ?: 'https://classify.ai.uky.edu/', '/') . $apiPath;
        $apiKey = $this->getProjectSetting('api_key')[0];

        $rawInput = file_get_contents('php://input');

        // Parse URL-encoded POST body into array
        parse_str($rawInput, $postData);

        // REDCap CSRF token will be in $_POST
         $_POST = $postData;

        // Remove the CSRF token
        unset($postData['redcap_csrf_token']);
        try {
            $response = $guzzleClient->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $postData,
            ]);
            http_response_code($response->getStatusCode());
            header('Content-Type: application/json'); // ✅ required

            $body = (string) $response->getBody();
            $contentType = $response->getHeaderLine('Content-Type');

            // Try to decode JSON, if applicable
            $data = stripos($contentType, 'application/json') !== false
                ? json_decode($body, true)
                : $body;

            echo json_encode([
                'success' => true,
                'message' => 'Action proxied successfully',
                'data' => $data,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 500;
            $body = $response ? (string) $response->getBody() : $e->getMessage();

            http_response_code($statusCode);
            header('Content-Type: application/json'); // ✅ required
            echo json_encode([
                'success' => false,
                'message' => 'Request failed',
                'error' => $body,
            ]);
        }
    }

    public function proxyJsonGet($apiPath) {
        // Set up Monolog logger
        //$logFile = __DIR__ . '/logs/guzzle.log';
        //$logger = new Logger('guzzle');
        //$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

        // Guzzle logging
        $stack = HandlerStack::create();
        //$stack->push(Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG)));

        $guzzleClient = new Client(['handler' => $stack]);

        $apiUrl = rtrim($this->getSystemSetting('api_url') ?: 'https://classify.ai.uky.edu/', '/') . $apiPath;
        $apiKey = $this->getProjectSetting('api_key')[0];

        $rawInput = file_get_contents('php://input');

        // Parse URL-encoded POST body into array
        parse_str($rawInput, $postData);

        // REDCap CSRF token will be in $_POST
        $_POST = $postData;

        // Remove the CSRF token
        unset($postData['redcap_csrf_token']);
        try {
            $response = $guzzleClient->get($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
            http_response_code($response->getStatusCode());
            header('Content-Type: application/json'); // ✅ required

            $body = (string) $response->getBody();
            $contentType = $response->getHeaderLine('Content-Type');

            // Try to decode JSON, if applicable
            $data = stripos($contentType, 'application/json') !== false
                ? json_decode($body, true)
                : $body;

            echo json_encode($data);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 500;
            $body = $response ? (string) $response->getBody() : $e->getMessage();

            http_response_code($statusCode);
            header('Content-Type: application/json'); // ✅ required
            echo json_encode([
                'success' => false,
                'message' => 'Request failed',
                'error' => $body,
            ]);
        }
    }


    public function proxyFormPost($apiPath)
    {
        // Set up Monolog logger
        //$logFile = __DIR__ . '/logs/guzzle.log';  // __DIR__ is the current module folder
        //$logger = new Logger('guzzle');
        //$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));


        // Create Guzzle handler stack with logging
        $stack = HandlerStack::create();
        //$stack->push(Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG)));

        $guzzleClient = new Client(['handler' => $stack]);
        $apiUrl = rtrim($this->getSystemSetting('api_url') ?: 'https://classify.ai.uky.edu', '/') . $apiPath;

        // Check uploaded file
        if (empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
            return;
        }

        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $apiKey = $this->getProjectSetting('api_key')[0];

        try {
            $response = $guzzleClient->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($fileTmpPath, 'r'),
                        'filename' => $fileName,
                    ],
                ]
            ]);

            http_response_code($response->getStatusCode());
            echo $response->getBody();
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 500;
            $body = $response ? (string) $response->getBody() : $e->getMessage();

            http_response_code($statusCode);
            echo json_encode([
                'success' => false,
                'message' => 'Request failed',
                'error' => $body,
            ]);
        }
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
                    $records = filter_tags(REDCap::getData([
                        'project_id' => $project_id,
                        'return_format' => 'json',
                        'fields' => $instrument_fields
                    ]));

                    $data_by_instrument[$instrument_name] = json_decode($records, true);
                }
            }
            $form = $this->getProjectSetting('form-id');
            $classifier = $this->getProjectSetting('class-field');
            $email = $this->getProjectSetting('classify-email');
            $data = filter_tags(REDCap::getData($project_id, 'csv'));
            $project_title = filter_tags(REDCap::getProjectTitle());
            $filename = $this->getProjectSetting('filename');
            $GLOBALS['api_key'] = $this->getProjectSetting('api_key');
            $GLOBALS['proxy'] = $this->getURL('classify_proxy');

            ?>

            <!-- Sets some global variables that are needed in JS code across the module. -->
            <script>
                const instruments = <?=json_encode($instruments)?>;
                const moduleData = <?= json_encode($data) ?>;
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
