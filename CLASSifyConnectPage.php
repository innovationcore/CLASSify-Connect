<?php
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
$page = 'home';
global $rootURL;
echo "ROOT: " . $rootURL;
global $apiURL;
echo "API: " . $apiURL;
?>

<style>
    html { height: 100%; }

body { min-height: 100%; }

#login-user {
    color: white;
}

#user-guide {
    color: white;
}

.admin-item {
    display: none !important;
}

.display-text {
    font-size: 1rem;
}

.screenshot-container {
    text-align: center; /* Centers the image horizontally */
}

.image-helper img {
    width: 60vw;
}

.responsive-text {
    font-size: .7rem;
}

@media (min-width: 576px) {
    .display-text {
        font-size: 1.2rem;
    }

    .responsive-text {
        font-size: .75rem;
    }
}

@media (min-width: 768px) {
    .display-text {
        font-size: 1.3rem;
    }

    .responsive-text {
        font-size: .8rem;
    }
}

@media (min-width: 992px) {
    .display-text {
        font-size: 1.4rem;
    }

    .responsive-text {
        font-size: .9rem;
    }
}

@media (min-width: 1200px) {
    .display-text {
        font-size: 1.5rem;
    }

    .responsive-text {
        font-size: 1rem;
    }
}

/* Tooltip container */
#tooltip {
    visibility: hidden;
    width: 250px;
    background-color: black;
    color: #fff;
    padding: 6px;
    border-radius: 6px;

    /* Position the tooltip text - see examples below! */
    position: absolute;
    z-index: 5000;
}

/*
 * Fixes
 */
.btn-group-xs > .btn, .btn-xs {
  padding: .25rem .4rem;
  font-size: .875rem;
  line-height: .5;
  border-radius: .2rem;
}

table.dataTable tbody td {
    vertical-align: middle;
}

.cell-middle tbody td {
    vertical-align: middle !important;
}

.filled,
.filledKey {
    fill: #8888ff !important;
}

.found,
.foundKey,
.new,
.newKey,
.onShelf,
.changeShelfSelected {
    fill: coral !important;
}


.selected,
.selectedKey {
    fill: #cb0101 !important;
    stroke-width: 2px;
}

#ColorKey {
    max-height: 75px;
}

html,
body {
    height: 100%;
    padding-top: 36px;
}

.container-fluid,
main {
    height: calc(100vh - 72px);
}

body {
    font-size: .875rem;
}

nav {
    background-color: #1a48aa;
}

.navbar {
    background-color: #1a48aa;
    position: fixed;
    width: 100%;
}

.feather {
    width: 16px;
    height: 16px;
    vertical-align: text-bottom;
}

aside {
    /* padding-top: 72px; */
}

@media (min-width: 768px) {
    .navbar-collapse {
        flex-grow: 0;
    }
}

/*
 * Sidebar
 */

