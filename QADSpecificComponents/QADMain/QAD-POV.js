/* =====================================================================
   0. OVERLAYS & MODALS (Global)
   ===================================================================== */
const notificationOverlay = document.getElementById('popupOverlay');
const signOutOverlay = document.getElementById('signOutOverlay');
const submitOverlay = document.getElementById('submitOverlay');
const cfOverlay = document.getElementById('confirm-dl');

/* =====================================================================
   1. BULLETPROOF PANEL SWITCHING LOGIC
   ===================================================================== */
// This helper function guarantees we never get a "null" style error!
function safeHide(selector) {
    const el = document.querySelector(selector);
    if (el) el.style.display = 'none';
}

function hideAllPanels() {
    safeHide('#Welcome-Panel');
    safeHide('#policy-repo-content');
    safeHide('#policy-submission-content');
    safeHide('.Department-Manager-Panel');
    safeHide('.Policy-Manager-Panel');
    safeHide('.Role-Manager-Panel');
    safeHide('.Workspace-Panel');
}

function showWorkspace() {
    hideAllPanels();
    const panel = document.querySelector('.Workspace-Panel');
    if (panel) {
        panel.style.display = 'block';
    } else {
        alert("CRITICAL ERROR: Workspace Panel is missing! PHP crashed inside taskManager.php.");
    }
}

function showPolicyRepository() {
    hideAllPanels();
    const panel = document.querySelector('#policy-repo-content');
    if (panel) panel.style.display = 'block';
}

function showPolicySubmission() {
    hideAllPanels();
    const panel = document.querySelector('#policy-submission-content');
    if (panel) panel.style.display = 'flex';
}

function showDepartmentManager() {    
    hideAllPanels();
    const panel = document.querySelector('.Department-Manager-Panel');
    if (panel) panel.style.display = 'block';
}

function showPolicyManager() {
    hideAllPanels();
    const panel = document.querySelector('.Policy-Manager-Panel');
    if (panel) panel.style.display = 'block';
}

function showRoleManager() {
    hideAllPanels();
    const panel = document.querySelector('.Role-Manager-Panel');
    if (panel) panel.style.display = 'block';
}

function showReports() {
    hideAllPanels();
    console.warn('Reports panel is not yet implemented.');
}

// Attach Event Listeners to Sidebar Icons
document.addEventListener('DOMContentLoaded', () => {
    const icons = document.querySelectorAll('.menu-icons');
    if (icons[0]) icons[0].addEventListener('click', showPolicyRepository);
    if (icons[1]) icons[1].addEventListener('click', showPolicySubmission);
    if (icons[2]) icons[2].addEventListener('click', showWorkspace);
    if (icons[3]) icons[3].addEventListener('click', showRoleManager);
    if (icons[4]) icons[4].addEventListener('click', showDepartmentManager);
    if (icons[5]) icons[5].addEventListener('click', showPolicyManager);
    if (icons[6]) icons[6].addEventListener('click', showReports);
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

// ✨ POLICY REPO PDF ENGINE VARIABLES (Prefixed with pr_) ✨
var pr_pdfDoc = null,
    pr_pageNum = 1,
    pr_pageRendering = false,
    pr_pageNumPending = null,
    pr_scale = 1.2,
    pr_canvas = null,
    pr_ctx = null;

function pr_renderPage(num) {
    pr_pageRendering = true;
    if (pr_pdfDoc) {
        pr_pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({scale: pr_scale});
            pr_canvas.height = viewport.height;
            pr_canvas.width = viewport.width;

            let renderContext = {
                canvasContext: pr_ctx,
                viewport: viewport
            };
            let renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                pr_pageRendering = false;
                if (pr_pageNumPending !== null) {
                    pr_renderPage(pr_pageNumPending);
                    pr_pageNumPending = null;
                }
            });
        });

        const pageNumEl = document.getElementById('pr_pageNum');
        const zoomLevelEl = document.getElementById('pr_zoomLevel');
        if (pageNumEl) pageNumEl.textContent = num;
        if (zoomLevelEl) zoomLevelEl.textContent = Math.round(pr_scale * 100) + '%';
    }
}

function pr_queueRenderPage(num) {
    if (pr_pageRendering) {
        pr_pageNumPending = num;
    } else {
        pr_renderPage(num);
    }
}

function pr_onPrevPage() {
    if (pr_pageNum <= 1) return;
    pr_pageNum--;
    pr_queueRenderPage(pr_pageNum);
}

function pr_onNextPage() {
    if (!pr_pdfDoc || pr_pageNum >= pr_pdfDoc.numPages) return;
    pr_pageNum++;
    pr_queueRenderPage(pr_pageNum);
}

