<?php
file_put_contents('redcap_ajax_debug.log', print_r($_POST, true));

use ExternalModules\ExternalModules;
use ExternalModules\AbstractExternalModule;

$module = ExternalModules::getModuleInstance('CLASSify-Connect'); // replace with your module directory

header('Content-Type: application/json');

// FOR TESTING ONLY:
//echo json_encode(['success' => true, 'message' => 'Test successful! The proxy.php file is reachable.']);
//exit(); // Stop the script here

if (!defined('PAGE')) define('PAGE', 'ajax');

header('Content-Type: application/json');

/*if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);

    // Manually populate $_POST so REDCap’s CSRF checker works
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $_POST[$key] = $value;
        }
    }
}*/

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'reports_submit':
            $response = $module->proxyRootFormPost('/reports/submit');
            echo $response;
            break;

        case 'update_action':
            $response = $module->proxyRootJsonPost('/actions/update_action');
            echo $response;
            break;

        case 'verify_dataset':
            // File upload
            //$response = $module->proxyUpload('/verify_dataset');
            $response = $module->proxyRootJsonUpload('/verify_dataset');
            echo $response;
            break;

        case 'get_column_types':
            //$response = $module->proxyFileUpload('/get_column_types');
            $response = $module->proxyApiJsonUpload('/get_column_types');
            echo $response;
            break;

        case 'delete_dataset':
            $response = $module->proxyRootJsonPost('/delete_dataset');
            echo $response;
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid or missing action.']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
