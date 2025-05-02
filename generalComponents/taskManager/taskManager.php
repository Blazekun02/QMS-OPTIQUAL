<?php 
//include filepaths
require_once __DIR__ . '/../../filepaths.php';

//include set message
require_once genMsg_dir . '/setMessage.php';
?>

<style>
    /* for Task manager*/
    .task-manager {
            width: 93%;
            float: right;
            margin: 8vh 0 0 0;
            background-color: #293A82;
            display: none;
            padding: 2%;
            color: white;
            position: relative;
            height: calc(100vh - 8vh); /* Make it take up the full height of the screen */
            border-radius: 20px; /* Add rounded corners on the left side */
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


        /*task manager table*/

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

        .task-manager-table td:nth-child(4) {
          text-align: left;
        }

/* WHEN ROW IN THE TABLE IS CLICKED */

        .introduction-section {
            width: 100%;
            height: 100%;
        }

        .introduction-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5vh;
            padding-right: 2vw;
        }

        .back-button {
            background-color: white;
            border: none;
            padding: 0;
            border-radius: 0.3em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            margin-right: 0.5vw;
            position: relative;
            overflow: visible;
            margin-left: -0.7vw;
            margin-top: -0.3em;
        }

        .back-button.with-overlay::before {
           content: "Back";
            font-size: 2.2vh;
            position: absolute;
            top: -1.96vw;
            left: -1vw;
            background-color: transparent;
            color: white;
            padding: 0.5vh 1vw;
            border-radius: 0.3vh;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            z-index: 1;
            white-space: nowrap;
        }

        .back-button.with-overlay:hover::before {
          opacity: 1;
        }

        .back-arrow {
            font-size: 3.5vh;
            border: none;
            border-radius: 3%;
            font-weight: bold;
            color: #293A82;
            display: inline-block;
            padding: 0.1vw;
            line-height: 1;
            background-color: transparent;
        }

        .back-arrow hover{
            color: black;
        }

        .introduction-title {
            font-size: 5vh;
            font-weight: bold;
            margin-left: 1vw;
        }

        .header-actions {
            display: flex;
            align-items: center;
            position: absolute;
            top: 8%;
            right: 2%;
            transform: translateY(-50%);
        }

        .view-policy-button {
            border-radius: 1.5vh;
            border: none;
            color: #293A82;
            font-weight: bold;
            font-size: 2.5vh;
            text-align: center;
        }
/* Style for QAP policy management buttons */
        .QAP_management_btns {
            display: none;
        }


/*hamburger button in task manager*/
        .menu-icon {
            margin-left: 0.5vw;
            font-size: 3.7vh;
            border: none;
            border-radius: 3%;
            background-color: #293A82;
            color: white;
            cursor: pointer;
            text-align: center;
        }

        .menu-dropdown {
            position: absolute;
            top: 14%;
            right: 0%;
            background-color: #293A82;
            border-radius: 2vh;
            z-index: 1;
            display: none;
            margin-top: 0.5vh;
            Width: auto;
            min-width: 15%;
        }

        .dropdown-button {
            display: block;
            padding: 3.5% 3.5%;
            margin-bottom: 0.5vh;
            text-align: center;
            border: none;
            border-radius: 2vh;
            background-color: #fbaf41;
            color: black;
            cursor: pointer;
            font-size: 80%;
            width: 85%;
            height: auto;
        }

        .dropdown-button:hover {
            background-color: #db8804;
        }

