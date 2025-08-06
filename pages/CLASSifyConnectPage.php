<?php
/** @var \ExternalModules\AbstractExternalModule $module */

$page = 'home';
global $classifyURL;
global $api_url;
global $api_key;

$selectedForms = isset($_POST['selectedForms']) ? $_POST['selectedForms'] : [];
$instruments = REDCap::getInstrumentNames();

$metadata = \REDCap::getDataDictionary('array');

$fieldsByInstrument = [];
foreach ($metadata as $field => $attributes) {
    $instrument = $attributes['form_name'];
    $fieldsByInstrument[$instrument][] = $field;
}
?>
    <script>
        const ExternalModules = window.ExternalModules || {};
        ExternalModules.CSRF_TOKEN = '<?= $module->getCSRFToken() ?>';
    </script>

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
        <h1 class="h4">Data - <span class="text-muted">Upload</span></h1>
    </div>

    <div class="row selection-btns">
        <div class="col-md-6">
            <a id="add-data-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <div class="center-home-sects">
                    <span><i class="fa fa-plus"></i></span><br>
                    <h5>Add Data File</h5>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a id="view-all-btn" href="https://classify.ai.uky.edu/result" class="center-home-sects">
                <div class="center-home-sects">
                    <span><i class="fas fa-bars"></i></span><br>
                    <h5>View All Data</h5>
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
                            <select id="select-class" class="selectpicker ms-2 me-1 mb-1" title="Choose classifier column" data-width="100%" data-live-search="true">
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
                    <a href="https://classify.ai.uky.edu/result" id="gotoMLOpts" type="button" class="btn btn-primary me-2" style="display:none;">
                        <i class="fa fa-eye"></i> View Uploaded Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- upload file Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="flex-wrap: wrap;">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Report</h5>
                    <!--                    <div class="fa-solid fa-circle-info help-tooltip">-->
                    <!--                        <span class="tooltip-text" id="help-tooltip-text">Tooltip text</span>-->
                    <!--                    </div>-->
                    <a id="site-help" class="fa fa-question-circle me-auto" aria-hidden="true" onmouseover="show_tooltip()" onmouseout="hide_tooltip()"></a>
                    <div id="instruction-panel" class="instruction-panel"> <!-- This div contains the tooltip -->
                        <h5>Tips for Uploading Reports:</h5>
                        <ul>
                            <li>Report must be in the .csv file format</li>
                            <li>You will be able to select a class column on the next page</li>
                            <ul>
                                <li>If your class label is binary, ensure it only has values 0/1, yes/no, or TRUE/FALSE</li>
                                <li>If the class label is multiclass, it must have integer values (0,1,2...)</li>
                            </ul>
                            <li>You will also be able to drop any unnecessary columns on the next page. It is recommended to drop any index or ID columns</li>
                            <li>Rows with missing values can be handled through Classify in several ways</li>
                            <ul>
                                <li>For each column with missing values, you can choose to drop missing rows, synthetically fill, or use a constant fill value</li>
                                <li>Any other method for handling missing data should be done before uploading to Classify</li>
                            </ul>
                            <li>Categorical string variables will be automatically one-hot encoded. Ordinal encodings (encoding categories as integers) should be done before uploading</li>
                            <ul>
                                <li>If you have a column with a high number of unique categories, it is recommended to drop this column or encode differently prior to upload</li>
                            </ul>
                        </ul>
                    </div>
                    <h6>(Not HIPAA-Compliant)</h6>
                </div>
                <div class="modal-body" id="uploadModalBody">
                    <div class="col-lg-12 mb-3 form-floating">
                        <div class="custom-file" id="customFile">
                           <!-- <input type="file" class="form-control custom-file-input" accept=".csv" id="uploadReportFile" aria-describedby="fileHelp">-->
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitUploadBtn">Preview</button>
                </div>
            </div>
        </div>
    </div>
    <div id="cover-spin"></div>

    <script type="text/javascript">
        console.log(ExternalModules.CSRF_TOKEN)
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

        // Change this so that it rejects user if they haven't input an API key.
        $('#uploadModal').on('shown.bs.modal', function () {
            console.log('forms modal shown');
        });

        function getSelectedForms() {
            return Array.from(document.querySelectorAll("input.instrument-selection:checked"))
                .map(input => input.value);
        }

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

        function generateCSVFromData(dataArray) {
            if (!dataArray.length) return '';

            const headers = [...new Set(dataArray.flatMap(obj => Object.keys(obj)))];
            const csvRows = [headers.join(',')];

            for (const row of dataArray) {
                const values = headers.map(h => {
                    const val = row[h] ?? '';
                    return `"${String(val).replace(/"/g, '""')}"`; // escape quotes
                });
                csvRows.push(values.join(','));
            }

            return csvRows.join('\n');
        }


        $('#columnsModal').on('hidden.bs.modal', function () {
            document.getElementById('column_names').innerHTML = '';
            $('#select-class').empty();
            $('#select-class').append('<option value="no-class-column-selected" style="color:gray; font-style: italic;">No Class Column</option>');
            $("#select-class").selectpicker('refresh');
            if (!uploaded_to_clearml) {
                $.ajax({
                    url: '<?= $module->getUrl("proxy.php") ?>&action=update_action',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    data: {
                        redcap_csrf_token: ExternalModules.CSRF_TOKEN,
                        report_uuid: report_uuid,
                        action: 'Deleted report'
                    },
                    success: function(res) {
                        if (res.success) {
                            $.ajax({ //Delete report to prevent clearml dataset error
                                url: '<?= $module->getUrl("proxy.php") ?>&action=reports_delete',
                                type: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                data: {
                                    redcap_csrf_token: ExternalModules.CSRF_TOKEN,
                                    'report_uuid': report_uuid
                                },
                                success: function(data) {
                                    if (data.success){
                                    } else {
                                        showError(data.message);
                                    }
                                },
                                error: function (xhr, status, error) {
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
            else { //Already uploaded to clearml
                $('#gotoMLOpts').hide();
                $('#submit-to-automl').show();
                uploaded_to_clearml=false;
            }
        });

        // This needs to be updated to create a file from the selected data in the REDCap Project.
        $('#submitUploadBtn').click(function() {
            console.log('submit clicked')
            var fileName = $(this).val().split('\\').pop();
            $('#uploadReportFileLabel').html(fileName);

            const selectedForms = getSelectedForms();
            const combinedData = [];

            selectedForms.forEach(form => {
                if (moduleByIns[form]) {
                    //const flattened = flattenRecords(moduleByIns[form]);
                    combinedData.push(...moduleByIns[form]);
                }
            });

            if (combinedData.length === 0) {
                showError('No data found for selected forms.');
                return null;
            }

            console.log('user_uuid: ' + user_uuid);

            const csv = generateCSVFromData(combinedData);

            let filename = document.getElementById('filename').value;
            filename = filename.replace(/\s+/g, '_');
            // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
            filename = filename.endsWith('.csv') ? filename : filename + '.csv';

            const blob = new Blob([csv], {type: 'text/csv'});
            let file = new File([blob], filename, {type: 'text/csv'});

            let uploadFile = $('#uploadReportFile');
            if (uploadFile.val() === null || uploadFile.val() === '') {
                showError('Please upload a .csv file');
                return;
            }

            if (file.name.includes(' ')) { //If space in file name, remove it
                file = new File(
                    [file], // File content remains the same
                    file.name.replace(/\s+/g, '_'), // Replace spaces with underscores
                    {type: file.type} // Preserve the file type
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
            let max_filesize = 500 * 1024 * 1024; //500 MB
            if (file.size > max_filesize) {
                showError('File is too large. Max filesize is 500 MB');
                return;
            }

            toggleLoadingScreenOverlay();

            console.log(csv);

            //var form_data = new FormData();
            //form_data.append('file', csv); // from the aaron version

            const formData = new FormData();
            formData.append('redcap_csrf_token', ExternalModules.CSRF_TOKEN);
            formData.append('action', 'reports-submit');
            formData.append('file', file);  // Assuming `csv` is a string

            //console.log(file);


            //console.log(<?= json_encode($api_key[0]) ?>);
            //console.log('<?= $classifyURL ?>');
            //console.log(ExternalModules.CSRF_TOKEN);
            //console.log(form_data);

            $.ajax({
                url: '<?= $module->getUrl("proxy.php") ?>&action=reports_submit',
                method: 'POST',
                data: formData,
                processData: false,        // prevent jQuery from processing data
                contentType: false,        // prevent jQuery from setting Content-Type
                success: function (res) {
                    if (res.success) {
                        let column_types = res.column_types;
                        report_uuid = res.report_uuid;

                        $.ajax({
                            url: '<?= $module->getUrl("proxy.php") ?>&action=update_action',
                            method: 'POST',
                            data: {
                                redcap_csrf_token: ExternalModules.CSRF_TOKEN,
                                report_uuid: report_uuid,
                                action: 'Uploaded dataset'
                            },
                            success: function (res) {
                                console.log(res)
                                if (res.success) {
                                    console.log('res success!')
                                    toggleLoadingScreenOverlay();
                                    showSuccess('Dataset uploaded');
                                    $('#uploadModal').modal('hide');
                                    $('#columnsModal').modal('show');
                                    showColumns(column_types['data_types'], column_types['missing_values']);
                                } else {
                                    console.log(res);
                                    showError(res.message);
                                    toggleLoadingScreenOverlay();
                                }
                            },
                            error: function () {
                                console.log(res)
                                toggleLoadingScreenOverlay();
                                showError('Error communicating with the server');
                            }
                        });

                    } else {
                        showError(res.message);
                        toggleLoadingScreenOverlay();
                    }
                },
                error: function (xhr, status, error) {
                    toggleLoadingScreenOverlay();
                    showError('Error communicating with the server');
                }
            });
        });

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

        /*$('#submit-to-automl').click(function() {
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
                        url: '<?= $module->getUrl("proxy.php") ?>&action=change_column_types',
                        type: 'POST',
                        data: {
                            redcap_csrf_token: ExternalModules.CSRF_TOKEN,
                            'filename': currentFile,
                            'data_types': JSON.stringify(form)
                        },
                        contentType: 'application/json; charset=utf-8',
                        success: function(data) {
                            if (data.success === false) {
                                toggleLoadingScreenOverlay()
                                showError(data.message);
                                return null;
                            }
                            else {
                                let column_types_updated = data.data_types;
                                $.ajax({ //Update table with column changes so they can be applied to test set if necessary
                                    url: '<?= $module->getUrl("proxy.php") ?>&action=set-column_changes',
                                    type: 'POST',
                                    data: {
                                        redcap_csrf_token: ExternalModules.CSRF_TOKEN,
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
        });*/

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
                else if (report_uuid !== null) {
                    $.ajax({ //Update table with column changes so they can be applied to test set if necessary
                        url: '<?= $module->getUrl("proxy.php") ?>&action=set_column_changes',
                        type: 'POST',
                        data: {
                            redcap_csrf_token: ExternalModules.CSRF_TOKEN,
                            'report_uuid': report_uuid,
                            'column_changes': JSON.stringify(form)
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