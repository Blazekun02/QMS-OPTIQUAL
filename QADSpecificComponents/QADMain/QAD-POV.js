/* =====================================================================
   0. GLOBAL VARIABLES & DOM ELEMENTS
   ===================================================================== */
// Main Panels
const policyRepositoryPanel = document.getElementById('policy-repo-content');
const policySubmissionPanel = document.getElementById('policy-submission-content');
const departmentPanel = document.querySelector('.Department-Manager-Panel');
const processTrackerPanel = document.querySelector('.Process-Tracker-Panel2'); 
const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
const taskManagerPanel = document.querySelector('.Task-Manager-Panel');

// Overlays & Modals
const notificationOverlay = document.getElementById('popupOverlay');
const signOutOverlay = document.getElementById('signOutOverlay');
const submitOverlay = document.getElementById('submitOverlay');
const cfOverlay = document.getElementById('confirm-dl');


/* =====================================================================
   1. PANEL SWITCHING LOGIC (Sidebar Navigation)
   ===================================================================== */
function hideAllPanels() {
    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (processTrackerPanel) processTrackerPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (taskManagerPanel) taskManagerPanel.style.display = 'none';
}

function showPolicyRepository() {
    console.log("Policy Repository Triggered");
    hideAllPanels();
    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
}

function showPolicySubmission() {
    console.log("Policy Submission Triggered");
    hideAllPanels();
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'flex';
}

function showProcessTracker() {
    console.log("Process Tracker Triggered");
    hideAllPanels();
    if (processTrackerPanel) processTrackerPanel.style.display = 'block';
}

function showTaskManager() {
    console.log('Task Manager Triggered');
    hideAllPanels();
    if (taskManagerPanel) taskManagerPanel.style.display = 'flex';

    // Additional Task Manager-specific logic
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');

    if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'block';
    if (taskManagerTable) taskManagerTable.style.display = 'table';
    if (introductionSection) introductionSection.style.display = 'none';
}

function showDepartmentManager() {    
    console.log("Department Manager Triggered");
    hideAllPanels();
    if (departmentPanel) departmentPanel.style.display = 'block';
}

function showPolicyManager() {
    console.log("Policy Manager Triggered");
    hideAllPanels();
    if (policyManagerPanel) policyManagerPanel.style.display = 'block';
}

// Attach Event Listeners to Sidebar Icons
document.addEventListener('DOMContentLoaded', () => {
    const icons = document.querySelectorAll('.menu-icons');
    if(icons[0]) icons[0].addEventListener('click', showPolicyRepository);
    if(icons[1]) icons[1].addEventListener('click', showPolicySubmission);
    if(icons[2]) icons[2].addEventListener('click', showProcessTracker);
    if(icons[3]) icons[3].addEventListener('click', showTaskManager);
    // Note: Assuming icon[4] is Manage Roles (skipped here based on your code)
    if(icons[5]) icons[5].addEventListener('click', showDepartmentManager);
    if(icons[6]) icons[6].addEventListener('click', showPolicyManager);
});


/* =====================================================================
   2. TOP BAR & SIDEBAR TOGGLE
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    // Sidebar hover/pin behavior
    const sidebar = document.querySelector('.Sidebar');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    let sidebarPinned = false;

    const applySidebarState = (expanded) => {
        if (!sidebar || !hamburgerIcon) return;
        sidebar.classList.toggle('extended', expanded);
        sidebar.style.width = expanded ? '2.4in' : '0.7in';
        hamburgerIcon.style.left = expanded ? '2.5in' : '0.8in';
    };

    if (sidebar && hamburgerIcon) {
        sidebar.addEventListener('mouseenter', () => { if (!sidebarPinned) applySidebarState(true); });
        sidebar.addEventListener('mouseleave', () => { if (!sidebarPinned) applySidebarState(false); });
        hamburgerIcon.addEventListener('click', () => {
            sidebarPinned = !sidebarPinned;
            applySidebarState(sidebarPinned);
        });
    }

    // Top Bar Buttons
    const notifButton = document.getElementById('notifButton');
    if (notifButton && notificationOverlay) {
        notifButton.addEventListener('click', () => {
            notificationOverlay.style.display = notificationOverlay.style.display === 'block' ? 'none' : 'block';
        });
    }

    const userButton = document.getElementById('userButton');
    if (userButton && signOutOverlay) {
        userButton.addEventListener('click', () => {
            signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
        });
        signOutOverlay.addEventListener("click", function () {
            window.location.href = "landingPage.html";
        });
    }
});


/* =====================================================================
   3. POLICY REPOSITORY & PDF VIEWER
   ===================================================================== */
