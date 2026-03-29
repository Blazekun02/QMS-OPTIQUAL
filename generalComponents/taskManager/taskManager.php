<?php 
//start session
if(!session_id()){
    session_start();
}

//include filepaths
require_once __DIR__ . '/../../filepaths.php';

//include set message
require_once genMsg_dir . '/setMessage.php';

// ✨ FIX: SECURE ROLE CHECK ✨
// We must grab the exact roleID from the database to ensure Staff cannot see QAD buttons
require_once BASE_DIR . '/connect.php';
$roleID = 0;
if(isset($_SESSION['accID'])){
    $accID = $_SESSION['accID'];
    $stmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    $stmt->bind_param("i", $accID);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        $roleID = $row['roleID'];
    }
    $stmt->close();
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    }
</script>

<style>
    /* ========================================= */
    /* TASK MANAGER MAIN LAYOUT                  */
    /* ========================================= */
    .task-manager {
        width: 100%;             
        float: none;             
        margin: 0;               
        background-color: #293A82;
        display: block;
        padding: 2%;
        color: white;
        position: relative;
        height: 100%;            
        border-radius: 20px;
        box-sizing: border-box;
    }

    .task-manager .task-header {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 1vh;
    }

    .task-manager .taskWhite-line {
        position: absolute;
        top: 11vh;
        width: 96.5%;
        margin-left: -0.3vw;
        height: 0.2vw;
        background-color: white;
        z-index: 10;
        display: none;
        margin-bottom: 1vh;
    }

    /* ========================================= */
    /* TASK MANAGER TABLE                        */
    /* ========================================= */
    .task-manager-table {
        width: 96%;
        height: auto;
        color: black;
        font-family: 'Istok Web', sans-serif;
        margin-left: 1.7vw;
        margin-top: 15.3vh;
        position: absolute;
        top: 0;
        left: 0;
        border-collapse: collapse;
    }

    .task-manager-table th,
    .task-manager-table td {
        padding: 1vh 2vw;
        text-align: left;
    }

    .task-manager-table tHead th {
        background-color: #fbaf41;
        color: black;
        text-align: left;
    }

    .task-manager-table tBody tr:nth-child(odd) td {
        background-color: #E0E0E0;
    }

    .task-manager-table tBody tr:nth-child(even) td {
        background-color: #FFFFFF;
    }

    .task-manager-table tBody tr:hover td {
        background-color: grey;
    }

    .task-manager-table tBody tr:nth-child(odd):hover td {
        background-color: #343A40;
        cursor: pointer;
    }

    /* ========================================= */
    /* POLICY INTRODUCTION & CUSTOM PDF VIEWER   */
    /* ========================================= */
    .introduction-section {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .introduction-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 15px;
        margin-bottom: 15px;
        border-bottom: 2px solid white; 
        width: 100%;
        box-sizing: border-box;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    .back-button {
        background: transparent;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        margin-right: 15px;
        padding: 0;
        transition: color 0.2s;
    }

    .back-button:hover {
        color: #fbaf41;
    }

    .introduction-title {
        font-size: 28px;
        font-weight: bold;
        color: white;
        margin: 0;
    }

    /* --- FIGMA ACTION BUTTONS --- */
    .qad-action-buttons {
        display: flex;
        gap: 15px;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        border: 2px solid #fbaf41; 
        color: #fbaf41; 
        border-radius: 8px;
        width: 60px;
        height: 60px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        font-family: 'Istok Web', sans-serif;
        font-size: 11px;
        font-weight: bold;
    }

    .action-btn i {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .action-btn:hover:not(:disabled) {
        background-color: #fbaf41;
        color: #293A82; 
    }

    .action-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        border-color: #999;
        color: #999;
    }

    .pdf-container-wrapper {
        flex-grow: 1;
        width: 100%;
        background-color: white; 
        border-radius: 8px;
        overflow: hidden;
        margin-top: 10px;
    }

    /* ✨ CUSTOM PDF TOOLBAR STYLES ✨ */
    .custom-pdf-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #343A40;
        color: white;
        padding: 10px 20px;
        border-radius: 8px 8px 0 0;
    }

    .pdf-btn {
        background-color: transparent;
        color: white;
        border: 1px solid #fbaf41;
        border-radius: 5px;
        padding: 5px 12px;
        cursor: pointer;
        transition: 0.2s;
    }

    .pdf-btn:hover {
        background-color: #fbaf41;
        color: black;
    }

    .page-info, #tm_zoomLevel {
        margin: 0 15px;
        font-size: 14px;
        font-family: 'Istok Web', sans-serif;
    }

    .pdf-canvas-container {
        background-color: #525659;
        height: 60vh;
        overflow: auto; 
        display: block;      /* ✨ FIX: Removed Flexbox */
        text-align: center;  /* ✨ FIX: Centers the PDF safely */
        padding: 20px 0;
        border-radius: 0 0 8px 8px;
    }

    #tm_pdfCanvas {
        box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        margin: 0 auto;      /* ✨ FIX: Keeps the canvas centered when zoomed out */
    }

    /* ========================================= */
    /* MODALS & OVERLAYS                         */
    /* ========================================= */
    .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; display: none; justify-content: center; align-items: center; }
    .reply-modal { background-color: #293A82; color: white; padding: 20px; border-radius: 10px; width: 60%; height: 75%; position: relative; }
    .reply-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2%; }
    .reply-header h2 { margin: 0; font-size: 200%; font-weight: bold; text-align: center; flex-grow: 1; }
    .close-button { background: none; border: none; color: #f44336; font-size: 30px; cursor: pointer; padding: 0; line-height: 1; }
    .reply-content { display: flex; flex-direction: column; gap: 1%; margin-bottom: 15%; }
    .reply-content textarea { width: 57%; height: 40%; padding: 5%; border: 1px solid #ccc; border-radius: 1%; box-sizing: border-box; color: black; flex-grow: 1; min-height: 50vh; position: fixed; resize: none; }
    .reply-content .submit-reply-button { background-color: #fbaf41; color: black; border: none; padding: 1% 3%; border-radius: 1em; cursor: pointer; font-weight: bold; font-size: 1em; position: absolute; margin-top: 43%; margin-left: 40%; }
    .reply-content .submit-reply-button:hover { background-color: #db8804; }
    .character-counter { font-size: 0.9em; color: black; margin-top: 5px; }
    .character-counter span { font-weight: bold; }
    .confirm-reply-modal, .download-confirm-modal { background-color: #293A82; color: white; padding: 2vw; border-radius: 1vw; text-align: center; }
    .confirm-reply-modal { width: 25%; height: 25%; }
    .download-confirm-modal { width: 28%; max-width: 28%; height: 23%; }
    .confirm-reply-modal h2, .download-header h2 { margin-top: 0; margin-bottom: 2.5vw; font-size: 2.5vw; font-weight: bold; }
    .download-header { display: flex; justify-content: center; align-items: center; margin-bottom: 2.8vw; }
    .download-header h2 { font-size: 2vw; margin-bottom: 0; }
    .confirm-actions, .download-actions { display: flex; justify-content: center; gap: 1.5vw; margin-top: 1.5vw; }
    .confirm-actions button, .download-actions button { padding: 0.6vw 1.8vw; border: none; border-radius: 1em; cursor: pointer; font-weight: bold; font-size: 1em; }
    .cancel-button { background-color: #D3D3D3; color: black; }
    .cancel-button:hover { background-color: grey; color: white; }
    .confirm-button { background-color: #fbaf41; color: black; }
    .confirm-button:hover { background-color: #db8804; }

    /* ✨ ASSIGN TASK MODAL STYLES ✨ */
    .assign-task-modal {
        background-color: #293A82; 
        color: white; 
        padding: 2vw; 
        border-radius: 1vw; 
        text-align: center;
        width: 50%; 
        height: 75vh; 
        display: flex; 
        flex-direction: column;
    }

    .assign-dpt-folder {
        background-color: #4963D4; 
        color: white; 
        padding: 15px 20px; 
        border-radius: 8px;
        cursor: pointer; 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        font-family: 'Istok Web', sans-serif;
        font-size: 18px;
        font-weight: bold; 
        transition: background-color 0.2s;
        margin-bottom: 5px;
    }
    .assign-dpt-folder:hover { background-color: #3b4e9b; }

    .assign-emp-item {
        background-color: #BFE6F8; 
        color: black; 
        padding: 12px 15px; 
        border-radius: 8px;
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        transition: all 0.2s;
        border: 2px solid transparent;
        margin-bottom: 5px;
    }
    .assign-emp-item:hover { background-color: #9cd5f0; }
    .assign-emp-item.selected { 
        border-color: #fbaf41; 
        background-color: #fbaf41; 
    }
    
    #assignContentArea::-webkit-scrollbar { width: 8px; }
    #assignContentArea::-webkit-scrollbar-track { background: #525659; border-radius: 4px; }
    #assignContentArea::-webkit-scrollbar-thumb { background: #fbaf41; border-radius: 4px; }
    #assignContentArea::-webkit-scrollbar-thumb:hover { background: #db8804; }
</style>

<div class="task-manager">
    <div class="task-manager-header-container">
        <h2 class="task-header">Task Manager <br> </h2>
        <div class="taskWhite-line" style="display: flex"></div>
    </div>

    <div class="introduction-section" style="display: none;">
        
        <div class="introduction-header">
            <div class="header-left">
                <button class="back-button" onclick="showTaskTable()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span class="introduction-title">[Policy Title from Database]</span>
            </div>
            
            <div class="qad-action-buttons" id="qadActionButtons" style="display: none;">
                <button class="action-btn" id="qadESignBtn" style="border-color: #4CAF50; color: #4CAF50; display: none;">
                    <i class="fas fa-file-signature"></i>
                    <span>Verify</span>
                </button>

                <button class="action-btn" id="qadRejectBtn">
                    <i class="fas fa-file-excel"></i>
                    <span>Reject</span>
                </button>
                <button class="action-btn" id="qadAssignBtn">
                    <i class="fas fa-user-plus"></i>
                    <span>Assign</span>
                </button>
                <button class="action-btn" id="qadUploadBtn" disabled>
                    <i class="fas fa-upload"></i>
                    <span>Upload</span>
                </button>
            </div>
        </div>

        <div class="pdf-container-wrapper" id="tmPdfWrapper" style="display: none; flex-direction: column;">
            
            <div class="custom-pdf-toolbar" id="customPdfToolbar">
                <div class="pdf-tools-left">
                    <button id="tm_prevPage" class="pdf-btn"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-info">Page <span id="tm_pageNum">1</span> of <span id="tm_pageCount">?</span></span>
                    <button id="tm_nextPage" class="pdf-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="pdf-tools-right">
                    <button id="tm_zoomOut" class="pdf-btn"><i class="fas fa-search-minus"></i></button>
                    <span id="tm_zoomLevel">120%</span>
                    <button id="tm_zoomIn" class="pdf-btn"><i class="fas fa-search-plus"></i></button>
                </div>
            </div>

            <div class="pdf-canvas-container" id="pdfCanvasContainer">
                <canvas id="tm_pdfCanvas"></canvas>
            </div>
        </div>
        
    </div>

    <div id="eSignOverlay" class="overlay" style="display: none;">
        <div class="confirm-reply-modal" style="width: 30%; height: auto; padding: 2vw;">
            <h2>E-Signature Required</h2>
            <p style="margin-bottom: 20px; font-size: 16px; color: #d3d3d3;">Please enter your account password to officially sign and verify this document.</p>
            
            <input type="password" id="eSignPasswordInput" placeholder="Enter Password..." style="width: 100%; padding: 12px; border-radius: 10px; margin-bottom: 25px; color: black; font-size: 16px; border: none; text-align: center;">
            
            <div class="confirm-actions">
                <button class="cancel-button" onclick="document.getElementById('eSignOverlay').style.display='none'">Cancel</button>
                <button class="confirm-button" id="confirmESignBtn">Sign & Submit</button>
            </div>
        </div>
    </div>

    <div id="assignTaskOverlay" class="overlay" style="display: none;">
        <div class="assign-task-modal">
            <h2 id="assignTaskTitle" style="margin-bottom: 5px;">Assign Verifier</h2>
            <p id="assignTaskSubtitle" style="margin-bottom: 20px; font-size: 16px; color: #d3d3d3;">Navigate departments to select an employee.</p>

            <div id="assignFolderNav" style="display: none; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #fbaf41;">
                <button id="assignBackBtn" style="background: transparent; border: none; color: #fbaf41; font-size: 16px; cursor: pointer; display: flex; align-items: center; font-weight: bold;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
                </button>
                <span id="assignCurrentFolderName" style="margin-left: 15px; font-weight: bold; font-size: 18px; color: white;"></span>
            </div>

            <div id="assignContentArea" style="flex-grow: 1; overflow-y: auto; background-color: #343A40; border-radius: 10px; padding: 15px; text-align: left;">
                <div id="assignLoading" style="text-align: center; color: white; margin-top: 20px; font-size: 18px;">
                    <i class="fas fa-spinner fa-spin" style="margin-right: 10px; color: #fbaf41;"></i> Loading departments...
                </div>
                <div id="assignFoldersList" style="display: none; flex-direction: column;"></div>
                <div id="assignEmployeesList" style="display: none; flex-direction: column;"></div>
            </div>

            <div class="confirm-actions" style="margin-top: 25px; display: flex; justify-content: center; gap: 1.5vw;">
                <button class="cancel-button" onclick="document.getElementById('assignTaskOverlay').style.display='none'">Cancel</button>
                <button class="confirm-button" id="confirmAssignTaskBtn" disabled style="opacity: 0.5; cursor: not-allowed;">Assign</button>
            </div>
        </div>
    </div>

    <table class="task-manager-table" style="display: none;">
        <thead>
        <tr>
            <th>Policy Title</th>
            <th>Sender</th>
            <th>Date Submitted</th>
            <th>Version no.</th>
            <th>Reviewed by</th>
            <th>Approved by</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody id="taskTableBody">
        </tbody>
    </table>
</div>

<script>
var userRole = "<?php echo isset($_SESSION['accID']) ? $_SESSION['accID'] : ''; ?>"; 

// ✨ We now correctly have the true Database Role ID to secure the buttons!
var systemRoleID = <?php echo $roleID; ?>; 

// ✨ GLOBAL TASK TRACKERS ✨
var currentTaskPolicyID = null;
var currentTaskStatus = null;

// ✨ UNIQUE PDF.JS ENGINE VARIABLES ✨
var tm_pdfDoc = null,
    tm_pageNum = 1,
    tm_pageRendering = false,
    tm_pageNumPending = null,
    tm_scale = 1.2,
    tm_canvas = null,
    tm_ctx = null;

function tm_renderPage(num) {
    tm_pageRendering = true;
    if (tm_pdfDoc) {
        tm_pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({scale: tm_scale});
            tm_canvas.height = viewport.height;
            tm_canvas.width = viewport.width;

            let renderContext = {
                canvasContext: tm_ctx,
                viewport: viewport
            };
            let renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                tm_pageRendering = false;
                if (tm_pageNumPending !== null) {
                    tm_renderPage(tm_pageNumPending);
                    tm_pageNumPending = null;
                }
            });
        });

        const pageNumEl = document.getElementById('tm_pageNum');
        const zoomLevelEl = document.getElementById('tm_zoomLevel');
        if (pageNumEl) pageNumEl.textContent = num;
        if (zoomLevelEl) zoomLevelEl.textContent = Math.round(tm_scale * 100) + '%';
    }
}

function tm_queueRenderPage(num) {
    if (tm_pageRendering) {
        tm_pageNumPending = num;
    } else {
        tm_renderPage(num);
    }
}

function tm_onPrevPage() {
    if (tm_pageNum <= 1) return;
    tm_pageNum--;
    tm_queueRenderPage(tm_pageNum);
}

function tm_onNextPage() {
    if (!tm_pdfDoc || tm_pageNum >= tm_pdfDoc.numPages) return;
    tm_pageNum++;
    tm_queueRenderPage(tm_pageNum);
}

function tm_onZoomIn() {
    tm_scale += 0.2;
    tm_queueRenderPage(tm_pageNum);
}

function tm_onZoomOut() {
    if (tm_scale <= 0.6) return; 
    tm_scale -= 0.2;
    tm_queueRenderPage(tm_pageNum);
}

function populateTaskTable(tasks) {
    const tableBody = document.getElementById('taskTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = ''; 

    if (!Array.isArray(tasks) || tasks.length === 0) {
        const row = tableBody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 7; 
        cell.textContent = tasks.message || "No tasks found.";
        cell.style.textAlign = "center";
        cell.style.padding = "20px";
        return; 
    }

    tasks.forEach(task => {
        const row = tableBody.insertRow();
        row.onclick = function() {
            showTaskIntroduction(task.policyID, task.policyTitle, task.author, task.pdfPath, task.status);
        };

        const titleCell = row.insertCell();
        titleCell.textContent = task.policyTitle;

        const senderCell = row.insertCell();
        senderCell.textContent = task.author; 

        const dateCell = row.insertCell();
        if (task.dateSubmitted) {
            const d = new Date(task.dateSubmitted);
            const month = (d.getMonth() + 1).toString().padStart(2, '0');
            const day = d.getDate().toString().padStart(2, '0');
            const year = d.getFullYear().toString().slice(-2);
            dateCell.textContent = `${month}/${day}/${year}`;
        } else {
            dateCell.textContent = '---';
        }

        const versionCell = row.insertCell();
        versionCell.textContent = task.version || 'New'; 

        const reviewedCell = row.insertCell();
        reviewedCell.textContent = '---'; 

        const approvedCell = row.insertCell();
        approvedCell.textContent = '---'; 

        const statusCell = row.insertCell();
        statusCell.textContent = task.status;
        
        if (task.status === 'For Upload') {
            statusCell.style.color = '#00C853'; 
            statusCell.style.fontWeight = 'bold';
        } else if (task.status === 'For Review') {
            statusCell.style.color = '#2962FF'; 
            statusCell.style.fontWeight = 'bold';
        } else {
            statusCell.style.color = 'black';
            statusCell.style.fontWeight = 'bold';
        }
    });
}

function showTaskIntroduction(policyID, policyTitle, policyContent, pdfPath, policyStatus) {
    currentTaskPolicyID = policyID;
    currentTaskStatus = policyStatus;

    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const introductionTitleElement = introductionSection.querySelector('.introduction-title');
    
    const tmPdfWrapper = document.getElementById('tmPdfWrapper');
    const customPdfToolbar = document.getElementById('customPdfToolbar');
    const pdfCanvasContainer = document.getElementById('pdfCanvasContainer');
    const actionButtons = document.getElementById('qadActionButtons');
    
    if (introductionTitleElement) introductionTitleElement.textContent = policyTitle;

    if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'none'; 
    if (taskManagerTable) taskManagerTable.style.display = 'none';
    if (introductionSection) introductionSection.style.display = 'flex';
    if (actionButtons) actionButtons.style.display = 'flex';

    // ✨ FIX: STRICT SECURE VISIBILITY LOGIC
    const uploadBtn = document.getElementById('qadUploadBtn');
    const assignBtn = document.getElementById('qadAssignBtn');
    const eSignBtn = document.getElementById('qadESignBtn');
    const rejectBtn = document.getElementById('qadRejectBtn');

    // 1. Hide everything first to prevent visual bugs
    if (uploadBtn) uploadBtn.style.display = 'none';
    if (assignBtn) assignBtn.style.display = 'none';
    if (eSignBtn) eSignBtn.style.display = 'none';
    if (rejectBtn) rejectBtn.style.display = 'none';

    // 2. QAD (Role 2) Check: Only they see Assign, Upload, and Reject
    if (systemRoleID == 2) {
        if (assignBtn) assignBtn.style.display = 'flex';
        if (rejectBtn) rejectBtn.style.display = 'flex';
        if (uploadBtn) {
            uploadBtn.style.display = 'flex';
            uploadBtn.disabled = !(policyStatus === 'Approved' || policyStatus === 'For Upload');
        }
    } 
    // 3. Staff / QA Staff Check: Only they see the Verify/Sign button!
    else {
        if (eSignBtn) {
            eSignBtn.style.display = 'flex';
            
            // Smart Text: Change "Verify" to "Approve" dynamically if needed!
            const eSignText = eSignBtn.querySelector('span');
            if (eSignText) {
                if (policyStatus === 'Verifying' || policyStatus === 'Pending' || policyStatus === 'For Review') {
                    eSignText.textContent = 'Verify';
                } else {
                    eSignText.textContent = 'Approve';
                }
            }
        }
    }

    if (tmPdfWrapper) {
        let placeholder = document.getElementById('tmPdfPlaceholder');
        if (!placeholder) {
            placeholder = document.createElement('div');
            placeholder.id = 'tmPdfPlaceholder';
            placeholder.style = 'display:flex; flex-direction:column; justify-content:center; align-items:center; height:65vh; background-color:#E0E0E0; color:#555; border-radius:8px;';
            placeholder.innerHTML = `
                <i class="fas fa-file-pdf fa-3x" style="margin-bottom:15px; color:#A0A0A0;"></i>
                <span style="font-size: 24px; font-weight:bold;">No Document Uploaded Yet</span>
            `;
            tmPdfWrapper.appendChild(placeholder);
        }

        tmPdfWrapper.style.display = 'flex';

        if (pdfPath && pdfPath !== 'null' && pdfPath.trim() !== '') {
            placeholder.style.display = 'none';
            if (customPdfToolbar) customPdfToolbar.style.display = 'flex';
            if (pdfCanvasContainer) pdfCanvasContainer.style.display = 'flex';

            if (typeof pdfjsLib !== 'undefined') {
                const encodedUrl = encodeURI(pdfPath);
                pdfjsLib.getDocument(encodedUrl).promise.then(function(pdfDoc_) {
                    tm_pdfDoc = pdfDoc_;
                    const pageCountEl = document.getElementById('tm_pageCount');
                    if (pageCountEl) pageCountEl.textContent = tm_pdfDoc.numPages;
                    
                    tm_pageNum = 1;
                    tm_scale = 1.2;
                    tm_renderPage(tm_pageNum);
                }).catch(function(error) {
                    console.error("Error loading PDF: ", error);
                    placeholder.innerHTML = `<span style="color:red; font-weight:bold;">Error Loading Document</span>`;
                    placeholder.style.display = 'flex';
                    if (customPdfToolbar) customPdfToolbar.style.display = 'none';
                    if (pdfCanvasContainer) pdfCanvasContainer.style.display = 'none';
                });
            } else {
                placeholder.innerHTML = `<span style="color:red; font-weight:bold;">PDF Viewer Failed to Load</span>`;
                placeholder.style.display = 'flex';
                if (customPdfToolbar) customPdfToolbar.style.display = 'none';
                if (pdfCanvasContainer) pdfCanvasContainer.style.display = 'none';
            }

        } else {
            if (customPdfToolbar) customPdfToolbar.style.display = 'none';
            if (pdfCanvasContainer) pdfCanvasContainer.style.display = 'none';
            placeholder.style.display = 'flex';
        }
    }
}

function showTaskTable() {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const actionButtons = document.getElementById('qadActionButtons');
    
    if (tm_ctx && tm_canvas) {
        tm_ctx.clearRect(0, 0, tm_canvas.width, tm_canvas.height);
        tm_canvas.height = 0;
    }

    if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'block';
    if (taskManagerTable) taskManagerTable.style.display = 'table';
    if (introductionSection) introductionSection.style.display = 'none';
    if (actionButtons) actionButtons.style.display = 'none'; 
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Safely attach PDF Viewer Listeners
    const prevBtn = document.getElementById('tm_prevPage');
    if (prevBtn) prevBtn.addEventListener('click', tm_onPrevPage);
    
    const nextBtn = document.getElementById('tm_nextPage');
    if (nextBtn) nextBtn.addEventListener('click', tm_onNextPage);
    
    const zoomInBtn = document.getElementById('tm_zoomIn');
    if (zoomInBtn) zoomInBtn.addEventListener('click', tm_onZoomIn);
    
    const zoomOutBtn = document.getElementById('tm_zoomOut');
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', tm_onZoomOut);
    
    tm_canvas = document.getElementById('tm_pdfCanvas');
    if(tm_canvas) tm_ctx = tm_canvas.getContext('2d');

    // THIS FETCH POPULATES YOUR TABLE!
    fetch('/qms_optiqual/generalComponents/taskManager/fetchTasks.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching tasks:', data.error);
            } else {
                populateTaskTable(data); 
                const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
                const taskManagerTable = document.querySelector('.task-manager-table');
                if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'block';
                if (taskManagerTable) taskManagerTable.style.display = 'table';
            }
        })
        .catch(error => console.error('Fetch Error:', error));

    // ✨ E-SIGNATURE LOGIC ✨
    const qadESignBtn = document.getElementById('qadESignBtn');
    const eSignOverlay = document.getElementById('eSignOverlay');
    const confirmESignBtn = document.getElementById('confirmESignBtn');
    const eSignPasswordInput = document.getElementById('eSignPasswordInput');

    if (qadESignBtn) {
        qadESignBtn.addEventListener('click', () => {
            eSignOverlay.style.display = 'flex';
            eSignPasswordInput.value = ''; // Clear old password
        });
    }

    if (confirmESignBtn) {
        confirmESignBtn.addEventListener('click', () => {
            const pwd = eSignPasswordInput.value.trim();
            if (!pwd) return alert("You must enter your password to sign.");

            // Add loading state
            confirmESignBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing...';
            confirmESignBtn.disabled = true;

            fetch('/qms_optiqual/generalComponents/taskManager/eSignTaskBE.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    policyID: currentTaskPolicyID,
                    password: pwd
                })
            })
            .then(res => res.json())
            .then(data => {
                confirmESignBtn.innerHTML = 'Sign & Submit';
                confirmESignBtn.disabled = false;

                if (data.success) {
                    // SHOW THE "BARCODE" HASH TO THE USER SO THEY KNOW IT WORKED!
                    alert(data.message + "\n\nYour Unique Digital Signature Hash:\n" + data.signatureHash);
                    eSignOverlay.style.display = 'none';
                    location.reload(); // Refresh table
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error('E-Sign Error:', err);
                alert("Network error processing signature.");
                confirmESignBtn.innerHTML = 'Sign & Submit';
                confirmESignBtn.disabled = false;
            });
        });
    }

    // ✨ ASSIGN BUTTON LOGIC (DRILL-DOWN FOLDER UI) ✨
    const qadAssignBtn = document.getElementById('qadAssignBtn');
    const assignTaskOverlay = document.getElementById('assignTaskOverlay');
    const assignTaskTitle = document.getElementById('assignTaskTitle');
    const assignTaskSubtitle = document.getElementById('assignTaskSubtitle');
    const confirmAssignTaskBtn = document.getElementById('confirmAssignTaskBtn');
    const assignBackBtn = document.getElementById('assignBackBtn');

    let assignDepartmentsData = [];
    let assignEmployeesData = [];
    let assignSelectedAccID = null;
    let assignFolderHistory = []; // Tracks where we are!

    if (qadAssignBtn) {
        qadAssignBtn.addEventListener('click', () => {
            if (!currentTaskPolicyID) return alert("No task selected.");

            let requiredRole = "Verifier";
            if (currentTaskStatus === 'Verified' || currentTaskStatus === 'For Review') {
                requiredRole = "Approver";
            }

            assignTaskTitle.textContent = `Assign ${requiredRole}`;
            assignTaskSubtitle.textContent = `Select a department folder to find the ${requiredRole}.`;
            assignTaskOverlay.style.display = 'flex';

            document.getElementById('assignLoading').style.display = 'block';
            document.getElementById('assignFoldersList').style.display = 'none';
            document.getElementById('assignEmployeesList').style.display = 'none';
            document.getElementById('assignFolderNav').style.display = 'none';
            
            confirmAssignTaskBtn.disabled = true;
            confirmAssignTaskBtn.style.opacity = '0.5';
            confirmAssignTaskBtn.style.cursor = 'not-allowed';
            
            assignSelectedAccID = null;
            assignFolderHistory = [];

            // Fetch Data
            fetch('/qms_optiqual/generalComponents/dpManagerPHP/getDepartments.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('assignLoading').style.display = 'none';
                    if (data.success) {
                        assignDepartmentsData = data.departments || [];
                        assignEmployeesData = data.employees || [];
                        renderAssignView(null, 'Departments'); 
                    } else {
                        document.getElementById('assignLoading').innerHTML = '<i class="fas fa-exclamation-triangle" style="color:red;"></i> Error loading data.';
                        document.getElementById('assignLoading').style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('Fetch Error:', err);
                    document.getElementById('assignLoading').innerHTML = '<i class="fas fa-exclamation-triangle" style="color:red;"></i> Network error.';
                    document.getElementById('assignLoading').style.display = 'block';
                });
        });
    }

    function renderAssignView(dptID, dptName) {
        const foldersList = document.getElementById('assignFoldersList');
        const empList = document.getElementById('assignEmployeesList');
        const navHeader = document.getElementById('assignFolderNav');
        const currentNameEl = document.getElementById('assignCurrentFolderName');

        foldersList.innerHTML = '';
        empList.innerHTML = '';

        assignSelectedAccID = null;
        confirmAssignTaskBtn.disabled = true;
        confirmAssignTaskBtn.style.opacity = '0.5';
        confirmAssignTaskBtn.style.cursor = 'not-allowed';

        if (dptID === null) {
            navHeader.style.display = 'none';
        } else {
            navHeader.style.display = 'flex';
            currentNameEl.innerHTML = `<i class="fas fa-folder-open" style="color:#fbaf41; margin-right:8px;"></i> ${dptName}`;
        }

        const isRoot = (id) => id === null || id === undefined || id === "null" || id === 0 || id === "0" || id === "";

        const subFolders = assignDepartmentsData.filter(d => {
            if (dptID === null) return isRoot(d.dptParentID);
            return d.dptParentID == dptID;
        });

        const emps = assignEmployeesData.filter(e => {
            if (dptID === null) return false; 
            return e.dptID == dptID;
        });

        if (subFolders.length === 0 && emps.length === 0) {
            foldersList.innerHTML = '<p style="color:white; text-align:center; margin-top:30px; font-size: 18px;"><i class="fas fa-folder-open" style="display:block; font-size: 40px; margin-bottom: 10px; color:#555;"></i>This folder is empty.</p>';
            foldersList.style.display = 'flex';
            empList.style.display = 'none';
        } else {
            subFolders.forEach(dpt => {
                const folderDiv = document.createElement('div');
                folderDiv.className = 'assign-dpt-folder';
                folderDiv.innerHTML = `
                    <span><i class="fas fa-folder" style="color: #fbaf41; margin-right: 12px;"></i> ${dpt.dptName}</span>
                    <i class="fas fa-chevron-right" style="font-size: 14px; opacity: 0.7;"></i>
                `;
                folderDiv.onclick = () => {
                    assignFolderHistory.push({ id: dptID, name: dptName });
                    renderAssignView(dpt.dptID, dpt.dptName);
                };
                foldersList.appendChild(folderDiv);
            });

            emps.forEach(emp => {
                const empDiv = document.createElement('div');
                empDiv.className = 'assign-emp-item';
                empDiv.innerHTML = `
                    <i class="fas fa-user-circle" style="font-size: 32px; margin-right: 15px; color: #293A82;"></i>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-weight: bold; font-size: 16px;">${emp.fullName}</span>
                        <span style="font-size: 13px; color: #444; margin-top:2px;"><strong>${emp.departmentRole || 'Employee'}</strong> | ${emp.email}</span>
                    </div>
                    <i class="fas fa-check-circle check-icon" style="margin-left: auto; font-size: 24px; color: #293A82; display: none;"></i>
                `;
                empDiv.onclick = () => {
                    document.querySelectorAll('.assign-emp-item').forEach(el => {
                        el.classList.remove('selected');
                        el.querySelector('.check-icon').style.display = 'none';
                    });
                    empDiv.classList.add('selected');
                    empDiv.querySelector('.check-icon').style.display = 'block';
                    
                    assignSelectedAccID = emp.accID;
                    confirmAssignTaskBtn.disabled = false;
                    confirmAssignTaskBtn.style.opacity = '1';
                    confirmAssignTaskBtn.style.cursor = 'pointer';
                };
                empList.appendChild(empDiv);
            });

            foldersList.style.display = subFolders.length > 0 ? 'flex' : 'none';
            empList.style.display = emps.length > 0 ? 'flex' : 'none';
        }
    }

    if (assignBackBtn) {
        assignBackBtn.addEventListener('click', () => {
            if (assignFolderHistory.length > 0) {
                const prevState = assignFolderHistory.pop();
                renderAssignView(prevState.id, prevState.name);
            } else {
                renderAssignView(null, 'Departments');
            }
        });
    }

    if (confirmAssignTaskBtn) {
        confirmAssignTaskBtn.addEventListener('click', () => {
            if (!assignSelectedAccID) return alert("Please select an employee.");

            const requiredRole = assignTaskTitle.textContent.includes('Verifier') ? 'Verifier' : 'Approver';

            fetch('/qms_optiqual/generalComponents/taskManager/assignTaskBE.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    policyID: currentTaskPolicyID,
                    assigneeID: assignSelectedAccID,
                    roleType: requiredRole
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`${requiredRole} successfully assigned!`);
                    assignTaskOverlay.style.display = 'none';
                    location.reload(); 
                } else {
                    alert("Error assigning task: " + data.message);
                }
            })
            .catch(err => {
                console.error('Assign Task Error:', err);
                alert("A network error occurred while assigning the task.");
            });
        });
    }
});
</script>