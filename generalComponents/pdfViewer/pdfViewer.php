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
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f4f4f4;
    }
    #pdf_container {
        width: 80%;
        height: 90vh;
        border: 1px solid #ccc;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        overflow: auto;
        position: relative;
    }
    canvas {
        display: block;
        margin: 0 auto;
    }
    .controls {
        margin-bottom: 10px;
    }
    .controls button {
        margin: 0 5px;
        padding: 5px 10px;
        font-size: 16px;
        cursor: pointer;
    }
</style>

<div class="controls">
    <button id="prevPage">Previous</button>
    <span>Page: <span id="pageNum">1</span> / <span id="pageCount">0</span></span>
    <button id="nextPage">Next</button>
</div>
<div id="pdf_container">
    <canvas id="pdfViewer"></canvas>
</div>

<script>
    const url = '/qms_optiqual/files/RIZ_GCODE.pdf'; // Replace with the path to your PDF file

    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.5,
        canvas = document.getElementById('pdfViewer'),
        ctx = canvas.getContext('2d');

    // Load the PDF
    pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
        pdfDoc = pdfDoc_;
        document.getElementById('pageCount').textContent = pdfDoc.numPages;
        renderPage(pageNum);
    });

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
    });

    // Show next page
    document.getElementById('nextPage').addEventListener('click', () => {
        if (pageNum >= pdfDoc.numPages) {
            return;
        }
        pageNum++;
        queueRenderPage(pageNum);
    });
</script>