// Folder Accordion Toggle
const parentFolders = document.querySelectorAll('.PR-Parent-Folders');
parentFolders.forEach(folder => {
    folder.addEventListener('click', () => {
        const parentId = folder.getAttribute('data-id');
        document.querySelectorAll('.child-folders').forEach(child => child.style.display = 'none');
        document.querySelectorAll('.Policies-Folder').forEach(policyFolder => policyFolder.style.display = 'none');
        
        const childToShow = document.querySelector(`.child-folders[data-parent-id='${parentId}']`);
        if (childToShow) childToShow.style.display = 'flex';
    });
});

const childFolders = document.querySelectorAll('.PR-Child-Folders');
childFolders.forEach(childFolder => {
    childFolder.addEventListener('click', () => {
        const childId = childFolder.getAttribute('data-id');
        document.querySelectorAll('.Policies-Folder').forEach(pf => pf.style.display = 'none');
        
        const policiesFolderToShow = document.querySelector(`.Policies-Folder[data-pol-id='${childId}']`);
        if (policiesFolderToShow) policiesFolderToShow.style.display = 'flex';
    });
});

// Search Policies
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');

function searchPolicies() {
    const searchTerm = searchInput.value.toLowerCase();
    const allParentFolders = document.querySelectorAll('.PR-Parent-Folders');
    const allChildFolders = document.querySelectorAll('.PR-Child-Folders');
    const allPolicies = document.querySelectorAll('.PR-Policies');

    // Hide everything first
    allParentFolders.forEach(p => p.style.display = 'none');
    document.querySelectorAll('.child-folders').forEach(c => c.style.display = 'none');
    allChildFolders.forEach(c => c.style.display = 'none');
    document.querySelectorAll('.Policies-Folder').forEach(p => p.style.display = 'none');
    allPolicies.forEach(p => p.style.display = 'none');

    // Search and reveal
    allParentFolders.forEach(parent => {
        if (parent.innerText.toLowerCase().includes(searchTerm)) parent.style.display = 'flex';
    });

    allChildFolders.forEach(child => {
        if (child.innerText.toLowerCase().includes(searchTerm)) {
            child.style.display = 'flex';
            const parentId = child.closest('.child-folders').getAttribute('data-parent-id');
            document.querySelector(`.PR-Parent-Folders[data-id='${parentId}']`).style.display = 'flex';
            document.querySelector(`.child-folders[data-parent-id='${parentId}']`).style.display = 'flex';
        }
    });

    allPolicies.forEach(policy => {
        if (policy.innerText.toLowerCase().includes(searchTerm)) {
            policy.style.display = 'flex';
            const policiesFolder = policy.closest('.Policies-Folder');
            policiesFolder.style.display = 'flex';

            const childFolder = policiesFolder.previousElementSibling; 
            if (childFolder) childFolder.style.display = 'flex';

            const parentId = childFolder.closest('.child-folders').getAttribute('data-parent-id');
            document.querySelector(`.PR-Parent-Folders[data-id='${parentId}']`).style.display = 'flex';
            document.querySelector(`.child-folders[data-parent-id='${parentId}']`).style.display = 'flex';
        }
    });
}

if (searchButton) searchButton.addEventListener('click', searchPolicies);
if (searchInput) {
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') searchPolicies();
    });
}