function pr_onZoomIn() {
    pr_scale += 0.2;
    pr_queueRenderPage(pr_pageNum);
}

function pr_onZoomOut() {
    if (pr_scale <= 0.6) return; 
    pr_scale -= 0.2;
    pr_queueRenderPage(pr_pageNum);
}

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
        
        // ✨ FIX: Safety check! Don't freeze if no PDF exists.
        if (!filePath || filePath === 'null' || filePath.trim() === '') {
            alert("No PDF document has been uploaded for this policy yet.");
            return; 
        }

        const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
        const repoPanel = document.getElementById('policy-repo-content'); // ✨ FIX: Safely grab the panel
        
        if(pdfViewerContainer) pdfViewerContainer.style.display = 'flex'; 
        if(repoPanel) repoPanel.style.display = 'none'; // ✨ FIX: Safely hide it

        // Load PDF via custom engine
        if (typeof pdfjsLib !== 'undefined') {
            const encodedUrl = encodeURI(filePath);
            pdfjsLib.getDocument(encodedUrl).promise.then(function(pdfDoc_) {
                pr_pdfDoc = pdfDoc_;
                const pageCountEl = document.getElementById('pr_pageCount');
                if (pageCountEl) pageCountEl.textContent = pr_pdfDoc.numPages;
                
                pr_pageNum = 1;
                pr_scale = 1.2;
                pr_renderPage(pr_pageNum);
            }).catch(function(error) {
                console.error("Error loading PDF: ", error);
                alert("Error loading document. The file may be missing or corrupted.");
            });
        } else {
            alert("PDF Library failed to load. Please refresh the page.");
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    
    // Bind Repository PDF Canvas & Buttons
    pr_canvas = document.getElementById('pr_pdfCanvas');
    if(pr_canvas) pr_ctx = pr_canvas.getContext('2d');

    const prevBtn = document.getElementById('pr_prevPage');
    if(prevBtn) prevBtn.addEventListener('click', pr_onPrevPage);
    
    const nextBtn = document.getElementById('pr_nextPage');
    if(nextBtn) nextBtn.addEventListener('click', pr_onNextPage);
    
    const zoomInBtn = document.getElementById('pr_zoomIn');
    if(zoomInBtn) zoomInBtn.addEventListener('click', pr_onZoomIn);
    
    const zoomOutBtn = document.getElementById('pr_zoomOut');
    if(zoomOutBtn) zoomOutBtn.addEventListener('click', pr_onZoomOut);

    // Close Viewer Logic
    const closePdfViewerButton = document.getElementById('closePdfViewer');
    const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
    
    if (closePdfViewerButton) {
        closePdfViewerButton.addEventListener('click', () => {
            const repoPanel = document.getElementById('policy-repo-content'); // ✨ FIX: Safely grab the panel
            
            if (pdfViewerContainer) pdfViewerContainer.style.display = 'none';
            if (repoPanel) repoPanel.style.display = 'block'; // ✨ FIX: Safely show it again
            
            // Clear memory when closing
            if (pr_ctx && pr_canvas) {
                pr_ctx.clearRect(0, 0, pr_canvas.width, pr_canvas.height);
                pr_canvas.height = 0;
            }
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
    
    let departmentToDelete = null;
    let currentTargetDepartment = null;
    let currentlyEditingRoleTextSpan = null;
    let roleToDelete = null;
    let activeDepartmentForStructure = null; 

    // Helper: Builds the folder AND its hidden container
    function displayNewDepartment(name, id = null, isChild = false, parentId = null) {
        const departmentDiv = createDepartmentElement(name, id, isChild);
        
        // Create the hidden container that will hold its children/employees
        const childContainer = document.createElement('div');
        childContainer.classList.add('department-children-container');
        childContainer.style.display = 'none'; // Hidden by default!
        childContainer.style.flexDirection = 'column';
        childContainer.dataset.parentId = id; 

        if (isChild && parentId) {
            // Find the parent's hidden container and drop this inside it
            const parentContainer = document.querySelector(`.department-children-container[data-parent-id="${parentId}"]`);
            if (parentContainer) {
                parentContainer.appendChild(departmentDiv);
                parentContainer.appendChild(childContainer);
            }
        } else {
            // It's a main folder, drop it in the main list
            departmentListContainer.appendChild(departmentDiv);
            departmentListContainer.appendChild(childContainer);
        }
    }

    function createDepartmentElement(name, id = null, isChild = false) {
        const departmentDiv = document.createElement('div');
        departmentDiv.classList.add('department-item');
        if (isChild) departmentDiv.classList.add('dpt-child-folder');
        
        departmentDiv.dataset.departmentName = name;
        if (id) departmentDiv.dataset.departmentId = id; 

        const nameSpan = document.createElement('span');
        nameSpan.textContent = name;
        nameSpan.id = `department-name-${id ? id : Date.now()}`; 
        departmentDiv.appendChild(nameSpan);
  
        const iconsDiv = document.createElement('div');
        iconsDiv.classList.add('department-icons');
  
        const addUserBtn = document.createElement('div');
        addUserBtn.className = 'expandable-btn';
        addUserBtn.innerHTML = '<i class="fas fa-user-plus"></i><span class="btn-text">Assign User</span>';
        addUserBtn.addEventListener('click', (e) => {
            e.stopPropagation(); 
            assignRoleContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            currentTargetDepartment = departmentDiv;
            assignRoleContainer.dataset.targetDepartment = departmentDiv;
        });
        iconsDiv.appendChild(addUserBtn);
  
        if (!isChild) {
            const structureBtn = document.createElement('div');
            structureBtn.className = 'expandable-btn';
            structureBtn.innerHTML = '<i class="fas fa-sitemap"></i><span class="btn-text">Add Sub-folder</span>';
            structureBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                departmentStructureContainer.style.display = 'block';
                if(overlay) overlay.style.display = 'block';
                activeDepartmentForStructure = departmentDiv; 
            });
            iconsDiv.appendChild(structureBtn);
        }
  
        const editBtn = document.createElement('div');
        editBtn.className = 'expandable-btn';
        editBtn.innerHTML = '<i class="fas fa-pencil-alt"></i><span class="btn-text">Rename</span>';
        editBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            renameDepartmentContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            renameDepartmentInput.value = nameSpan.textContent;
            renameDepartmentContainer.dataset.targetDepartmentSpan = nameSpan.id;
        });
        iconsDiv.appendChild(editBtn);
  
        const deleteBtn = document.createElement('div');
        deleteBtn.className = 'expandable-btn delete-btn';
        deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i><span class="btn-text">Delete</span>';
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            deleteConfirmationContainer.style.display = 'block';
            if(overlay) overlay.style.display = 'block';
            departmentToDelete = departmentDiv;
            roleToDelete = null; 
        });
        iconsDiv.appendChild(deleteBtn);
  
        departmentDiv.appendChild(iconsDiv);

        // TOGGLE FOLDER LOGIC
        departmentDiv.addEventListener('click', () => {
            const container = document.querySelector(`.department-children-container[data-parent-id="${id}"]`);
            if (container) {
                const isHidden = container.style.display === 'none';
                container.style.display = isHidden ? 'flex' : 'none';
            }
        });

        return departmentDiv;
    }

    // INITIAL LOAD: Fetch Departments AND Employees
    fetch('../../generalComponents/dpManagerPHP/getDepartments.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.departments)) {
            if (data.hasHierarchy) {
                const parentDepartments = data.departments.filter(dep => !dep.dptParentID);
                const childDepartments = data.departments.filter(dep => dep.dptParentID);
                
                parentDepartments.forEach(dep => displayNewDepartment(dep.dptName, dep.dptID, false));
                childDepartments.forEach(child => displayNewDepartment(child.dptName, child.dptID, true, child.dptParentID));
            } else {
                data.departments.forEach(dep => displayNewDepartment(dep.dptName, dep.dptID, false));
            }

            // Render all assigned employees into their parent's hidden container!
            if (data.employees && Array.isArray(data.employees)) {
                data.employees.forEach(emp => {
                    const parentContainer = document.querySelector(`.department-children-container[data-parent-id="${emp.dptID}"]`);
                    const targetDepartment = document.querySelector(`[data-department-id="${emp.dptID}"]`);
                    
                    if (parentContainer && targetDepartment) {
                        const emailOnly = emp.email ? emp.email.trim() : '';
                        const newRoleText = `${emp.departmentRole} - ${emp.fullName.trim()} (${emailOnly})`;

                        const assignedRoleDiv = document.createElement('div');
                        assignedRoleDiv.classList.add('assigned-role-item');
                        
                        if (targetDepartment.classList.contains('dpt-child-folder')) {
                            assignedRoleDiv.classList.add('nested-employee');
                        } else {
                            assignedRoleDiv.classList.add('parent-employee');
                        }

                        assignedRoleDiv.dataset.accId = emp.accID;
                        assignedRoleDiv.dataset.dptId = emp.dptID;
                        
                        assignedRoleDiv.innerHTML = `
                            <span>${newRoleText}</span>
                            <div class="assigned-role-icons" style="display: flex;">
                                <div class="expandable-btn edit-role-icon">
                                    <i class="fas fa-pencil-alt"></i><span class="btn-text">Edit</span>
                                </div>
                                <div class="expandable-btn delete-role-icon">
                                    <i class="fas fa-trash-alt"></i><span class="btn-text">Remove</span>
                                </div>
                            </div>
                        `;
                        
                        const editRoleIcon = assignedRoleDiv.querySelector('.edit-role-icon');
                        const deleteRoleIcon = assignedRoleDiv.querySelector('.delete-role-icon');

                        editRoleIcon.addEventListener('click', () => {
                            currentlyEditingRoleTextSpan = assignedRoleDiv.querySelector('span');
                            if (renameRoleContainer) renameRoleContainer.style.display = 'block';
                            if (overlay) overlay.style.display = 'block';
                        });

                        deleteRoleIcon.addEventListener('click', () => {
                            roleToDelete = assignedRoleDiv;
                            departmentToDelete = null; 
                            if (deleteConfirmationContainer) deleteConfirmationContainer.style.display = 'block';
                            if (overlay) overlay.style.display = 'block';
                        });

                        parentContainer.appendChild(assignedRoleDiv);
                    }
                });
            }
        }
    });
  
    // Add New Main Department
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
                        const newId = data.departmentId || data.dptID || Date.now();
                        displayNewDepartment(departmentName, newId);
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
  
    const cancelAssignRoleButton = document.getElementById('cancelAssignRole');
    const confirmAssignRoleButton = document.getElementById('confirmAssignRole');
  
    if (cancelAssignRoleButton) {
        cancelAssignRoleButton.addEventListener('click', () => {
            assignRoleContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            positionInput.value = '';
            if(nameInput) nameInput.value = '';
            document.querySelectorAll('.scrollable-account-list input[type="radio"]:checked').forEach(radio => radio.checked = false);
        });
    }
  
    // Add New Employee
    if (confirmAssignRoleButton) {
        confirmAssignRoleButton.addEventListener('click', () => {
            const position = positionInput.value.trim();
            const selectedAccount = document.querySelector('.scrollable-account-list input[type="radio"]:checked');

            if (!position) return alert('Please fill in the Position field.');
            if (!selectedAccount) return alert('Please select an account.');
            if (!currentTargetDepartment) return alert('No target department selected.');

            const dptID = currentTargetDepartment.dataset.departmentId;
            const accID = selectedAccount.value;

            fetch('../../generalComponents/dpManagerPHP/assignEmployee.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accID: accID, dptID: dptID, departmentRole: position })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectedAccountLabel = selectedAccount.nextElementSibling.textContent;
                    const [fullName, email] = selectedAccountLabel.split(' (');
                    const emailOnly = email.replace(')', '').trim();
                    const newRoleText = `${position} - ${fullName.trim()} (${emailOnly})`;

                    const assignedRoleDiv = document.createElement('div');
                    assignedRoleDiv.classList.add('assigned-role-item');
                    
                    if (currentTargetDepartment.classList.contains('dpt-child-folder')) {
                        assignedRoleDiv.classList.add('nested-employee');
                    } else {
                        assignedRoleDiv.classList.add('parent-employee');
                    }

                    assignedRoleDiv.dataset.accId = accID;
                    assignedRoleDiv.dataset.dptId = dptID;
                    
                    assignedRoleDiv.innerHTML = `
                            <span>${newRoleText}</span>
                            <div class="assigned-role-icons" style="display: flex;">
                                <div class="expandable-btn edit-role-icon">
                                    <i class="fas fa-pencil-alt"></i><span class="btn-text">Edit</span>
                                </div>
                                <div class="expandable-btn delete-role-icon">
                                    <i class="fas fa-trash-alt"></i><span class="btn-text">Remove</span>
                                </div>
                            </div>
                        `;
                    
                    const editRoleIcon = assignedRoleDiv.querySelector('.edit-role-icon');
                    const deleteRoleIcon = assignedRoleDiv.querySelector('.delete-role-icon');

                    editRoleIcon.addEventListener('click', () => {
                        currentlyEditingRoleTextSpan = assignedRoleDiv.querySelector('span');
                        if (renameRoleContainer) renameRoleContainer.style.display = 'block';
                        if (overlay) overlay.style.display = 'block';
                    });

                    deleteRoleIcon.addEventListener('click', () => {
                        roleToDelete = assignedRoleDiv;
                        departmentToDelete = null;
                        if (deleteConfirmationContainer) deleteConfirmationContainer.style.display = 'block';
                        if (overlay) overlay.style.display = 'block';
                    });

                    // Drop the new employee into the hidden container
                    const parentContainer = document.querySelector(`.department-children-container[data-parent-id="${dptID}"]`);
                    if (parentContainer) {
                        parentContainer.appendChild(assignedRoleDiv);
                        parentContainer.style.display = 'flex'; // Auto-expand to show new addition
                    }

                    assignRoleContainer.style.display = 'none';
                    if(overlay) overlay.style.display = 'none';
                    positionInput.value = '';
                    if(nameInput) nameInput.value = '';
                    selectedAccount.checked = false;
                } else {
                    alert('Error assigning role: ' + data.message);
                }
            })
            .catch(err => {
                alert("Database blocked insertion. Employee may already be in this folder.");
            });
        });
    }
  
    document.querySelectorAll('.scrollable-account-list input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                const accountLabel = radio.nextElementSibling.textContent;
                const [fullName] = accountLabel.split(' (');
                if(nameInput) nameInput.value = fullName.trim();
            }
        });
    });
  
    if (cancelStructureButton) {
        cancelStructureButton.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            structureNameInput.value = '';
            activeDepartmentForStructure = null; 
        });
    }
  
    // Add Sub-Folder
    if (confirmStructureButton) {
        confirmStructureButton.addEventListener('click', () => {
            const structureName = structureNameInput.value.trim();
            if (!structureName) return alert('Please enter a structure name.');
            if (!activeDepartmentForStructure) return alert('Error: No department selected for structure.');

            const parentDptID = activeDepartmentForStructure.dataset.departmentId;
            if (!parentDptID) return alert("System Error: Could not find the ID for this department.");

            fetch('../../generalComponents/dpManagerPHP/addSubDepartment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ dptName: structureName, dptParentID: parentDptID })
            })
            .then(response => response.text()) 
            .then(text => {
                try {
                    const data = JSON.parse(text); 
                    if (data.success) {
                        const newId = data.departmentId || data.dptID || data.id || Date.now();
                        displayNewDepartment(structureName, newId, true, parentDptID);
                        
                        // Auto-expand parent container so user sees the new folder
                        const parentContainer = document.querySelector(`.department-children-container[data-parent-id="${parentDptID}"]`);
                        if (parentContainer) parentContainer.style.display = 'flex';
                        
                        departmentStructureContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                        structureNameInput.value = '';
                    } else {
                        alert('Error adding structure: ' + data.message);
                    }
                } catch (e) {
                    alert("Database saved folder, reloading UI.");
                    window.location.reload(); 
                }
            })
            .catch(err => alert("Network Error."));
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

    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            departmentToDelete = null;
            roleToDelete = null;
        });
    }
  
    // Delete Folder or User
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
                        // Delete the folder AND its hidden container to prevent ghost elements
                        const containerToDelete = document.querySelector(`.department-children-container[data-parent-id="${departmentId}"]`);
                        if (containerToDelete) containerToDelete.remove();
                        
                        departmentToDelete.remove();
                        departmentToDelete = null;
                        deleteConfirmationContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                    } else {
                        alert('Failed to delete department: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => alert('Error deleting department: ' + error));
                
            } else if (roleToDelete) {
                const accID = roleToDelete.dataset.accId;
                const dptID = roleToDelete.dataset.dptId;
                
                fetch('../../generalComponents/dpManagerPHP/removeEmployee.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accID: accID, dptID: dptID })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        roleToDelete.remove();
                        roleToDelete = null;
                        deleteConfirmationContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                    } else {
                        alert("Error removing user: " + data.message);
                    }
                })
                .catch(err => console.error("Error:", err));
            }
        });
    }

    if (cancelRenameRoleButton) {
        cancelRenameRoleButton.addEventListener('click', () => {
            if(renameRoleContainer) renameRoleContainer.style.display = 'none';
            if(overlay) overlay.style.display = 'none';
            if(renameRoleInput) renameRoleInput.value = '';
            currentlyEditingRoleTextSpan = null;
        });
    }
  
    // Rename User Role
    if (confirmRenameRoleButton) {
        confirmRenameRoleButton.addEventListener('click', () => {
            const newRoleName = renameRoleInput.value.trim();
            if (newRoleName && currentlyEditingRoleTextSpan) {
                const parentDiv = currentlyEditingRoleTextSpan.closest('.assigned-role-item');
                const accID = parentDiv.dataset.accId;
                const dptID = parentDiv.dataset.dptId;

                fetch('../../generalComponents/dpManagerPHP/renameEmployeeRole.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accID: accID, dptID: dptID, newRole: newRoleName })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const currentText = currentlyEditingRoleTextSpan.textContent;
                        const firstDashIndex = currentText.indexOf(' - ');
                        const nameAndEmailPart = currentText.substring(firstDashIndex + 3); 
                        
                        currentlyEditingRoleTextSpan.textContent = `${newRoleName} - ${nameAndEmailPart}`;
                        
                        if(renameRoleContainer) renameRoleContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                        if(renameRoleInput) renameRoleInput.value = '';
                        currentlyEditingRoleTextSpan = null;
                    } else {
                        alert("Error renaming role: " + data.message);
                    }
                })
                .catch(err => console.error("Error:", err));
            } else {
                alert('Please enter a new role name.');
            }
        });
    }
});

