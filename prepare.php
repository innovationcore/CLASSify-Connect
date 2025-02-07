<?php
/** @var UserSession $userSession */
$page = 'prepare';
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h4">Data - <span class="text-muted">Prepare (Hover over options to learn more)</span></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button id="add-testset-btn" type="button" class="btn btn-success mr-2" hidden data-toggle="modal" data-target="#testsetModal">
            <i class="fas fa-plus"></i>
            Add Separate Testset
        </button>
        <button id="reset-parameters" onclick="resetMLOpts()" type="button" class="btn btn-secondary mr-2">Reset to Defaults</button>
        <button id="submit-to-automl" type="button" class="btn btn-primary mr-2">Submit for Training</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <form id="optionsForML">
            <div id="ml-opts">

            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="testsetModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testsetModalLabel">Upload Testset</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="testsetModalBody">
                <p>Ensure that the testset follows the same format as your original dataset. The same column drops and transformations (if any) will be applied here.</p>
                    <div class="col-lg-12 mb-3 form-floating">
                        <div class="custom-file" id="testsetFile">
                            <input type="file" class="form-control custom-file-input" accept=".csv" id="uploadTestsetFile" aria-describedby="fileHelp">
                            <label class="form-control custom-file-label" for="uploadTestsetFile" id="uploadTestsetFileLabel">
                                Select file...
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitTestsetBtn">Upload</button>
            </div>
        </div>
    </div>
</div>
<div id="cover-spin"></div>