/* reply button overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
        }

/* Reply Modal Styles */
        .reply-modal {
            background-color: #293A82;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            height: 75%;
            position: relative;
        }

        .reply-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2%;
        }

        .reply-header h2 {
            margin: 0;
            font-size: 200%;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }

        .close-button {
            background: none;
            border: none;
            color: #f44336;
            font-size: 30px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .reply-content{
            display: flex;
            flex-direction: column;
            gap: 1%;
            margin-bottom: 15%;
        }

        .reply-content textarea {
            width: 57%;
            height: 40%;
            padding: 5%;
            padding-top: 1%;
            padding-bottom: 5%;
            padding-left: 1%;
            padding-right: 5%;
            margin-bottom: 5%;
            border: 1px solid #ccc;
            border-radius: 1%;
            box-sizing: border-box;
            color: black;
            flex-grow: 1;
            min-height: 50vh;
            position: fixed;
            resize: none;
        }

        .reply-content .submit-reply-button {
            background-color: #fbaf41;
            color: black;
            border: none;
            padding: 1% 3%;
            border-radius: 1em;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            position: absolute;
            margin-top: 43%;
            margin-left: 40%;
        }

        .reply-content .submit-reply-button:hover {
            background-color: #db8804;
        }

/* this is for the overlay of the submit button in reply*/
        .confirm-reply-modal {
            background-color: #293A82;
            color: white;
            padding: 1.5vw;
            border-radius: 1vw;
            width: 25%;
            height: 25%;
            text-align: center;
        }

        .confirm-reply-modal h2 {
            margin-top: 0;
            margin-bottom: 2.5vw;
            font-size: 2.5vw;
            font-weight: bold;
        }

        .confirm-actions {
            display: flex;
            justify-content: center;
            gap: 1.5vw;
            margin-top: 1.5vw;
        }

        .confirm-actions button {
            padding: 0.6vw 1.8vw;
            border: none;
            border-radius: 1em;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
        }

        .confirm-actions .cancel-button {
            background-color: D3D3D3;
            color: black;
        }

        .confirm-actions .confirm-button {
            background-color: #fbaf41;
            color: black;
        }

        .confirm-actions .cancel-button:hover {
            background-color: grey;
            color: white;
        }

        .confirm-actions .confirm-button:hover {
            background-color: #db8804;
        }

/* REQUEST FOR REVISION BUTTON*/
      #revisionOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
        }

        #revisionOverlay .reply-modal {
            background-color: #293A82;
            color: white;
            padding: 1.5em;
            border-radius: 0.3em;
            width: 35%;
            max-width: 35%;
            max-height: 25vh;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #revisionOverlay .reply-header {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1em;
            margin-top: -0.5em;
        }

        #revisionOverlay .reply-header h2 {
            margin: 0;
            font-size: 1.8em;
            font-weight: bold;
            text-align: center;
            margin-left: 1em;
        }

        #revisionOverlay .reply-modal .reply-header .close-button {
            background: none;
            border: none;
            color: #f44336;
            font-size: 1.2em;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.3s ease-in-out;
            margin-top: -1em;
            margin-right: -0.7em;
        }

        #revisionOverlay .reply-modal .reply-header .close-button:hover {
            opacity: 1;
        }

        #revisionOverlay .reply-content {
            display: flex;
            flex-direction: column;
            align-items: left;
            gap: 1em;
            width: 90%;
        }

        #revisionOverlay .attach-option {
            display: flex;
            align-items: left;
            gap: 0.5em;
            font-size: 1.2em;
            margin-left: -0.8em;
        }

        #revisionOverlay .attach-option svg {
            width: 1.3em;
            height: 1.3em;
            fill: white;
        }

        #revisionOverlay .reply-content button {
            padding: 0.3em 1.8em;
            border-radius: 1em;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            align-items: center;
            margin-left: 1em;
        }

        #revisionOverlay .reply-content button#cancelRevision {
            background-color:#D3D3D3;
            color: black;
            margin-left: 4em;
            border: 0.1em solid #293A82;
        }

        #revisionOverlay .reply-content button#cancelRevision:hover {
            background-color: Grey;
        }

        #revisionOverlay .reply-content button#submitRevision {
            background-color: #fbaf41;
            color: black;
            border: none;
        }

        #revisionOverlay .reply-content button#submitRevision:hover {
            background-color: #db8804;
        }

        #revisionOverlay .reply-content .button-container {
            display: flex;
            gap: 1em;
            margin-top: -0.7em;
            width: 100%;
            align-items: flex-start;
            flex-wrap: wrap;
        }


