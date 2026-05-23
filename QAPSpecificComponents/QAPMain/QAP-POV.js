/* =====================================================================
   0. OVERLAYS & MODALS (Global)
   ==================================================================== */
const notificationOverlay = document.getElementById('popupOverlay');
const signOutOverlay = document.getElementById('signOutOverlay');
const submitOverlay = document.getElementById('submitOverlay');
const cfOverlay = document.getElementById('confirm-dl');


/* =====================================================================
   1. BULLETPROOF PANEL SWITCHING LOGIC (WITH STATE MEMORY)
   ==================================================================== */

// 👉 ✨ A custom reload function that leaves a "breadcrumb" for the script
function syncAndReload() {
    sessionStorage.setItem('internalSync', 'true');
    window.location.reload();
}

// This helper function guarantees we never get a "null" style error!
function safeHide(selector) {
    const el = document.querySelector(selector);
    if (el) el.style.display = 'none';
}

function hideAllPanels() {
    safeHide('#Welcome-Panel');
    safeHide('#policy-repo-content');
    safeHide('#policy-submission-content');
    safeHide('.Workspace-Panel');
    safeHide('.Role-Manager-Panel');
}

function showWorkspace() {
    hideAllPanels();
    const panel = document.querySelector('.Workspace-Panel');
    if (panel) panel.style.display = 'block';
    localStorage.setItem('activePanel', 'workspace'); 
}

function showPolicyRepository() {
    hideAllPanels();
    const panel = document.querySelector('#policy-repo-content');
    if (panel) panel.style.display = 'block';
    localStorage.setItem('activePanel', 'repository'); 
}

function showPolicySubmission() {
    hideAllPanels();
    const panel = document.querySelector('#policy-submission-content');
    if (panel) panel.style.display = 'flex';
    localStorage.setItem('activePanel', 'submission'); 
}

function showRoleManager() {
    hideAllPanels();
    const panel = document.querySelector('.Role-Manager-Panel');
    if (panel) panel.style.display = 'block';
    localStorage.setItem('activePanel', 'role'); 
}

function showInformation() {
    hideAllPanels();
    alert("Information Module - Coming Soon");
    localStorage.setItem('activePanel', 'information'); 
}

// Attach Event Listeners to Sidebar Icons & Check Memory
document.addEventListener('DOMContentLoaded', () => {
    // Map the sidebar icons to their respective functions
    const icons = document.querySelectorAll('.menu-icons');
    if (icons[0]) icons[0].addEventListener('click', showPolicyRepository);
    if (icons[1]) icons[1].addEventListener('click', showPolicySubmission);
    if (icons[2]) icons[2].addEventListener('click', showWorkspace);
    if (icons[3]) icons[3].addEventListener('click', showRoleManager);
    if (icons[4]) icons[4].addEventListener('click', showInformation);

    // 👉 ✨ Check if it was a Human or the Script that reloaded the page
    const savedPanel = localStorage.getItem('activePanel');
    const isInternalSync = sessionStorage.getItem('internalSync') === 'true';
    
    if (isInternalSync && savedPanel) {
        // The script reloaded the page. Restore the panel!
        sessionStorage.removeItem('internalSync'); 
        
        if (savedPanel === 'workspace') showWorkspace();
        else if (savedPanel === 'repository') showPolicyRepository();
        else if (savedPanel === 'submission') showPolicySubmission();
        else if (savedPanel === 'role') showRoleManager();
        else if (savedPanel === 'information') showInformation();
    } else {
        // A human hit F5, or just logged in! Show the Welcome Screen.
        hideAllPanels();
        const welcome = document.querySelector('#Welcome-Panel');
        if (welcome) welcome.style.display = 'flex';
    }
});


