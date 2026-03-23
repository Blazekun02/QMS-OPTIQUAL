<?php 
//start session
if(!session_id()){
    session_start();
}

//include filepaths
require_once __DIR__ . '/../../filepaths.php';

//include set message
require_once genMsg_dir . '/setMessage.php';
?>

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
    /* POLICY INTRODUCTION (FIGMA PDF VIEW)      */
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
        justify-content: space-between; /* Pushes left block and right block to opposite ends */
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

    /* Zero-Conflict PDF Container */
    .pdf-container-wrapper {
        flex-grow: 1;
        width: 100%;
        background-color: white; 
        border-radius: 8px;
        overflow: hidden;
        margin-top: 10px;
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
    #revisionOverlay .reply-modal { padding: 1.5em; width: 35%; max-width: 35%; max-height: 25vh; align-items: center; }
    #revisionOverlay .reply-header { width: 100%; justify-content: center; margin-top: -0.5em; }
    #revisionOverlay .reply-header h2 { font-size: 1.8em; margin-left: 1em; }
    #revisionOverlay .reply-modal .reply-header .close-button { opacity: 0.8; margin-top: -1em; margin-right: -0.7em; }
    #revisionOverlay .reply-modal .reply-header .close-button:hover { opacity: 1; }
    #revisionOverlay .reply-content { align-items: left; width: 90%; }
    #revisionOverlay .attach-option { display: flex; align-items: left; gap: 0.5em; font-size: 1.2em; margin-left: -0.8em; }
    #revisionOverlay .attach-option svg { width: 1.3em; height: 1.3em; fill: white; }
    #revisionOverlay .reply-content button { padding: 0.3em 1.8em; margin-left: 1em; }
    #revisionOverlay .reply-content button#cancelRevision { background-color: #D3D3D3; margin-left: 4em; border: 0.1em solid #293A82; }
    #revisionOverlay .reply-content button#cancelRevision:hover { background-color: Grey; }
    #revisionOverlay .reply-content button#submitRevision { background-color: #fbaf41; }
    #revisionOverlay .reply-content button#submitRevision:hover { background-color: #db8804; }
    #revisionOverlay .reply-content .button-container { display: flex; gap: 1em; margin-top: -0.7em; width: 100%; align-items: flex-start; flex-wrap: wrap; }
    #submitRevisionConfirmationOverlay { background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1001; display: none; }
    .submit-revision-confirmation-modal { background-color: #293A82; color: white; padding: 2vw; border-radius: 1vw; text-align: center; width: 30%; max-width: 30%; }
    .submit-revision-confirmation-modal h2 { margin-top: 0; margin-bottom: 1.9vw; font-size: 2vw; font-weight: bold; }
</style>

<!--FOR TASK MANAGER-->
<div class="task-manager">
    <div class="task-manager-header-container">
        <h2 class="task-header">Task Manager <br> </h2>
        <div class="taskWhite-line" style="display: flex"></div>
    </div>

    <!-- INTRODUCTION / PDF VIEW SECTION -->
    <div class="introduction-section" style="display: none;">
        
        <div class="introduction-header">
            <div class="header-left">
                <button class="back-button" onclick="showTable()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span class="introduction-title">[Policy Title from Database]</span>
            </div>
            
            <!-- NEW FIGMA ACTION BUTTONS -->
            <div class="qad-action-buttons" id="qadActionButtons" style="display: none;">
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

        <!-- The Safe iframe PDF Viewer -->
        <div class="pdf-container-wrapper" id="tmPdfWrapper" style="display: none;">
            <iframe id="tm-pdf-frame" src="" width="100%" height="100%" style="border: none; min-height: 65vh; background-color: white; border-radius: 8px;"></iframe>
        </div>
        
    </div>

    <!-- MODALS -->
    <!-- (Your modals remain identical) -->
    <div id="replyOverlay" class="overlay" style="display: none;">
        <div class="reply-modal">
            <div class="reply-header">
                <h2>Reply</h2>
                <button class="close-button" onclick="closeReplyModal()">&times;</button>
            </div>
            <div class="reply-content">
                <form id="replyForm" action="/qms_optiqual/generalComponents/taskManager/TMReplyBE.php" method="POST">
                    <label>
                        <textarea name="replyMessage" id="replyMessage" placeholder="Enter your reply here..."></textarea>
                    </label>
                    <input type="hidden" name="submitReplybtn" value="1">
                    <div class="character-counter">
                        <span id="charCount">0</span>/255
                    </div>
                <button type="button" class="submit-reply-button" name="submitReplybtn">Submit</button>
            </form>
            </div>
        </div>
    </div>
    <div id="confirmReplyOverlay" class="overlay" style="display: none;">
        <div class="confirm-reply-modal">
            <h2>Confirm Reply?</h2>
            <div class="confirm-actions">
                <button class="cancel-button" onclick="closeConfirmReply()">Cancel</button>
                <button class="confirm-button" onclick="handleReplyConfirmation()">Confirm</button>
            </div>
        </div>
    </div>
    <div id="revisionOverlay" class="overlay" style="display: none;">
        <div class="reply-modal">
            <div class="reply-header">
                <h2>Request for Revision</h2>
                <button class="close-button" onclick="closeRevisionModal()">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 1.2em; height: 1.2em;">
                        <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="reply-content">
                <p class="attach-option">
                    <i class="fa fa-paperclip" style="margin-right: 0.5em;"></i> Attach
                </p>
                <div class="button-container">
                    <button id="cancelRevision">Cancel</button>
                    <button id="submitRevision">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div id="submitRevisionConfirmationOverlay">
        <div class="submit-revision-confirmation-modal">
            <h2>Confirm Submission?</h2>
            <div class="confirm-actions">
                <button id="revisionConfirmNo" class="cancel-button">No</button>
                <button id="revisionConfirmYes" class="confirm-button">Yes</button>
            </div>
        </div>
    </div>
    <div id="downloadConfirmationOverlay" class="overlay" style="display: none;">
        <div class="download-confirm-modal">
            <div class="download-header">
                <i class="fa fa-download fa-2x" style="margin-right: 0.5em; margin-top: -0.2em; font-size: 1.5em;"></i>
                <h2>Confirm Download?</h2>
            </div>
            <div class="download-actions">
                <button id="downloadConfirmNo" class="cancel-button">No</button>
                <button id="downloadConfirmYes" class="confirm-button">Yes</button>
            </div>
        </div>
    </div>

    <!-- MAIN TASK TABLE -->
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

function populateTaskTable(tasks) {
    const tableBody = document.getElementById('taskTableBody');
    tableBody.innerHTML = ''; 

    if (!Array.isArray(tasks)) {
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
            showTaskIntroduction(task.policyTitle, task.description, task.pdfPath, task.status);
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

function showTaskIntroduction(policyTitle, policyContent, pdfPath, policyStatus) {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const introductionTitleElement = introductionSection.querySelector('.introduction-title');
    
    // Grab the new conflict-free iframe wrapper
    const tmPdfWrapper = document.getElementById('tmPdfWrapper');
    const tmPdfFrame = document.getElementById('tm-pdf-frame');
    const actionButtons = document.getElementById('qadActionButtons');
    
    // Set Title
    if (introductionTitleElement) introductionTitleElement.textContent = policyTitle;

    // Hide Main Table
    if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'none'; 
    if (taskManagerTable) taskManagerTable.style.display = 'none';

    // Show Section Wrapper
    if (introductionSection) introductionSection.style.display = 'flex';

    // 1. SHOW ACTION BUTTONS IMMEDIATELY 
    if (actionButtons) actionButtons.style.display = 'flex';

    // 2. CHECK STATUS FOR UPLOAD BUTTON
    const uploadBtn = document.getElementById('qadUploadBtn');
    if (uploadBtn) {
        if (policyStatus === 'Approved') {
            uploadBtn.disabled = false;
        } else {
            uploadBtn.disabled = true;
        }
    }

    // 3. SHOW PDF VIEWER
    if (tmPdfWrapper) tmPdfWrapper.style.display = 'block'; 

    // 4. LOAD THE PDF DIRECTLY INTO THE IFRAME
    if (tmPdfFrame && pdfPath) {
        tmPdfFrame.src = pdfPath;
    }
}

function showTaskTable() {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const actionButtons = document.getElementById('qadActionButtons');
    const tmPdfFrame = document.getElementById('tm-pdf-frame');

    if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'block';
    if (taskManagerTable) taskManagerTable.style.display = 'table';
    if (introductionSection) introductionSection.style.display = 'none';
    if (actionButtons) actionButtons.style.display = 'none'; 
    
    // Clear the PDF source so it doesn't stay loaded in memory when navigating away
    if (tmPdfFrame) tmPdfFrame.src = "";
}

function showReplyModal() { const o = document.getElementById('replyOverlay'); if (o) o.style.display = 'flex'; }
function closeReplyModal() { const o = document.getElementById('replyOverlay'); if (o) o.style.display = 'none'; }
function showConfirmReply() {
    const rm = document.getElementById('replyMessage').value.trim();
    if (!rm) { alert('Reply message cannot be empty.'); return; }
    const o = document.getElementById('confirmReplyOverlay');
    if (o) o.style.display = 'flex';
}
function closeConfirmReply() { const o = document.getElementById('confirmReplyOverlay'); if (o) o.style.display = 'none'; }
function handleReplyConfirmation() {
    document.getElementById('confirmReplyOverlay').style.display = 'none';
    document.getElementById('replyOverlay').style.display = 'none';
    document.getElementById('replyForm').submit();
}
function showRevisionModal() { const o = document.getElementById('revisionOverlay'); if (o) o.style.display = 'flex'; }
function closeRevisionModal() { const o = document.getElementById('revisionOverlay'); if (o) o.style.display = 'none'; }

document.addEventListener('DOMContentLoaded', function() {
    fetch('/qms_optiqual/generalComponents/taskManager/fetchTasks.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching tasks:', data.error);
            } else {
                populateTaskTable(data); 
                // Show the table and header after populating
                const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
                const taskManagerTable = document.querySelector('.task-manager-table');
                if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'block';
                if (taskManagerTable) taskManagerTable.style.display = 'table';
            }
        })
        .catch(error => console.error('Error:', error));

    const replyMessage = document.getElementById('replyMessage');
    const charCount = document.getElementById('charCount');
    const maxChars = 255; 

    if (replyMessage && charCount) {
        replyMessage.addEventListener('input', function () {
            const currentLength = replyMessage.value.length;
            charCount.textContent = currentLength;
            if (currentLength > maxChars) {
                charCount.style.color = 'red'; 
            } else {
                charCount.style.color = 'black'; 
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const submitOverlay = document.getElementById('submitRevisionConfirmationOverlay');
    const noBtn = document.getElementById('revisionConfirmNo');
    const yesBtn = document.getElementById('revisionConfirmYes');
    const trigBtn = document.getElementById('submitRevision'); 
    const revOverlay = document.getElementById('revisionOverlay'); 

    if (trigBtn && submitOverlay && revOverlay) {
        trigBtn.addEventListener('click', function () {
            revOverlay.style.display = 'none'; 
            submitOverlay.style.display = 'flex'; 
        });
    }

    if (noBtn && submitOverlay) {
        noBtn.addEventListener('click', function () {
            submitOverlay.style.display = 'none';
            showRevisionModal();
        });
    }

     if (yesBtn && submitOverlay) {
        yesBtn.addEventListener('click', function () {
            submitOverlay.style.display = 'none';
            alert('Submission confirmed!');
        });
    }
});
</script>