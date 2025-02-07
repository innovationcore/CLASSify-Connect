<?php
global $results;
$page = 'results-details';
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div class="btn-toolbar mb-2 mb-md-0 justify-content-right">
            <h1 class="h4">Data - <span class="text-muted">Results</span></h1>
            <select id="select-model" class="selectpicker ml-5" multiple data-actions-box="true" title="Model Select"></select>
            <button onclick="download_model();" id="download-model" type="button" class="btn btn-primary mr-1" title="Download .joblib files for trained models."><i class="fa fa-download"></i> Download Selected Model(s)</button>
            <button data-toggle="modal" data-target="#uploadModal" id="retest-model" type="button" class="btn btn-primary mr-1" title="Upload new test data. If it contains 'class' variable, performance metrics will be returned. If not, raw predictions based on the uploaded testset will be returned."><i class="fa fa-upload"></i> Re-Test Selected Model(s)</button>
            <button onclick="exportToCSV();" id="export-results" type="button" class="btn btn-primary mr-1" title="Download performance metrics as .csv."><i class="fa fa-file-export"></i> Export Results</button>
            <button onclick="download_synthetic();" id="download-synthetic" type="button" class="btn btn-primary mr-1" title="Download training dataset with any modifications, and any synthetic data generated."><i class="fa fa-download"></i> Download Data</button>
            <a href="/result/visualize" class="btn btn-primary mr-1" data-toggle="tooltip" data-placement="left" title="View explanatory graphs. Contains SHAP visualizations if 'beeswarm' is set to true before training." id="view-visualizations"><i class="fa fa-image"></i>
                 View Visualizations
            </a>
            <a href="/result/log" class="btn btn-primary mr-1" data-toggle="tooltip" data-placement="left" title="View output of training. May help troubleshoot any errors." id="view-log"><i class="fa fa-file-alt"></i>
                 View Output Log
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2 id="full_filename_text" class="h5"><?= $results->getShortenedReportFile() ?></h2>
            <h2 id="filename_text" class="h5" hidden><?= $results->getReportFile() ?></h2> <!--Stupid way to get the user_uuid for the downloads but it's all I got-->
            <?php
                $doesFileExist = file_exists($results->getReportFile());
                if ($doesFileExist){
                    $file_to_read = fopen($results->getReportFile(), 'r');
                    if($file_to_read !== false){
                        echo '<table id="reportcsv" class="table table-striped table-bordered dt-responsive responsive-text" style="width:100%">';
                        // write code to get the headers from a csv
                        $headings = fgetcsv($file_to_read, null, ',');
                        $headCount = count($headings);
                        echo "<thead><tr>";
                        for ($i = 0; $i < $headCount; $i++) {
                            echo "<th>".$headings[$i]."</th>";
                        }
                        echo "</tr></thead><tbody>";

                        while(($data = fgetcsv($file_to_read, null, ',')) !== false){
                            $maxDataRow = count($data);
                            echo "<tr>";
                            for($i = 0; $i < $headCount; $i++) {
                                if($i < $maxDataRow) {
                                    echo "<td>".$data[$i]."</td>";
                                } else {
                                    echo "<td></td>";
                                }
                            }
                            echo "</tr>\n";
                        }
                        echo "</tbody></table>\n";
                        fclose($file_to_read);
                    }
                } else {
                    $filename = $results->getReportFile();
                    $filename = substr($filename, 0, strrpos($filename, '_'));
                    $output_filename = $filename . '_output.txt';
                    if (file_exists($output_filename)) {
                        $fileContent = file_get_contents($output_filename);
                        echo '<p style="font-size: 18px;">' . nl2br($fileContent) . '</p>';
                    }
                    else {
                        echo '<h2>No results available.</h2>';
                    }
                }
            ?>

        </div>
    </div>

    <!-- upload file Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload New Test File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="uploadModalBody">
                        <div class="col-lg-12 mb-3 form-floating">
                            <div class="custom-file" id="customFile">
                                <input type="file" class="form-control custom-file-input" accept=".csv" id="uploadReportFile" aria-describedby="fileHelp">
                                <label class="form-control custom-file-label" for="uploadReportFile" id="uploadReportFileLabel">
                                    Select file...
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="retest_model">Re-Test</button>
                </div>
            </div>
        </div>
    </div>
    <div id="cover-spin"></div>