/* SUBMIT REVISION CONFIRMATION OVERLAY */
        #submitRevisionConfirmationOverlay {
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1001;
            display: none;
        }

        .submit-revision-confirmation-modal {
            background-color: #293A82;
            color: white;
            padding: 2vw;
            border-radius: 1vw;
            text-align: center;
            width: 30%;
            max-width: 30%;
        }

        .submit-revision-confirmation-modal h2 {
            margin-top: 0;
            margin-bottom: 1.9vw;
            font-size: 2vw;
            font-weight: bold;
        }

        .submit-revision-confirmation-modal .confirm-actions {
            display: flex;
            justify-content: center;
            gap: 1.5vw;
            margin-top: 1.5vw;
        }

        .submit-revision-confirmation-modal .confirm-actions button {
            padding: 0.4vw 2.3vw;
            border: none;
            border-radius: 1em;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
        }

        .submit-revision-confirmation-modal .confirm-actions .cancel-button {
            background-color: white;
            color: black;
        }

        .submit-revision-confirmation-modal .confirm-actions .confirm-button {
            background-color: #fbaf41;
            color: black;
        }

        .submit-revision-confirmation-modal .confirm-actions .cancel-button:hover {
            background-color: #D3D3D3;
            color: black;
        }

        .submit-revision-confirmation-modal .confirm-actions .confirm-button:hover {
            background-color: #db8804;
        }


/* Download of change request form Confirmation Modal Styles */
        .download-confirm-modal {
            background-color: #293A82;
            color: white;
            padding: 2vw;
            border-radius: 1vw;
            text-align: center;
            width: 28%;
            max-width: 28%;
            height: 23%;
        }

        .download-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2.8vw;
        }

        .download-header h2 {
            margin: 0;
            font-size: 2vw;
            font-weight: bold;
        }

        .download-actions {
            display: flex;
            justify-content: center;
            gap: 1.5vw;
            margin-top: 1.5vw;
        }

        .download-actions button {
            padding: 0.4vw 2.3vw;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
        }

        .download-actions .cancel-button {
            background-color: white;
            color: black;
        }

        .download-actions .confirm-button {
            background-color: #fbaf41;
            color: black;
        }

        .download-actions .cancel-button:hover {
            background-color: #D3D3D3;
            color: black;
        }

        .download-actions .confirm-button:hover {
            background-color: #db8804;
        }

/* Buttons for document signing for either review, verification, approval */
        .signDocuBtns {
            display: none;
        }

</style>

<!--FOR TASK MANAGER-->
<div class="task-manager">
    <div class="task-manager-header-container">
        <h2 class="task-header">Task Manager <br> </h2>
        <div class = "taskWhite-line" style="display: flex">
        </div>
    </div>

    <div class="introduction-section" style="display: none;">
        <div class="introduction-header">
            <button class="back-button with-overlay" onclick="showTable()">
                <span class="back-arrow">&larr;</span>
            </button>

            <span class="introduction-title">[Policy Title from Database]</span>
            <div class="header-actions">
                <button id="viewPolicyButton" class="view-policy-button">View Policy</button>
                <button class="menu-icon">&#9776;</button>
            </div>

            <!-- For Staff -->
            <div class="menu-dropdown">
                <button class="dropdown-button">Reply</button>
                <button class="dropdown-button" onclick="showRevisionModal()">Request for Revision</button>
                <button class="dropdown-button">Download Change Request Form</button>
                <button class="dropdown-button">Submit Revision</button>
                <button class="dropdown-button">Download Editable File</button>
            </div>

            <!-- QAP policy management buttons-->
            <div class="QAP_management_btns">
                <button class="QAP_general_btns">Reject</button>
                <button class="QAP_general_btns">Assign</button>
                <button class="QAP_general_btns">Upload</button>
                <button class="QAP_revisionRequest_btns">Reject</button>
                <button class="QAP_revisionRequest_btns">Send</button>
            </div>

            <div class = "taskWhite-line" style="margin-top: 1vh; display: flex"></div>
        </div>

        <div class="signDocuBtns">
            <form action="signDocument.php" method="POST">
                <button type="button" id="rejectDocuBtn" name = "reject">Reject Document</button>  
                <button type="submit" id="signDocuBtn" name="sign">Sign Document</button>
            </form>     
        </div>

        <p class="introduction-content">[Policy Description/Content Here]</p>
        <div id="policyFeedbackContent" style="display: none; margin-top: 20px; background-color:transparent; color: white;">
            <h3>Policy Feedback Placeholder</h3>
            <p>This section will eventually display the actual policy feedback fetched from the database.</p>
            <p>For now, this is just a placeholder to show where the feedback message will appear when the selected policy is opened.</p>
        </div>
        <?php 
        //include pdfViewer
        require_once pdfViewer_dir . '/pdfViewer.php'; ?>
    </div>

    <div id="replyOverlay" class="overlay" style="display: none;">
        <div class="reply-modal">
            <div class="reply-header">
                <h2>Reply</h2>
                <button class="close-button" onclick="closeReplyModal()">&times;</button>
            </div>
            <div class="reply-content">
                <label>
                    <textarea placeholder="Enter your reply here..."></textarea>
                </label>
                <button class="submit-reply-button">Submit</button>
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


    <table class="task-manager-table" style="display: none;">
        <thead>
        <tr>
            <th>Policy Title</th>
            <th> Author</th>
            <th>Date Submitted</th>
            <th>Version no.</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody id="taskTableBody">
        </tbody>
    </table>
