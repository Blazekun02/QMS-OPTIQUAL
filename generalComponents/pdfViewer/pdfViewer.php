<?php
//start session
if(!session_id()){
    session_start();
}

//Include filepaths
require_once __DIR__ . '/../../filepaths.php';

//Include set message
require_once genMsg_dir . '/setMessage.php';
?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<style>
    .pdfViewerContainer {
        display: flex; /* Use flexbox for better alignment of child elements */
        flex-direction: column; /* Arrange child elements vertically */
        width: 100%; /* Take up the full width of the parent container */
        height: 95%; /* Take up the full height of the parent container */
        box-sizing: border-box; /* Include padding and border in width/height calculations */
        overflow: hidden; /* Prevent content overflow */
    }

    #pdf_container {
        flex-grow: 1; /* Allow the container to grow and fill available space */
        width: 100%; /* Ensure it spans the full width of the parent */
        height: 100%; /* Ensure it spans the full height of the parent */
        overflow: auto; /* Enable scrolling if the content exceeds the container size */
        position: relative;
    }
    canvas {
        display: block;
        margin: 0 auto;
        transform-origin: top center; /* Ensure scaling happens from the top-left corner */
    }
    .pdf_controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        font-size: 2vh;
    }
    .pdf_controls button {
        width: 6vw;
        padding: 1vh 1vw;
        border: none;
        background-color: #007bff;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .pdf_controls input[type="range"] {
    width: 150px;
    margin: 0 10px;
    -webkit-appearance: none;
    appearance: none;
    height: 5px;
    background: #ddd;
    border-radius: 5px;
    outline: none;
    cursor: pointer;
    }

    .pdf_controls input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 15px;
        height: 15px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .pdf_controls input[type="range"]::-webkit-slider-thumb:hover {
        background: #0056b3;
    }
</style>

<div class="pdfViewerContainer">
    <div class="pdf_controls">
    <button id="prevPage">Previous</button>
    <span>Page: <span id="pageNum">1</span> / <span id="pageCount">0</span></span>
    <input type="range" id="zoomSlider" min=".1" max="3" step="0.1" value="1">
    <span id="zoom_percentage">100%</span>
    <button id="nextPage">Next</button> 
    </div>

    <div id="pdf_container">
    <canvas id="pdfViewer"></canvas>
    </div>
</div>    

    <?php
        $URL = $_POST['URL'] ?? '';
        
    ?>


<script>
    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.5, // Default scale
        canvas = document.getElementById('pdfViewer'),
        ctx = canvas.getContext('2d');

    // Function to load and render a PDF
    function loadPDF(url) {
        // Ensure pdf.js is loaded
        if (typeof pdfjsLib === 'undefined') {
            console.error("pdf.js is not loaded.");
            return;
        }

        console.log("Attempting to load PDF from:", url); // Debugging line

        // Asynchronously load the PDF
        var loadingTask = pdfjsLib.getDocument(url);
        loadingTask.promise.then(function(pdf) {
            console.log('PDF loaded');
            pdfDoc = pdf;
            document.getElementById('pageCount').textContent = pdfDoc.numPages;
            // Reset to the first page whenever a new PDF is loaded
            pageNum = 1; 
            renderPage(pageNum);
        }, function (reason) {
            // PDF loading error
            console.error(reason);
        });
    }

    // Make the function globally accessible
    window.loadPDF = loadPDF;

    // Function to render a specific page
    function renderPage(num) {
        pageRendering = true;
        // Using promise to fetch the page
        pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport({ scale: scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas context
            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);

            // Wait for rendering to finish
            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    // New page rendering is pending
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        // Update page counters
        document.getElementById('pageNum').textContent = num;
    }

    // Function to queue a page for rendering
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    // Event listener for the 'Previous Page' button
    document.getElementById('prevPage').addEventListener('click', function() {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        queueRenderPage(pageNum);
        document.getElementById('pdf_container').scrollTop = 0; // Reset scroll
    });

    // Event listener for the 'Next Page' button
    document.getElementById('nextPage').addEventListener('click', function() {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);
        document.getElementById('pdf_container').scrollTop = 0; // Reset scroll
    });

    // Event listener for the zoom slider
    document.getElementById('zoomSlider').addEventListener('input', function() {
        if (!pdfDoc) {
            return;
        }
        scale = parseFloat(this.value);
        document.getElementById('zoom_percentage').textContent = Math.round(scale * 100) + '%';
        
        // Re-render the page with the new scale
        renderPage(pageNum);
    });

</script>