<script>
    $(document).ready(function() {
        $('#select-model').selectpicker();
        let currentFile = document.getElementById('filename_text').textContent;
        let parts = currentFile.split('/');
        let filename = parts[parts.length - 1];
        filename = filename.slice(0, -4);
        $('#reportcsv').DataTable({
            responsive: true,
            pageLength: 100,
            dom: 'Bfrtip',
            buttons: [
                'pageLength', 'colvis'
            ],
            order: [[3, 'desc']], //order by test_auc by default
            columnDefs: [
                {
                    'targets': '_all',
                    'render': function (data, type, full, meta) {
                        var maxLength = 10;
                        var expandLength = 100;
                        if (data) {
                            if (data.length > maxLength) {
                                var tempData = '';
                                while (data.length > maxLength) {
                                    tempData = tempData + data.substring(0, maxLength) + '<br/>';
                                    data = data.substring(maxLength);
                                }
                                tempData = tempData + data;
                                if (tempData.length > expandLength) {
                                    var shortened = tempData.substring(0, 20);
                                    var long = tempData.substring(20);
                                    var spanText = '<span class="long-text" hidden>';
                                    var endText = '</span><button class="btn btn-link btn-sm expand-button">Show More</button>';
                                    tempData = shortened + spanText + long + endText;
                                }
                                return tempData;
                            }
                            else {
                                return data;
                            }

                        }
                        else {
                            return '';
                        }

                    }
                },
                {
                    'targets': '_all',
                    'className': 'dt-center'
                }
            ],
        });
        $('#reportcsv').on('click', '.expand-button', function() {
            var cell = $(this).closest('td');
            var longText = cell.find('.long-text');
            var buttonText = $(this).text();

            if (buttonText === 'Show More') {
                // Show full text
                longText.removeAttr("hidden");
                $(this).text('Show Less');
            } else {
                // Collapse to shortened text
                longText.attr("hidden", "hidden");
                $(this).text('Show More');
            }
        });
        filename = getFilename();
        let currentURL = window.location.href;
        let uuidparts = currentURL.split('/');
        let uuid = uuidparts[uuidparts.length - 1];
        let visualizeButton = document.getElementById('view-visualizations');
        let url = '<?= $rootURL ?>/result/visualize/'.concat(uuid);
        visualizeButton.setAttribute("href", url);
        let logButton = document.getElementById('view-log');
        let logurl = '<?= $rootURL ?>/result/log/'.concat(uuid);
        logButton.setAttribute("href", logurl);
        filename = filename.slice(0, -7);
        $.ajax({
            url: '<?= $rootURL ?>/reports/get-model-files',
            type: 'GET',
            data: {'filename':filename},
            success: function(data) {
                data.model_files.forEach((file) => {
                    filename = file.slice(0, -7);
                    let parts = filename.split('_');
                    let model = "";
                    if (parts[parts.length - 1] == 'scaler') {
                        model = 'scaler';
                    }
                    else {
                        model = parts[parts.length - 2];
                    }
                    $("#select-model").append($('<option value="'+file+'">'+model+'</option>'));

                });
                //$("#select-model").append($('<button type="button" id="model-download-button" class="btn btn-primary mr-2">Download Models</button>'));
                //$('<button type="button" id="model-download-button" class="btn btn-primary mr-2">Download Models</button>').appendTo($('.bs-searchbox'));
                $("#select-model").selectpicker('refresh');
            },
            error: function(xhr, request, error) {
                showError('Error downloading files.');
            }
        });
    });

    $('#uploadReportFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $('#uploadReportFileLabel').html(fileName);
    });

    $('#uploadModal').on('hidden.bs.modal', function () {
        $('#uploadReportFile').val('');
        $('#uploadReportFileLabel').html('Select file...');
    });

    $('#retest_model').click(function(){
        let uploadFile = $('#uploadReportFile');
        if (uploadFile.val() === null || uploadFile.val() === '') {
            showError('Please upload a .csv file');
            return;
        }
        var file = uploadFile[0].files[0];
        if (file === undefined) {
            showError('Unknown file error encountered');
            return;
        }
        $.ajax({ //Get user uuid first
            url: '<?= $rootURL ?>/users/getUser',
            method: 'get',
            success: function(data) {
                user_uuid = data.user.id;
                var form_data = new FormData();
                form_data.append('file', file);

                let currentFile = document.getElementById('filename_text').textContent;
                let filename = currentFile.slice(0, -11); //Get just the filename
                filename = filename.substring(8); //Remove the results/ directory

                let retestName = filename + '_retest.csv'
                form_data.append('filename', retestName);
                let training_file = filename + '.csv'
                //form_data.append('filename', currentFile);
                $.ajax({
                    url: '<?= $rootURL ?>/reports/get-column_changes',
                    type: 'get',
                    data: {
                        'filename': training_file
                    },
                    success: function(res) {
                        let data_types = res.column_changes;
                        form_data.append('data_types', data_types);
                        $.ajax({
                            url: '<?= $api_url ?>/upload_testset',
                            type: 'POST',
                            data: form_data,
                            contentType: false,
                            processData: false,
                            success: function(res) {
                                if (res.success) {
                                    showSuccess(res.message);
                                    testset_filename = res.name;
                                    var selectedValues = $('#select-model').val();
                                    let filenames = selectedValues.filter(element => !element.includes('scaler.joblib'));
                                    let model_names_array = filenames.map(filepath => { //Extract model names from file names
                                        const lastUnderscore = filepath.lastIndexOf('_');
                                        const secondLastUnderscore = filepath.lastIndexOf('_', lastUnderscore - 1);
                                        return filepath.substring(secondLastUnderscore + 1, filepath.length - 13);
                                    });
                                    let model_names = JSON.stringify({models: model_names_array})
                                    $('#uploadModal').modal('hide');
                                    if (filenames.length == 0) {
                                        showError('No selected models to re-test.');
                                    }
                                    else {
                                        let report_uuid = window.location.pathname.split('/').pop();
                                        $.ajax({
                                            url: '<?= $rootURL ?>/actions/update_action',
                                            method: 'POST',
                                            data: {
                                                'report_uuid': report_uuid,
                                                'user_uuid': user_uuid,
                                                'action': 'Re-testing with new dataset',
                                                'addl_info': model_names
                                            },
                                            success: function(res) {
                                                if (res.success) {
                                                    $.ajax({ //Do retesting
                                                        url: '<?= $api_url ?>/retest_model',
                                                        type: 'POST',
                                                        data: JSON.stringify({
                                                            'filenames': filenames,
                                                            'testset': testset_filename
                                                        }),
                                                        contentType: 'application/json; charset=utf-8',
                                                        success: function(data) {
                                                            if (data.success){
                                                                results_file = data.results_file;
                                                                var parts = results_file.split('/');
                                                                results_file = parts[parts.length-1]
                                                                results_file = results_file.slice(0, -4);
                                                                $.ajax({ //Export file
                                                                    url: '<?= $rootURL ?>/reports/export-results',
                                                                    method: 'post',
                                                                    dataType: 'text',
                                                                    data: {'filename':results_file},
                                                                    success: function(data) { //Download file to the user
                                                                        if (!data.includes("File not found!")) {
                                                                            var link = document.createElement("a");
                                                                            link.href = "data:text/csv;charset=utf-8," + encodeURIComponent(data);
                                                                            let segments = results_file.split('_'); //Remove the user_uuid from the filename
                                                                            segments.splice(-3, 1);
                                                                            let new_filename = segments.join('_');
                                                                            link.download = new_filename.concat(".csv");
                                                                            document.body.appendChild(link);
                                                                            link.click();
                                                                            document.body.removeChild(link);
                                                                            $.ajax({ //Delete generated files
                                                                                url: '<?= $rootURL ?>/reports/delete-retest',
                                                                                type: 'POST',
                                                                                data: {'filename':results_file},
                                                                                success: function(data) {
                                                                                    //console.log(data);
                                                                                },
                                                                                error: function(xhr, request, error) {
                                                                                    showError('Error deleting files.');
                                                                                }
                                                                            });
                                                                        }
                                                                    },
                                                                    error: function(xhr, request, error) {
                                                                        showError('Error downloading results file.');
                                                                        $.ajax({ //Delete generated files
                                                                            url: '<?= $rootURL ?>/reports/delete-retest',
                                                                            type: 'POST',
                                                                            data: {'filename':results_file},
                                                                            success: function(data) {
                                                                                console.log(data);
                                                                            },
                                                                            error: function(xhr, request, error) {
                                                                                showError('Error deleting files.');
                                                                            }
                                                                        });
                                                                    }
                                                                });

                                                            } else {
                                                                showError(data.message);
                                                            }

                                                        },
                                                        error: function (xhr, status, error) {
                                                            showError("Error communicating with the server.");
                                                            return null;
                                                        }
                                                    });
                                                } else {
                                                    showError(res.message);
                                                }
                                            },
                                            error: function(xhr, ajaxOptions, thrownError) {
                                                showError('Error communicating with the server');
                                            }
                                        });
                                    }
                                } else {
                                    showError(res.message);
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                showError('Error communicating with the server');
                            }
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        showError('Error communicating with the server');
                    }
                });
            },
            error: function(xhr, request, error) {
                showError('Error getting user data.');
            }
        });
    });

    function download_synthetic() {
        let currentFile = document.getElementById('filename_text').textContent;
        let filename = currentFile.slice(0, -11); //Get just the filename
        filename = filename.substring(8); //Remove the results/ directory
        let original_dataset = filename.concat('_dataset.csv')
        let testset = filename.concat('_testset.csv')
        let synthetic_filled = filename.concat('_synthetic_filled.csv');
        let synthetic_balanced = filename.concat('_synthetic_balanced.csv');
        let synthetic_new = filename.concat('_synthetic_new.csv');
        let synthetic_testset = filename.concat('_synthetic_testset.csv');
        let metrics = filename.concat('_synthetic_metrics.json');
        let metadata = filename.concat('_metadata.json');
        let files = [original_dataset, testset, synthetic_filled, synthetic_balanced, synthetic_new, synthetic_testset, metrics, metadata];
        var files_counter = 0;
        files.forEach((file) => { //Loop through each possible file and try to download
            $.ajax({
                url: '<?= $api_url ?>/download-dataset',
                method: 'get',
                data: {'filename':file},
                xhrFields: {responseType: 'blob'},
                success: function(data, status, xhr) {
                    var blob = new Blob([data]);
                    var contentDisposition = xhr.getResponseHeader('Content-Disposition');
                    var actualFilename = file;

                    if (contentDisposition && contentDisposition.indexOf('filename=') !== -1) {
                        var match = contentDisposition.match(/filename="?([^"]+)"?/);
                        if (match && match.length > 1) {
                            actualFilename = match[1];
                        }
                    }

                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = actualFilename;
                    link.click();
                },
                error: function(xhr, request, error) {
                    files_counter = files_counter + 1;
                    if (files_counter == files.length) {
                        showError('No files to download!');
                    }
                }
            });
        });
    }

    function download_model() {
        var selectedValues = $('#select-model').val();

        $.each(selectedValues, function(index, value) {
            $.ajax({
                url: '<?= $rootURL ?>/reports/export-model',
                method: 'post',
                dataType: 'text',
                data: {'filename':value},
                success: function(data) {
                    if (!data.includes("File not found!")) { //If the file exists, download it

                        let slashSegments = value.split('/'); //Remove the directory from the filename
                        let filename = slashSegments[slashSegments.length - 1]
                        let segments = filename.split('_');
                        if (segments[segments.length - 1] == 'scaler.joblib') {
                            segments.splice(-2, 1);
                            filename = segments.join('_');
                        }
                        else {
                            segments.splice(-3, 1);
                            filename = segments.join('_');
                        }

                        let binaryData = atob(data);
                        let arrayBuffer = new ArrayBuffer(binaryData.length);
                        let uint8Array = new Uint8Array(arrayBuffer);
                        for (var i = 0; i < binaryData.length; i++) {
                            uint8Array[i] = binaryData.charCodeAt(i);
                        }
                        let blob = new Blob([uint8Array], { type: "application/joblib" });
                        let link = document.createElement("a");
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;

                        link.click();
                    }
                },
                error: function(xhr, request, error) {
                    showError('Error downloading models.');
                }
            });
        });
    }

    function getFilename() {
        let currentFile = document.getElementById('filename_text').textContent;
        let parts = currentFile.split('/');
        let filename = parts[parts.length - 1];
        filename = filename.slice(0, -4);
        return filename;
    }

    function exportToCSV() {
        filename = getFilename();
        $.ajax({
            url: '<?= $rootURL ?>/reports/export-results',
            method: 'post',
            dataType: 'text',
            data: {'filename':filename},
            success: function(data) { //Download file to the user
                if (!data.includes("File not found!")) {
                    var link = document.createElement("a");
                    link.href = "data:text/csv;charset=utf-8," + encodeURIComponent(data);
                    let segments = filename.split('_'); //Remove the user_uuid from the filename
                    segments.splice(segments.length - 2, 1);
                    let new_filename = segments.join('_');
                    link.download = new_filename.concat(".csv");
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            },
            error: function(xhr, request, error) {
                showError('Error downloading results file.');
            }
        });
    }
</script>
