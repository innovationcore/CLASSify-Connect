<?php
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h4">Data - <span class="text-muted">Output Log</span></h1>
    </div>
    <div id="output-log">
    </div>


<script>

    $(document).ready(function(){
        let currentURL = window.location.href;
        let parts = currentURL.split('/');
        let uuid = parts[parts.length - 1];
        $.ajax({
            url: '<?= $rootURL ?>/result/get-output-log',
            method: 'get',
            dataType: 'json',
            data: {'uuid':uuid},
            success: function(data) {
                let output = document.getElementById("output-log");
                output.innerHTML = data.file;
                output.style.fontSize = '18px';
            },
            error: function(xhr, request, error) {
                showError('Error retrieving logs.');
                console.log(xhr);
            }
        });
    });
</script>