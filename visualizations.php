<?php
$page = 'results-visualizations';
$rootURL = "https://data.ai.uky.edu/classify";
$apiURL = "https://data.ai.uky.edu/classify/api";
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h4">Data - <span class="text-muted">Visualizations</span></h1>
        <button onclick="download_image();" id="download-image" type="button" class="btn btn-primary mr-2"><i class="fa fa-download"></i> Download Image</button>
    </div>
    <div class="slideshow-container" id="slideshow-slides">
        <a class="prev_viz" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next_viz" onclick="plusSlides(1)">&#10095;</a>
    </div>
    <br>


<script>
    let slideIndex = 1;
    $(document).ready(function(){
        let currentURL = window.location.href;
        let parts = currentURL.split('/');
        let uuid = parts[parts.length - 1];
        $.ajax({
            url: '<?= $rootURL ?>/result/get-image-names',
            method: 'get',
            dataType: 'json',
            data: {'uuid':uuid},
            success: function(data) {
                for (const key in data.files) {
                    if (data.files.hasOwnProperty(key)) {
                        let image_name = data.files[key];
                        let newDiv = $('<div>');
                        newDiv.addClass('slideshow-slide');
                        let newImg = $('<img>');
                        image_path = '../../visualizations/'.concat(image_name);
                        newImg.attr('src', image_path);
                        newImg.attr('alt', 'Visualization');
                        newDiv.append(newImg);
                        $('#slideshow-slides').append(newDiv);
                    }
                }
            },
            error: function(xhr, request, error) {
                showError('Error retrieving images.');

            }
        });
        setTimeout(function() { //Need to delay to allow pictures to load
          showSlides(slideIndex);
        }, 700);
        //showSlides(slideIndex);
    });

    function download_image() {
        var currentImageDiv = $('#slideshow-slides').find(':visible');
        var currentImage = currentImageDiv.children(); //Get the current image
        var imagePath = currentImage.attr('src');
        imagePath = imagePath.substring(4);
        $.ajax({
            url: '<?= $rootURL ?>/reports/download-image',
            method: 'post',
            //dataType: 'text',
            data: {'filename':imagePath},
            success: function(data) {
                if (!data.includes("File not found!")) { //If the file exists, download it

                    let slashSegments = imagePath.split('/'); //Remove the directory from the filename
                    let filename = slashSegments[slashSegments.length - 1]
                    let segments = filename.split('_');
                    if (imagePath.includes('SHAP')) {
                        segments.splice(segments.length - 3, 1);
                    }
                    else {
                        segments.splice(segments.length - 2, 1);
                    }

                    let new_filename = segments.join('_');

                    let binaryData = atob(data);
                    let arrayBuffer = new ArrayBuffer(binaryData.length);
                    let uint8Array = new Uint8Array(arrayBuffer);
                    for (var i = 0; i < binaryData.length; i++) {
                        uint8Array[i] = binaryData.charCodeAt(i);
                    }
                    let blob = new Blob([uint8Array], { type: "image/png" });
                    let link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = new_filename;

                    link.click();
                }
                else {
                    showError('File not found.');
                }
            },
            error: function(xhr, request, error) {
                showError('Error downloading image.');
                console.log(xhr);
                console.log(request);
                console.log(error);
            }
        });
    }


    // Next/previous controls
    function plusSlides(n) {
      showSlides(slideIndex += n);
    }

    // Thumbnail image controls
    function currentSlide(n) {
      showSlides(slideIndex = n);
    }

    function showSlides(n) {
        let i;
        const slides = document.getElementsByClassName("slideshow-slide");
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slides[slideIndex-1].style.display = "block";
    }
</script>
