<?php

namespace CAAIModules\CLASSifyConnect;

use ExternalModules\ExternalModules;
use ExternalModules\AbstractExternalModule;

$module = ExternalModules::getModuleInstance('CLASSify-Connect'); // replace with your module directory

header('Content-Type: application/json');

if (!defined('PAGE')) define('PAGE', 'ajax');

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'reports_submit':
            $response = $module->proxyFormPost('/reports/submit');
            echo $response;
            break;

        case 'reports_delete':
            $response = $module->proxyJsonPost('/reports/delete');
            echo $response;
            break;

        case 'update_action':
            $response = $module->proxyJsonPost('/actions/update_action');
            echo $response;
            break;

        case 'verify_dataset':
            $response = $module->proxyJsonPost('/verify_dataset');
            echo $response;
            break;

        case 'get_column_types':
            $response = $module->proxyJsonPost('/get_column_types');
            echo $response;
            break;

        case 'change_column_types':
            $response = $module->proxyJsonPost('/api/change_column_types');
            echo $response;
            break;

        case 'set_column_changes':
            $response = $module->proxyJsonPost('/reports/set-column_changes');
            echo $response;
            break;

        case 'delete_dataset':
            $response = $module->proxyJsonPost('/delete_dataset');
            echo $response;
            break;

        case 'reports_list':
            $response = $module->proxyJsonGet('/reports/list');
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