<script type="text/javascript">

    var user_uuid = null;
    var testset_uploaded = false;
    $(function() {
        let task_id = sessionStorage.getItem('task_id');
        sessionStorage.removeItem('task_id');
        $.ajax({
            url : '<?= $api_url ?>/get-ml-options',
            type : 'GET',
            success : function(data) {
                parseMLOpts(data);
                if (task_id !== null && task_id !== 'undefined') {
                    setOldParameters(task_id);
                }
                $("#synthesize_original").change(function() {
                    if ($(this).is(":checked")) {
                        $("#synthesize_model").removeAttr('disabled');
                    } else {
                        $("#synthesize_model").attr('disabled', 'disabled');
                    }
                });
                $("#synthesize_new").change(function() {
                    if ($(this).is(":checked")) {
                        $("#synthesize_model").removeAttr('disabled');
                    } else {
                        $("#synthesize_model").attr('disabled', 'disabled');
                    }
                });
                $("#parameter_tune").change(function() {
                    if ($(this).is(":checked")) {
                        $('.parameter').each(function() {
                            $(this).removeAttr('disabled');
                        });
                    } else {
                        $('.parameter').each(function() {
                            $(this).attr('disabled', 'disabled');
                        });
                    }
                });
                $("#separate_testset").change(function() {
                    if ($(this).is(":checked")) {
                        $("#add-testset-btn").removeAttr("hidden");
                    } else {
                        $("#add-testset-btn").attr("hidden", "hidden");
                    }
                });
                $('#comprehensive_feature_combinations').change(function() {
                    if ($(this).is(":checked")) {
                        $("#starting_feature_num").removeAttr('disabled');
                        $("#ending_feature_num").removeAttr('disabled');
                        $("#n_features_loop").removeAttr('disabled');
                    } else {
                        $("#starting_feature_num").attr('disabled', 'disabled');
                        $("#ending_feature_num").attr('disabled', 'disabled');
                        $("#n_features_loop").attr('disabled', 'disabled');
                    }
                });
                $('#shap_feature_explainability').change(function() {
                    if ($(this).is(":checked")) {
                        $("#shap_sample_size").removeAttr('disabled');
                        $("#shap_diagram_features").removeAttr('disabled');
                    } else {
                        $("#shap_sample_size").attr('disabled', 'disabled');
                        $("#shap_diagram_features").attr('disabled', 'disabled');
                    }
                });
                $("#multiclass").change(function() {
                    var to_remove = ['tabpfn', 'xgboost', 'gradientboosting', 'histgradientboosting', 'bagging', 'sgdclassifier']
                    if ($(this).is(":checked")) {
                        $('#train_group option').each(function(){
                            if (to_remove.includes($(this).val())) {
                                $(this).prop("disabled", true);
                                $(this).prop("selected", false);
                            }
                        });
                        $('#train_group').trigger('optionsChanged');
                        $('select').selectpicker('refresh');
                    } else {
                        $('#train_group option').each(function(){
                            if (to_remove.includes($(this).val())) {
                                $(this).prop("disabled", false);
                            }
                        });
                        $('select').selectpicker('refresh');
                    }
                });
                $("#synthesize_model").attr('disabled', 'disabled');
                $("#starting_feature_num").attr('disabled', 'disabled');
                $("#ending_feature_num").attr('disabled', 'disabled');
                $("#n_features_loop").attr('disabled', 'disabled');
            },
            error : function(request,error) {
                console.error("Request: "+JSON.stringify(request));
            }
        });
        $.ajax({
            url: '<?= $rootURL ?>/users/getUser',
            method: 'get',
            success: function(data) {
                user_uuid = data.user.id;
            },
            error : function(request,error) {
                console.error("Request: "+JSON.stringify(request));
            }
        });
    }); //document ready

    function setOldParameters(task_id) {
        $.ajax({
            url: '<?= $api_url ?>/get_parameters',
            type: 'POST',
            data: JSON.stringify({
                'task_id': task_id
            }),
            contentType: 'application/json; charset=utf-8',
            success: function(data) {
                let parameters = data.parameters;
                let parameter_object = JSON.parse(parameters);
                for (let key in parameter_object) {
                    if (key == 'train_group') {
                        let train_group = parameter_object[key].replace(/'/g, '"');
                        train_group = JSON.parse(train_group);
                        $('#'+key).val(train_group);
                    }
                    else if (parameter_object[key] == 'True') {
                        $('#'+key).prop('checked', true).trigger('change');
                    }
                    else if (parameter_object[key] == 'False'){
                        $('#'+key).prop('checked', false).trigger('change');
                    }
                    else {
                        $('#'+key).val(parameter_object[key]);
                    }
                }
                $('select').selectpicker('refresh');
                change_blocked()
            },
            error: function (xhr, status, error) {
                showError("Error communicating with the server.");
                return null;
            }
        });
    }

    function change_blocked() {
        let models = $('.selectpicker').val();
        let ignore = ['synthesize_model', 'starting_feature_num', 'ending_feature_num', 'n_features_loop']
        $('.toggle-parameter').each(function() {
            let hasClass = 0;
            models.forEach((model) => {
                if ($(this).hasClass(model)) {
                    hasClass = 1;
                }
            });
            if (!ignore.includes($(this).attr('id'))) {
                if (hasClass == 0) {
                    $(this).attr('disabled', 'disabled');
                }
                else {
                    $(this).removeAttr('disabled');
                }
            }

        });
    }

    $("body").on('change optionsChanged','#train_group',function() {
        change_blocked()
    });

    function toggleLoadingScreenOverlay() {
        if ($('#cover-spin').is(':visible')){
            $('#cover-spin').hide();
        } else {
            $('#cover-spin').show();
        }

    }

    $('#uploadTestsetFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $('#uploadTestsetFileLabel').html(fileName);
    });

    $('#testsetModal').on('hidden.bs.modal', function () {
        $('#uploadTestsetFile').val('');
        $('#uploadTestsetFileLabel').html('Select file...');
    });


    $('#submitTestsetBtn').click(function(){
        let uploadFile = $('#uploadTestsetFile');
        if (uploadFile.val() === null || uploadFile.val() === '') {
            showError('Please upload a .csv file');
            return;
        }
        var file = uploadFile[0].files[0];
        if (file === undefined) {
            showError('Unknown file error encountered');
            return;
        }
        let trainingFile = '<?= $filename; ?>';
        let trainingFileName = trainingFile.slice(0, -4)
        let testsetName = trainingFileName.concat('_'+user_uuid+'_testset.csv');
        toggleLoadingScreenOverlay();
        var form_data = new FormData();
        form_data.append('file', file);
        form_data.append('filename', testsetName)
        // get filename from uploaded file
        let original_filename = testsetName.substring(0, testsetName.lastIndexOf('_')) + '.csv';
        $.ajax({
            url: '<?= $rootURL ?>/reports/get-column_changes',
            type: 'get',
            data: {
                'filename': original_filename
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
                        toggleLoadingScreenOverlay();
                        if (res.success) {
                            $.ajax({
                                url: '<?= $rootURL ?>/actions/update_action',
                                method: 'POST',
                                data: {
                                    'filename': original_filename,
                                    'user_uuid': user_uuid,
                                    'action': 'Uploaded separate testset'
                                },
                                success: function(resp) {
                                    if (res.message === 'File uploaded. Retesting...') {
                                        showSuccess('Test set uploaded');
                                    } else {
                                        showSuccess(res.message);
                                    }
                                    testset_uploaded = true;
                                    $('#testsetModal').modal('hide');
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    showError('Error communicating with the server');
                                }
                            });
                        } else {
                            showError(res.message);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        toggleLoadingScreenOverlay();
                        showError('Error communicating with the server');
                    }
                });
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError('Error communicating with the server');
            }
        });
    }); // upload testset


    function getTrainedModels(name, array) {
        return array
            .filter(pair => pair.name === name)  // Filter pairs with the given name
            .map(pair => pair.value);            // Extract only the values
    }

    $('#submit-to-automl').click(function() {
        if(confirm("Are you sure you want to submit this data for processing?")){
            let form = $('#optionsForML').serializeArray();
            $('.form-check-input').each(function(index, element) {
                if (!$(element).is(':checked')) {
                    form.push({
                        'name': $(element).attr('name'),
                        'value': "False"
                    });
                }
            });

            if (!form.some(item => item.name === "train_group")) {
                showError("Must select at least one model to train");
                return;
            }

            let separate_testset = form.find(item => item.name === 'separate_testset');
            if (separate_testset['value'] === 'True' && testset_uploaded === false) {
                showError("Please upload a testset or deselect the 'separate testset' option.");
                return;
            }

            let currentFile = '<?= $filename; ?>';


            showSuccess("Starting training of: " + currentFile + ". This will take some time, feel free to navigate away from this page.");
            currentFile = currentFile.replace('.csv', '_'+user_uuid+'.csv')
            $.ajax({
                url: '<?= $api_url ?>/train',
                type: 'POST',
                data: JSON.stringify({
                    'filename': currentFile,
                    'options': form
                }),
                contentType: 'application/json; charset=utf-8',
                success: function(data) {
                    let task_id = data.task_id;
                    $.ajax({
                        url: '<?= $rootURL ?>/reports/set-task_id',
                        method: 'post',
                        data: {
                            'task_id':task_id,
                            'filename':currentFile,
                        },
                        success: function(data) {
                            let trained_models_array = getTrainedModels('train_group', form);
                            //let trained_models = trained_models_array.join(', ')
                            let trained_models = JSON.stringify({models: trained_models_array})
                            $.ajax({
                                url: '<?= $rootURL ?>/actions/update_action',
                                method: 'POST',
                                data: {
                                    'filename': currentFile,
                                    'user_uuid': user_uuid,
                                    'action': 'Submitted training job',
                                    'addl_info': trained_models
                                },
                                success: function(res) {
                                    if (res.success) {
                                        window.location.href = "<?= $rootURL ?>/result";
                                    }
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    showError('Error communicating with the server');
                                }
                            });
                        },
                        error : function(request,error) {
                            console.error("Request: "+JSON.stringify(request));
                        }
                    });
                },
                error: function (xhr, status, error) {
                    showError("Error communicating with the server.");
                    return null;
                }
            });
        }
    });

    function resetMLOpts(){
        $.ajax({
            url : '<?= $api_url ?>/get-ml-options',
            type : 'GET',
            success : function(data) {
                for(let key in data){
                    if (data[key]['default'] == true || data[key]['default'] == false) {
                        $('#'+key).prop('checked', data[key]['default']).trigger('change');
                    }
                    else {
                        $('#'+key).val(data[key]['default']);
                    }
                }
                change_blocked()
                $('select').selectpicker('refresh');
            },
            error : function(request,error) {
                console.error("Request: "+JSON.stringify(request));
            }
        });
    }

    function parseMLOpts(data){
        let toAppendNumber = "";
        let toAppendList = "";
        let toAppendBool = "";
        let toggle = 0;
        let first = 1;
        let toggle_checkbox = 0;
        for(let key in data){
            let classes = data[key]['models'].join(' ');
            let display_name = key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            if (data[key]['type'] == 'int' || data[key]['type'] == 'float' || data[key]['type'] == 'str'){
                if (first == 1) {
                    toAppendNumber += '<h5 class="border-top-param">General Parameters</h5>';
                    first = 0;
                }
                if (key=='n_estimators_start') {
                    toAppendNumber += '</div><h5 class="border-top-param">Model Parameters</h5>';
                    toggle = 0;
                }
                if (toggle == 0) {
                    if (key=='synthesize_model') {
                        toAppendNumber += '<div class="row border-bottom-param">';
                    } else {
                        toAppendNumber += '<div class="row">';
                    }
                }
                toAppendNumber += `<div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div title="${data[key]['help']}" class="input-group-prepend">
                                                <span class="input-group-text"">${display_name}</span>
                                            </div>
                                            <input type="text" class="form-control toggle-parameter ${classes}" id="${key}" name="${key}" value="${data[key]['default']}" aria-describedby="${data[key]['help']}">
                                        </div>
                                    </div>`;
                if (toggle == 0) {
                    toggle = 1;
                } else{
                    toAppendNumber += '</div>';
                    toggle = 0;
                }

            } else if (data[key]['type'] == "list"){
                toAppendList += `<div class="input-group mb-3">
                                    <div title="${data[key]['help']}" class="input-group-prepend">
                                        <span class="input-group-text"">${display_name}</span>
                                    </div>
                                    <select name="${key}" id="${key}" class="ml-1 selectpicker ${classes}" multiple data-actions-box="true">`;
                    for (let i = 0; i < data[key]['default'].length; i++) {
                        toAppendList += `<option value="${data[key]['default'][i]}">${data[key]['default'][i]}</option>`;
                    }
                    toAppendList += `</select></div>`;
            } else if (data[key]['type'] == 'bool'){
                if (toggle_checkbox == 0) {
                    toAppendBool += '<div class="row">';
                }
                if (data[key]['default']) {
                    toAppendBool += `<div class="col-md-6">
                                        <div class="form-check">
                                            <label class="form-check-label mb-3" title="${data[key]['help']}">
                                                <input id="${key}" name="${key}" type="checkbox" class="form-check-input ${classes}" value="True" checked>${display_name}
                                            </label>
                                        </div>
                                    </div>`;
                } else {
                    toAppendBool += `<div class="col-md-6">
                                        <div class="form-check">
                                            <label class="form-check-label mb-3" title="${data[key]['help']}">
                                                <input id="${key}" name="${key}" type="checkbox" class="form-check-input ${classes}" value="True">${display_name}
                                            </label>
                                        </div>
                                    </div>`;
                }
                if (toggle_checkbox == 0) {
                    toggle_checkbox = 1;
                } else{
                    toAppendBool += '</div>';
                    toggle_checkbox = 0;
                }
            }
        }
        $('#ml-opts').append(toAppendBool);
        $('#ml-opts').append(toAppendList);
        $('#ml-opts').append(toAppendNumber);

        $('select').selectpicker('refresh');
        $('select').selectpicker('selectAll');
    }

</script>
