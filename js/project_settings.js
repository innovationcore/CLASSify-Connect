const classify_root = 'https://data.ai.uky.edu/classify';
const classify_api = 'https://data.ai.uky.edu/classify/api';

function handleUpload() {
    document.getElementById('columnsModal').innerHTML = `<div class="modal-dialog" role="document">
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
                    <a href="${classify_root}/result" id="gotoMLOpts" target="_blank" type="button" class="btn btn-primary mr-2" style="display:none;">
                        <i class="fa fa-eye"></i> View Uploaded Data
                    </a>
                </div>
            </div>
        </div>`;


    // Parse the CSV with the classifier field
    const parsed = parseCSVWithNewNames(moduleData, classifier[0]);

    // Get email field from input
    const email = document.getElementsByName('classify-email____0')[0].value;

    $.ajax({
        url: `${classify_root}/users/getUserFromEmail?email=${email}`,
        method: 'get',
        success: function(data) {
            const user_uuid = data.user_id;

            // Create form data object
            var form_data = new FormData();

            // Create a Blob from the parsed CSV string
            const csvBlob = new Blob([parsed], { type: 'text/csv' });

            // Define or fallback to a default filename
            var currentFile = filename; // Fallback if filename is not defined

            // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
            currentFile = currentFile.endsWith('.csv') ? currentFile : currentFile + '.csv';

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
                                    console.log(res.report_uuid);
                                    //let fname = res.file_name;
                                    $.ajax({
                                        url: `${classify_root}/actions/update_action`,
                                        method: 'POST',
                                        data: {
                                            'report_uuid': res.report_uuid,
                                            'user_uuid': user_uuid,
                                            'action': 'Uploaded dataset'
                                        },
                                        success: function (res) {
                                            console.log(res);
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
                                        }
                                    });

                                } else {
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

    function toggleLoadingScreenOverlay() {
        if ($('#cover-spin').is(':visible')){
            $('#cover-spin').hide();
        } else {
            $('#cover-spin').show();
        }
    }


    // Get the user UUID from the email
    /*$.get(`${classify_root}/users/getUserFromEmail?email=${email}`, function(data, status) {
        const user_uuid = data.user_id;

        // Create form data object
        var form_data = new FormData();

        // Create a Blob from the parsed CSV string
        const csvBlob = new Blob([parsed], { type: 'text/csv' });

        // Define or fallback to a default filename
        var currentFile = filename; // Fallback if filename is not defined

        // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
        currentFile = currentFile.endsWith('.csv') ? currentFile : currentFile + '.csv';

        // Append the Blob and other fields to the form data
        form_data.append('file', csvBlob, currentFile);
        form_data.append('user_uuid', user_uuid);

        $.ajax({
            url: s3_url,
            type: 'POST',
            data: form_data,
            processData: false,  // Don't process the files
            contentType: false,  // Let jQuery set the content type
            success: function (response) {
                const response_div = document.getElementById('upload-result');

                if (response.success) {
                    console.log('File successfully uploaded to s3.', response);
                    console.log(response.data_types);
                    $('#columnsModal').modal('show');
                    showColumns(response.data_types);
                    $('#submit-to-automl').click(function() {
                        if(confirm("Are you sure you want to submit this data for processing?")){
                            $('#spinner').show();
                            //let form = $('#column_names').serializeArray();
                            let form = [];
                            $('.form-check-input').each(function(index, element) {
                                if (element.id !== 'class') {
                                    if ($(element).attr('type') == 'checkbox') {
                                        if ($(element).is(':checked')) {
                                            let checked_type = document.querySelector('input[name="'+element.id+'"]:checked').id;
                                            let type = checked_type.substring(checked_type.lastIndexOf('-')+1);
                                            form.push({column:element.id, data_type:type, checked:true})
                                        } else {
                                            form.push({column:element.id, data_type:'none', checked:false}) //If dropped column, update actions
                                            $.ajax({
                                                url: '${classify_root}/actions/update_action',
                                                method: 'POST',
                                                data: {
                                                    'filename': currentFile,
                                                    'user_uuid': user_uuid,
                                                    'action': 'Dropped column '+element.id
                                                },
                                                crossDomain: true,
                                                success: function(res) {
                                                },
                                                error: function(xhr, ajaxOptions, thrownError) {
                                                    console.log('Error communicating with the server');
                                                }
                                            });
                                        }

                                    }
                                }
                            });
                            if (currentFile !== null) {
                                $.ajax({
                                    url: change_columns,
                                    type: 'POST',
                                    data: JSON.stringify({
                                        'filename': currentFile.replace('.csv', `_${user_uuid}.csv`),
                                        'data_types': JSON.stringify(form)
                                    }),
                                    contentType: 'application/json; charset=utf-8',
                                    success: function(data) {
                                        if (data.success == false) {
                                            $('#spinner').hide();
                                            //console.log(data.message);
                                            return null;
                                        }
                                        else {
                                            console.log(data.message);
                                            $('#gotoMLOpts').show();
                                            $('#submit-to-automl').hide();
                                            //uploaded_to_clearml=true;
                                            $('#spinner').hide();
                                            // Send the form data via a POST request
                                            $.ajax({
                                                url: upload_url,
                                                type: 'POST',
                                                data: form_data,
                                                processData: false,  // Don't process the files
                                                contentType: false,  // Let jQuery set the content type
                                                success: function(response) {
                                                    const response_div = document.getElementById('upload-result');

                                                    if (response.success) {
                                                        console.log('File successfully uploaded', response);
                                                    }
                                                    else {
                                                        console.log(response.message);
                                                    }

                                                },
                                                error: function(jqXHR, textStatus, errorThrown) {
                                                    console.error('Error uploading file:', textStatus, errorThrown);
                                                }
                                            });
                                        }

                                    },
                                    error: function (xhr, status, error) {
                                        console.log("Error communicating with the server.");
                                        return null;
                                    }
                                });

                            } else {
                                console.log("Please upload a file first.");
                            }
                        }
                    });
                } else {
                    console.log('Error uploading file to s3.', response);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error uploading file:', textStatus, errorThrown);
            }
        });
    });*/
}

/*function showColumns(data_types) {
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
                                        </div>
                                    </div>`;
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
}*/

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

    // Replace `?` with `0` and filter rows with a blank in the "class" column
    const updatedRows = lines.slice(1)
        .map(row => row.split(',').map(value => value.trim() === '?' ? '0' : value.trim()))
        .filter(row => row[classIndex] !== '');

    return [headers.join(','), ...updatedRows.map(row => row.join(','))].join('\n');
}

function classifyRedirect() {
    window.open(`${classify_root}/result`, "_blank");
}
