<?php
$page = 'home';
global $classifyURL;
global $api_url;

$selectedForms = isset($_POST['selectedForms']) ? $_POST['selectedForms'] : [];
$instruments = REDCap::getInstrumentNames();

$metadata = \REDCap::getDataDictionary('array');

$fieldsByInstrument = [];
foreach ($metadata as $field => $attributes) {
    $instrument = $attributes['form_name'];
    $fieldsByInstrument[$instrument][] = $field;
}
?>
    <style>
        .selection-btns {
            margin: 0 10% 0 10%;
        }

        .selection-btns a {
            text-decoration: none;
        }

        .center-home-sects {
            text-align:center;
            border-radius: 5%;
            padding: 5% 0 5% 0;
            margin:0;
            color: #606060;
            -webkit-transition: background-color 100ms linear;
            -ms-transition: background-color 100ms linear;
            transition: background-color 100ms linear;
        }

        .center-home-sects:hover {
            color: #fff;
            background-color: #606060;
        }

        .center-home-sects:hover span{
            color: #fff;
        }

        .center-home-sects span {
            font-size: 10vw;
            color: #606060;
            -webkit-transition: background-color 100ms linear;
            -ms-transition: background-color 100ms linear;
            transition: background-color 100ms linear;
        }
    </style>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h4"><b>CLASSify</b></h1>
    </div>

    <div class="row selection-btns">
        <div class="col-md-6">
            <a id="form-select-btn" data-toggle="modal" data-target="#formsModal">
                <div class="center-home-sects">
                    <span><i class="fas fa-plus"></i></span><br>
                    <h5>Upload Instrument Data</h5>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a id="view-all-btn" href="<?= $classifyURL?>/result" class="center-home-sects">
                <div class="center-home-sects">
                    <span><i class="fas fa-paper-plane"></i></span><br>
                    <h5>Go to CLASSify</h5>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col">

            <h2 id="preview-text" style="display:none;">Preview</h2>
            <table id="collection" class="table table-bordered dt-responsive responsive-text" style="width:100%">
                <thead>
                <tr></tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Form Selection Modal -->
    <div class="modal fade" id="formsModal" tabindex="-1" role="dialog" aria-labelledby="formsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formsModalLabel">Form Selection&nbsp;</h5>
                    <div class="spinner-border" role="status" id="spinner" style="display:none;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="formsModalBody">
                    <h5>Choose which forms/instruments to include in uploaded dataset.</h5>
                    <h6>Selecting a form here allows you to pick which columns you'd like to include in the next step.</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="forms">
                                <div id="form_names" class="columns-div">
                                    <?php
                                        foreach ($instruments as $key => $value) {
                                            // Check if this instrument was previously selected
                                            $checked = in_array($key, $selectedForms) ? "checked" : "";
                                            echo "<h6><input name='selectedForms[]' class='instrument-selection' id='" . $key . "' type='checkbox' value='" . $key . "' $checked> " . $value . "</h6>";
                                        }
                                        echo "<label for='filename'><h6 style='padding-right: 2px;'>Filename for upload: </h6></label>";
                                        echo "<input type='text' id='filename' value='redcap_upload.csv'></input>";
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a id="add-data-btn" data-toggle="modal" data-target="#classifierModal">
                        <div class="center-home-sects">
                            <button type="button" class="btn btn-primary" id="go-to-classifier">Next</button>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- choose classifier modal -->
    <div class="modal fade" id="classifierModal" tabindex="-1" role="dialog" aria-labelledby="classifierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="classifierModalLabel">Preview&nbsp;</h5>
                    <div class="spinner-border" role="status" id="spinner" style="display:none;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="classifierModalBody">
                    <label for="class-selector">Select a column for your classifier: </label>
                    <select id="class-selector" name="class-selector"> -->
                        <!-- This will be filled with option tags for each column header -->
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a id="add-data-btn" data-toggle="modal" data-target="#columnsModal">
                        <div class="center-home-sects">
                            <button type="button" class="btn btn-primary" id="go-to-columns">Next</button>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

