const classify_root = 'https://data.ai.uky.edu/classify';
const classify_api = 'https://data.ai.uky.edu/classify/api';
let uploaded_to_clearml = false;

var userUUID = null;
var currentFileUUID = null;
var reportUUID = null;

function handleUpload() {
    document.getElementById('columnsModal').innerHTML = `<div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="columnsModalLabel">Preview&nbsp;</h5>
                    <div class="spinner-border" role="status" id="spinner" style="display:none;">
                      <span class="sr-only">Loading...</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="close_modal()">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="close_modal()">Close</button>
                    <button type="button" class="btn btn-primary" id="submit-to-automl">Upload Dataset</button>
                    <a href="${classify_root}/result" id="gotoMLOpts" target="_blank" type="button" class="btn btn-primary mr-2" style="display:none;">
                        <i class="fa fa-eye"></i> View Uploaded Data
                    </a>
                </div>
            </div>
        </div>
        <div id="cover-spin"></div>
`;

    // Parse the CSV with the classifier field
    const parsed = parseCSVWithNewNames(moduleData, classifier[0]);

    // Get email field from input
    const email = document.getElementsByName('classify-email____0')[0].value;

    // Define or fallback to a default filename
    var currentFile = filename; // Fallback if filename is not defined

    // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
    currentFile = currentFile.endsWith('.csv') ? currentFile : currentFile + '.csv';

    $.ajax({
        url: `${classify_root}/users/getUserFromEmail?email=${email}`,
        method: 'get',
        success: function(data) {
            const user_uuid = data.user_id;
            userUUID = data.user_id;
            currentFileUUID = currentFile.split('.csv')[0] + `_${userUUID}.csv`;

            // Create form data object
            var form_data = new FormData();

            // Create a Blob from the parsed CSV string
            const csvBlob = new Blob([parsed], { type: 'text/csv' });

            // Append the Blob and other fields to the form data
            form_data.append('file', csvBlob, currentFile);
            form_data.append('user_uuid', user_uuid);

            $.ajax({
                url:`${classify_api}/verify_dataset`,
                type: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.success) {
                        $.ajax({
                            url: `${classify_root}/reports/submit`,
                            method: 'post',
                            dataType: 'json',
                            data: form_data,
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.success) {
                                    reportUUID = res.report_uuid;
                                    let report_uuid = res.report_uuid;
                                    $.ajax({
                                        url: `${classify_root}/actions/update_action`,
                                        method: 'POST',
                                        data: {
                                            'report_uuid': report_uuid,
                                            'user_uuid': user_uuid,
                                            'action': 'Uploaded dataset',
                                            'session_id': 'REDCap Upload',
                                            'api_key': '7217be72-156e-4bda-9798-d7d6c8fc59da'
                                        },
                                        success: function (res) {
                                            if (res.success) {
                                                $.ajax({
                                                    url: `${classify_api}/get_column_types`,
                                                    type: 'POST',
                                                    data: form_data,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function (data) {
                                                        toggleLoadingScreenOverlay();
                                                        console.log('Dataset uploaded');
                                                        $('#uploadModal').modal('hide');
                                                        $('#columnsModal').modal('show');
                                                        console.log(data.missing_values);
                                                        showColumns(data.data_types, data.missing_values);
                                                    },
                                                    error: function (xhr, status, error) {
                                                        console.log("Error communicating with the S3 server.");
                                                        toggleLoadingScreenOverlay();
                                                        return null;
                                                    }
                                                });
                                            } else {
                                                console.log(res.message);
                                                toggleLoadingScreenOverlay();
                                            }
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            toggleLoadingScreenOverlay();
                                            console.log('Error communicating with update_action.');
                                            console.log(xhr);
                                            console.log(ajaxOptions);
                                            console.log(thrownError);
                                        }
                                    });

                                }
                                else {
                                    console.log(res.message);
                                    toggleLoadingScreenOverlay();
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                toggleLoadingScreenOverlay();
                                console.log('Error communicating with the upload server');
                            }
                        });
                    }
                    else {
                        console.log(data.message);
                        toggleLoadingScreenOverlay();
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Error communicating with the verification server.");
                    toggleLoadingScreenOverlay();
                    return null;
                }
            });
        },
        error: function(xhr, request, error) {
            console.log('Error getting user data.');
            toggleLoadingScreenOverlay();
        }
    });

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
                if (data_types[column] == 'integer') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Integer
                                                </label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-check-label mb-3" title="float">
                                                    <input id="${column}-float" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column] == 'float') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Float
                                                </label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-check-label mb-3" title="bool">
                                                    <input id="${column}-bool" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column] == 'bool') {
                    toAppendBool += ' checked'
                }
                toAppendBool += `>Bool
                                                </label>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-check-label mb-3" title="categorical">
                                                    <input id="${column}-categorical" name="${column}" type="radio" class="form-check-input"`
                if (data_types[column] == 'string') {
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
            } else {
                toggle = 0;
            }
        });
        $('#columnsModal #column_names').append(toAppendBool);
        var modal = document.getElementById('columnsModal');
        var modal_checkboxes = modal.querySelectorAll("input[type='checkbox']")
        var missing_dropdowns = modal.querySelectorAll("select");

        modal_checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('click', function () {
                if (!this.checked) {
                    var radios = modal.querySelectorAll("input[type='radio'][name='" + this.id + "']");
                    radios.forEach(function (radio) {
                        radio.disabled = true;
                    });
                } else {
                    var radios = modal.querySelectorAll("input[type='radio'][name='" + this.id + "']");
                    radios.forEach(function (radio) {
                        radio.disabled = false;
                    });
                }
            });
        });

        missing_dropdowns.forEach(function (dropdown) {
            dropdown.addEventListener('change', function (event) {
                let select_id = event.target.id.slice(0, -15);
                let text_id = select_id + '-fill-value'
                if (event.target.value === 'constant') {
                    if (document.getElementById(select_id + '-categorical').checked) { //Change default value for categorical variables
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

        $('#submit-to-automl').click(function () {
            if (confirm("Are you sure you want to submit this data for processing?")) {
                toggleLoadingScreenOverlay()
                let error = 0;
                //let form = $('#column_names').serializeArray();
                let form = [];
                $('.form-check-input').each(function (index, element) {
                    if (element.id !== 'class') {
                        if ($(element).attr('type') === 'checkbox') {
                            if ($(element).is(':checked')) {
                                let checked_type = document.querySelector('input[name="' + element.id + '"]:checked').id;
                                let type = checked_type.substring(checked_type.lastIndexOf('-') + 1);
                                if (document.getElementById(element.id + '-missing-values')) { //If there's missing values to deal with
                                    let fill_method = document.getElementById(element.id + '-missing-values').value;
                                    let fill_value = null;
                                    if (fill_method === 'constant') {
                                        fill_value = document.getElementById(element.id + '-fill-value').value;
                                        if (type === 'integer') {
                                            if (Number.isInteger(Number(fill_value))) {
                                                fill_value = Number(fill_value);
                                            } else {
                                                alert('Fill value for column ' + element.id + ' not valid for type integer.');
                                                error = 1;
                                                return;
                                            }
                                        } else if (type === 'float') {
                                            if (!isNaN(Number(fill_value)) && (Number.isFinite(Number(fill_value)))) {
                                                fill_value = Number(fill_value);
                                            } else {
                                                alert('Fill value for column ' + element.id + ' not valid for type float.');
                                                error = 1;
                                                return;
                                            }
                                        } else if (type === 'bool') {
                                            if (fill_value === 'true' || fill_value === '1' || fill_value === 'True' || fill_value === 'TRUE') {
                                                fill_value = 1;
                                            } else if (fill_value === 'false' || fill_value === '0' || fill_value === 'False' || fill_value === 'FALSE') {
                                                fill_value = 0;
                                            } else {
                                                alert('Fill value for column ' + element.id + ' not valid for type bool.');
                                                error = 1;
                                                return;
                                            }
                                        } //Don't need to check categorical type, because any entry would be valid for string
                                    }
                                    form.push({
                                        column: element.id,
                                        data_type: type,
                                        checked: true,
                                        missing: fill_method,
                                        fill_value: fill_value
                                    })
                                } else {
                                    form.push({
                                        column: element.id,
                                        data_type: type,
                                        checked: true,
                                        missing: null,
                                        fill_value: null
                                    })
                                }
                            } else {
                                form.push({column: element.id, data_type: 'none', checked: false}) //If dropped column, update actions
                            }

                        }
                    }
                });
                if (error === 1) {
                    toggleLoadingScreenOverlay()
                    return null;
                } else if (currentFile !== null) {
                    console.log(currentFileUUID);
                    $.ajax({
                        url: `${classify_api}/change_column_types`,
                        type: 'POST',
                        data: JSON.stringify({
                            'filename': currentFileUUID,
                            'data_types': JSON.stringify(form)
                        }),
                        contentType: 'application/json; charset=utf-8',
                        success: function (data) {
                            console.log(form);
                            console.log(data);
                            if (data.success == false) {
                                toggleLoadingScreenOverlay()
                                alert(data.message);
                                return null;
                            } else {
                                console.log('right before set-column_changes')
                                $.ajax({ //Update table with column changes so they can be applied to test set if necessary
                                    url: `${classify_root}/reports/set-column_changes`,
                                    type: 'POST',
                                    data: {
                                        'filename': currentFileUUID,
                                        'column_changes': JSON.stringify(form)
                                    },
                                    success: function (data) {
                                        if (data.success == false) {
                                            toggleLoadingScreenOverlay()
                                            alert(data.message);
                                            return null;
                                        } else {
                                            alert(data.message);
                                            $('#gotoMLOpts').show();
                                            $('#submit-to-automl').hide();
                                            uploaded_to_clearml = true;
                                            toggleLoadingScreenOverlay()
                                        }

                                    },
                                    error: function (xhr, status, error) {
                                        toggleLoadingScreenOverlay()
                                        alert("Error communicating with the server.");
                                        return null;
                                    }
                                });
                            }

                        },
                        error: function (xhr, status, error) {
                            toggleLoadingScreenOverlay()
                            console.log(xhr);
                            console.log(status);
                            console.log(error);
                            alert("Error communicating with the server.");
                            return null;
                        }
                    });

                } else {
                    toggleLoadingScreenOverlay()
                    alert("Please upload a file first.");
                }
            }
        });
    }
}

function checkEmail() {
    const email_field = document.getElementsByName('classify-email____0')[0];
    let email = email_field.value;
    console.log(email);
    $.get(`${classify_root}/users/getUserFromEmail?email=${email}`, function(data, status) {
        let response = JSON.stringify(data);
        response = JSON.parse(response)
        const res_element = document.getElementById('response');

        if (!response.success) {
            res_element.innerText = `${email} is not registered with CLASSify. Use the collaboration request below to
            request access.`;
        }
        else {
            if(response.accepted_terms) {
                res_element.innerHTML = `${email} is registered with CLASSify. You have agreed to the site's 
                usage terms. You may proceed.`;
            }
            else {
                res_element.innerHTML = `${email} is registered with CLASSify. You have not agreed to the site's 
                usage terms. To do so, navigate to <a href="${classify_root}/" target="_blank">CLASSify</a> to do so.`;
            }
        }
    })
}

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

function toggleLoadingScreenOverlay() {
    if ($('#cover-spin').is(':visible')){
        $('#cover-spin').hide();
    } else {
        $('#cover-spin').show();
    }
}

function classifyRedirect() {
    window.open(`${classify_root}/result`, "_blank");
}

function close_modal() {
    $('#columnsModal').modal('hide');

    if (!uploaded_to_clearml) {
        $.ajax({
            url: `${classify_root}/reports/delete`,
            type: 'POST',
            data: {
                'uuid': reportUUID,
                'filename': filename
            },
            success: function(data) {
                $.ajax({
                    url: `${classify_api}/delete_dataset`,
                    type: 'POST',
                    data: JSON.stringify({
                        'filename': filename,
                        'uuid': userUUID
                    }),
                    contentType: 'application/json; charset=utf-8',
                    success: function(data) {
                        if (data.success) {
                            $.ajax({
                                url: `${classify_root}/actions/update_action`,
                                method: 'POST',
                                data: {
                                    'report_uuid': reportUUID,
                                    'user_uuid': userUUID,
                                    'action': 'Deleted report',
                                    'session_id': 'REDCap Upload',
                                    'api_key': '7217be72-156e-4bda-9798-d7d6c8fc59da'
                                },
                                success: function(res) {
                                    if (res.success) {
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
            error: function (xhr, status, error) {
                console.log("Error communicating with the server.");
                return null;
            }
        });
    }
}
