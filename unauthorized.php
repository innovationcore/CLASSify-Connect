<?php
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
?>
<div class="container">
    <h1>Sorry folks, your account is forbidden.</h1>
    <p>The moose out front shoulda told ya.</p>
    <p>If you would like access to this tool, submit a request <a href="https://redcap.uky.edu/redcap/surveys/?s=K7WTCDH37AXLEKNM">here</a>.</p>
    <small>Or reach out to <a href="mailto:ai@uky.edu">ai@uky.edu</a> if you have any questions.</small>
</div>

<style>
    @import url('https://fonts.googleapis.com/css?family=Chicle');
    html, body {
        margin: 0;
        height: 100vh;
        font-family: 'Chicle', cursive;
    }
    body {
        background: url('<?= $rootURL ?>/img/lampoon.jpg');
        background-size: cover;
        background-position: 50% 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .container {
        background: rgba(255,255,255,0.8);
        padding: 1em 2em;
        font-size: 1.5em;
        text-align: center;
        margin-top: 30vh;
    }
    h1 {
        color: #e60000;
    }

</style>