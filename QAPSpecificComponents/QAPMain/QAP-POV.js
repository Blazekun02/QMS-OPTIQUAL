/* =====================================================================
   0. GLOBAL VARIABLES & DOM ELEMENTS
   ===================================================================== */
// Main Panels
const welcomePanel = document.getElementById('Welcome-Panel');
const policyRepositoryPanel = document.getElementById('policy-repo-content');
const policySubmissionPanel = document.getElementById('policy-submission-content');
const processTrackerPanel = document.querySelector('.Process-Tracker-Panel2'); 
const taskManagerPanel = document.querySelector('.Task-Manager-Panel');
const roleManagerPanel = document.querySelector('.Role-Manager-Panel');

// Overlays & Modals
const notificationOverlay = document.getElementById('popupOverlay');
const signOutOverlay = document.getElementById('signOutOverlay');
const submitOverlay = document.getElementById('submitOverlay');
const cfOverlay = document.getElementById('confirm-dl');


/* =====================================================================
   1. PANEL SWITCHING LOGIC (Sidebar Navigation)
   ===================================================================== */
function hideAllPanels() {
    if (welcomePanel) welcomePanel.style.display = 'none';
    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (processTrackerPanel) processTrackerPanel.style.display = 'none';
    if (taskManagerPanel) taskManagerPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display = 'none';
}

function showPolicyRepository() {
    hideAllPanels();
    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
}

function showPolicySubmission() {
    hideAllPanels();
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'flex';
}

function showProcessTracker() {
    hideAllPanels();
    if (processTrackerPanel) processTrackerPanel.style.display = 'block';
}

function showTaskManager() {
    hideAllPanels();
    if (taskManagerPanel) taskManagerPanel.style.display = 'flex';

    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');

    if (taskManagerHeaderContainer) taskManagerHeaderContainer.style.display = 'block';
    if (taskManagerTable) taskManagerTable.style.display = 'table';
    if (introductionSection) introductionSection.style.display = 'none';
}

function showRoleManager() {
    hideAllPanels();
    if (roleManagerPanel) roleManagerPanel.style.display = 'block';
}

function showInformation() {
    alert("Information Module - Coming Soon");
}


/* =====================================================================
   2. TOP BAR & SIDEBAR TOGGLE
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
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

    allParentFolders.forEach(p => p.style.display = 'none');
    document.querySelectorAll('.child-folders').forEach(c => c.style.display = 'none');
    allChildFolders.forEach(c => c.style.display = 'none');
    document.querySelectorAll('.Policies-Folder').forEach(p => p.style.display = 'none');
    allPolicies.forEach(p => p.style.display = 'none');

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
        
        if(pdfViewerContainer) pdfViewerContainer.style.display = 'flex'; 
        if(policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';

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
                alert("Error loading document.");
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
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

    const closePdfViewerButton = document.getElementById('closePdfViewer');
    const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
    if (closePdfViewerButton) {
        closePdfViewerButton.addEventListener('click', () => {
            if (pdfViewerContainer) pdfViewerContainer.style.display = 'none';
            if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
            
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

const cancelBtn = document.getElementById("cancelBtn");
if (cancelBtn) {
    cancelBtn.addEventListener("click", function () {
        if(submitOverlay) submitOverlay.style.display = "none";
    });
}


/* =====================================================================
   6. ROLE MANAGER SCRIPT
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    const rmGridContainer = document.getElementById('rmGridContainer');
    const rmAddRoleBtn = document.getElementById('rmAddRoleBtn');
    const rmAddUserModal = document.getElementById('rmAddUserModal');
    const globalOverlay = document.getElementById('overlay');
    const rmCancelAddUser = document.getElementById('rmCancelAddUser');
    const rmConfirmAddUser = document.getElementById('rmConfirmAddUser');
    const rmUserSelectInput = document.getElementById('rmUserSelectInput'); 
    const rmUserEmailInput = document.getElementById('rmUserEmailInput'); 

    const rmDeleteRoleBtn = document.getElementById('rmDeleteRoleBtn');
    const rmConfirmDeleteBtn = document.getElementById('rmConfirmDeleteBtn');
    const rmDeleteInstruction = document.getElementById('rmDeleteInstruction');

    let availableUsersForDropdown = [];
    let isDeleteMode = false;
    let selectedUsersForDeletion = new Set(); 

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

    loadRoleData();

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

    function renderUserCard(name, accId) {
        const cardDiv = document.createElement('div');
        cardDiv.className = 'rm-user-card';
        cardDiv.dataset.accId = accId;

        cardDiv.innerHTML = `
            <div class="rm-user-icon-circle"><i class="far fa-user"></i></div>
            <p class="rm-user-name">${name}</p>
        `;

        cardDiv.addEventListener('click', () => {
            if (isDeleteMode) {
                if (selectedUsersForDeletion.has(accId)) {
                    selectedUsersForDeletion.delete(accId); 
                    cardDiv.classList.remove('selected-for-deletion');
                } else {
                    selectedUsersForDeletion.add(accId); 
                    cardDiv.classList.add('selected-for-deletion');
                }
            }
        });

        if (rmGridContainer) rmGridContainer.appendChild(cardDiv);
    }

    function cancelDeleteMode() {
        isDeleteMode = false;
        rmConfirmDeleteBtn.style.display = 'none';
        rmDeleteInstruction.style.display = 'none';
        rmDeleteRoleBtn.style.color = '#f44336'; 
        selectedUsersForDeletion.clear(); 
        
        document.querySelectorAll('.rm-user-card.selected-for-deletion').forEach(card => {
            card.classList.remove('selected-for-deletion');
        });
    }

    if (rmDeleteRoleBtn) {
        rmDeleteRoleBtn.addEventListener('click', () => {
            isDeleteMode = !isDeleteMode; 
            if (isDeleteMode) {
                rmConfirmDeleteBtn.style.display = 'inline-block';
                rmDeleteInstruction.style.display = 'inline-block';
                rmDeleteRoleBtn.style.color = '#999'; 
            } else {
                cancelDeleteMode();
            }
        });
    }

    if (rmConfirmDeleteBtn) {
        rmConfirmDeleteBtn.addEventListener('click', () => {
            if (selectedUsersForDeletion.size === 0) {
                alert("Please select at least one user to remove.");
                return;
            }

            const accIDsArray = Array.from(selectedUsersForDeletion);

            fetch('../../generalComponents/roleManagerPHP/removeRoleUser.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accIDs: accIDsArray })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cancelDeleteMode(); 
                    loadRoleData(); 
                } else {
                    alert("Database Error: " + data.message);
                }
            })
            .catch(err => console.error("Error removing users:", err));
        });
    }

    if (rmAddRoleBtn) {
        rmAddRoleBtn.addEventListener('click', () => {
            if (isDeleteMode) cancelDeleteMode(); 
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
                    loadRoleData(); 
                } else {
                    alert("Database Error: " + data.message);
                }
            })
            .catch(err => console.error("Error adding user:", err));
        });
    }
});