// PDF Viewer Loading & Closing
document.querySelectorAll('.PR-Policies').forEach(policy => {
    policy.addEventListener('click', function () {
        const filePath = policy.getAttribute('data-file'); 
        const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
        
        if(pdfViewerContainer) pdfViewerContainer.style.display = 'block'; 
        if (typeof loadPDF === 'function') {
            loadPDF(filePath); 
        }
        if(policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const closePdfViewerButton = document.getElementById('closePdfViewer');
    const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
    if (closePdfViewerButton) {
        closePdfViewerButton.addEventListener('click', () => {
            if (pdfViewerContainer) pdfViewerContainer.style.display = 'none';
            if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
        });
    }
});


/* =====================================================================
   4. POLICY SUBMISSION LOGIC
   ===================================================================== */
const dlBtn = document.querySelector('.policy-submission-buttons button:first-child');
if (dlBtn && cfOverlay) {
    dlBtn.addEventListener('click', () => {
        cfOverlay.style.display = cfOverlay.style.display === 'block' ? 'none' : 'block';
    });
}

const firstChildBtn = document.getElementById("first-child");
if (firstChildBtn) {
    firstChildBtn.addEventListener("click", function () {
        if(cfOverlay) cfOverlay.style.display = "none";
        alert("Download cancelled");
    });
}

const lastChildBtn = document.getElementById("last-child");
if (lastChildBtn) {
    lastChildBtn.addEventListener("click", function () {
        if(cfOverlay) cfOverlay.style.display = "none";
        alert("Downloading template");
    });
}

const submitButtonTrigger = document.getElementById('submitButton');
if (submitButtonTrigger && submitOverlay) {
    submitButtonTrigger.addEventListener('click', () => {
        submitOverlay.style.display = submitOverlay.style.display === 'block' ? 'none' : 'block';
    });
}

const formSubmitBtn = document.getElementById("submitBtn");
if (formSubmitBtn) {
    formSubmitBtn.addEventListener("click", function () {
        if(submitOverlay) submitOverlay.style.display = "none";
    });
}

const cancelBtn = document.getElementById("cancelBtn");
if (cancelBtn) {
    cancelBtn.addEventListener("click", function () {
        if(submitOverlay) submitOverlay.style.display = "none";
    });
}


/* =====================================================================
   5. TASK MANAGER (Specific Modals)
   ===================================================================== */
function showIntroduction(policyTitle, policyContent, pdfPath) {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const introductionTitleElement = introductionSection.querySelector('.introduction-title');
    const introductionContentElement = introductionSection.querySelector('.introduction-content');
    const policyFeedbackContent = document.getElementById('policyFeedbackContent'); 
    const pdfViewerContainer = document.querySelector('.pdfViewerContainer'); 
    const viewPolicyButton = document.getElementById('viewPolicyButton'); 
    const introductionContent = document.querySelector('.introduction-content'); 

    if(introductionTitleElement) introductionTitleElement.textContent = policyTitle;
    if(introductionContentElement) introductionContentElement.textContent = policyContent;

    if(taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'none'; 
    if(taskManagerTable) taskManagerTable.style.display = 'none';
    if(pdfViewerContainer) pdfViewerContainer.style.display = 'none'; 
    if(introductionSection) introductionSection.style.display = 'block';
    if(policyFeedbackContent) policyFeedbackContent.style.display = 'block';  
    if(introductionContent) introductionContent.style.display = 'block';
    if(viewPolicyButton) viewPolicyButton.textContent = 'View Policy';

    let isPolicyVisible = false;
    if(viewPolicyButton) {
        viewPolicyButton.addEventListener('click', function () {
            if (!isPolicyVisible) {
                introductionContent.style.display = 'none';
                pdfViewerContainer.style.display = 'block'; 
                policyFeedbackContent.style.display = 'none';
                viewPolicyButton.textContent = 'View Feedback Report';
                isPolicyVisible = true;

                // Load PDF via PDF.js
                const pdfUrl = `${pdfPath}`; 
                pdfjsLib.getDocument(pdfUrl).promise.then(pdfDoc_ => {
                    window.pdfDoc = pdfDoc_;
                    document.getElementById('pageCount').textContent = window.pdfDoc.numPages;
                    renderPage(1); 
                });
            } else {
                introductionContent.style.display = 'block';
                pdfViewerContainer.style.display = 'none'; 
                policyFeedbackContent.style.display = 'block';
                viewPolicyButton.textContent = 'View Policy';
                isPolicyVisible = false;
            }
        });
    }
} 


/* =====================================================================
   6. DEPARTMENT MANAGER
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    // Buttons & Inputs
    const addDepartmentButton = document.getElementById('addDepartmentButton');
    const assignNameContainer = document.getElementById('assignNameContainer');
    const overlay = document.getElementById('overlay');
    const cancelAssignNameButton = document.getElementById('cancelAssignName');
    const confirmAssignNameButton = document.getElementById('confirmAssignName');
    const departmentNameInput = document.getElementById('departmentNameInput');
    const departmentListContainer = document.getElementById('departmentListContainer');
    const assignRoleContainer = document.getElementById('assignRoleContainer');
    const positionInput = document.getElementById('positionInput');
    const nameInput = document.getElementById('nameInput');
    const departmentStructureContainer = document.getElementById('departmentStructureContainer');
    const cancelStructureButton = document.getElementById('cancelStructure');
    const confirmStructureButton = document.getElementById('confirmStructure');
    const structureNameInput = document.getElementById('structureNameInput'); 
    const renameDepartmentContainer = document.getElementById('renameDepartmentContainer');
    const cancelRenameButton = document.getElementById('cancelRename');
    const confirmRenameButton = document.getElementById('confirmRenameButton');
    const renameDepartmentInput = document.getElementById('renameDepartmentInput');
    const deleteConfirmationContainer = document.getElementById('deleteConfirmationContainer');
    const cancelDeleteButton = document.getElementById('cancelDelete');
    const confirmDeleteButton = document.getElementById('confirmDelete');
    const renameRoleContainer = document.getElementById('renameRoleContainer');
    const cancelRenameRoleButton = document.getElementById('cancelRenameRole');
    const confirmRenameRoleButton = document.getElementById('confirmRenameRole');
    const renameRoleInput = document.getElementById('renameRoleInput');
    
    // State Tracking Variables
    let departmentToDelete = null;
    let currentTargetDepartment = null;
    let currentlyEditingRoleTextSpan = null;
    let roleToDelete = null;
    let currentlyEditingRole = null;
    let activeDepartmentForStructure = null; 

    // Initialize - Fetch Departments
    fetch('../../generalComponents/dpManagerPHP/getDepartments.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.departments)) {
            data.departments.forEach(dep => {
                displayNewDepartment(dep.dptName, dep.dptID);
            });
        }
    });
  
    // Add New Department
    if(addDepartmentButton) {
        addDepartmentButton.addEventListener('click', () => {
            assignNameContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
        });
    }
  
    if(cancelAssignNameButton) {
        cancelAssignNameButton.addEventListener('click', () => {
            assignNameContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            departmentNameInput.value = '';
        });
    }
  
    if(confirmAssignNameButton) {
        confirmAssignNameButton.addEventListener('click', () => {
            const departmentName = departmentNameInput.value.trim();
            if (departmentName) {
                fetch('../../generalComponents/dpManagerPHP/addDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ departmentName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayNewDepartment(departmentName, data.departmentId);
                        assignNameContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                        departmentNameInput.value = '';
                    } else {
                        alert('Failed to add department: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => alert('Error adding department: ' + error));
            } else {
                alert('Please enter a department name.');
            }
        });
    }

    // Render Department Item to DOM
    function displayNewDepartment(name, id = null) {
        const departmentDiv = document.createElement('div');
        departmentDiv.classList.add('department-item');
        departmentDiv.dataset.departmentName = name;
        if (id) departmentDiv.dataset.departmentId = id; 

        const nameSpan = document.createElement('span');
        nameSpan.textContent = name;
        nameSpan.id = `department-name-${id ? id : Date.now()}`; 
        departmentDiv.appendChild(nameSpan);
  
        const iconsDiv = document.createElement('div');
        iconsDiv.classList.add('department-icons');
  
        // Add User Icon
        const addUserIcon = document.createElement('i');
        addUserIcon.classList.add('fas', 'fa-user-plus');
        addUserIcon.addEventListener('click', () => {
            assignRoleContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            currentTargetDepartment = departmentDiv;
            assignRoleContainer.dataset.targetDepartment = departmentDiv;
        });
        iconsDiv.appendChild(addUserIcon);
  
        // Structure Icon
        const structureIcon = document.createElement('i');
        structureIcon.classList.add('fas', 'fa-sitemap');
        structureIcon.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            activeDepartmentForStructure = departmentDiv; 
        });
        iconsDiv.appendChild(structureIcon);
  
        // Edit Icon
        const editIcon = document.createElement('i');
        editIcon.classList.add('fas', 'fa-pencil-alt');
        editIcon.addEventListener('click', () => {
            renameDepartmentContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            renameDepartmentInput.value = nameSpan.textContent;
            renameDepartmentContainer.dataset.targetDepartmentSpan = nameSpan.id;
        });
        iconsDiv.appendChild(editIcon);
  
        // Delete Icon
        const deleteIcon = document.createElement('i');
        deleteIcon.classList.add('fas', 'fa-trash-alt');
        deleteIcon.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            departmentToDelete = departmentDiv;
        });
        iconsDiv.appendChild(deleteIcon);
  
        departmentDiv.appendChild(iconsDiv);
        departmentListContainer.appendChild(departmentDiv);
    }
  
    // Assign Roles
    const cancelAssignRoleButton = document.getElementById('cancelAssignRole');
    const confirmAssignRoleButton = document.getElementById('confirmAssignRole');
  
    if (cancelAssignRoleButton) {
        cancelAssignRoleButton.addEventListener('click', () => {
            assignRoleContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            positionInput.value = '';
            if(nameInput) nameInput.value = '';
            currentlyEditingRole = null;
            document.querySelectorAll('.scrollable-account-list input[type="radio"]:checked').forEach(radio => radio.checked = false);
        });
    }
  
    if (confirmAssignRoleButton) {
        confirmAssignRoleButton.addEventListener('click', () => {
            const position = positionInput.value.trim();
            const selectedAccount = document.querySelector('.scrollable-account-list input[type="radio"]:checked');
  
            if (!position) return alert('Please fill in the Position field.');
            if (!selectedAccount) return alert('Please select an account.');
  
            const selectedAccountLabel = selectedAccount.nextElementSibling.textContent;
            const [fullName, email] = selectedAccountLabel.split(' (');
            const emailOnly = email.replace(')', '').trim();
            const newRoleText = `${position} - ${fullName.trim()} (${emailOnly})`;
  
            if (currentlyEditingRole) {
                const roleTextSpan = currentlyEditingRole.querySelector('span');
                roleTextSpan.textContent = newRoleText;
                currentlyEditingRole = null;
            } else {
                const assignedRoleDiv = document.createElement('div');
                assignedRoleDiv.classList.add('assigned-role-item');
                assignedRoleDiv.innerHTML = `
                    <span>${newRoleText}</span>
                    <div class="assigned-role-icons">
                        <i class="fas fa-pencil-alt edit-role-icon" title="Edit Role"></i>
                        <i class="fas fa-trash-alt delete-role-icon" title="Delete Role"></i>
                    </div>
                `;
                const editRoleIcon = assignedRoleDiv.querySelector('.edit-role-icon');
                const deleteRoleIcon = assignedRoleDiv.querySelector('.delete-role-icon');
                const roleTextSpan = assignedRoleDiv.querySelector('span');
  
                editRoleIcon.addEventListener('click', () => {
                    const currentRoleText = roleTextSpan.textContent;
                    const [currentPosition, currentFullNameWithEmail] = currentRoleText.split(' - ');
                    const [currentFullName] = currentFullNameWithEmail.split(' (');
            
                    positionInput.value = currentPosition;
                    if(nameInput) nameInput.value = currentFullName.trim();
            
                    document.querySelectorAll('.scrollable-account-list .account-item').forEach(item => {
                        const label = item.querySelector('label').textContent;
                        item.querySelector('input[type="radio"]').checked = label.startsWith(currentFullName.trim());
                    });
            
                    currentlyEditingRole = assignedRoleDiv;
                    assignRoleContainer.style.display = 'block';
                    if(overlay) overlay.style.display = 'block';
                });
  
                deleteRoleIcon.addEventListener('click', () => {
                    deleteConfirmationContainer.style.display = 'block';
                    if(overlay) overlay.style.display = 'block';
                    roleToDelete = assignedRoleDiv;
                });
  
                currentTargetDepartment.parentNode.insertBefore(assignedRoleDiv, currentTargetDepartment.nextSibling);
            }
  
            assignRoleContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            positionInput.value = '';
            if(nameInput) nameInput.value = '';
            selectedAccount.checked = false;
            currentTargetDepartment = null;
        });
    }
  
    // Account Selection Radio Behavior
    document.querySelectorAll('.scrollable-account-list input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                const accountLabel = radio.nextElementSibling.textContent;
                const [fullName] = accountLabel.split(' (');
                if(nameInput) nameInput.value = fullName.trim();
            }
        });
    });
  
    // Department Structure Items
    if (cancelStructureButton) {
        cancelStructureButton.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            structureNameInput.value = '';
            activeDepartmentForStructure = null; 
        });
    }
  
    if (confirmStructureButton) {
        confirmStructureButton.addEventListener('click', () => {
            const structureName = structureNameInput.value.trim();
  
            if (structureName && activeDepartmentForStructure) {
                const structureDiv = document.createElement('div');
                structureDiv.classList.add('department-structure-item'); 
                structureDiv.textContent = `- ${structureName}`; 
        
                activeDepartmentForStructure.parentNode.insertBefore(structureDiv, activeDepartmentForStructure.nextSibling);
        
                departmentStructureContainer.style.display = 'none';
                if(overlay) overlay.style.display = 'none';
                structureNameInput.value = '';
                activeDepartmentForStructure = null; 
            } else if (!structureName) {
                alert('Please enter a structure name.');
            } else if (!activeDepartmentForStructure) {
                alert('Error: No department selected for structure.');
            }
        });
    }
  
    // Rename Department
    if (cancelRenameButton) {
        cancelRenameButton.addEventListener('click', () => {
            renameDepartmentContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            renameDepartmentInput.value = '';
            renameDepartmentContainer.dataset.targetDepartmentSpan = '';
        });
    }

    if (confirmRenameButton) {
        confirmRenameButton.addEventListener('click', () => {
            const newDepartmentName = renameDepartmentInput.value.trim();
            const targetSpanId = renameDepartmentContainer.dataset.targetDepartmentSpan;
    
            if (newDepartmentName && targetSpanId) {
                const targetNameSpan = document.getElementById(targetSpanId);
                const departmentDiv = targetNameSpan.closest('.department-item');
                const departmentId = departmentDiv ? departmentDiv.dataset.departmentId : null;
    
                if (!departmentId) return alert('Error: Department ID not found.');
    
                fetch('../../generalComponents/dpManagerPHP/renameDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ departmentId, newDepartmentName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        targetNameSpan.textContent = newDepartmentName;
                        renameDepartmentContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                        renameDepartmentInput.value = '';
                        renameDepartmentContainer.dataset.targetDepartmentSpan = '';
                    } else {
                        alert('Failed to rename department: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => alert('Error renaming department: ' + error));
            } else {
                alert('Please enter a new department name.');
            }
        });
    }

    // Delete Department/Role
    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            departmentToDelete = null;
            roleToDelete = null;
        });
    }
  
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', () => {
            if (departmentToDelete) {
                const departmentId = departmentToDelete.dataset.departmentId;
                if (!departmentId) return alert('Department ID not found. Cannot delete.');

                fetch('../../generalComponents/dpManagerPHP/deleteDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ departmentId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const assignedRoles = departmentToDelete.querySelectorAll('.assigned-role-item');
                        assignedRoles.forEach(role => role.remove());
                        departmentToDelete.remove();
                        departmentToDelete = null;
                    } else {
                        alert('Failed to delete department: ' + (data.message || 'Unknown error'));
                    }
                    deleteConfirmationContainer.style.display = 'none';
                    if(overlay) overlay.style.display = 'none';
                })
                .catch(error => alert('Error deleting department: ' + error));
            } else if (roleToDelete) {
                roleToDelete.remove();
                roleToDelete = null;
                deleteConfirmationContainer.style.display = 'none';
                if(overlay) overlay.style.display = 'none';
            }
        });
    }

    // Rename Role
    if (cancelRenameRoleButton) {
        cancelRenameRoleButton.addEventListener('click', () => {
            if(renameRoleContainer) renameRoleContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            if(renameRoleInput) renameRoleInput.value = '';
            currentlyEditingRoleTextSpan = null;
        });
    }
  
    if (confirmRenameRoleButton) {
        confirmRenameRoleButton.addEventListener('click', () => {
            const newRoleName = renameRoleInput.value.trim();
            if (newRoleName && currentlyEditingRoleTextSpan) {
                const currentRoleTextParts = currentlyEditingRoleTextSpan.textContent.split(' - ');
                const currentPosition = currentRoleTextParts[0];
                currentlyEditingRoleTextSpan.textContent = `${currentPosition} - ${newRoleName}`;
                
                if(renameRoleContainer) renameRoleContainer.style.display = 'none';
                if(overlay) overlay.style.display = 'none';
                if(renameRoleInput) renameRoleInput.value = '';
                currentlyEditingRoleTextSpan = null;
            } else {
                alert('Please enter a new role name.');
            }
        });
    }
});
/* =====================================================================
   8. POLICY MANAGER SCRIPT
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    // 1. DOM Elements
    const pmAddBtn = document.getElementById('pmAddPolicyBtn');
    const pmCreateModal = document.getElementById('pmCreateFolderModal');
    const globalOverlay = document.getElementById('overlay'); 
    const pmCancelBtn = document.getElementById('pmCancelCreateFolder');
    const pmConfirmBtn = document.getElementById('pmConfirmCreateFolder');
    const pmFolderInput = document.getElementById('pmFolderNameInput');
    const pmFoldersContainer = document.getElementById('pmFoldersContainer');

    // Modals for Rename & Delete
    const pmRenameModal = document.getElementById('pmRenameFolderModal');
    const pmRenameInput = document.getElementById('pmRenameInput');
    const pmConfirmRename = document.getElementById('pmConfirmRename');
    const pmCancelRename = document.getElementById('pmCancelRename');
    
    const pmDeleteModal = document.getElementById('pmDeleteFolderModal');
    const pmConfirmDelete = document.getElementById('pmConfirmDelete');
    const pmCancelDelete = document.getElementById('pmCancelDelete');

    // Add File Modals
    const pmAddFileModal = document.getElementById('pmAddFileModal');
    const pmPolicySelect = document.getElementById('pmPolicySelect');
    const pmConfirmAddFile = document.getElementById('pmConfirmAddFile');
    const pmCancelAddFile = document.getElementById('pmCancelAddFile');

    // Remove Policy Modals
    const pmRemovePolicyModal = document.getElementById('pmRemovePolicyModal');
    const pmCancelRemovePolicy = document.getElementById('pmCancelRemovePolicy');
    const pmConfirmRemovePolicy = document.getElementById('pmConfirmRemovePolicy');

    // 2. Tracking Variables
    let currentParentCategoryId = null; 
    let folderToEditId = null; 
    let folderToEditElement = null; 
    let currentFolderForFile = null; 
    let policyToRemoveId = null;
    let policyToRemoveElement = null;

    // --- FETCH FOLDERS AND FILES ON LOAD ---
    fetch('../../generalComponents/policyManagerPHP/getCategories.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Draw all folders first
                if (data.categories) {
                    data.categories.forEach(folder => {
                        renderPMFolder(folder.categoryName, folder.categoryID, folder.parentCategoryID);
                    });
                }
                // Draw all policies inside those folders
                if (data.policies) {
                    data.policies.forEach(policy => {
                        renderPMPolicy(policy.title, policy.policyID, policy.categoryID);
                    });
                }
            } else {
                console.error("Error fetching data:", data.message);
            }
        })
        .catch(error => console.error("Network error:", error));


    // --- CREATE FOLDER LOGIC ---
    if (pmAddBtn) {
        pmAddBtn.addEventListener('click', () => {
            currentParentCategoryId = null; 
            pmCreateModal.style.display = 'block';
            if(globalOverlay) globalOverlay.style.display = 'block';
            pmFolderInput.focus();
        });
    }

    if (pmCancelBtn) {
        pmCancelBtn.addEventListener('click', () => {
            pmCreateModal.style.display = 'none';
            if(globalOverlay) globalOverlay.style.display = 'none';
            pmFolderInput.value = '';
        });
    }

    if (pmConfirmBtn) {
        pmConfirmBtn.addEventListener('click', () => {
            const folderName = pmFolderInput.value.trim();
            if (!folderName) return alert("Please enter a folder name.");

            fetch('../../generalComponents/policyManagerPHP/createCategory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    categoryName: folderName,
                    parentCategoryID: currentParentCategoryId 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderPMFolder(folderName, data.categoryID, currentParentCategoryId);
                    pmCreateModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    pmFolderInput.value = '';
                } else {
                    alert("Database Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }


    // --- RENAME FOLDER LOGIC ---
    if (pmCancelRename) {
        pmCancelRename.addEventListener('click', () => {
            pmRenameModal.style.display = 'none';
            if(globalOverlay) globalOverlay.style.display = 'none';
            pmRenameInput.value = '';
            folderToEditId = null;
        });
    }

    if (pmConfirmRename) {
        pmConfirmRename.addEventListener('click', () => {
            const newName = pmRenameInput.value.trim();
            if (!newName) return alert("Please enter a new name.");

            fetch('../../generalComponents/policyManagerPHP/renameCategory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ categoryID: folderToEditId, newName: newName })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    folderToEditElement.querySelector('.pm-folder-name').textContent = newName;
                    pmRenameModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    pmRenameInput.value = '';
                    folderToEditId = null;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }


    // --- DELETE FOLDER LOGIC ---
    if (pmCancelDelete) {
        pmCancelDelete.addEventListener('click', () => {
            pmDeleteModal.style.display = 'none';
            if(globalOverlay) globalOverlay.style.display = 'none';
            folderToEditId = null;
        });
    }

    if (pmConfirmDelete) {
        pmConfirmDelete.addEventListener('click', () => {
            fetch('../../generalComponents/policyManagerPHP/deleteCategory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ categoryID: folderToEditId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    folderToEditElement.remove();
                    pmDeleteModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    folderToEditId = null;
                } else {
                    alert("Cannot delete: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }


    // --- ADD FILE MODAL LOGIC ---
    if (pmCancelAddFile) {
        pmCancelAddFile.addEventListener('click', () => {
            pmAddFileModal.style.display = 'none';
            if(globalOverlay) globalOverlay.style.display = 'none';
            currentFolderForFile = null;
        });
    }

    if (pmConfirmAddFile) {
        pmConfirmAddFile.addEventListener('click', () => {
            const selectedPolicyID = pmPolicySelect.value;
            if (!selectedPolicyID) return alert("Please select a policy.");

            fetch('../../generalComponents/policyManagerPHP/assignPolicyToFolder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    categoryID: currentFolderForFile, 
                    policyID: selectedPolicyID 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const policyTitle = pmPolicySelect.options[pmPolicySelect.selectedIndex].text;
                    renderPMPolicy(policyTitle, selectedPolicyID, currentFolderForFile);
                    
                    pmAddFileModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    currentFolderForFile = null;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }


    // --- REMOVE POLICY LOGIC ---
    if (pmCancelRemovePolicy) {
        pmCancelRemovePolicy.addEventListener('click', () => {
            pmRemovePolicyModal.style.display = 'none';
            if(globalOverlay) globalOverlay.style.display = 'none';
            policyToRemoveId = null;
        });
    }

    if (pmConfirmRemovePolicy) {
        pmConfirmRemovePolicy.addEventListener('click', () => {
            fetch('../../generalComponents/policyManagerPHP/removePolicyFromFolder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ policyID: policyToRemoveId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    policyToRemoveElement.remove();
                    pmRemovePolicyModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    policyToRemoveId = null;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }


    // --- RENDER POLICY FILE FUNCTION ---
    function renderPMPolicy(title, policyId, categoryId) {
        const policyDiv = document.createElement('div');
        policyDiv.style.backgroundColor = '#7B8EDE'; 
        policyDiv.style.color = 'white';
        policyDiv.style.padding = '12px 25px';
        policyDiv.style.marginLeft = '40px'; 
        policyDiv.style.borderBottom = '1px solid rgba(255, 255, 255, 0.2)';
        policyDiv.style.fontFamily = "'Istok Web', sans-serif";
        policyDiv.style.display = 'flex';
        policyDiv.style.justifyContent = 'space-between';
        policyDiv.style.alignItems = 'center';
        
        policyDiv.innerHTML = `
            <span><i class="fas fa-file-pdf" style="margin-right: 10px; color: #fbaf41;"></i> ${title}</span>
            <i class="fas fa-trash-alt remove-policy-btn" style="cursor: pointer; transition: color 0.2s;" title="Remove Policy"></i>
        `;

        const trashIcon = policyDiv.querySelector('.remove-policy-btn');
        trashIcon.addEventListener('mouseenter', () => trashIcon.style.color = '#f44336');
        trashIcon.addEventListener('mouseleave', () => trashIcon.style.color = 'white');

        trashIcon.addEventListener('click', () => {
            policyToRemoveId = policyId;
            policyToRemoveElement = policyDiv;
            
            pmRemovePolicyModal.style.display = 'block';
            if(globalOverlay) globalOverlay.style.display = 'block';
        });

        const parentFolder = document.querySelector(`.pm-folder-item[data-category-id="${categoryId}"]`);
        if (parentFolder) {
            parentFolder.after(policyDiv);
        }
    }


    // --- RENDER FOLDER FUNCTION ---
    function renderPMFolder(name, categoryId, parentId) {
        const folderDiv = document.createElement('div');
        folderDiv.className = 'pm-folder-item';
        folderDiv.dataset.categoryId = categoryId;
        
        let iconsHTML = '';

        if (parentId === null) {
            iconsHTML = `
                <i class="fas fa-file add-file-btn" title="Add File"></i>
                <i class="fas fa-folder add-child-btn" title="Add Child Folder"></i>
                <i class="fas fa-pencil-alt edit-folder-btn" title="Rename Folder"></i>
                <i class="fas fa-trash-alt delete-folder-btn" title="Delete Folder"></i>
            `;
        } else {
            folderDiv.classList.add('pm-child-folder');
            iconsHTML = `
                <i class="fas fa-file add-file-btn" title="Add File"></i>
                <i class="fas fa-pencil-alt edit-folder-btn" title="Rename Folder"></i>
                <i class="fas fa-trash-alt delete-folder-btn" title="Delete Folder"></i>
            `;
        }

        folderDiv.innerHTML = `
            <p class="pm-folder-name">${name}</p>
            <div class="pm-folder-icons">
                ${iconsHTML}
            </div>
        `;

        // Button Click: Add Child
        const addChildBtn = folderDiv.querySelector('.add-child-btn');
        if (addChildBtn) {
            addChildBtn.addEventListener('click', () => {
                currentParentCategoryId = categoryId; 
                pmCreateModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
                pmFolderInput.focus();
            });
        }

        // Button Click: Rename
        const renameBtn = folderDiv.querySelector('.edit-folder-btn');
        if (renameBtn) {
            renameBtn.addEventListener('click', () => {
                folderToEditId = categoryId;
                folderToEditElement = folderDiv;
                pmRenameInput.value = name; 
                
                pmRenameModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
                pmRenameInput.focus();
            });
        }

        // Button Click: Delete
        const deleteBtn = folderDiv.querySelector('.delete-folder-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                folderToEditId = categoryId;
                folderToEditElement = folderDiv;
                
                pmDeleteModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
            });
        }

        // Button Click: Add File
        const addFileBtn = folderDiv.querySelector('.add-file-btn');
        if (addFileBtn) {
            addFileBtn.addEventListener('click', () => {
                currentFolderForFile = categoryId;
                
                pmAddFileModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
                
                pmPolicySelect.innerHTML = '<option value="">Loading policies...</option>';

                fetch('../../generalComponents/policyManagerPHP/getAvailablePolicies.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.policies.length === 0) {
                                pmPolicySelect.innerHTML = '<option value="">No "Approved" policies available</option>';
                            } else {
                                pmPolicySelect.innerHTML = '<option value="" disabled selected>Select a policy...</option>';
                                data.policies.forEach(policy => {
                                    const option = document.createElement('option');
                                    option.value = policy.policyID;
                                    option.textContent = policy.title;
                                    pmPolicySelect.appendChild(option);
                                });
                            }
                        } else {
                            pmPolicySelect.innerHTML = '<option value="">Error loading policies</option>';
                        }
                    })
                    .catch(err => console.error("Error:", err));
            });
        }

        // Insert into DOM
        if (parentId === null) {
            pmFoldersContainer.appendChild(folderDiv);
        } else {
            const parentDiv = document.querySelector(`.pm-folder-item[data-category-id="${parentId}"]`);
            if (parentDiv) parentDiv.after(folderDiv);
            else pmFoldersContainer.appendChild(folderDiv);
        }
    }
});

/* =====================================================================
   7. AUTO-REFRESH SCRIPT
   ===================================================================== */
let lastUpdate = null;

function checkForUpdates() {
    fetch('../../generalComponents/Refresh/Policy_Repo_Refresh.php')
        .then(response => response.text())
        .then(timestamp => {
            if (lastUpdate === null) {
                lastUpdate = timestamp;
            } else if (lastUpdate !== timestamp) {
                location.reload(); 
            }
        });
}

// Check every 5 seconds
setInterval(checkForUpdates, 5000);