/* =====================================================================
   2. TOP BAR & SIDEBAR TOGGLE
   ==================================================================== */
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

    // ✨ NEW: Notification Tab Switching Logic ✨
    const unreadTab = document.querySelector('.notif-tabs .unread-tab');
    const readTab = document.querySelector('.notif-tabs .read-tab');
    const unreadList = document.getElementById('notif-unread-list');
    const readList = document.getElementById('notif-read-list');

    if (unreadTab && readTab && unreadList && readList) {
        const showUnread = () => {
            unreadList.style.display = 'block';
            readList.style.display = 'none';
            unreadTab.classList.add('active');
            readTab.classList.remove('active');
        };

        const showRead = () => {
            unreadList.style.display = 'none';
            readList.style.display = 'block';
            readTab.classList.add('active');
            unreadTab.classList.remove('active');
        };

        unreadTab.addEventListener('click', showUnread);
        readTab.addEventListener('click', showRead);
    }

    // Top Bar Buttons
    const notifButton = document.getElementById('notifButton');
    if (notifButton && notificationOverlay) {
        notifButton.addEventListener('click', (e) => {
            e.stopPropagation();
            
            if (notificationOverlay.style.display === 'block' || notificationOverlay.style.display === 'flex') {
                notificationOverlay.style.display = 'none';
            } else {
                notificationOverlay.style.display = 'block';
                if (signOutOverlay) signOutOverlay.style.display = 'none';

                // Reset to Unread tab when opening
                if (unreadTab && readTab && unreadList && readList) {
                    unreadList.style.display = 'block';
                    readList.style.display = 'none';
                    unreadTab.classList.add('active');
                    readTab.classList.remove('active');
                }
            }
        });
    }

    // ✨ NEW: Logic to mark notifications as read AND navigate to the correct screen!
    document.addEventListener('click', (e) => {
        const notifItem = e.target.closest('.notification-item'); 
        
        if (notifItem) {
            const notifId = notifItem.getAttribute('data-id') || notifItem.id || notifItem.getAttribute('value');
            const pTag = notifItem.querySelector('p');
            const msgText = pTag ? pTag.innerText.toLowerCase() : '';

            // 1. THE ROUTER: Where should this notification take us?
            if (msgText.includes('assigned') || msgText.includes('task')) {
                if (typeof showWorkspace === 'function') showWorkspace();
            } else if (msgText.includes('document') || msgText.includes('policy') || msgText.includes('removed') || msgText.includes('moved')) {
                if (typeof showPolicyRepository === 'function') showPolicyRepository();
            } 

            // 2. Close the notification menu so they can see the new screen
            if (notificationOverlay) notificationOverlay.style.display = 'none';

            // 3. If it was UNREAD, do the visual swap and tell the database
            if (notifItem.classList.contains('unread') && notifId) {
                // Change appearance to "Read"
                notifItem.classList.remove('unread');
                notifItem.style.backgroundColor = '#555'; 
                notifItem.style.borderLeft = '4px solid transparent';
                notifItem.style.cursor = 'default';
                
                if (pTag) {
                    pTag.style.fontWeight = 'normal';
                    pTag.style.color = '#d3d3d3';
                }

                // Slide it over to the Read tab
                const readContainer = document.getElementById('notif-read-list');
                if (readContainer) {
                    readContainer.prepend(notifItem);
                    const noReadMsg = readContainer.querySelector('.no-notifications');
                    if (noReadMsg) noReadMsg.remove(); 
                }

                // Check if the Unread tab is now completely empty
                const unreadContainer = document.getElementById('notif-unread-list');
                if (unreadContainer && unreadContainer.querySelectorAll('.notification-item.unread').length === 0) {
                    unreadContainer.innerHTML = "<p class='no-notifications'><i class='fas fa-bell-slash' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No unread notifications.</p>";
                }

                // ✨ Use the absolute path to permanently fix the background crash!
                fetch('/qms_optiqual/generalComponents/header/markNotifsReadBE.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ notifID: notifId })
                }).catch(err => console.error("Fetch error:", err));
            }
        }
    });

    const userButton = document.getElementById('userButton');
    if (userButton && signOutOverlay) {
        userButton.addEventListener('click', (e) => {
            e.stopPropagation();
            signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
            if (notificationOverlay) notificationOverlay.style.display = 'none';
        });
        signOutOverlay.addEventListener("click", function (e) {
            e.stopPropagation();
            window.location.href = "/qms_optiqual/auth/log_out/logout.php";
        });
    }

    // Close overlays when clicking outside
    document.addEventListener('click', (e) => {
        if (notificationOverlay && notificationOverlay.style.display === 'block') {
            if (!notificationOverlay.contains(e.target) && !e.target.closest('#notifButton')) {
                notificationOverlay.style.display = 'none';
            }
        }
        if (signOutOverlay && signOutOverlay.style.display === 'block') {
            if (!signOutOverlay.contains(e.target) && !e.target.closest('#userButton')) {
                signOutOverlay.style.display = 'none';
            }
        }
    });
});


/* =====================================================================
   3. POLICY REPOSITORY & PDF VIEWER
   ==================================================================== */

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
        const policyRepositoryPanel = document.getElementById('policy-repo-content');
        
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
    const policyRepositoryPanel = document.getElementById('policy-repo-content');
    
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
   ==================================================================== */
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

function setupPolicyFileAutoTitle() {
    const titleInput = document.getElementById('policyTitle');
    const fileInput = document.querySelector('input[type="file"][name="policyFile"]');
    if (!titleInput || !fileInput) return;

    let userEdited = false;
    titleInput.addEventListener('input', () => {
        userEdited = titleInput.value.trim().length > 0;
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length === 0) return;
        const fileName = fileInput.files[0].name.replace(/\.pdf$/i, '');
        if (!userEdited || titleInput.value.trim() === '') {
            titleInput.value = fileName;
        }
    });
}

setupPolicyFileAutoTitle();

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
   ==================================================================== */
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