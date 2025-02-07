<?php
$page = 'home';
include_once __DIR__ . '/_header.php';
global $rootURL;
global $api_url;
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h4">Data - <span class="text-muted">Upload</span></h1>
    </div>

    <div class="row selection-btns">
        <div class="col-md-6">
            <a id="add-data-btn" data-toggle="modal" data-target="#uploadModal">
                <div class="center-home-sects">
                    <span><i class="fa fa-plus"></i></span><br>
                    <h5>Add Data File</h5>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a id="view-all-btn" href="<?= $rootURL?>/result" class="center-home-sects">
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="columnsModalBody">
                    <h5>Choose which columns to include in uploaded dataset.</h5>
                    <h6>You may also change data types of each column here.</h6>
                    <h6>Categorical variables will be one-hot encoded.</h6>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit-to-automl">Upload Dataset</button>
                    <a href="<?= $rootURL ?>/result" id="gotoMLOpts" type="button" class="btn btn-primary mr-2" style="display:none;">
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
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Report</h5>
                    <!--                    <div class="fa-solid fa-circle-info help-tooltip">-->
                    <!--                        <span class="tooltip-text" id="help-tooltip-text">Tooltip text</span>-->
                    <!--                    </div>-->
                    <a id="site-help" class="fa fa-question-circle" aria-hidden="true" onmouseover="show_tooltip()" onmouseout="hide_tooltip()"></a>
                    <div id="instruction-panel" class="instruction-panel"> <!-- This div contains the tooltip -->
                        <h5>Tips for Uploading Reports:</h5>
                        <ul>
                            <li>Report must be in the .csv file format</li>
                            <li>Report must contain a column labeled 'class'</li>
                            <ul>
                                <li>If the class label is binary, it must have values 0/1 or TRUE/FALSE</li>
                                <li>If the class label is multiclass, it must have integer values (0,1,2...)</li>
                            </ul>
                            <li>No column names or values should contain commas</li>
                            <li>Rows with missing values can be handled through Classify in several ways</li>
                            <ul>
                                <li>For each column with missing values, you can choose to drop missing rows, synthetically fill, or use a constant fill value.</li>
                                <li>Any other method for handling missing data should be done before uploading to Classify.</li>
                            </ul>
                            <li>Categorical string variables will be automatically one-hot encoded. Ordinal encodings (encoding categories as integers) should be done before uploading</li>
                        </ul>
                    </div>
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
                    <button type="button" class="btn btn-primary" id="submitUploadBtn">Preview</button>
                </div>
            </div>
        </div>
    </div>
    <div id="cover-spin"></div>

    <script type="text/javascript">
        var collection = {};
        var collectionTable = $('#collection');
        var collectionDataTable = null;
        var user_uuid = null;
        var currentFile = null;
        var report_uuid = null;
        var uploaded_to_clearml=false;

        $(function() {

        }); //document ready

        $('#uploadModal').on('shown.bs.modal', function () {
            $.ajax({
                url: '<?= $rootURL ?>/users/getUser',
                method: 'get',
                success: function(data) {
                    if (!data.user.accepted_terms) { //If user has not accepted terms yet, show modal
                        window.location.href = "<?= $rootURL ?>";
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

        $('#columnsModal').on('hidden.bs.modal', function () {
            document.getElementById('column_names').innerHTML = '';
            if (!uploaded_to_clearml) {
                let filename_no_uuid = currentFile.substring(0, currentFile.lastIndexOf('_')) + currentFile.substring(currentFile.lastIndexOf('.'));
                $.ajax({ //Delete report to prevent clearml dataset error
                    url: '<?= $rootURL ?>/reports/delete',
                    type: 'POST',
                    data: {
                        'uuid': report_uuid,
                        'filename': filename_no_uuid
                    },
                    success: function(data) {
                        let user_uuid = null;
                        $.ajax({
                            url: '<?= $rootURL?>/users/getUser',
                            method: 'get',
                            success: function(data) {
                                user_uuid = data.user.id;
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
                                                url: '<?= $rootURL ?>/actions/update_action',
                                                method: 'POST',
                                                data: {
                                                    'report_uuid': report_uuid,
                                                    'user_uuid': user_uuid,
                                                    'action': 'Deleted report'
                                                },
                                                success: function(res) {
                                                    if (res.success) {
                                                    } else {
                                                        showError(res.message);
                                                    }
                                                },
                                                error: function(xhr, ajaxOptions, thrownError) {
                                                    showError('Error communicating with the server');
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
                            },
                            error: function(xhr, request, error) {

                                showError('User not found.');
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

        $('#submitUploadBtn').click(function(){
            let uploadFile = $('#uploadReportFile');
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
            if (file === undefined) {
                showError('Unknown file error encountered');
                return;
            }
            let max_filesize = 500*1024*1024; //500 MB
            if (file.size > max_filesize) {
                showError('File is too large. Max filesize is 500 MB');
                return;
            }

            toggleLoadingScreenOverlay();

            $.ajax({
                url: '<?= $rootURL ?>/users/getUser',
                method: 'get',
                success: function(data) {
                    user_uuid = data.user.id;
                    var form_data = new FormData();
                    form_data.append('file', file);
                    form_data.append('user_uuid', user_uuid);
                    // get filename from uploaded file
                    currentFile = file.name;
                    currentFile = currentFile.replace('.csv', '_'+user_uuid+'.csv');
                    // form_data.append('filename', currentFile);
                    $.ajax({
                        url: '<?= $api_url ?>/verify_dataset',
                        type: 'POST',
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            if (data.success) {
                                $.ajax({
                                    url: '<?= $rootURL ?>/reports/submit',
                                    method: 'post',
                                    dataType: 'json',
                                    data: form_data,
                                    processData: false,
                                    contentType: false,
                                    success: function(res) {
                                        if (res.success) {
                                            report_uuid = res.report_uuid;
                                            filename = res.file_name;
                                            $.ajax({
                                                url: '<?= $rootURL ?>/actions/update_action',
                                                method: 'POST',
                                                data: {
                                                    'report_uuid': report_uuid,
                                                    'user_uuid': user_uuid,
                                                    'action': 'Uploaded dataset'
                                                },
                                                success: function(res) {
                                                    if (res.success) {
                                                        $.ajax({
                                                            url: '<?= $api_url ?>/get_column_types',
                                                            type: 'POST',
                                                            data: form_data,
                                                            contentType: false,
                                                            processData: false,
                                                            success: function(data) {
                                                                toggleLoadingScreenOverlay();
                                                                showSuccess('Dataset uploaded');
                                                                $('#uploadModal').modal('hide');
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
                                                error: function(xhr, ajaxOptions, thrownError) {
                                                    toggleLoadingScreenOverlay();
                                                    showError('Error communicating with the server');
                                                }
                                            });

                                        } else {
                                            showError(res.message);
                                            toggleLoadingScreenOverlay();
                                        }
                                    },
                                    error: function(xhr, ajaxOptions, thrownError) {
                                        toggleLoadingScreenOverlay();
                                        showError('Error communicating with the server');
                                    }
                                });
                            }
                            else {
                                showError(data.message);
                                toggleLoadingScreenOverlay();
                            }
                        },
                        error: function (xhr, status, error) {
                            showError("Error communicating with the server.");
                            toggleLoadingScreenOverlay();
                            return null;
                        }
                    });
                },
                error: function(xhr, request, error) {
                    showError('Error getting user data.');
                    toggleLoadingScreenOverlay();
                }
            });
        }); // upload


        function showColumns(data_types, missing_values) {
            let toAppendBool = "";
            let toggle = 0;
            Object.keys(data_types).forEach((column) => {
                column_name = column.replace(/^\w/, c => c.toUpperCase());
                if (column == 'class') {
                    toAppendBool += `<div class="form-check" style="border-bottom: 0.1rem solid;`
                    if (toggle == 1) {
                        toAppendBool += ' background-color: #DDDDDD;'
                    }
                    toAppendBool += `">

                                        <input id="${column}" type="checkbox" class="form-check-input" checked disabled>
                                        <label for="${column}" class="bold-label">${column_name}</label>
                                    </div>`;
                } else {
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
                                        <select id="${column}-missing-values" class="selectpicker" style="display:block !important;">
                                            <option value="drop">Drop Missing Rows</option>
                                            <option value="constant">Constant Fill Value</option>
                                            <option value="synthetic">Synthetically Fill</option>
                                        </select></div>
                                        <div class="col-md-1"><input type="text" id="${column}-fill-value" value="0" size="6" hidden></div></div>`;
                    }
                    toAppendBool += `</div>`;
                }
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

            modal_checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('click', function() {
                    if (!this.checked) {
                        var radios = modal.querySelectorAll("input[type='radio'][name='" + this.id + "']");
                        radios.forEach(function(radio) {
                            radio.disabled = true;
                        });
                    } else {
                        var radios = modal.querySelectorAll("input[type='radio'][name='" + this.id + "']");
                        radios.forEach(function(radio) {
                            radio.disabled = false;
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
        }

        $('#submit-to-automl').click(function() {
            if(confirm("Are you sure you want to submit this data for processing?")){
                toggleLoadingScreenOverlay()
                let error = 0;
                //let form = $('#column_names').serializeArray();
                let form = [];
                $('.form-check-input').each(function(index, element) {
                    if (element.id !== 'class') {
                        if ($(element).attr('type') == 'checkbox') {
                            if ($(element).is(':checked')) {
                                let checked_type = document.querySelector('input[name="'+element.id+'"]:checked').id;
                                let type = checked_type.substring(checked_type.lastIndexOf('-')+1);
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
                                    form.push({column:element.id, data_type:type, checked:true, missing:fill_method, fill_value:fill_value})
                                }
                                else {
                                    form.push({column:element.id, data_type:type, checked:true, missing:null, fill_value:null})
                                }
                            } else {
                                form.push({column:element.id, data_type:'none', checked:false}) //If dropped column, update actions
                            }

                        }
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
                                $.ajax({ //Update table with column changes so they can be applied to test set if necessary
                                    url: '<?= $rootURL ?>/reports/set-column_changes',
                                    type: 'POST',
                                    data: {
                                        'filename': currentFile,
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

        $('#gotoMLOpts').click(function() {
            window.location
        });

        function delete_report(uuid){
            let confirmedDeletion = confirm("Are you sure you want to delete this report? This action is irreversible.");
            if (confirmedDeletion) {
                $.ajax({
                    url: '<?= $rootURL ?>/reports/delete',
                    type: 'POST',
                    data: {
                        'uuid': uuid
                    },
                    success: function(data) {
                        if (data.success) {
                            let user_uuid = null;
                            $.ajax({
                                url: '<?= $rootURL?>/users/getUser',
                                method: 'get',
                                success: function(data) {
                                    user_uuid = data.user.id;
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
                                                    url: '<?= $rootURL ?>/actions/update_action',
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
                                                            showError(res.message);
                                                        }
                                                    },
                                                    error: function(xhr, ajaxOptions, thrownError) {
                                                        showError('Error communicating with the server');
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
                                },
                                error: function(xhr, request, error) {

                                    showError('User not found.');
                                }
                            });
                        } else {
                            showError(data.message);
                            return null;
                        }
                    },
                    error: function (xhr, status, error) {
                        showError("Error communicating with the server.");
                        return null;
                    }
                });
            }
        } // deleteReport

        function toggleLoadingScreenOverlay() {
            if ($('#cover-spin').is(':visible')){
                $('#cover-spin').hide();
            } else {
                $('#cover-spin').show();
            }

        }
    </script>
<?php
include_once __DIR__ . '/_footer.php';