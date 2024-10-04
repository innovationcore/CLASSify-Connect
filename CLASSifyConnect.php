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

    function redcap_every_page_top($project_id) {
        print_r("oh yeah baby this script ran.");
        $users = REDCap::getUsers();
        //print_r(REDCap::getProjectTitle());
        $user_to_look_for = "ncpe227";
        if (in_array($user_to_look_for, $users)) {
            print_r("User $user_to_look_for has access to this project.");
        } else {
            print_r("User $user_to_look_for does NOT have access to this project.");
        }

        if (self::isExternalModulePage()) {
            $this->includeJS('js/project_settings.js');
            print_r("oh yeah baybee, that's a module page");

        }
    }


    /*function redcap_every_page_top($project_id) {
        print_r('oh yeah baby this script ran.');
    }*/

}