/* =====================================================================
   8. POLICY MANAGER SCRIPT (With Sync Logic)
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
                // Draw all policies inside their respective hidden containers
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
                    
                    // Auto-open parent folder so user sees the new sub-folder
                    if (currentParentCategoryId) {
                        const parentContainer = document.querySelector(`.pm-children-container[data-parent-folder-id="${currentParentCategoryId}"]`);
                        if (parentContainer) parentContainer.style.display = 'flex';
                    }

                    pmCreateModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    pmFolderInput.value = '';
                    
                    // ✨ INSTANT SYNC FIX
                    location.reload();
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
                    
                    // ✨ INSTANT SYNC FIX
                    location.reload();
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
                    // Delete the folder AND its hidden container to prevent ghost elements
                    const containerToDelete = document.querySelector(`.pm-children-container[data-parent-folder-id="${folderToEditId}"]`);
                    if (containerToDelete) containerToDelete.remove();

                    folderToEditElement.remove();
                    pmDeleteModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    folderToEditId = null;
                    
                    // ✨ INSTANT SYNC FIX
                    location.reload();
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
                    
                    // Auto-expand folder so user sees the new file
                    const parentContainer = document.querySelector(`.pm-children-container[data-parent-folder-id="${currentFolderForFile}"]`);
                    if (parentContainer) parentContainer.style.display = 'flex';

                    pmAddFileModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    currentFolderForFile = null;
                    
                    // ✨ INSTANT SYNC FIX
                    location.reload();
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
                    
                    // ✨ INSTANT SYNC FIX
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }

    // =====================================================================
    // CORE RENDERING FUNCTIONS (MERGED ACCORDION LOGIC)
    // =====================================================================

    // --- RENDER FOLDER FUNCTION ---
    function renderPMFolder(name, categoryId, parentId) {
        const folderDiv = document.createElement('div');
        folderDiv.className = 'pm-folder-item';
        if (parentId !== null) folderDiv.classList.add('pm-child-folder');
        folderDiv.dataset.categoryId = categoryId;
        
       if (parentId === null) {
            iconsHTML = `
                <div class="expandable-btn add-file-btn"><i class="fas fa-file"></i><span class="btn-text">Add File</span></div>
                <div class="expandable-btn add-child-btn"><i class="fas fa-folder"></i><span class="btn-text">Add Sub-folder</span></div>
                <div class="expandable-btn edit-folder-btn"><i class="fas fa-pencil-alt"></i><span class="btn-text">Rename</span></div>
                <div class="expandable-btn delete-folder-btn"><i class="fas fa-trash-alt"></i><span class="btn-text">Delete</span></div>
            `;
        } else {
            iconsHTML = `
                <div class="expandable-btn add-file-btn"><i class="fas fa-file"></i><span class="btn-text">Add File</span></div>
                <div class="expandable-btn edit-folder-btn"><i class="fas fa-pencil-alt"></i><span class="btn-text">Rename</span></div>
                <div class="expandable-btn delete-folder-btn"><i class="fas fa-trash-alt"></i><span class="btn-text">Delete</span></div>
            `;
        }

        folderDiv.innerHTML = `
            <p class="pm-folder-name">${name}</p>
            <div class="pm-folder-icons">
                ${iconsHTML}
            </div>
        `;

        // EVENT LISTENERS FOR ICONS (Added stopPropagation!)
        const addChildBtn = folderDiv.querySelector('.add-child-btn');
        if (addChildBtn) {
            addChildBtn.addEventListener('click', (e) => {
                e.stopPropagation(); 
                currentParentCategoryId = categoryId; 
                pmCreateModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
                pmFolderInput.focus();
            });
        }

        const renameBtn = folderDiv.querySelector('.edit-folder-btn');
        if (renameBtn) {
            renameBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                folderToEditId = categoryId;
                folderToEditElement = folderDiv;
                pmRenameInput.value = name; 
                pmRenameModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
                pmRenameInput.focus();
            });
        }

        const deleteBtn = folderDiv.querySelector('.delete-folder-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                folderToEditId = categoryId;
                folderToEditElement = folderDiv;
                pmDeleteModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
            });
        }

        const addFileBtn = folderDiv.querySelector('.add-file-btn');
        if (addFileBtn) {
            addFileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
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

        // CREATE HIDDEN CHILD CONTAINER
        const childContainer = document.createElement('div');
        childContainer.classList.add('pm-children-container');
        childContainer.style.display = 'none'; // Hidden by default
        childContainer.dataset.parentFolderId = categoryId;

        // TOGGLE ACCORDION CLICK
        folderDiv.addEventListener('click', () => {
            const isHidden = childContainer.style.display === 'none';
            childContainer.style.display = isHidden ? 'flex' : 'none';
        });

        // INSERT BOTH INTO DOM
        if (parentId === null) {
            pmFoldersContainer.appendChild(folderDiv);
            pmFoldersContainer.appendChild(childContainer);
        } else {
            const parentContainer = document.querySelector(`.pm-children-container[data-parent-folder-id="${parentId}"]`);
            if (parentContainer) {
                parentContainer.appendChild(folderDiv);
                parentContainer.appendChild(childContainer);
            } else {
                pmFoldersContainer.appendChild(folderDiv);
                pmFoldersContainer.appendChild(childContainer);
            }
        }
    }

    // --- RENDER POLICY FILE FUNCTION ---
    function renderPMPolicy(title, policyId, categoryId) {
        // Drop directly into the parent folder's hidden container!
        const parentContainer = document.querySelector(`.pm-children-container[data-parent-folder-id="${categoryId}"]`);
        
        if (parentContainer) {
            const policyDiv = document.createElement('div');
            policyDiv.classList.add('pm-policy-item');
            policyDiv.dataset.policyId = policyId;
            
            policyDiv.innerHTML = `
                <span><i class="fas fa-file-pdf" style="margin-right: 10px; color: #fbaf41;"></i> ${title}</span>
                <div class="pm-folder-icons" style="display: flex;">
                    <div class="expandable-btn remove-policy-btn">
                        <i class="fas fa-trash-alt"></i><span class="btn-text">Remove</span>
                    </div>
                </div>
            `;

            const trashIcon = policyDiv.querySelector('.remove-policy-btn');
            trashIcon.addEventListener('mouseenter', () => trashIcon.style.color = '#f44336');
            trashIcon.addEventListener('mouseleave', () => trashIcon.style.color = 'black');

            trashIcon.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevents folder from toggling
                policyToRemoveId = policyId;
                policyToRemoveElement = policyDiv;
                pmRemovePolicyModal.style.display = 'block';
                if(globalOverlay) globalOverlay.style.display = 'block';
            });

            parentContainer.appendChild(policyDiv);
        }
    }
});


/* =====================================================================
   9. ROLE MANAGER SCRIPT
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const rmGridContainer = document.getElementById('rmGridContainer');
    const rmAddRoleBtn = document.getElementById('rmAddRoleBtn');
    const rmAddUserModal = document.getElementById('rmAddUserModal');
    const globalOverlay = document.getElementById('overlay');
    const rmCancelAddUser = document.getElementById('rmCancelAddUser');
    const rmConfirmAddUser = document.getElementById('rmConfirmAddUser');
    const rmUserSelectInput = document.getElementById('rmUserSelectInput'); 
    const rmUserEmailInput = document.getElementById('rmUserEmailInput'); 

    // Delete Mode Elements
    const rmDeleteRoleBtn = document.getElementById('rmDeleteRoleBtn');
    const rmConfirmDeleteBtn = document.getElementById('rmConfirmDeleteBtn');
    const rmDeleteInstruction = document.getElementById('rmDeleteInstruction');

    // State Variables
    let availableUsersForDropdown = [];
    let isDeleteMode = false;
    let selectedUsersForDeletion = new Set(); // Uses a Set so IDs don't get duplicated

    // --- DATA LOADING FUNCTION ---
    function loadRoleData() {
        fetch('../../generalComponents/roleManagerPHP/getRoleUsers.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (rmGridContainer) rmGridContainer.innerHTML = '';
                    if (data.teamMembers) {
                        data.teamMembers.forEach(user => {
                            renderUserCard(user.fullName, user.accID);
                        });
                    }
                    if (data.availableUsers) {
                        availableUsersForDropdown = data.availableUsers;
                        populateUserDropdown(availableUsersForDropdown);
                    }
                }
            })
            .catch(err => console.error("Error loading users:", err));
    }

    // Initial Load
    loadRoleData();

    // --- POPULATE DROPDOWN FUNCTION ---
    function populateUserDropdown(users) {
        if (!rmUserSelectInput) return;
        const options = rmUserSelectInput.querySelectorAll('option:not(:disabled)');
        options.forEach(opt => opt.remove());

        users.forEach(user => {
            const option = document.createElement('option');
            option.value = user.accID; 
            option.textContent = user.fullName; 
            rmUserSelectInput.appendChild(option);
        });
    }

    // --- AUTO-FILL EMAIL LOGIC ---
    if (rmUserSelectInput) {
        rmUserSelectInput.addEventListener('change', () => {
            const selectedAccId = rmUserSelectInput.value;
            if (!selectedAccId) {
                rmUserEmailInput.value = '';
                return;
            }
            const selectedUser = availableUsersForDropdown.find(user => user.accID == selectedAccId);
            if (selectedUser) rmUserEmailInput.value = selectedUser.email; 
        });
    }

    // --- RENDER USER CARD FUNCTION ---
    function renderUserCard(name, accId) {
        const cardDiv = document.createElement('div');
        cardDiv.className = 'rm-user-card';
        cardDiv.dataset.accId = accId;

        cardDiv.innerHTML = `
            <div class="rm-user-icon-circle"><i class="far fa-user"></i></div>
            <p class="rm-user-name">${name}</p>
        `;

        // Card Click Event: Handles the Delete Mode selection
        cardDiv.addEventListener('click', () => {
            if (isDeleteMode) {
                if (selectedUsersForDeletion.has(accId)) {
                    selectedUsersForDeletion.delete(accId); // Deselect
                    cardDiv.classList.remove('selected-for-deletion');
                } else {
                    selectedUsersForDeletion.add(accId); // Select
                    cardDiv.classList.add('selected-for-deletion');
                }
            }
        });

        if (rmGridContainer) rmGridContainer.appendChild(cardDiv);
    }

    // --- DELETE MODE TOGGLES ---
    function cancelDeleteMode() {
        isDeleteMode = false;
        rmConfirmDeleteBtn.style.display = 'none';
        rmDeleteInstruction.style.display = 'none';
        rmDeleteRoleBtn.style.color = '#f44336'; // Reset trash icon color
        selectedUsersForDeletion.clear(); // Empty the list
        
        // Remove red borders from all cards
        document.querySelectorAll('.rm-user-card.selected-for-deletion').forEach(card => {
            card.classList.remove('selected-for-deletion');
        });
    }

    if (rmDeleteRoleBtn) {
        rmDeleteRoleBtn.addEventListener('click', () => {
            isDeleteMode = !isDeleteMode; // Toggle mode
            if (isDeleteMode) {
                rmConfirmDeleteBtn.style.display = 'inline-block';
                rmDeleteInstruction.style.display = 'inline-block';
                rmDeleteRoleBtn.style.color = '#999'; // Turn trash icon gray to indicate it's active
            } else {
                cancelDeleteMode();
            }
        });
    }

    // --- CONFIRM DELETION LOGIC ---
    if (rmConfirmDeleteBtn) {
        rmConfirmDeleteBtn.addEventListener('click', () => {
            if (selectedUsersForDeletion.size === 0) {
                alert("Please select at least one user to remove.");
                return;
            }

            // Convert the Set to an Array to send to PHP
            const accIDsArray = Array.from(selectedUsersForDeletion);

            fetch('../../generalComponents/roleManagerPHP/removeRoleUser.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accIDs: accIDsArray })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cancelDeleteMode(); // Turn off delete mode
                    loadRoleData(); // Refresh the grid and dropdown seamlessly!
                } else {
                    alert("Database Error: " + data.message);
                }
            })
            .catch(err => console.error("Error removing users:", err));
        });
    }

    // --- MODAL TOGGLES (ADD USER) ---
    if (rmAddRoleBtn) {
        rmAddRoleBtn.addEventListener('click', () => {
            if (isDeleteMode) cancelDeleteMode(); // Turn off delete mode if opening add modal
            rmAddUserModal.style.display = 'block';
            if (globalOverlay) globalOverlay.style.display = 'block';
            if (rmUserSelectInput) rmUserSelectInput.focus();
        });
    }

    if (rmCancelAddUser) {
        rmCancelAddUser.addEventListener('click', () => {
            rmAddUserModal.style.display = 'none';
            if (globalOverlay) globalOverlay.style.display = 'none';
            if (rmUserSelectInput) rmUserSelectInput.selectedIndex = 0; 
            rmUserEmailInput.value = ''; 
        });
    }

    // --- CONFIRM ADD NEW USER LOGIC ---
    if (rmConfirmAddUser) {
        rmConfirmAddUser.addEventListener('click', () => {
            const selectedAccId = rmUserSelectInput.value;
            if (!selectedAccId) return alert("Please select an employee.");

            fetch('../../generalComponents/roleManagerPHP/addRoleUser.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accID: selectedAccId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    rmAddUserModal.style.display = 'none';
                    if (globalOverlay) globalOverlay.style.display = 'none';
                    if (rmUserSelectInput) rmUserSelectInput.selectedIndex = 0; 
                    rmUserEmailInput.value = ''; 
                    loadRoleData(); // Refresh grid and dropdown seamlessly!
                } else {
                    alert("Database Error: " + data.message);
                }
            })
            .catch(err => console.error("Error adding user:", err));
        });
    }
});