</div>



<script>
    //this is for the js of task manager
function showTaskManager() {
    document.getElementById('policies-repository-content').style.display = 'none';
    document.getElementById('policy-submission-content').style.display = 'none';
    document.querySelector('.process-tracker').style.display = 'none';
    document.querySelector('.task-manager').style.display = 'flex';
    document.querySelector('.information').style.display = 'none';

    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');

    taskManagerHeaderContainer.style.display = 'block'; // Show header and line
    taskManagerTable.style.display = 'table'; // Show the table initially
    introductionSection.style.display = 'none'; // Ensure introduction is hidden initially
}

//show selected policy content when clicked
function showIntroduction(policyTitle, policyContent, pdfPath) {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const introductionTitleElement = introductionSection.querySelector('.introduction-title');
    const introductionContentElement = introductionSection.querySelector('.introduction-content');
    const policyFeedbackContent = document.getElementById('policyFeedbackContent'); // Get the placeholder
    const pdfViewerContainer = document.querySelector('.pdfViewerContainer'); // Get the PDF viewer container
    const viewPolicyButton = document.getElementById('viewPolicyButton'); // Get the View Policy button
    const introductionContent = document.querySelector('.introduction-content'); // Get the initial content

    introductionTitleElement.textContent = policyTitle;
    introductionContentElement.textContent = policyContent;

    taskManagerHeaderContainer.style.display = 'none'; // Hide header and line
    taskManagerTable.style.display = 'none';
    pdfViewerContainer.style.display = 'none'; // Hide the PDF viewer container
    introductionSection.style.display = 'block';
    policyFeedbackContent.style.display = 'block'; // Show the placeholder  
    introductionContent.style.display = 'block';
    viewPolicyButton.textContent = 'View Policy';

    // Add event listener to dynamically load the PDF when "View Policy" is clicked
    let isPolicyVisible = false;
    viewPolicyButton.addEventListener('click', function () {
        if (!isPolicyVisible) {
            introductionContent.style.display = 'none';
            pdfViewerContainer.style.display = 'block'; // Show the PDF viewer
            policyFeedbackContent.style.display = 'none';
            viewPolicyButton.textContent = 'View Feedback Report';
            isPolicyVisible = true;

            // Dynamically load the PDF into the viewer
            const pdfUrl = `${pdfPath}`; // Adjust the path as needed
            pdfjsLib.getDocument(pdfUrl).promise.then(pdfDoc_ => {
                pdfDoc = pdfDoc_;
                document.getElementById('pageCount').textContent = pdfDoc.numPages;
                renderPage(1); // Render the first page
            });
        } else {
            introductionContent.style.display = 'block';
            pdfViewerContainer.style.display = 'none'; // Hide the PDF viewer
            policyFeedbackContent.style.display = 'block';
            viewPolicyButton.textContent = 'View Policy';
            isPolicyVisible = false;
        }
    });
}   

function populateTaskTable(tasks) {
    const tableBody = document.getElementById('taskTableBody');
    tableBody.innerHTML = ''; // Clear any existing rows

    console.log(tasks); // Log the tasks to check the data
    tasks.forEach(task => {
        const row = tableBody.insertRow();
        row.onclick = function() {
            showIntroduction(task.policyTitle, task.description, task.pdfPath);
        };

        const titleCell = row.insertCell();
        titleCell.textContent = task.policyTitle;

        const authorCell = row.insertCell();
        authorCell.textContent = task.author;

        const dateCell = row.insertCell();
        dateCell.textContent = task.dateSubmitted;

        const versionCell = row.insertCell();
        versionCell.textContent = task.version;

        const statusCell = row.insertCell();
        statusCell.textContent = task.status;
    });
}