.sidebar {
    position: fixed;
    top: 72px;
    bottom: calc(100vh - 72px);
    z-index: 100; /* Behind the navbar */
    padding: 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-menu {
    position: fixed;
    top: 72px;
    bottom: calc(100vh - 72px);
    z-index: 100; /* Behind the navbar */
}

.sidebar ul {
    justify-content: space-between !important;
}

.sidebar ul li a {
    margin-left: 10px;
    margin-right: 10px;
}

@media (min-width: 768px) {
    .sidebar ul {
        justify-content: flex-start !important;
    }
}

.sidebar-sticky {
    position: fixed;
    position: -webkit-sticky !important;
    position: sticky !important;
    z-index: 999;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
}

.sidebar .nav-link i {
    min-width: 18px;
    text-align: center;
}

.sidebar .nav-link.active {
    color: #007bff !important;
}

.sidebar .nav-link:hover .feather,
.sidebar .nav-link.active .feather {
    color: inherit;
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
    cursor: pointer;
}

/*
 * Navbar
 */

.navbar {
    height: 72px;
}

.navbar-short {
    height: 100%;
    /* padding-top: 72px; */
}

.navbar-brand {
    margin-left: .25rem;
    padding-top: .2rem;
    padding-bottom: .2rem;
    padding-left: .5rem;
    padding-right: .75rem;
    font-size: 1.15rem;
    border-radius: 5px;
    min-width: 210px;
    background-image: url(../img/UKHCLogo.svg);
    background-repeat: no-repeat;
    background-position: center;
    background-color: rgba(26, 72, 170, 0.75);
    background-blend-mode: lighten;
    text-align: center;
    text-shadow: 1px 1px #07132c;
}

.navbar .form-control {
    padding: .75rem 1rem;
    border-width: 0;
    border-radius: 0;
}

.form-control-dark {
    color: #fff;
    background-color: rgba(255, 255, 255, .1);
    border-color: rgba(255, 255, 255, .1);
}

.form-control-dark:focus {
    border-color: transparent;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
}

.nav-item a{
    color: #716f6f !important;
}

@media screen and (min-width: 768px) {
    .nav-item {
        margin-left: 10px;
    }
}

.columns-div {
    overflow-y: scroll;
    height: 400px;
    border-top: 0.1rem solid;
}

.bold-label {
    font-weight: bold;
}

.instruction-panel {
    display: none;
    position: absolute;
    top: 0;
    left: 102%;
    right: -120%;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    z-index: 9999; /* Ensure the panel is above other content */
}

/*
 * Utilities
 */

.border-top { border-top: 1px solid #e5e5e5; }
.border-bottom { border-bottom: 1px solid #e5e5e5; }
.border-top-param { border-top: 3px solid #007bff;}

/*
 *  DataTables modifications
 */
th.dt-center, td.dt-center { text-align: center; }
div.dataTables_info {
    padding-left: .5em;
    padding-top: 0.3em !important;
}

/*
 *  Fixes
 */
.modal { overflow: auto !important; }

/*
 *  Collapsable Cards
 */
.card-header .fa {
    transition: .3s transform ease-in-out;
}

a.collapsed, a.d-block,
a.collapsed:hover, a.d-block:hover,
a.collapsed:visited, a.d-block:visited {
    color: black;
    text-decoration: none;
}

/*
 *  Components
 */

#components {
    margin-top: 0;
}

@media screen and (min-width: 768px) {
    #components {
        margin-top: 0;
    }
}

/*
 *  ScrollNav
 */

#scroll-nav {
    display: none;
    padding: 0 0 0 10px;
    margin: 0;
}

#scroll-nav-todo-header {
    position: fixed;
    top: 132px;
}

.scroll-nav-todo {
    position: fixed;
    background-color: #f8f9fa;
    border: 1px solid #343a40!important;
    border-radius: 5px;
    padding: 5px;
    margin-top: 20px;
    max-height: calc(50vh - 85px);
    overflow-y: scroll;
    width: 100%;
}

#scroll-nav-components-header {
    position: fixed;
}

.scroll-nav-components {
    position: fixed;
    background-color: #f8f9fa;
    border: 1px solid #343a40!important;
    border-radius: 5px;
    padding: 5px;
    overflow-y: scroll;
    width: 100%;
}

@media screen and (min-width: 576px) {
    #scroll-nav {
        display: block;
    }

    #scroll-nav-todo-header {
        top: 122px;
    }

    .scroll-nav-todo {
        float: left;
        max-height: calc(50vh - 100px);
        width: 25%;
    }

    #scroll-nav-components-header {
        top: calc(50vh + 60px);
    }

    .scroll-nav-components {
        float: left;
        top: calc(50vh + 90px);
        max-height: calc(50vh - 100px);
        width: 25%;
     }
}

@media screen and (min-width: 768px) {
    #scroll-nav-todo-header {
        top: 85px;
    }

    .scroll-nav-todo {
        max-height: calc(50vh - 80px);
        width: 20%;
    }

    #scroll-nav-components-header {
        top: calc(50vh + 40px);
    }

    .scroll-nav-components {
        top: calc(50vh + 70px);
        max-height: calc(50vh - 80px);
        width: 20%;
    }
}

.scroll-nav-components__list,
.scroll-nav-todo__list {
    margin: 0;
    padding-left: 1.4em;
    list-style-type: none;
}

.scroll-nav-components__item,
.scroll-nav-todo__item {
    margin-bottom: 5px;
}

.scroll-nav-components__item--active,
.scroll-nav-todo__item--active{
    font-weight: 600;
    position: relative;
}
.scroll-nav-components__item--active:before,
.scroll-nav-todo__item--active:before {
    content: '';
    display: block;
    width: 14px;
    height: 14px;
    background: black;
    position: absolute;
    left: -20px;
    top: 2px;
}

.scroll-nav-components__link,
.scroll-nav-todo__link{
    color: #0645ad;
    text-decoration: none;
}