<!-- choose features modal -->
<div class="modal fade" id="columnsModal" tabindex="-1" role="dialog" aria-labelledby="columnsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnsModalLabel">Preview&nbsp;</h5>
                <div class="spinner-border" role="status" id="spinner" style="display:none;">
                    <span class="sr-only">Loading...</span>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="columnsModalBody">
                <h5>Choose which columns to include in uploaded dataset.</h5>
                <h6>You may also change data types of each column here.</h6>
                <h6>Categorical variables will be one-hot encoded.</h6>
                <div class="row">
                    <div class="col-md-12">
                        <h6><b>Choose which column represents your class labels:</b></h6>
                        <select id="select-class" class="selectpicker ms-2 me-1 mb-1" title="Choose classifier column" data-width="100%">
                            <option value="no-class-column-selected" style="color:gray; font-style: italic;">No Class Column</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form id="columns">
                            <div id="column_names" class="columns-div">

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit-to-automl">Upload Dataset</button>
                <a href="<?= $classifyURL ?>/result" id="gotoMLOpts" type="button" class="btn btn-primary me-2" style="display:none;">
                    <i class="fa fa-eye"></i> View Uploaded Data
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="notificationModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="errorModalLabel">Error!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="errorModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var collection = {};
        var collectionTable = $('#collection');
        var collectionDataTable = null;
        var user_uuid = null;
        var currentFile = null;
        var report_uuid = null;
        var uploaded_to_clearml=false;
        var parsed = null;

        $(function() {

        }); //document ready

        $('#formsModal').on('shown.bs.modal', function () {
            $.ajax({
                url: `<?= $classifyURL ?>/users/getUserFromEmail?email=${email}`,
                method: 'get',
                success: function(data) {
                    if (!data.accepted_terms) { //If user has not accepted terms yet, show modal
                        // this needs work, probably should create a modal for it
                        alert("You have not accepted the CLASSify terms of service. Please navigate to https://data.ai.uky.edu/classify/ and log in for the first time to accept terms.");
                        $('#formsModal').hide();
                    } else {
                        user_uuid = data.user_id;
                    }
                },
                error : function(request,error) {
                    console.error("Request: "+JSON.stringify(request));
                }
            });
        });

        function show_tooltip() {
            var panel = document.getElementById("instruction-panel");
            panel.style.display = "block";
        }

        function hide_tooltip() {
            var panel = document.getElementById("instruction-panel");
            panel.style.display = "";
        }

        $('#uploadReportFile').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $('#uploadReportFileLabel').html(fileName);
        });

        $('#uploadModal').on('hidden.bs.modal', function () {
            $('#uploadReportFile').val('');
            $('#uploadReportFileLabel').html('Select file...');
        });

        function parseCSVWithNewNames(csvString, classifierField) {
            if (!csvString || !classifierField) {
                console.error('Invalid input. Please provide both CSV content and a classifier.');
                return;
            }

            const lines = csvString.split('\n');
            if (lines.length < 2) {
                console.error('Invalid CSV format. At least one header row and one data row are required.');
                return;
            }

            // Replace header
            const headers = lines[0].split(',').map(h => {
                const cleanHeader = h.trim();
                return cleanHeader === classifierField ? "class" : cleanHeader;
            });

            // Check if the classifierField was found in headers
            const classIndex = headers.indexOf("class");
            if (classIndex === -1) {
                console.warn(`The classifier field "${classifierField}" was not found in the CSV headers.`);
            }

            // Replace ? with 0 and filter rows with a blank in the "class" column
            const updatedRows = lines.slice(1)
                .map(row => row.split(',').map(value => value.trim() === '?' ? '' : value.trim()))
                .filter(row => row[classIndex] !== '');

            return [headers.join(','), ...updatedRows.map(row => row.join(','))].join('\n');
        }

        $('#columnsModal').on('hidden.bs.modal', function () {
            document.getElementById('column_names').innerHTML = '';
            if (!uploaded_to_clearml) {
                let filename_no_uuid = currentFile.substring(0, currentFile.lastIndexOf('_')) + currentFile.substring(currentFile.lastIndexOf('.'));                console.log('Current File at Columns Modal hidden.bs.modal: ' + currentFile);
                console.log('Filename no UUID: ' + filename_no_uuid);
                $.ajax({ //Delete report to prevent clearml dataset error
                    url: '<?= $classifyURL ?>/reports/delete',
                    type: 'POST',
                    data: {
                        'uuid': user_uuid,
                        'filename': filename_no_uuid
                    },
                    success: function(data) {
                        $.ajax({
                            url: `<?= $classifyURL ?>/users/getUserFromEmail?email=${email}`,
                            method: 'get',
                            success: function(data) {
                                user_uuid = data.user_id;
                                $.ajax({
                                    url: '<?= $api_url ?>/delete_dataset',
                                    type: 'POST',
                                    data: JSON.stringify({
                                        'filename': filename_no_uuid,
                                        'uuid': user_uuid
                                    }),
                                    contentType: 'application/json; charset=utf-8',
                                    success: function(data) {
                                        if (data.success){
                                            $.ajax({
                                                url: '<?= $classifyURL ?>/actions/update_action',
                                                method: 'POST',
                                                data: {
                                                    'report_uuid': report_uuid,
                                                    'user_uuid': user_uuid,
                                                    'action': 'Deleted report'
                                                },
                                                success: function(res) {
                                                    if (res.success) {
                                                    } else {
                                                        console.log(res.message);
                                                    }
                                                },
                                                error: function(xhr, ajaxOptions, thrownError) {
                                                    console.log('Error communicating with the server');
                                                }
                                            });
                                        } else {
                                            console.log(data.message);
                                        }

                                    },
                                    error: function (xhr, status, error) {
                                        console.log("Error communicating with the server.");
                                        return null;
                                    }
                                });
                            },
                            error: function(xhr, request, error) {

                                console.log('User not found.');
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                    }
                });
            }
            else { //Already uploaded to clearml
                $('#gotoMLOpts').hide();
                $('#submit-to-automl').show();
                uploaded_to_clearml=false;
            }
        });

        // Since we're using a different upload method, we change this to show columns modal
        //$('#submitUploadBtn').click(function(){
        $('#columnsModal').on('shown.bs.modal', function () {
            currentFile = document.getElementById('filename').value;

            // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
            currentFile = currentFile.endsWith('.csv') ? currentFile : currentFile + '.csv';

            console.log('Current File at submitUploadBtn: ' + currentFile)
            console.log('User UUID at submitUploadBtn: ' + user_uuid)

            /*            let uploadFile = $('#uploadReportFile');
                        if (uploadFile.val() === null || uploadFile.val() === '') {
                            showError('Please upload a .csv file');
                            return;
                        }
                        var file = uploadFile[0].files[0];
                        if (file.name.includes(' ')) { //If space in file name, remove it
                            file = new File(
                                [file], // File content remains the same
                                file.name.replace(/\s+/g, '_'), // Replace spaces with underscores
                                { type: file.type } // Preserve the file type
                            );
                        }
                        if (!file.name.endsWith('.csv')) {
                            showError('File must be a .csv');
                            return;
                        }

                        if (file.name.length > 104) { //100 characters + .csv
                            showError('File name exceeds maximum length. Reduce name to less than 100 characters');
                            return;
                        }

                        if (file === undefined) {
                            showError('Unknown file error encountered');
                            return;
                        }
                        let max_filesize = 500*1024*1024; //500 MB
                        if (file.size > max_filesize) {
                            showError('File is too large. Max filesize is 500 MB');
                            return;
                        }*/

            toggleLoadingScreenOverlay();

            var form_data = new FormData();

            // Create a Blob from the parsed CSV string
            const csvBlob = new Blob([parsed], { type: 'text/csv' });
            console.log(parsed);
            console.log(csvBlob);
            // Append the Blob and other fields to the form data
            form_data.append('file', csvBlob, currentFile);
            form_data.append('user_uuid', user_uuid);
            // get filename from uploaded file
            //currentFile = file.name;
            currentFile = currentFile.replace('.csv', '_'+user_uuid+'.csv');
            console.log(currentFile);

            /*// Create a temporary link element
            const link = document.createElement('a');
            link.href = URL.createObjectURL(csvBlob);
            link.download = filename;

            // Append to the document and trigger the download
            document.body.appendChild(link);
            link.click();

            // Clean up
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);*/
            $.ajax({
                url: '<?= $api_url ?>/verify_dataset',
                type: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.success) {
                        $.ajax({
                            url: '<?= $classifyURL ?>/reports/submit',
                            method: 'post',
                            dataType: 'json',
                            data: form_data,
                            processData: false,
                            contentType: false,
                            success: function (res) {
                                if (res.success) {
                                    report_uuid = res.report_uuid;
                                    filename = res.file_name;
                                    console.log(user_uuid + " at update action");
                                    $.ajax({
                                        url: '<?= $classifyURL ?>/actions/update_action',
                                        method: 'POST',
                                        data: {
                                            'report_uuid': report_uuid,
                                            'user_uuid': user_uuid,
                                            'action': 'REDCap Data Upload',
                                            'api_key': '7217be72-156e-4bda-9798-d7d6c8fc59da'
                                        },
                                        success: function (res) {
                                            if (res.success) {
                                                $.ajax({
                                                    url: '<?= $api_url ?>/get_column_types',
                                                    type: 'POST',
                                                    data: form_data,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function (data) {
                                                        toggleLoadingScreenOverlay();
                                                        showSuccess('Dataset uploaded');

                                                        // Get either the default name, or the user set name
                                                        currentFile = document.getElementById('filename').value;

                                                        // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
                                                        currentFile = currentFile.endsWith('.csv') ? currentFile : currentFile + '.csv';

                                                        console.log("Current file before columnsModal.show: " + currentFile);

                                                        //$('#uploadModal').modal('hide');
                                                        $('#columnsModal').modal('show');
                                                        showColumns(data.data_types, data.missing_values);
                                                    },
                                                    error: function (xhr, status, error) {
                                                        showError("Error communicating with the server.");
                                                        toggleLoadingScreenOverlay();
                                                        return null;
                                                    }
                                                });
                                            } else {
                                                showError(res.message);
                                                toggleLoadingScreenOverlay();
                                            }
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            toggleLoadingScreenOverlay();
                                            showError('Error communicating with the server');
                                        }
                                    });

                                } else {
                                    showError(res.message);
                                    toggleLoadingScreenOverlay();
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                toggleLoadingScreenOverlay();
                                showError('Error communicating with the server');
                            }
                        });
                    }
                }
            });
        }); // upload


        function showColumns(data_types, missing_values) {
            console.log(data_types);
            let toAppendBool = "";
            let toggle = 0;
            let column_names = []
            Object.keys(data_types).forEach((column) => {
                column_name = column.replace(/^\w/, c => c.toUpperCase());
                column_names.push(column)
                toAppendBool += `<div class="form-check" style="border-bottom: 0.1rem solid;`
                if (toggle == 1) {
                    toAppendBool += ' background-color: #DDDDDD;'
                }
                toAppendBool += `">
                                    <input id="${column}" type="checkbox" class="form-check-input" checked>
                                    <label for="${column}" class="bold-label">${column_name}</label>
                                    <div class="row">
                                        <div class="col-md-1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-check-label mb-3" title="integer">
                                                <input id="${column}-integer" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column]=='integer') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Integer
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-check-label mb-3" title="float">
                                                <input id="${column}-float" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column]=='float') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Float
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-check-label mb-3" title="bool">
                                                <input id="${column}-bool" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column]=='bool') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Bool
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-check-label mb-3" title="categorical">
                                                <input id="${column}-categorical" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column]=='string') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Categorical
                                            </label>
                                        </div>
                                    </div>`
                if (missing_values[column]) {
                    toAppendBool += `<div class="row"><div class="col-md-5">
                                    <p>Contains missing values.<\p></div><div class="col-md-4">
                                    <select id="${column}-missing-values" name="${column}" class="selectpicker" style="display:block !important;">
                                        <option value="drop">Drop Missing Rows</option>
                                        <option value="constant">Constant Fill Value</option>
                                        <option value="synthetic">Synthetically Fill</option>
                                    </select></div>
                                    <div class="col-md-1"><input type="text" id="${column}-fill-value" name="${column}" value="0" size="6" hidden></div></div>`;
                }
                toAppendBool += `</div>`;
                if (toggle == 0) {
                    toggle = 1;
                }
                else {
                    toggle = 0;
                }
            });
            $('#columnsModal #column_names').append(toAppendBool);
            var modal = document.getElementById('columnsModal');
            var modal_checkboxes = modal.querySelectorAll("input[type='checkbox']")
            var missing_dropdowns = modal.querySelectorAll("select");
            missing_dropdowns = Array.from(missing_dropdowns).filter(function(selectElement) {
                return selectElement.id !== 'select-class';
            });
            let classOptionExists = false;
            column_names.forEach(function(name) {
                var option = $('<option></option>').val(name).text(name.replace(/^\w/, c => c.toUpperCase()));
                $('#select-class').append(option);

                if (name.toLowerCase() === 'class') {
                    option.prop('selected', true);
                    classOptionExists = true;
                }
            });
            if (!classOptionExists) {
                $('#select-class').prop('selectedIndex', -1);
            }
            $("#select-class").selectpicker('refresh');

            modal_checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('click', function() {
                    if (!this.checked) {
                        var radios = modal.querySelectorAll("input[type='radio'][name='" + this.id + "']");
                        radios.forEach(function(radio) {
                            radio.disabled = true;
                        });
                        var selectpickers = modal.querySelectorAll("select[name='" + this.id + "']");
                        selectpickers.forEach(function(selectpicker) {
                            $(selectpicker).prop('disabled', true);
                        });
                        var texts = modal.querySelectorAll("input[type='text'][name='" + this.id + "']");
                        texts.forEach(function(text) {
                            text.disabled = true;
                        });
                    } else {
                        var radios = modal.querySelectorAll("input[type='radio'][name='" + this.id + "']");
                        radios.forEach(function(radio) {
                            radio.disabled = false;
                        });
                        var selectpickers = modal.querySelectorAll("select[name='" + this.id + "']");
                        selectpickers.forEach(function(selectpicker) {
                            $(selectpicker).prop('disabled', false);
                        });
                        var texts = modal.querySelectorAll("input[type='text'][name='" + this.id + "']");
                        texts.forEach(function(text) {
                            text.disabled = false;
                        });
                    }
                });
            });

            missing_dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('change', function(event) {
                    let select_id = event.target.id.slice(0, -15);
                    let text_id = select_id + '-fill-value'
                    if (event.target.value === 'constant') {
                        if (document.getElementById(select_id+'-categorical').checked) { //Change default value for categorical variables
                            document.getElementById(text_id).value = 'Unknown';
                        } else {
                            document.getElementById(text_id).value = '0';
                        }
                        document.getElementById(text_id).removeAttribute('hidden');
                    } else {
                        document.getElementById(text_id).setAttribute('hidden', true);
                    }
                });
            });

            $("#select-class").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                $('#select-class').closest('.bootstrap-select').removeClass('highlighted');
            });
        }

        $('#gotoMLOpts').click(function() {
            window.location
        });

        function delete_report(uuid){
            let confirmedDeletion = confirm("Are you sure you want to delete this report? This action is irreversible.");
            if (confirmedDeletion) {
                $.ajax({
                    url: '<?= $classifyURL ?>/reports/delete',
                    type: 'POST',
                    data: {
                        'uuid': uuid
                    },
                    success: function(data) {
                        if (data.success) {
                            user_uuid = null;
                            console.log(email);
                            $.ajax({
                                url: `<?= $classifyURL ?>/users/getUserFromEmail?email=${email}`,
                                method: 'get',
                                success: function(data) {
                                    user_uuid = data.user_id;
                                    $.ajax({
                                        url: '<?= $api_url ?>/delete_dataset',
                                        type: 'POST',
                                        data: JSON.stringify({
                                            'filename': filename,
                                            'uuid': user_uuid
                                        }),
                                        contentType: 'application/json; charset=utf-8',
                                        success: function(data) {
                                            if (data.success){
                                                let success_message = data.message;
                                                $.ajax({
                                                    url: '<?= $classifyURL ?>/actions/update_action',
                                                    method: 'POST',
                                                    data: {
                                                        'report_uuid': uuid,
                                                        'user_uuid': user_uuid,
                                                        'action': 'Deleted report'
                                                    },
                                                    success: function(res) {
                                                        if (res.success) {
                                                            showSuccess(data.message);
                                                            $('#collection').DataTable().ajax.reload();
                                                        } else {
                                                            console.log(res.message);
                                                        }
                                                    },
                                                    error: function(xhr, ajaxOptions, thrownError) {
                                                        console.log('Error communicating with the server');
                                                    }
                                                });
                                            } else {
                                                console.log(data.message);
                                            }

                                        },
                                        error: function (xhr, status, error) {
                                            console.log("Error communicating with the server.");
                                            return null;
                                        }
                                    });
                                },
                                error: function(xhr, request, error) {

                                    console.log('User not found.');
                                }
                            });
                        } else {
                            console.log(data.message);
                            return null;
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log("Error communicating with the server.");
                        return null;
                    }
                });
            }
        } // deleteReport

        $('#submit-to-automl').click(function() {
            if(confirm("Are you sure you want to submit this data for processing?")){
                toggleLoadingScreenOverlay()
                let error = 0;
                //let form = $('#column_names').serializeArray();
                let form = [];
                let class_column = $('#select-class').val();
                if (class_column == null || class_column === 'no-class-column-selected') {
                    if(!confirm("You have not selected a class column, which will limit your potential models to only unsupervised clustering. Continue?")){
                        $('#select-class').closest('.bootstrap-select').addClass('highlighted'); //Highlight the selectpicker to emphasize to user
                        error = 1;
                    }
                }
                $('.form-check-input').each(function(index, element) {
                    if ($(element).attr('type') === 'checkbox') {
                        if (element.id === class_column) {
                            if (!$(element).is(':checked')) {
                                showError('Your chosen class column, "'+element.id+'", must be selected in the column list.');
                                error = 1;
                                return;
                            }
                        }
                        let checked_type = document.querySelector('input[name="'+element.id+'"]:checked').id;
                        let type = checked_type.substring(checked_type.lastIndexOf('-')+1);
                        if ((element.id === class_column ) && (type === 'float' || type === 'categorical')) {
                            showError('Your chosen class column, "'+element.id+'", must be of type bool or integer.');
                            error = 1;
                            return;
                        }
                        if (document.getElementById(element.id+'-missing-values')) { //If there's missing values to deal with
                            let fill_method = document.getElementById(element.id+'-missing-values').value;
                            let fill_value = null;
                            if (fill_method === 'constant') {
                                fill_value = document.getElementById(element.id+'-fill-value').value;
                                if (type === 'integer') {
                                    if (Number.isInteger(Number(fill_value))) {
                                        fill_value = Number(fill_value);
                                    } else {
                                        showError('Fill value for column '+element.id+' not valid for type integer.');
                                        error = 1;
                                        return;
                                    }
                                }
                                else if (type === 'float') {
                                    if (!isNaN(Number(fill_value)) && (Number.isFinite(Number(fill_value)))) {
                                        fill_value = Number(fill_value);
                                    } else {
                                        showError('Fill value for column '+element.id+' not valid for type float.');
                                        error = 1;
                                        return;
                                    }
                                }
                                else if (type === 'bool') {
                                    if (fill_value === 'true' || fill_value === '1' || fill_value === 'True' || fill_value === 'TRUE') {
                                        fill_value = 1;
                                    } else if (fill_value === 'false' || fill_value === '0' || fill_value === 'False' || fill_value === 'FALSE'){
                                        fill_value = 0;
                                    } else {
                                        showError('Fill value for column '+element.id+' not valid for type bool.');
                                        error = 1;
                                        return;
                                    }
                                } //Don't need to check categorical type, because any entry would be valid for string
                            }
                            if (element.id === class_column) {
                                form.push({column:element.id, data_type:type, checked:$(element).is(':checked'), missing:fill_method, fill_value:fill_value, class:true})
                            }
                            else {
                                form.push({column:element.id, data_type:type, checked:$(element).is(':checked'), missing:fill_method, fill_value:fill_value})
                            }
                        }
                        else {
                            if (element.id === class_column) {
                                form.push({column:element.id, data_type:type, checked:$(element).is(':checked'), missing:null, fill_value:null, class:true})
                            }
                            else {
                                form.push({column:element.id, data_type:type, checked:$(element).is(':checked'), missing:null, fill_value:null})
                            }
                        }
                        // } else {
                        //     form.push({column:element.id, data_type:'none', checked:false}) //If dropped column, update actions
                        // }

                    }
                });
                if (error === 1) {
                    toggleLoadingScreenOverlay()
                    return null;
                }
                else if (currentFile !== null) {
                    $.ajax({
                        url: '<?= $api_url ?>/change_column_types',
                        type: 'POST',
                        data: JSON.stringify({
                            'filename': currentFile,
                            'data_types': JSON.stringify(form)
                        }),
                        contentType: 'application/json; charset=utf-8',
                        success: function(data) {
                            if (data.success == false) {
                                toggleLoadingScreenOverlay()
                                showError(data.message);
                                return null;
                            }
                            else {
                                let column_types_updated = data.data_types;
                                $.ajax({ //Update table with column changes so they can be applied to test set if necessary
                                    url: '<?= $classifyURL ?>/reports/set-column_changes',
                                    type: 'POST',
                                    data: {
                                        'filename': currentFile,
                                        'column_changes': JSON.stringify(column_types_updated)
                                    },
                                    success: function(data) {
                                        if (data.success == false) {
                                            toggleLoadingScreenOverlay()
                                            showError(data.message);
                                            return null;
                                        }
                                        else {
                                            showSuccess(data.message);
                                            $('#gotoMLOpts').show();
                                            $('#submit-to-automl').hide();
                                            uploaded_to_clearml=true;
                                            toggleLoadingScreenOverlay()
                                        }

                                    },
                                    error: function (xhr, status, error) {
                                        toggleLoadingScreenOverlay()
                                        showError("Error communicating with the server.");
                                        return null;
                                    }
                                });
                            }

                        },
                        error: function (xhr, status, error) {
                            toggleLoadingScreenOverlay()
                            showError("Error communicating with the server.");
                            return null;
                        }
                    });

                } else {
                    toggleLoadingScreenOverlay()
                    showError("Please upload a file first.");
                }
            }
        });

        $('#classifierModal').on('shown.bs.modal', function () {
            console.log('classifierModal');
            let dropdown = document.getElementById('class-selector');

            let fields_sorted = <?= json_encode($fieldsByInstrument)?>;

            for(const [key, value] of Object.entries(fields_sorted)) {
                if (document.getElementById(key).checked) {
                    for(let i=0; i<value.length; i++) {
                        let newElement = document.createElement('option');
                        newElement.value = value[i];
                        newElement.innerHTML = value[i];
                        dropdown.appendChild(newElement);
                    }
                }
            }
        });

        $('#classifierModal').on('hidden.bs.modal', function () {
            parsed = parseCSVWithNewNames(moduleCSV, document.getElementById('class-selector').value);
        });

        $('#gotoMLOpts').click(function() {
            window.location
        });

        function toggleLoadingScreenOverlay() {
            if ($('#cover-spin').is(':visible')){
                $('#cover-spin').hide();
            } else {
                $('#cover-spin').show();
            }

        }
    </script>