// Show the task manager table and hide the introduction section
function showTable() {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const pdfViewerContainer = document.querySelector('.pdfViewerContainer');

    taskManagerHeaderContainer.style.display = 'block';
    taskManagerTable.style.display = 'table';
    introductionSection.style.display = 'none';
    pdfViewerContainer.style.display = 'none';
}

// Show the reply modal when the reply button is clicked
function showReplyModal() {
    const replyOverlay = document.getElementById('replyOverlay');
    if (replyOverlay) {
        replyOverlay.style.display = 'flex';
    }
}

// Close the reply modal when the close button is clicked
function closeReplyModal() {
    const replyOverlay = document.getElementById('replyOverlay');
    if (replyOverlay) {
        replyOverlay.style.display = 'none';
    }
}

// Close the confirmation modal when the close button is clicked
function showConfirmReply() {
    const confirmReplyOverlay = document.getElementById('confirmReplyOverlay');
    if (confirmReplyOverlay) {
        confirmReplyOverlay.style.display = 'flex';
    }
}

// Close the confirmation modal when the close button is clicked
function closeConfirmReply() {
    const confirmReplyOverlay = document.getElementById('confirmReplyOverlay');
    if (confirmReplyOverlay) {
        confirmReplyOverlay.style.display = 'none';
    }
}

// Handle the confirmation of the reply submission
function handleReplyConfirmation() {
    alert("Reply Confirmed!");
    closeConfirmReply();
    closeReplyModal();
}

// Show the revision modal when the revision button is clicked
function showRevisionModal() {
    const revisionOverlay = document.getElementById('revisionOverlay');
    if (revisionOverlay) {
        revisionOverlay.style.display = 'flex';
    }
}

// Close the revision modal when the close button is clicked
function closeRevisionModal() {
    const revisionOverlay = document.getElementById('revisionOverlay');
    if (revisionOverlay) {
        revisionOverlay.style.display = 'none';
    }
}




