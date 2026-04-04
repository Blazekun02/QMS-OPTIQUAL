<?php 
// ✨ QAD-POV.php already started the session and connected to the database!
// We do NOT need to require filepaths.php or connect.php again. This stops the crash.
$roleID = 0;
if(isset($_SESSION['accID']) && isset($conn)){
    $accID = $_SESSION['accID'];
    $stmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    if ($stmt) { 
        $stmt->bind_param("i", $accID);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()){
            $roleID = $row['roleID'];
        }
        $stmt->close();
    }
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
    /* WORKSPACE MAIN LAYOUT                 */
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
        overflow-y: auto;
    }

    .task-manager .task-header {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
        font-family: 'Istok Web', sans-serif;
    }

    /* ✨ NEW TABS STYLES ✨ */
    .workspace-tabs { 
        display: flex; 
        gap: 10px; 
        border-bottom: 2px solid rgba(255, 255, 255, 0.3); 
        padding-bottom: 10px; 
        margin-bottom: 15px; 
    }
    .ws-tab { 
        background: transparent; 
        border: 2px solid transparent; 
        color: white; 
        padding: 10px 20px; 
        font-size: 16px; 
        border-radius: 8px; 
        cursor: pointer; 
        transition: all 0.3s; 
        font-family: 'Istok Web', sans-serif; 
    }
    .ws-tab:hover { background: rgba(255,255,255,0.1); }
    .ws-tab.active { background: #fbaf41; color: black; font-weight: bold; }

    /* ========================================= */
    /* TABLE & BUTTONS                           */
    /* ========================================= */
    .task-manager-table {
        width: 100%;
        height: auto;
        color: black;
        font-family: 'Istok Web', sans-serif;
        border-collapse: collapse;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
    }

    .task-manager-table th,
    .task-manager-table td {
        padding: 12px 15px;
        text-align: left;
    }

    .task-manager-table tHead th {
        background-color: #fbaf41;
        color: black;
        text-align: left;
        font-size: 16px;
    }

    .task-manager-table tBody tr:nth-child(odd) td { background-color: #E0E0E0; }
    .task-manager-table tBody tr:nth-child(even) td { background-color: #FFFFFF; }
    .task-manager-table tBody tr:hover td { background-color: grey; color: white; cursor: pointer; }

    .action-btn-inline {
        background-color: #fbaf41;
        color: black;
        border: none;
        padding: 6px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        font-family: 'Istok Web', sans-serif;
        font-size: 13px;
        transition: 0.2s;
    }
    .action-btn-inline:hover { background-color: #db8804; }

    /* ========================================= */
    /* VISUAL TRACKER TIMELINE STYLES            */
    /* ========================================= */
    .progress-timeline { display: flex; justify-content: space-between; margin-bottom: 30px; padding: 0 40px; }
    .timeline-step { display: flex; flex-direction: column; align-items: center; color: #999; width: 80px; }
    .timeline-step .circle { width: 40px; height: 40px; border-radius: 50%; background-color: #eee; display: flex; justify-content: center; align-items: center; font-size: 18px; margin-bottom: 8px; transition: 0.3s; }
    .timeline-line { flex-grow: 1; height: 4px; background-color: #eee; margin-top: 18px; }
    
    .timeline-step.completed .circle { background-color: #4CAF50; color: white; }
    .timeline-step.completed span { color: #4CAF50; font-weight: bold; }
    .timeline-step.active .circle { background-color: #fbaf41; color: black; border: 2px solid black; }
    .timeline-step.active span { color: black; font-weight: bold; }
    .timeline-line.completed { background-color: #4CAF50; }

    /* ========================================= */
    /* POLICY INTRODUCTION & CUSTOM PDF VIEWER   */
    /* ========================================= */
    .introduction-section { width: 100%; height: 100%; display: flex; flex-direction: column; }
    .introduction-header { display: flex; align-items: center; justify-content: space-between; padding-bottom: 15px; margin-bottom: 15px; border-bottom: 2px solid white; width: 100%; box-sizing: border-box; }
    .header-left { display: flex; align-items: center; }
    .back-button { background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; margin-right: 15px; padding: 0; transition: color 0.2s; }
    .back-button:hover { color: #fbaf41; }
    .introduction-title { font-size: 28px; font-weight: bold; color: white; margin: 0; }
    .qad-action-buttons { display: flex; gap: 15px; }
    .action-btn { display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: transparent; border: 2px solid #fbaf41; color: #fbaf41; border-radius: 8px; width: 60px; height: 60px; cursor: pointer; transition: all 0.2s ease-in-out; font-family: 'Istok Web', sans-serif; font-size: 11px; font-weight: bold; }
    .action-btn i { font-size: 20px; margin-bottom: 4px; }
    .action-btn:hover:not(:disabled) { background-color: #fbaf41; color: #293A82; }
    .action-btn:disabled { opacity: 0.4; cursor: not-allowed; border-color: #999; color: #999; }
    
    .pdf-container-wrapper { flex-grow: 1; width: 100%; background-color: white; border-radius: 8px; overflow: hidden; margin-top: 10px; }
    .custom-pdf-toolbar { display: flex; justify-content: space-between; align-items: center; background-color: #343A40; color: white; padding: 10px 20px; border-radius: 8px 8px 0 0; }
    .pdf-btn { background-color: transparent; color: white; border: 1px solid #fbaf41; border-radius: 5px; padding: 5px 12px; cursor: pointer; transition: 0.2s; }
    .pdf-btn:hover { background-color: #fbaf41; color: black; }
    .page-info, #tm_zoomLevel { margin: 0 15px; font-size: 14px; font-family: 'Istok Web', sans-serif; }
    .pdf-canvas-container { background-color: #525659; height: 60vh; overflow: auto; display: block; text-align: center; padding: 20px 0; border-radius: 0 0 8px 8px; }
    #tm_pdfCanvas { box-shadow: 0 4px 8px rgba(0,0,0,0.5); margin: 0 auto; }

    /* ========================================= */
    /* MODALS & OVERLAYS                         */
    /* ========================================= */
    .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; display: none; justify-content: center; align-items: center; }
    .confirm-reply-modal { background-color: #293A82; color: white; padding: 2vw; border-radius: 1vw; text-align: center; }
    .confirm-reply-modal h2 { margin-top: 0; margin-bottom: 2.5vw; font-size: 2.5vw; font-weight: bold; }
    .confirm-actions { display: flex; justify-content: center; gap: 1.5vw; margin-top: 1.5vw; }
    .confirm-actions button { padding: 0.6vw 1.8vw; border: none; border-radius: 1em; cursor: pointer; font-weight: bold; font-size: 1em; }
    .cancel-button { background-color: #D3D3D3; color: black; }
    .cancel-button:hover { background-color: grey; color: white; }
    .confirm-button { background-color: #fbaf41; color: black; }
    .confirm-button:hover { background-color: #db8804; }

    .assign-task-modal { background-color: #293A82; color: white; padding: 2vw; border-radius: 1vw; text-align: center; width: 50%; height: 75vh; display: flex; flex-direction: column; }
    .assign-emp-item { background-color: #BFE6F8; color: black; padding: 12px 15px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; transition: all 0.2s; border: 2px solid transparent; margin-bottom: 5px; }
    .assign-emp-item:hover { background-color: #9cd5f0; }
    .assign-emp-item.selected { border-color: #fbaf41; background-color: #fbaf41; }
    
    #assignContentArea::-webkit-scrollbar { width: 8px; }
    #assignContentArea::-webkit-scrollbar-track { background: #525659; border-radius: 4px; }
    #assignContentArea::-webkit-scrollbar-thumb { background: #fbaf41; border-radius: 4px; }
    #assignContentArea::-webkit-scrollbar-thumb:hover { background: #db8804; }
</style>

<div class="task-manager">
    <div class="task-manager-header-container" id="workspaceHeaderArea">
        <h2 class="task-header">My Workspace</h2>
        
        <div class="workspace-tabs">
            <button class="ws-tab active" id="tabActionRequired" onclick="switchWorkspaceTab('action')">
                <i class="fas fa-clipboard-list"></i> My Tasks
            </button>
            <button class="ws-tab" id="tabMySubmissions" onclick="switchWorkspaceTab('track')">
                <i class="fas fa-paper-plane"></i> My Submissions
            </button>
        </div>
    </div>

    <div id="trackerTimelineUI" style="display: none; background: white; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
        <h3 id="trackerDocTitle" style="color: #293A82; text-align: center; margin-top:0; font-size: 24px;">Document Title</h3>
        
        <div class="progress-timeline" id="progressTimeline">
            <div class="timeline-step" id="step-submit"><div class="circle"><i class="fas fa-check"></i></div><span>Pending</span></div>
            <div class="timeline-line"></div>
            <div class="timeline-step" id="step-review"><div class="circle"><i class="fas fa-search"></i></div><span>Reviewed</span></div>
            <div class="timeline-line"></div>
            <div class="timeline-step" id="step-verify"><div class="circle"><i class="fas fa-file-signature"></i></div><span>Verified</span></div>
            <div class="timeline-line"></div>
            <div class="timeline-step" id="step-approve"><div class="circle"><i class="fas fa-stamp"></i></div><span>Approved</span></div>
        </div>
        
        <div style="text-align: center;">
            <button onclick="closeTracker()" class="action-btn-inline" style="background:#293A82; color:white; padding: 10px 20px; font-size: 16px;"><i class="fas fa-arrow-left"></i> Back to List</button>
        </div>
    </div>

    <table class="task-manager-table" id="workspaceTable">
        <thead>
            <tr id="workspaceTableHeaders">
                </tr>
        </thead>
        <tbody id="taskTableBody">
        </tbody>
    </table>

    <div class="introduction-section" id="workspaceDocViewer" style="display: none;">
        <div class="introduction-header">
            <div class="header-left">
                <button class="back-button" onclick="showTaskTable()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span class="introduction-title" id="policyTitleDisplay">[Policy Title from Database]</span>
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
            <p id="assignTaskSubtitle" style="margin-bottom: 20px; font-size: 16px; color: #d3d3d3;">Select an employee from the list below.</p>

            <div id="assignContentArea" style="flex-grow: 1; overflow-y: auto; background-color: #343A40; border-radius: 10px; padding: 15px; text-align: left;">
                <div id="assignLoading" style="text-align: center; color: white; margin-top: 20px; font-size: 18px;">
                    <i class="fas fa-spinner fa-spin" style="margin-right: 10px; color: #fbaf41;"></i> Loading employees...
                </div>
                <div id="assignEmployeesList" style="display: none; flex-direction: column;"></div>
            </div>

            <div class="confirm-actions" style="margin-top: 25px; display: flex; justify-content: center; gap: 1.5vw;">
                <button class="cancel-button" onclick="document.getElementById('assignTaskOverlay').style.display='none'">Cancel</button>
                <button class="confirm-button" id="confirmAssignTaskBtn" disabled style="opacity: 0.5; cursor: not-allowed;">Assign</button>
            </div>
        </div>
    </div>
</div>

<script>
var userRole = "<?php echo isset($_SESSION['accID']) ? $_SESSION['accID'] : ''; ?>"; 
var systemRoleID = <?php echo $roleID; ?>; 

// ✨ WORKSPACE DATA & STATE ✨
let workspaceData = { actionRequired: [], mySubmissions: [] };
let currentTab = 'action'; // 'action' or 'track'

var currentTaskPolicyID = null;
var currentTaskStatus = null;
var currentTaskPolicyTitle = null; // ✨ NEW

let assignSelectedAccID = null;

var tm_pdfDoc = null, tm_pageNum = 1, tm_pageRendering = false, tm_pageNumPending = null, tm_scale = 1.2, tm_canvas = null, tm_ctx = null;

function tm_renderPage(num) {
    tm_pageRendering = true;
    if (tm_pdfDoc) {
        tm_pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({scale: tm_scale});
            tm_canvas.height = viewport.height;
            tm_canvas.width = viewport.width;

            let renderContext = { canvasContext: tm_ctx, viewport: viewport };
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
function tm_queueRenderPage(num) { if (tm_pageRendering) tm_pageNumPending = num; else tm_renderPage(num); }
function tm_onPrevPage() { if (tm_pageNum <= 1) return; tm_pageNum--; tm_queueRenderPage(tm_pageNum); }
function tm_onNextPage() { if (!tm_pdfDoc || tm_pageNum >= tm_pdfDoc.numPages) return; tm_pageNum++; tm_queueRenderPage(tm_pageNum); }
function tm_onZoomIn() { tm_scale += 0.2; tm_queueRenderPage(tm_pageNum); }
function tm_onZoomOut() { if (tm_scale <= 0.6) return; tm_scale -= 0.2; tm_queueRenderPage(tm_pageNum); }

// ==========================================
// WORKSPACE LOGIC
// ==========================================
function switchWorkspaceTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.ws-tab').forEach(b => b.classList.remove('active'));
    document.getElementById(tab === 'action' ? 'tabActionRequired' : 'tabMySubmissions').classList.add('active');
    
    const headerRow = document.getElementById('workspaceTableHeaders');
    if (tab === 'action') {
        headerRow.innerHTML = `<th>Policy Title</th><th>Author</th><th>Date</th><th>Reviewed by</th><th>Verified by</th><th>Approved by</th><th>Status</th><th>Action</th>`;
        populateWorkspaceTable(workspaceData.actionRequired);
    } else {
        headerRow.innerHTML = `<th>Policy Title</th><th>Date Submitted</th><th>Current Status</th><th>Action</th>`;
        populateWorkspaceTable(workspaceData.mySubmissions);
    }
}

function populateWorkspaceTable(tasks) {
    const tableBody = document.getElementById('taskTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = ''; 

    if (!Array.isArray(tasks) || tasks.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 20px;">No documents found.</td></tr>`;
        return; 
    }

    tasks.forEach(task => {
        const row = tableBody.insertRow();
        
        if (currentTab === 'action') {
            row.onclick = function() { showTaskIntroduction(task.policyID, task.policyTitle, task.author, task.pdfPath, task.status); };
            
            row.innerHTML = `
                <td>${task.policyTitle}</td>
                <td>${task.author}</td>
                <td>${task.dateSubmitted ? new Date(task.dateSubmitted).toLocaleDateString() : '---'}</td>
                <td>${task.reviewerName || '---'}</td>
                <td>${task.verifierName || '---'}</td>
                <td>${task.approverName || '---'}</td>
            `;

            const statusCell = row.insertCell();
            statusCell.textContent = task.status;
            if (task.status === 'For Upload') statusCell.style.color = '#00C853'; 
            else if (task.status === 'For Review') statusCell.style.color = '#2962FF'; 
            statusCell.style.fontWeight = 'bold';

            const actionCell = row.insertCell();
            actionCell.onclick = (e) => e.stopPropagation();

            const actionBtn = document.createElement('button');
            actionBtn.className = 'action-btn-inline';

            if (systemRoleID == 2) { 
                if (task.status === 'Approved' || task.status === 'For Upload') {
                    actionBtn.textContent = 'Upload';
                    actionBtn.onclick = () => alert("Upload feature coming soon!"); 
                } else if (task.status === 'Reviewed' || task.status === 'Verified') {
                    actionBtn.textContent = 'Assign';
                    actionBtn.onclick = () => openAssignModalForTask(task.policyID, task.status, task.policyTitle);
                } else {
                    actionBtn.textContent = 'View';
                    actionBtn.onclick = () => row.click();
                }
            } else { 
                if (task.status === 'Pending') actionBtn.textContent = 'Review';
                else if (task.status === 'Reviewed') actionBtn.textContent = 'Verify';
                else actionBtn.textContent = 'Approve';
                
                actionBtn.onclick = () => {
                    currentTaskPolicyID = task.policyID;
                    currentTaskStatus = task.status;
                    document.getElementById('eSignPasswordInput').value = '';
                    document.getElementById('eSignOverlay').style.display = 'flex';
                };
            }
            actionCell.appendChild(actionBtn);

        } else {
            row.innerHTML = `
                <td>${task.policyTitle}</td>
                <td>${task.dateSubmitted ? new Date(task.dateSubmitted).toLocaleDateString() : '---'}</td>
                <td style="font-weight:bold;">${task.status}</td>
            `;
            const actionCell = row.insertCell();
            actionCell.innerHTML = `<button class="action-btn-inline" style="background:#293A82; color:white;" onclick="openTracker(${task.statusCode}, '${task.policyTitle}')"><i class="fas fa-eye"></i> Track</button>`;
        }
    });
}

// ==========================================
// VISUAL TRACKER LOGIC
// ==========================================
function openTracker(statusCode, title) {
    document.getElementById('workspaceTable').style.display = 'none';
    document.getElementById('workspaceHeaderArea').style.display = 'none';
    document.getElementById('trackerTimelineUI').style.display = 'block';
    document.getElementById('trackerDocTitle').textContent = title;

    document.querySelectorAll('.timeline-step').forEach(s => s.className = 'timeline-step');
    document.querySelectorAll('.timeline-line').forEach(l => l.className = 'timeline-line');

    const steps = ['submit', 'review', 'verify', 'approve'];
    for (let i = 0; i < steps.length; i++) {
        let stepEl = document.getElementById('step-' + steps[i]);
        if (i < statusCode) {
            stepEl.classList.add('completed');
            if (i > 0) document.querySelectorAll('.timeline-line')[i-1].classList.add('completed');
        } else if (i === statusCode - 1 || (statusCode === 1 && i === 0)) { 
            stepEl.classList.add('active');
        }
    }
}

function closeTracker() {
    document.getElementById('trackerTimelineUI').style.display = 'none';
    document.getElementById('workspaceHeaderArea').style.display = 'block';
    document.getElementById('workspaceTable').style.display = 'table';
}

// ==========================================
// PDF VIEWER / DOCUMENT INTRODUCTION LOGIC
// ==========================================
function showTaskIntroduction(policyID, policyTitle, policyContent, pdfPath, policyStatus) {
    console.log('showTaskIntroduction called with:', { policyID, policyTitle, policyContent, pdfPath, policyStatus });
    
    currentTaskPolicyID = policyID;
    currentTaskStatus = policyStatus;
    currentTaskPolicyTitle = policyTitle; // ✨ NEW

    document.getElementById('workspaceHeaderArea').style.display = 'none'; 
    document.getElementById('workspaceTable').style.display = 'none';
    document.getElementById('workspaceDocViewer').style.display = 'flex';
    
    // Update title immediately and with fallback
    const updateTitle = (title) => {
        const titleEl = document.getElementById('policyTitleDisplay');
        if (titleEl) {
            titleEl.textContent = title || 'Untitled Policy';
        }
    };
    
    updateTitle(policyTitle);
    
    // Also update after a short delay to ensure DOM is ready
    setTimeout(() => updateTitle(policyTitle), 50);

    const actionButtons = document.getElementById('qadActionButtons');
    if (actionButtons) actionButtons.style.display = 'flex';

    const uploadBtn = document.getElementById('qadUploadBtn');
    const assignBtn = document.getElementById('qadAssignBtn');
    const eSignBtn = document.getElementById('qadESignBtn');
    const rejectBtn = document.getElementById('qadRejectBtn');

    if (uploadBtn) uploadBtn.style.display = 'none';
    if (assignBtn) assignBtn.style.display = 'none';
    if (eSignBtn) eSignBtn.style.display = 'none';
    if (rejectBtn) rejectBtn.style.display = 'none';

    if (systemRoleID == 2) {
        if (assignBtn) assignBtn.style.display = 'flex';
        if (rejectBtn) rejectBtn.style.display = 'flex';
        if (uploadBtn) {
            uploadBtn.style.display = 'flex';
            uploadBtn.disabled = !(policyStatus === 'Approved' || policyStatus === 'For Upload');
        }
    } else {
        if (eSignBtn) {
            eSignBtn.style.display = 'flex';
            const eSignText = eSignBtn.querySelector('span');
            if (eSignText) {
                if (policyStatus === 'Pending') eSignText.textContent = 'Review';
                else if (policyStatus === 'Reviewed') eSignText.textContent = 'Verify';
                else eSignText.textContent = 'Approve';
            }
        }
    }

    const tmPdfWrapper = document.getElementById('tmPdfWrapper');
    const customPdfToolbar = document.getElementById('customPdfToolbar');
    const pdfCanvasContainer = document.getElementById('pdfCanvasContainer');

    if (tmPdfWrapper) {
        let placeholder = document.getElementById('tmPdfPlaceholder');
        if (!placeholder) {
            placeholder = document.createElement('div');
            placeholder.id = 'tmPdfPlaceholder';
            placeholder.style = 'display:flex; flex-direction:column; justify-content:center; align-items:center; height:65vh; background-color:#E0E0E0; color:#555; border-radius:8px;';
            placeholder.innerHTML = `<i class="fas fa-file-pdf fa-3x" style="margin-bottom:15px; color:#A0A0A0;"></i><span style="font-size: 24px; font-weight:bold;">No Document Uploaded Yet</span>`;
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
            }
        } else {
            if (customPdfToolbar) customPdfToolbar.style.display = 'none';
            if (pdfCanvasContainer) pdfCanvasContainer.style.display = 'none';
            placeholder.style.display = 'flex';
        }
    }
}

function showTaskTable() {
    document.getElementById('workspaceDocViewer').style.display = 'none';
    document.getElementById('workspaceHeaderArea').style.display = 'block';
    document.getElementById('workspaceTable').style.display = 'table';
    
    if (tm_ctx && tm_canvas) {
        tm_ctx.clearRect(0, 0, tm_canvas.width, tm_canvas.height);
        tm_canvas.height = 0;
    }
}

// ==========================================
// ASSIGNMENT LOGIC
// ==========================================
function openAssignModalForTask(policyID, status, policyTitle) { // ✨ Added policyTitle
    currentTaskPolicyID = policyID;
    currentTaskStatus = status;
    currentTaskPolicyTitle = policyTitle;
    
    let requiredRole = "Verifier";
    if (status === 'Verified') requiredRole = "Approver";

    document.getElementById('assignTaskTitle').textContent = `Assign ${requiredRole}`;
    
    // ✨ NEW: Inject the policy title into the subtitle!
    document.getElementById('assignTaskSubtitle').innerHTML = `Assigning for: <strong style="color:white; font-size: 18px;">${policyTitle}</strong><br><span style="font-size:14px;">Select an employee from the list below.</span>`;
    
    document.getElementById('assignTaskOverlay').style.display = 'flex';
    document.getElementById('assignLoading').style.display = 'block';
    document.getElementById('assignEmployeesList').style.display = 'none';
    
    const confirmAssignTaskBtn = document.getElementById('confirmAssignTaskBtn');
    confirmAssignTaskBtn.disabled = true;
    confirmAssignTaskBtn.style.opacity = '0.5';
    confirmAssignTaskBtn.style.cursor = 'not-allowed';
    
    assignSelectedAccID = null;

    fetch('/qms_optiqual/generalComponents/taskManager/fetchAssignees.php?t=' + new Date().getTime())
        .then(res => res.json())
        .then(data => {
            document.getElementById('assignLoading').style.display = 'none';
            if (data.success) {
                renderEmployeeList(data.employees); 
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
}

function renderEmployeeList(emps) {
    const empList = document.getElementById('assignEmployeesList');
    empList.innerHTML = '';
    
    if (!emps || emps.length === 0) {
        empList.innerHTML = '<p style="color:white; text-align:center; margin-top:30px; font-size: 18px;">No employees found.</p>';
    } else {
        emps.forEach(emp => {
            const empDiv = document.createElement('div');
            empDiv.className = 'assign-emp-item';
            empDiv.innerHTML = `
                <i class="fas fa-user-circle" style="font-size: 32px; margin-right: 15px; color: #293A82;"></i>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-weight: bold; font-size: 16px;">${emp.fullName}</span>
                    <span style="font-size: 13px; color: #444; margin-top:2px;">${emp.email}</span>
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
                const confirmAssignTaskBtn = document.getElementById('confirmAssignTaskBtn');
                confirmAssignTaskBtn.disabled = false; 
                confirmAssignTaskBtn.style.opacity = '1'; 
                confirmAssignTaskBtn.style.cursor = 'pointer';
            };
            empList.appendChild(empDiv);
        });
    }
    empList.style.display = 'flex';
}

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Attach PDF listeners
    if (document.getElementById('tm_prevPage')) document.getElementById('tm_prevPage').addEventListener('click', tm_onPrevPage);
    if (document.getElementById('tm_nextPage')) document.getElementById('tm_nextPage').addEventListener('click', tm_onNextPage);
    if (document.getElementById('tm_zoomIn')) document.getElementById('tm_zoomIn').addEventListener('click', tm_onZoomIn);
    if (document.getElementById('tm_zoomOut')) document.getElementById('tm_zoomOut').addEventListener('click', tm_onZoomOut);
    tm_canvas = document.getElementById('tm_pdfCanvas');
    if(tm_canvas) tm_ctx = tm_canvas.getContext('2d');

    // Fetch the dual arrays (Action & Track)
    const fetchUrl = '/qms_optiqual/generalComponents/taskManager/fetchTasks.php?t=' + new Date().getTime();
    fetch(fetchUrl, {
        method: 'GET',
        headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error fetching tasks:', data.error);
        } else {
            workspaceData = data; 
            switchWorkspaceTab('action'); 
        }
    })
    .catch(error => console.error('Fetch Error:', error));

    // Sign Modal Logic
    const qadESignBtn = document.getElementById('qadESignBtn');
    const eSignOverlay = document.getElementById('eSignOverlay');
    const confirmESignBtn = document.getElementById('confirmESignBtn');
    const eSignPasswordInput = document.getElementById('eSignPasswordInput');

    if (qadESignBtn) {
        qadESignBtn.addEventListener('click', () => {
            eSignOverlay.style.display = 'flex';
            eSignPasswordInput.value = ''; 
        });
    }

    if (confirmESignBtn) {
        confirmESignBtn.addEventListener('click', () => {
            const pwd = eSignPasswordInput.value.trim();
            if (!pwd) return alert("You must enter your password to sign.");

            confirmESignBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing...';
            confirmESignBtn.disabled = true;

            fetch('/qms_optiqual/generalComponents/taskManager/eSignTaskBE.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ policyID: currentTaskPolicyID, password: pwd })
            })
            .then(res => res.json())
            .then(data => {
                confirmESignBtn.innerHTML = 'Sign & Submit';
                confirmESignBtn.disabled = false;
                if (data.success) {
                    alert(data.message + "\n\nHash:\n" + data.signatureHash);
                    eSignOverlay.style.display = 'none';
                    location.reload(); 
                } else alert(data.message);
            });
        });
    }

    // Attach click event to the main Assign Button
    const qadAssignBtn = document.getElementById('qadAssignBtn');
    if (qadAssignBtn) {
        qadAssignBtn.addEventListener('click', () => {
            if (!currentTaskPolicyID) return alert("No task selected.");
            // ✨ NEW: Pass the title into the modal!
            openAssignModalForTask(currentTaskPolicyID, currentTaskStatus, currentTaskPolicyTitle); 
        });
    }

    // Submit Assignment Logic
    const confirmAssignTaskBtn = document.getElementById('confirmAssignTaskBtn');
    if (confirmAssignTaskBtn) {
        confirmAssignTaskBtn.addEventListener('click', () => {
            if (!assignSelectedAccID) return alert("Please select an employee.");
            const requiredRole = document.getElementById('assignTaskTitle').textContent.includes('Verifier') ? 'Verifier' : 'Approver';

            fetch('/qms_optiqual/generalComponents/taskManager/assignTaskBE.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ policyID: currentTaskPolicyID, assigneeID: assignSelectedAccID, roleType: requiredRole })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`${requiredRole} successfully assigned!`);
                    document.getElementById('assignTaskOverlay').style.display = 'none';
                    location.reload(); 
                } else alert("Error: " + data.message);
            });
        });
    }
});
</script>