#cover-spin {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(255,255,255,0.7);
    z-index:99999999999;
    display:none;
}

@-webkit-keyframes spin {
	from {-webkit-transform:rotate(0deg);}
	to {-webkit-transform:rotate(360deg);}
}

@keyframes spin {
	from {transform:rotate(0deg);}
	to {transform:rotate(360deg);}
}

#cover-spin::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:black;
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .8s linear infinite;
    animation: spin .8s linear infinite;
}

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

.slideshow-container {
  max-width: 1000px;
  position: relative;
  margin: auto;
}

/* Hide the images by default */
.slideshow-slide {
  display: none;
}

/* Next & previous buttons */
.prev_viz, .next_viz {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  margin-top: -22px;
  padding: 16px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next_viz {
  right: -5%;
  border-radius: 3px 0 0 3px;
}

.prev_viz {
  left: -5%;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev_viz:hover, .next_viz:hover {
  background-color: rgba(0,0,0,0.8);
}

/*.dot {*/
/*  cursor: pointer;*/
/*  height: 15px;*/
/*  width: 15px;*/
/*  margin: 0 2px;*/
/*  background-color: #bbb;*/
/*  border-radius: 50%;*/
/*  display: inline-block;*/
/*  transition: background-color 0.6s ease;*/
/*}*/

/*.active, .dot:hover {*/
/*  background-color: #717171;*/
/*}*/

</style>
<body>
<div class="container-fluid">
    <div class="row">
        <main role="main" class="col-12 col-md-11 bg-faded py-3 flex-grow-1">

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
        const email = <?= json_encode($this->getProjectSetting('classify-email')) ?>;
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
                url: `<?= $rootURL ?>/users/getUserFromEmail?email=${email}`,
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
                    url: `<?= $rootURL ?>/reports/delete`,
                    type: 'POST',
                    data: {
                        'uuid': report_uuid,
                        'filename': filename_no_uuid
                    },
                    success: function(data) {
                        let user_uuid = null;
                        $.ajax({
                            url: `<?= $rootURL?>/users/getUser`,
                            method: 'get',
                            success: function(data) {
                                user_uuid = data.user.id;
                                $.ajax({
                                    url: `<?= $apiURL ?>/delete_dataset`,
                                    type: 'POST',
                                    data: JSON.stringify({
                                        'filename': filename_no_uuid,
                                        'uuid': user_uuid
                                    }),
                                    contentType: 'application/json; charset=utf-8',
                                    success: function(data) {
                                        if (data.success){
                                            $.ajax({
                                                url: `<?= $rootURL ?>/actions/update_action`,
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
                        url: `<?= $apiURL ?>/verify_dataset`,
                        type: 'POST',
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            if (data.success) {
                                $.ajax({
                                    url: `<?= $rootURL ?>/reports/submit`,
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
                                                url: `<?= $rootURL ?>/actions/update_action`,
                                                method: 'POST',
                                                data: {
                                                    'report_uuid': report_uuid,
                                                    'user_uuid': user_uuid,
                                                    'action': 'Uploaded dataset'
                                                },
                                                success: function(res) {
                                                    if (res.success) {
                                                        $.ajax({
                                                            url: `<?= $apiURL ?>/get_column_types`,
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
                        url: `<?= $apiURL ?>/change_column_types`,
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
                                    url: `<?= $rootURL ?>/reports/set-column_changes`,
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
                    url: `<?= $rootURL ?>/reports/delete`,
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
                                        url: `<?= $apiURL ?>/delete_dataset`,
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
                                                    url: `<?= $rootURL ?>/actions/update_action`,
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
    <div class="modal fade" id="termsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Citation Acknowledgement&nbsp;</h5>
                    </div>
                    <div class="modal-body" id="termsModalBody">
                        <p>By using CLASSify and any models trained or data generated, I agree to cite the following paper in any related research or publications: <a href="https://arxiv.org/abs/2310.03618/">CLASSify: A Web-based Tool for Machine Learning</a>. The link to this paper is also available in the User Guide at any time. </p>
                        <p>Proper citation acknowledges the work that went into the development of CLASSify and supports continued development of the tools we provide at CAAI. Thank you for helping us continue to advance AI/ML research.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="decline-terms">Decline</button>
                        <button type="button" class="btn btn-primary" id="accept-terms">Accept</button>
                    </div>
                </div>
            </div>
        </main>
    </div>