document.addEventListener('DOMContentLoaded', function() {
    const menuIcon = document.querySelector('.menu-icon');
    const menuDropdown = document.querySelector('.menu-dropdown');
    const replyButton = menuDropdown ? menuDropdown.querySelector('.dropdown-button:nth-child(1)') : null;
    const replyOverlay = document.getElementById('replyOverlay');
    const submitReplyButton = replyOverlay ? replyOverlay.querySelector('.reply-content .submit-reply-button') : null;
    const confirmReplyOverlay = document.getElementById('confirmReplyOverlay');
    const revisionButton = menuDropdown ? menuDropdown.querySelector('.dropdown-button:nth-child(2)') : null;
    const revisionOverlay = document.getElementById('revisionOverlay');
    const revisionPopupContent = document.getElementById('revisionPopupContent'); // Target the correct content div
    const cancelRevisionButton = document.getElementById('cancelRevision');
    const viewPolicyButton = document.getElementById('viewPolicyButton'); // Get the View Policy button
    const policyFeedbackContent = document.getElementById('policyFeedbackContent'); // Get the placeholder
    const introductionContent = document.querySelector('.introduction-content'); // Get the initial content
    const pdfViewerContainer = document.querySelector('.pdfViewerContainer'); // Get the PDF viewer container
    let isPolicyVisible = false;

    if (menuIcon && menuDropdown) {
        menuIcon.addEventListener('click', function(event) {
            event.stopPropagation();
            menuDropdown.style.display = (menuDropdown.style.display === 'block') ? 'none' : 'block';
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.header-actions')) {
                menuDropdown.style.display = 'none';
            }
        });
    }

    if (replyButton && replyOverlay) {
        replyButton.addEventListener('click', function() {
            menuDropdown.style.display = 'none';
            showReplyModal();
        });
    }

    if (submitReplyButton && confirmReplyOverlay) {
        submitReplyButton.addEventListener('click', function() {
            showConfirmReply();
        });
    }

    if (revisionButton && revisionOverlay && revisionPopupContent) { // Ensure revisionPopupContent exists
        revisionButton.addEventListener('click', function() {
            menuDropdown.style.display = 'none';
            showRevisionModal();
        });
    }

    if (cancelRevisionButton && revisionOverlay) {
        cancelRevisionButton.addEventListener('click', function() {
            revisionOverlay.style.display = 'none';
        });
    }

    const downloadChangeRequestButton = document.querySelector('.dropdown-button:nth-child(3)'); // Target the "Download Change Request Form" button
    const downloadConfirmationOverlay = document.getElementById('downloadConfirmationOverlay');
    const downloadConfirmNoButton = document.getElementById('downloadConfirmNo');
    const downloadConfirmYesButton = document.getElementById('downloadConfirmYes');

    if (downloadChangeRequestButton && downloadConfirmationOverlay) {
        downloadChangeRequestButton.addEventListener('click', function() {
            const menuDropdown = document.querySelector('.menu-dropdown');
            if (menuDropdown) {
                menuDropdown.style.display = 'none'; // Close the dropdown
            }
            downloadConfirmationOverlay.style.display = 'flex';
        });
    }

    if (downloadConfirmNoButton && downloadConfirmationOverlay) {
        downloadConfirmNoButton.addEventListener('click', function() {
            downloadConfirmationOverlay.style.display = 'none';
        });
    }

    if (downloadConfirmYesButton && downloadConfirmationOverlay) {
        downloadConfirmYesButton.addEventListener('click', function() {
            downloadConfirmationOverlay.style.display = 'none'; // Hide the confirmation
            alert('Download initiated!'); // Show the alert message
            // In a real application, you would trigger the download here
        });
    }

    // View Policy Button Functionality
    if (viewPolicyButton && policyFeedbackContent && introductionContent) {
        viewPolicyButton.addEventListener('click', function() {
            if (!isPolicyVisible) {
                introductionContent.style.display = 'none';
                pdfViewerContainer.style.display = 'block'; // Show the PDF viewer container
                policyFeedbackContent.style.display = 'none';
                viewPolicyButton.textContent = 'View Feedback Report';
                isPolicyVisible = true;
            } else {
                introductionContent.style.display = 'block';
                pdfViewerContainer.style.display = 'none'; // Hide the PDF viewer container
                policyFeedbackContent.style.display = 'block';
                viewPolicyButton.textContent = 'View Policy';
                isPolicyVisible = false;
            }
        });
    }

    // Fetch task data from the server
    fetch('/qms_optiqual/generalComponents/taskManager/taskManagerBE.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching tasks:', data.error);
            } else {
                populateTaskTable(data); // Populate the table with fetched data
            }
        })
        .catch(error => console.error('Error:', error));
});

//overlay for the submission confirmation in request for revision button

document.addEventListener('DOMContentLoaded', function () {
    const submitRevisionConfirmationOverlay = document.getElementById('submitRevisionConfirmationOverlay');
    const revisionConfirmNoButton = document.getElementById('revisionConfirmNo');
    const revisionConfirmYesButton = document.getElementById('revisionConfirmYes');
    const triggerButton = document.getElementById('submitRevision'); // Correctly target the submit button in the revision modal
    const revisionOverlay = document.getElementById('revisionOverlay'); // Get the revision overlay

    // Show the overlay
    if (triggerButton && submitRevisionConfirmationOverlay && revisionOverlay) {
        triggerButton.addEventListener('click', function () {
            revisionOverlay.style.display = 'none'; // Hide the revision modal
            submitRevisionConfirmationOverlay.style.display = 'flex'; // Show the confirmation overlay
        });
    }

    // Hide the overlay when "No" is clicked
    if (revisionConfirmNoButton && submitRevisionConfirmationOverlay) {
        revisionConfirmNoButton.addEventListener('click', function () {
            submitRevisionConfirmationOverlay.style.display = 'none';
            showRevisionModal();
        });
    }

     if (revisionConfirmYesButton && submitRevisionConfirmationOverlay) {
        revisionConfirmYesButton.addEventListener('click', function () {
            submitRevisionConfirmationOverlay.style.display = 'none';
            alert('Submission confirmed!');
        });
    }
});
</script>