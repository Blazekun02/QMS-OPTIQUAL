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

    const url = ''; // URL of the PDF file
    function loadPDF(url) {
    pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
        pdfDoc = pdfDoc_;
        document.getElementById('pageCount').textContent = pdfDoc.numPages;
        renderPage(pageNum);
        console.log('PDF URL:', url); // Debugging
    });
}

// Expose loadPDF globally
window.loadPDF = loadPDF;
   



    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 2,
        canvas = document.getElementById('pdfViewer'),
        ctx = canvas.getContext('2d');

    // Load the PDF
    function loadPDF(url) {
    pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
        pdfDoc = pdfDoc_;
        document.getElementById('pageCount').textContent = pdfDoc.numPages;
        renderPage(pageNum);
    });
}
    

    // Render the page
    function renderPage(num) {
        pageRendering = true;
        pdfDoc.getPage(num).then(page => {
            const viewport = page.getViewport({ scale: scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport,
            };
            const renderTask = page.render(renderContext);

            renderTask.promise.then(() => {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        document.getElementById('pageNum').textContent = num;
    }

    // Queue a page for rendering
    function queueRenderPage(num) {
        if (pageRendering) {
            pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    // Show previous page
    document.getElementById('prevPage').addEventListener('click', () => {
        if (pageNum <= 1) {
            return;
        }
        pageNum--;
        queueRenderPage(pageNum);

        // Reset scroll position to the top of the container
        const pdfContainer = document.getElementById('pdf_container');
        pdfContainer.scrollTop = 0;
    });

    // Show next page
    document.getElementById('nextPage').addEventListener('click', () => {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);

        // Reset scroll position to the top of the container
        const pdfContainer = document.getElementById('pdf_container');
        pdfContainer.scrollTop = 0;
    });

    // Zoom functionality
    const zoomSlider = document.getElementById('zoomSlider');

    // Update zoom level when the slider value changes
    zoomSlider.addEventListener('input', () => {
        scale = parseFloat(zoomSlider.value); // Get the slider value
        document.getElementById('zoom_percentage').textContent = `${Math.round(scale * 100)}%`;

        // Apply scaling to the canvas
        const canvas = document.getElementById('pdfViewer');
        canvas.style.transform = `scale(${scale})`;
    });
</script>