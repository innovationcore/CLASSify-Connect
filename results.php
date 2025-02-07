<?php
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h4">Data - <span class="text-muted">Results</span></h1>
        <div class="spinner-border" role="status" id="spinner" style="display:none;">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table id="collection" class="table table-bordered dt-responsive responsive-text" style="width:100%">
                <thead>
                <tr>
                    <th style="text-align: center;">Filename</th>
                    <th style="text-align: center;">Date Added</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th style="text-align: center;">Filename</th>
                    <th style="text-align: center;">Date Added</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        var collection = {};
        var collectionTable = $('#collection');
        var collectionDataTable = null;

        $(function() {
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            $.ajax({
                url: '<?= $rootURL?>/users/getUser',
                method: 'get',
                success: function(data) {
                    user_uuid = data.user.id;
                    collectionDataTable = collectionTable.DataTable({
                        serverSide: true,
                        processing: true,
                        ajax: {
                            url: "<?= $rootURL ?>/reports/list/".concat(user_uuid)
                        },
                        order: [[ 1, "desc" ]],
                        responsive: true,
                        dom: 'Bfrtip',
                        buttons: [
                            'pageLength', 'colvis'
                        ],
                        columnDefs: [
                            {
                                className: "dt-center",
                                targets: [0, 1, 2, 3]
                            },
                            {
                                orderable: true,
                                targets: [0, 1, 2]
                            }
                        ],
                        language: {
                            emptyTable: "No data have been added"
                        },
                        pagingType: "full_numbers",
                        columns: [
                            {
                                data: 'filename',
                            },
                            {
                                data: 'dateAdded',
                            },
                            {
                                data: 'status',
                            },
                            {
                                data: null,
                                render: function(data) {
                                    if(data.status === "Processed"){
                                        return `<a href="<?= $rootURL ?>/result/${data.uuid}" class="btn btn-primary" data-toggle="tooltip" data-placement="left" title="View Results">
                                                    View Results <i class="fas fa-file-alt"></i>
                                                </a>
                                                <button onclick="rerun_data('${data.uuid}', '${data.filename}');" class="btn btn-secondary" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa-solid fa-arrow-rotate-left"></i> Re-Run Data
                                                </button>
                                                <button onclick="delete_data('${data.uuid}', '${data.filename}', false);" class="btn btn-danger" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa fa-trash"></i> Delete Results
                                                </button>`;
                                    } else if (data.status === "Uploaded") {
                                        return `<a href="<?= $rootURL ?>/reports/prepare/${data.uuid}" class="btn btn-primary" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa fa-file"></i> Prepare Dataset
                                                </a>
                                                <button onclick="delete_data('${data.uuid}', '${data.filename}', false);" class="btn btn-danger" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa fa-trash"></i> Delete Dataset
                                                </button>`;
                                    } else if (data.status === "Preview") {
                                        return `<a href="<?= $rootURL ?>/reports/prepare/${data.uuid}" class="btn btn-primary" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa fa-file"></i> Preview Dataset
                                                </a>
                                                <button onclick="delete_data('${data.uuid}', '${data.filename}', false);" class="btn btn-danger" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa fa-trash"></i> Delete Dataset
                                                </button>`;
				    } else {
                                        return `<button onclick="rerun_data('${data.uuid}', '${data.filename}');" class="btn btn-secondary" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa-solid fa-arrow-rotate-left"></i> Re-Run Data
                                                </button>
                                                <button onclick="delete_data('${data.uuid}', '${data.filename}', true);" class="btn btn-danger" data-toggle="tooltip" data-placement="left">
                                                    <i class="fa fa-trash"></i> Delete Dataset
                                                </button>`;
                                    }
                                }
                            },
                        ]
                    });
                },
                error: function(xhr, request, error) {
                    console.log(xhr);
                    showError('User not found.');
                }
            });


        });

        function delete_data(uuid, filename, still_processing) {
            if (still_processing) {
                if (!confirm("Are you sure you want to delete this while processing? There will be no way to recover any data or results.")) {
                    return;
                }
            }
            $.ajax({
                url: '<?= $rootURL ?>/reports/delete',
                type: 'POST',
                data: {
                    'uuid': uuid,
                    'filename': filename
                },
                success: function(data) {
                    if (data.success){
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
                                                        showSuccess(success_message);
                                                        collectionDataTable.ajax.reload();
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
                    }

                },
                error: function (xhr, status, error) {
                    showError("Error communicating with the server.");
                    return null;
                }
            });
        }

        function rerun_data(report_uuid, filename) {
            $('#spinner').show();
            $.ajax({
                url: '<?= $rootURL ?>/users/getUser', //Get user uuid
                method: 'get',
                success: function(data) {
                    let user_uuid = data.user.id;
                    $.ajax({
                        url: '<?= $api_url ?>/copy_dataset', //Copy dataset in s3
                        type: 'POST',
                        data: JSON.stringify({
                            'filename': filename,
                            'uuid': user_uuid
                        }),
                        contentType: 'application/json; charset=utf-8',
                        success: function(data) {
                            if (data.success){
                                let new_filename = data.message;
                                //new_filename = new_filename.substring(0, new_filename.lastIndexOf('_')) + '.csv'
                                $.ajax({
                                    url: '<?= $rootURL ?>/reports/submit-rerun', //Submit new dataset to postgres
                                    method: 'post',
                                    data: {
                                        'new_filename': new_filename,
                                        'old_filename': filename,
                                        'user_uuid': user_uuid
                                    },
                                    success: function(data) {
                                        let new_report_uuid = data.report_uuid
                                        $.ajax({
                                            url: '<?= $rootURL ?>/actions/update_action', //Create action for duplication
                                            method: 'POST',
                                            data: {
                                                'report_uuid': report_uuid,
                                                'user_uuid': user_uuid,
                                                'action': 'Duplicated this dataset'
                                            },
                                            success: function(res) {
                                                if (res.success) {
                                                    $.ajax({
                                                        url: '<?= $rootURL ?>/actions/update_action', //Create action for new dataset creation
                                                        method: 'POST',
                                                        data: {
                                                            'report_uuid': new_report_uuid,
                                                            'user_uuid': user_uuid,
                                                            'action': 'Created from duplication'
                                                        },
                                                        success: function(res) {
                                                            if (res.success) {
                                                                $.ajax({
                                                                    url: '<?= $rootURL ?>/reports/get-task_id', //Get task ID of original job to get parameters
                                                                    type: 'get',
                                                                    data: {
                                                                        'uuid': report_uuid
                                                                    },
                                                                    success: function(data) {
                                                                        let task_id = data.task_id;
                                                                        sessionStorage.setItem('task_id', task_id);
                                                                        $('#spinner').hide();
                                                                        window.location.href = "<?= $rootURL ?>/reports/prepare/".concat(new_report_uuid); //Open parameter page
                                                                    },
                                                                    error: function(xhr, request, error) {
                                                                        $('#spinner').hide();
                                                                        showError('Error getting task id.');
                                                                    }
                                                                });
                                                            }
                                                        },
                                                        error: function(xhr, ajaxOptions, thrownError) {
                                                            showError('Error communicating with the server');
                                                        }
                                                    });
                                                }
                                            },
                                            error: function(xhr, ajaxOptions, thrownError) {
                                                showError('Error communicating with the server');
                                            }
                                        });
                                    },
                                    error: function(xhr, request, error) {
                                        $('#spinner').hide();
                                        showError('Error getting user data.');
                                    }
                                });
                            } else {
                                $('#spinner').hide();
                                showError(data.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            $('#spinner').hide();
                            showError("Error communicating with the server.");
                            return null;
                        }
                    });
                },
                error: function(xhr, request, error) {
                    $('#spinner').hide();
                    showError('Error getting user data.');
                }
            });
        }


    </script>
<?php
