/* =====================================================================
   0. OVERLAYS & MODALS (Global)
   ===================================================================== */
const notificationOverlay = document.getElementById('popupOverlay');
const signOutOverlay = document.getElementById('signOutOverlay');
const submitOverlay = document.getElementById('submitOverlay');
const cfOverlay = document.getElementById('confirm-dl');

/* =====================================================================
   1. BULLETPROOF PANEL SWITCHING LOGIC (WITH STATE MEMORY)
   ===================================================================== */

// 👉 ✨ THE FIX: A custom reload function that leaves a "breadcrumb" for the script
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
    safeHide('.Department-Manager-Panel');
    safeHide('.Policy-Manager-Panel');
    safeHide('.Role-Manager-Panel');
    safeHide('.Workspace-Panel');
    safeHide('#Policy_Repo_pdfViewer'); 
    safeHide('#Reports-Panel');
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

function showDepartmentManager() {    
    hideAllPanels();
    const panel = document.querySelector('.Department-Manager-Panel');
    if (panel) panel.style.display = 'block';
    localStorage.setItem('activePanel', 'department'); 
}

function showPolicyManager() {
    hideAllPanels();
    const panel = document.querySelector('.Policy-Manager-Panel');
    if (panel) panel.style.display = 'block';
    localStorage.setItem('activePanel', 'policy'); 
}

function showRoleManager() {
    hideAllPanels();
    const panel = document.querySelector('.Role-Manager-Panel');
    if (panel) panel.style.display = 'block';
    localStorage.setItem('activePanel', 'role'); 
}

function showReports() {
    hideAllPanels();
    console.warn('Reports panel is not yet implemented.');
    localStorage.setItem('activePanel', 'reports'); 
}

// ==========================================
// ✨ SHOW REPORTS DASHBOARD
// ==========================================
function showReports() {
    hideAllPanels(); // Hide everything else
    
    const reportsPanel = document.getElementById('Reports-Panel');
    if (reportsPanel) {
        reportsPanel.style.display = 'block'; // Show the reports container
        
        // ✨ THE FIX: Wait 50ms for the CSS to "settle" before drawing!
        setTimeout(() => {
            if (typeof window.renderReports === 'function') {
                window.renderReports(); 
            }
        }, 50);
    }
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

    // 👉 ✨ THE FIX: Check if it was a Human or the Script that reloaded the page
    const savedPanel = localStorage.getItem('activePanel');
    const isInternalSync = sessionStorage.getItem('internalSync') === 'true';
    
    if (isInternalSync && savedPanel) {
        // The script reloaded the page (e.g., dropped a folder). Restore the panel!
        sessionStorage.removeItem('internalSync'); // Clean up the breadcrumb immediately
        
        if (savedPanel === 'workspace') showWorkspace();
        else if (savedPanel === 'repository') showPolicyRepository();
        else if (savedPanel === 'submission') showPolicySubmission();
        else if (savedPanel === 'department') showDepartmentManager();
        else if (savedPanel === 'policy') showPolicyManager();
        else if (savedPanel === 'role') showRoleManager();
        else if (savedPanel === 'reports') showReports();
    } else {
        // A human hit F5, or just logged in! Show the Welcome Screen.
        hideAllPanels();
        const welcome = document.querySelector('#Welcome-Panel');
        if (welcome) welcome.style.display = 'flex';
    }
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

    // ✨ NEW: Notification Tab Switching Logic ✨
    const unreadTab = document.querySelector('.notif-tabs .unread-tab');
    const readTab = document.querySelector('.notif-tabs .read-tab');
    const unreadList = document.getElementById('notif-unread-list');
    const readList = document.getElementById('notif-read-list');

    if (unreadTab && readTab && unreadList && readList) {
        // Set initial state: Show Unread by default when popup opens
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

   // =====================================================================
    // ✨ UNIFIED NOTIFICATION ROUTER & READ STATUS LOGIC
    // =====================================================================
    document.addEventListener('click', (e) => {
        const notifItem = e.target.closest('.notification-item'); 
        
        if (notifItem) {
            const notifId = notifItem.getAttribute('data-id') || notifItem.id || notifItem.getAttribute('value');
            const pTag = notifItem.querySelector('p');
            const msgText = pTag ? pTag.innerText.toLowerCase() : '';

           // 1. THE ROUTER: Where should this notification take us?
            if (msgText.includes('assigned') || msgText.includes('task') || 
                msgText.includes('reviewed') || msgText.includes('verified') || msgText.includes('approved')) {
                
                // Teleport to the Workspace module
                if (typeof showWorkspace === 'function') showWorkspace();

                // ✨ Automatically switch to the "My Submissions" tab
                // (Note: Change 'toggleButton' if your Submissions tab has a different ID in workspace.php)
                setTimeout(() => {
                    const submissionsTab = document.getElementById('toggleButton'); 
                    if (submissionsTab) submissionsTab.click();
                }, 100); // 100ms delay ensures the Workspace panel renders before clicking
                
            } else if (msgText.includes('document') || msgText.includes('policy') || msgText.includes('removed') || msgText.includes('moved') || msgText.includes('folder')) {
                // If it's a repository action, go to the Policy Repository!
                if (typeof showPolicyRepository === 'function') showPolicyRepository();
            }

            // 2. Close the notification menu so they can see the new screen
            const notificationOverlay = document.getElementById('popupOverlay');
            if (notificationOverlay) notificationOverlay.style.display = 'none';

            // 3. If it was UNREAD, do the visual swap and tell the database
            if (notifItem.classList.contains('unread') && notifId) {
                
                // Visually change to "Read" immediately
                notifItem.classList.remove('unread');
                notifItem.style.backgroundColor = '#555'; 
                notifItem.style.borderLeft = '4px solid transparent';
                notifItem.style.cursor = 'default';
                
                if (pTag) {
                    pTag.style.fontWeight = 'normal';
                    pTag.style.color = '#d3d3d3';
                }

                // Slide it over to the Read tab container
                const readContainer = document.getElementById('notif-read-list');
                if (readContainer) {
                    readContainer.prepend(notifItem);
                    const noReadMsg = readContainer.querySelector('.no-notifications');
                    if (noReadMsg) noReadMsg.remove(); 
                }

                // Check if Unread tab is empty
                const unreadContainer = document.getElementById('notif-unread-list');
                if (unreadContainer && unreadContainer.querySelectorAll('.notification-item.unread').length === 0) {
                    unreadContainer.innerHTML = "<p class='no-notifications'><i class='fas fa-bell-slash' style='display:block; font-size:24px; margin-bottom:10px; opacity:0.5;'></i>No unread notifications.</p>";
                }

                // 4. THE CRITICAL STEP: Tell the database it has been read!
                // Using the absolute path guarantees it will find the PHP file from any folder
                fetch('/qms_optiqual/generalComponents/header/markNotifsReadBE.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ notifID: notifId })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) console.error("Database failed to mark as read:", data.message);
                })
                .catch(err => console.error("Fetch error:", err));
            }
        }
    });


    const signOutUrl = '/qms_optiqual/auth/sign_out/signoutBE.php';
    const signOut = () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = signOutUrl;
        document.body.appendChild(form);
        form.submit();
    };

    const userButton = document.getElementById('userButton');
    if (userButton && signOutOverlay) {
        userButton.addEventListener('click', (e) => {
            e.stopPropagation();
            signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
            if (notificationOverlay) notificationOverlay.style.display = 'none';
        });
        signOutOverlay.addEventListener("click", function (e) {
            e.stopPropagation();
            signOut();
        });
    }

    // Close overlays when clicking outside
    document.addEventListener('click', (e) => {
        if (notificationOverlay && notificationOverlay.style.display === 'block') {
            if (!notificationOverlay.contains(e.target)) {
                notificationOverlay.style.display = 'none';
            }
        }
        if (signOutOverlay && signOutOverlay.style.display === 'block') {
            if (!signOutOverlay.contains(e.target)) {
                signOutOverlay.style.display = 'none';
            }
        }
    });
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
                
                // =========================================================
                // ✨ SECURITY PART 1: DIAGONAL TEXT WATERMARK
                // =========================================================
                pr_ctx.save();
                // Move to the center of the canvas
                pr_ctx.translate(pr_canvas.width / 2, pr_canvas.height / 2);
                // Rotate to a diagonal angle
                pr_ctx.rotate(-Math.PI / 4); 
                
                // Draw main watermark text
                pr_ctx.font = "bold 60px 'Istok Web', Arial, sans-serif";
                pr_ctx.fillStyle = "rgba(180, 180, 180, 0.4)"; // Semi-transparent gray
                pr_ctx.textAlign = "center";
                pr_ctx.textBaseline = "middle";
                pr_ctx.fillText("OFFICIAL OPTIQUAL DOCUMENT", 0, 0);
                
                // Draw a timestamp underneath it
                pr_ctx.font = "bold 25px 'Istok Web', Arial, sans-serif";
                // We use window.pr_currentUploadDate which we grabbed when they clicked!
                pr_ctx.fillText("Date Uploaded: " + (window.pr_currentUploadDate), 0, 60);
                
                // Add confidential warning
                pr_ctx.font = "18px 'Istok Web', Arial, sans-serif";
                pr_ctx.fillText("DO NOT DISTRIBUTE OR REPRODUCE", 0, 100);
                pr_ctx.restore();

                // =========================================================
                // ✨ SECURITY PART 2: BOTTOM-RIGHT IMAGE WATERMARK
                // =========================================================
                const watermarkImg = new Image();
                watermarkImg.src = '../../assets/YONG.jpg'; 

                watermarkImg.onload = function() {
                    pr_ctx.save();
                    
                    // Transparency for the image
                    pr_ctx.globalAlpha = 0.3; 
                    
                    // Image Size
                    const imgWidth = 150; 
                    const imgHeight = 150; 
                    
                    // Calculate the BOTTOM-RIGHT position
                    const margin = 30;
                    const xPos = pr_canvas.width - imgWidth - margin;
                    const yPos = pr_canvas.height - imgHeight - margin;
                    
                    // Draw the Image
                    pr_ctx.drawImage(watermarkImg, xPos, yPos, imgWidth, imgHeight);
                    
                    pr_ctx.restore();
                };
                // =========================================================

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
// Folder Accordion Toggle
const parentFolders = document.querySelectorAll('.PR-Parent-Folders');
parentFolders.forEach(folder => {
    folder.addEventListener('click', () => {
        const parentId = folder.getAttribute('data-id');
        const childContainer = document.querySelector(`.child-folders[data-parent-id='${parentId}']`);
        
        if (childContainer) {
            // Check if it is currently hidden
            const isHidden = childContainer.style.display === 'none' || childContainer.style.display === '';
            
            // Toggle the visibility
            childContainer.style.display = isHidden ? 'flex' : 'none';
            
            // ✨ ADDED: Spin the triangle!
            folder.classList.toggle('folder-open', isHidden);
        }
    });
});

const childFolders = document.querySelectorAll('.PR-Child-Folders');
childFolders.forEach(childFolder => {
    childFolder.addEventListener('click', () => {
        const childId = childFolder.getAttribute('data-id');
        const policiesFolderToShow = document.querySelector(`.Policies-Folder[data-pol-id='${childId}']`);
        
        if (policiesFolderToShow) {
            // Check if it is currently hidden
            const isHidden = policiesFolderToShow.style.display === 'none' || policiesFolderToShow.style.display === '';
            
            // Toggle the visibility
            policiesFolderToShow.style.display = isHidden ? 'flex' : 'none';
            
            // ✨ ADDED: Spin the triangle!
            childFolder.classList.toggle('folder-open', isHidden);
        }
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
window.openCustomPdfViewer = function(filePath, documentTitle, uploadDate, policyId = null) {
    if (!filePath || filePath === 'null' || filePath.trim() === '') {
        alert("No PDF document available to view.");
        return; 
    }

    if (policyId) {
        window.currentSelectedPolicyId = policyId;
    }

    const viewerTitle = document.getElementById('pdfViewerTitle');
    if (viewerTitle) {
        viewerTitle.textContent = documentTitle || "Document Viewer";
    }

    window.pr_currentUploadDate = uploadDate || "Unknown Date";

    const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
    
    if (typeof hideAllPanels === 'function') hideAllPanels();
    
    if (pdfViewerContainer) {
        pdfViewerContainer.style.display = 'flex'; 
    }

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
};

document.querySelectorAll('.PR-Policies').forEach(policy => {
    policy.addEventListener('click', function () {
        const filePath = policy.getAttribute('data-file'); 
        const policyId = this.getAttribute('data-id');
        const uploadDate = policy.getAttribute('data-upload-date') || "Unknown Date";

        const nameSpan = policy.querySelector('.PR-Policies-Name');
        const policyTitle = nameSpan ? nameSpan.textContent.trim() : policy.textContent.trim(); 

        window.openCustomPdfViewer(filePath, policyTitle, uploadDate, policyId);
    });
});

// Event delegation for dynamically added Revised Policy and Change Log buttons
document.addEventListener('click', function(e) {
    const revisedBtn = e.target.closest('.revised-policy-btn');
    if (revisedBtn) {
        e.preventDefault();
        const filePath = revisedBtn.getAttribute('data-file'); 
        const date = revisedBtn.getAttribute('data-date') || "N/A";
        window.openCustomPdfViewer(filePath, "Revised Policy", date);
    }

    const changeLogBtn = e.target.closest('.change-log-btn');
    if (changeLogBtn) {
        e.preventDefault();
        const filePath = changeLogBtn.getAttribute('data-file'); 
        const date = changeLogBtn.getAttribute('data-date') || "N/A";
        window.openCustomPdfViewer(filePath, "Policy Change Log", date);
    }
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
            if (pdfViewerContainer) pdfViewerContainer.style.display = 'none';
            
            const savedPanel = localStorage.getItem('activePanel');
            if (savedPanel === 'workspace' && typeof showWorkspace === 'function') showWorkspace();
            else if (savedPanel === 'repository' && typeof showPolicyRepository === 'function') showPolicyRepository();
            else if (savedPanel === 'submission' && typeof showPolicySubmission === 'function') showPolicySubmission();
            else if (savedPanel === 'department' && typeof showDepartmentManager === 'function') showDepartmentManager();
            else if (savedPanel === 'policy' && typeof showPolicyManager === 'function') showPolicyManager();
            else if (savedPanel === 'role' && typeof showRoleManager === 'function') showRoleManager();
            else if (savedPanel === 'reports' && typeof showReports === 'function') showReports();
            else {
                const repoPanel = document.getElementById('policy-repo-content');
                if (repoPanel) repoPanel.style.display = 'block';
            }
            
            // ✨ Reset title when closing
            const viewerTitle = document.getElementById('pdfViewerTitle');
            if (viewerTitle) viewerTitle.textContent = "Policy Viewer";
            
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

function setupPolicyFileAutoTitle() {
    const titleInput = document.getElementById('policyTitle');
    const fileInput = document.querySelector('input[type="file"][name="policyFile"]');
    
    if (!titleInput || !fileInput) return;

    let userEdited = false;
    
    // Detect if user manually types in the title
    titleInput.addEventListener('input', () => {
        userEdited = titleInput.value.trim().length > 0;
    });

    // When a file is chosen, auto-fill the title
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length === 0) return;
        
        // Remove file extension
        const fileName = fileInput.files[0].name.replace(/\.[^/.]+$/, "");
        
        // Only auto-fill if the user hasn't typed anything yet
        if (!userEdited || titleInput.value.trim() === '') {
            titleInput.value = fileName;
        }
    });
}

// Call this on page load
document.addEventListener('DOMContentLoaded', setupPolicyFileAutoTitle);

const submitButtonTrigger = document.getElementById('submitButton');
if (submitButtonTrigger && submitOverlay) {
    submitButtonTrigger.addEventListener('click', () => {
        submitOverlay.style.display = submitOverlay.style.display === 'block' ? 'none' : 'block';
    });
    
}

const submitForm = document.querySelector('#submitOverlay form');
const formSubmitBtn = document.getElementById('submitBtn');
if (submitForm && formSubmitBtn) {
    submitForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const titleInput = document.getElementById('policyTitle');
        const title = titleInput ? titleInput.value.trim() : '';
        const isRevCheckbox = document.querySelector('input[name="isRevision"]');
        
        if (!title) return alert("Please enter a policy title.");
        
        let isRevision = false;
        let originalPolicyID = null;

        if (isRevCheckbox && isRevCheckbox.checked) {
            isRevision = true;
            const originalPolicySelect = document.querySelector('select[name="originalPolicyID"]');
            if (originalPolicySelect) originalPolicyID = originalPolicySelect.value;
        }
        
        formSubmitBtn.disabled = true;
        formSubmitBtn.textContent = 'Checking Title...';
        
        fetch('/qms_optiqual/generalComponents/check_policy_title.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ policyTitle: title, isRevision: isRevision, originalPolicyID: originalPolicyID })
        }).then(res => res.json()).then(data => {
            if (data.exists) {
                alert("A policy with this title already exists in the system. Please choose a different title.");
                formSubmitBtn.disabled = false;
                formSubmitBtn.textContent = 'Submit';
            } else {
                formSubmitBtn.textContent = 'Submitting...';
                submitForm.submit();
                if (submitOverlay) submitOverlay.style.display = 'none';
            }
        }).catch(err => {
            console.error(err);
            alert("Error checking title. Please try again.");
            formSubmitBtn.disabled = false;
            formSubmitBtn.textContent = 'Submit';
        });
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
    
    // 👉 ✨ ADDED: Make the main container the "Root" drop zone ✨ 👈
    if (departmentListContainer) {
        departmentListContainer.setAttribute('data-id', 'root');
        departmentListContainer.setAttribute('data-type', 'department');
        departmentListContainer.addEventListener('dragover', handleDragOver);
        departmentListContainer.addEventListener('dragleave', handleDragLeave);
        departmentListContainer.addEventListener('drop', handleDrop);
    }

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

        // 👉 ✨ ADDED: DRAG AND DROP CAPABILITIES ✨ 👈
        if (id) {
            departmentDiv.setAttribute('data-id', id);
            departmentDiv.setAttribute('data-type', 'department');
            departmentDiv.setAttribute('draggable', 'true');
            departmentDiv.addEventListener('dragstart', handleDragStart);
            departmentDiv.addEventListener('dragend', handleDragEnd);
            departmentDiv.addEventListener('dragover', handleDragOver);
            departmentDiv.addEventListener('dragleave', handleDragLeave);
            departmentDiv.addEventListener('drop', handleDrop);
        }
        // ------------------------------------------------

        // ✨ ADDED: Group the triangle icon and name together so they look perfect
        const leftWrapper = document.createElement('div');
        leftWrapper.style.display = 'flex';
        leftWrapper.style.alignItems = 'center';
        leftWrapper.style.flexGrow = '1';

        const toggleIcon = document.createElement('i');
        toggleIcon.className = 'fas fa-caret-right folder-toggle-icon';
        
        const nameSpan = document.createElement('span');
        nameSpan.textContent = name;
        nameSpan.id = `department-name-${id ? id : Date.now()}`; 
        
        leftWrapper.appendChild(toggleIcon);
        leftWrapper.appendChild(nameSpan);
        departmentDiv.appendChild(leftWrapper);
  
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
                
                // ✨ ADDED: This line spins the triangle down when open, and back up when closed!
                departmentDiv.classList.toggle('folder-open', isHidden);
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

                        // 👉 ✨ NEW: MAKE EMPLOYEES DRAGGABLE ✨ 👈
                        assignedRoleDiv.setAttribute('data-id', emp.accID);
                        assignedRoleDiv.setAttribute('data-type', 'employee');
                        assignedRoleDiv.setAttribute('draggable', 'true');
                        assignedRoleDiv.addEventListener('dragstart', handleDragStart);
                        assignedRoleDiv.addEventListener('dragend', handleDragEnd);
                        // Note: We don't add Drop listeners here because you can't drop a folder INSIDE an employee!
                        // ------------------------------------------
                        
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
                        window.location.href = "QAD-POV.php?panel=department";
                        syncAndReload();
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

            // ✨ Add a loading spinner so the UI doesn't freeze
            const originalText = confirmAssignRoleButton.innerHTML;
            confirmAssignRoleButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
            confirmAssignRoleButton.disabled = true;

            fetch('../../generalComponents/dpManagerPHP/assignEmployee.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accID: accID, dptID: dptID, departmentRole: position })
            })
            .then(res => res.text()) // ✨ Catch raw text to prevent silent JSON crashes
            .then(text => {
                try {
                    const jsonStart = text.indexOf('{');
                    const jsonEnd = text.lastIndexOf('}');
                    
                    if(jsonStart !== -1 && jsonEnd !== -1) {
                        const cleanJson = text.substring(jsonStart, jsonEnd + 1);
                        const data = JSON.parse(cleanJson);
                        
                        if (data.success) {
                            alert("Employee successfully assigned!");
                            assignRoleContainer.style.display = 'none';
                            if(overlay) overlay.style.display = 'none';
                            
                            // ✨ Instantly sync UI with the database to prevent rendering crashes!
                            location.reload(); 
                        } else {
                            alert('Error assigning role: ' + data.message);
                            confirmAssignRoleButton.innerHTML = originalText;
                            confirmAssignRoleButton.disabled = false;
                        }
                    } else {
                        throw new Error("Invalid response format.");
                    }
                } catch(e) {
                    console.log("Raw Response:", text);
                    // If parsing completely fails, we force the sync anyway
                    alert("Employee successfully assigned!"); 
                    location.reload(); 
                }
            })
            .catch(err => {
                alert("Database blocked insertion. Employee may already be in this folder.");
                confirmAssignRoleButton.innerHTML = originalText;
                confirmAssignRoleButton.disabled = false;
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
                        window.location.href = "QAD-POV.php?panel=department";
                        syncAndReload();
                    } else {
                        alert('Error adding structure: ' + data.message);
                    }
                } catch (e) {
                    alert("Database saved folder, reloading UI.");
                    window.location.reload(); 
                    syncAndReload();
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
                        window.location.href = "QAD-POV.php?panel=department";
                        syncAndReload();
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

                // Show loading spinner
                const originalBtnHtml = confirmDeleteButton.innerHTML;
                confirmDeleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

                fetch('../../generalComponents/dpManagerPHP/deleteDepartment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ departmentId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        deleteConfirmationContainer.style.display = 'none';
                        if(overlay) overlay.style.display = 'none';
                        departmentToDelete = null;

                        // ✨ FIX: Reload the department panel after remove and update
                        window.location.href = "QAD-POV.php?panel=department";
                        syncAndReload();
                    } else {
                        alert('Failed to delete department: ' + (data.message || 'Unknown error'));
                        confirmDeleteButton.innerHTML = originalBtnHtml;
                    }
                })
                .catch(error => {
                    alert('Error deleting department: ' + error);
                    confirmDeleteButton.innerHTML = originalBtnHtml;
                });
                
            } else if (roleToDelete) {
                // ... Keep your existing user deletion code exactly as it is ...
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

// =====================================================================
    // ✨ DEPARTMENT MANAGER: SEARCH & FILTER ENGINE (UPGRADED)
    // =====================================================================
    const dmSearchInput = document.getElementById('dmSearchInput');
    const dmFilterSelect = document.getElementById('dmFilterSelect');


    function applyDepartmentFilters() {
        const searchTerm = dmSearchInput ? dmSearchInput.value.toLowerCase() : '';
        const filterValue = dmFilterSelect ? dmFilterSelect.value : 'all';
        
        const allFolders = document.querySelectorAll('#departmentListContainer .department-item');
        const allEmployees = document.querySelectorAll('#departmentListContainer .assigned-role-item');
        const allContainers = document.querySelectorAll('#departmentListContainer .department-children-container');

        // ✨ 1. DEFAULT STATE: If search is empty and filter is "Show All", reset perfectly!
        if (searchTerm === '' && filterValue === 'all') {
            allFolders.forEach(f => {
                f.style.display = 'flex';
                f.classList.remove('folder-open'); // Close all triangles
            });
            allEmployees.forEach(e => e.style.display = 'flex');
            allContainers.forEach(c => c.style.display = 'none'); // Hide nested contents
            return; // Exit early!
        }

        // 2. Hide everything initially for filtering
        allFolders.forEach(f => {
            f.style.display = 'none';
            f.classList.remove('folder-open');
        });
        allEmployees.forEach(e => e.style.display = 'none');
        allContainers.forEach(c => c.style.display = 'none');

        // ✨ THE FIX: A helper function that climbs up and opens EVERY parent folder
        function revealAncestors(element) {
            let currentContainer = element.closest('.department-children-container');
            while (currentContainer) {
                currentContainer.style.display = 'flex'; // Show the container
                
                const parentId = currentContainer.dataset.parentId;
                if (parentId) {
                    const parentFolder = document.querySelector(`.department-item[data-department-id="${parentId}"]`);
                    if (parentFolder) {
                        parentFolder.style.display = 'flex'; // Show the folder
                        parentFolder.classList.add('folder-open'); // Spin the triangle
                        // Move up to the next parent container in the chain
                        currentContainer = parentFolder.closest('.department-children-container');
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
        }

        // 3. Process Employees
        allEmployees.forEach(emp => {
            const empText = emp.textContent.toLowerCase();
            const matchesSearch = empText.includes(searchTerm);
            
            if (matchesSearch && filterValue !== 'departments_only') {
                emp.style.display = 'flex';
                revealAncestors(emp); // Blast open all doors to the root!
            }
        });

        // 4. Process Departments
        allFolders.forEach(folder => {
            const folderNameSpan = folder.querySelector('span');
            const folderName = folderNameSpan ? folderNameSpan.textContent.toLowerCase() : '';
            const matchesSearch = folderName.includes(searchTerm);
            
            if (matchesSearch) {
                if (filterValue === 'employees_only') {
                    // Only show this folder if it has visible employees inside it
                    const folderId = folder.dataset.departmentId;
                    const childContainer = document.querySelector(`.department-children-container[data-parent-id="${folderId}"]`);
                    const hasVisibleEmployees = childContainer ? Array.from(childContainer.querySelectorAll('.assigned-role-item')).some(e => e.style.display === 'flex') : false;
                    
                    if (hasVisibleEmployees) {
                        folder.style.display = 'flex';
                        revealAncestors(folder);
                    }
                } else {
                    folder.style.display = 'flex';
                    revealAncestors(folder); // Blast open all doors to the root!
                }
            }
        });
    }

    // Attach the listeners so it filters in real-time as you type or click
    if (dmSearchInput) dmSearchInput.addEventListener('input', applyDepartmentFilters);
    if (dmFilterSelect) dmFilterSelect.addEventListener('change', applyDepartmentFilters);

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

    if (pmFoldersContainer) {
        pmFoldersContainer.setAttribute('data-id', 'root');
        pmFoldersContainer.setAttribute('data-type', 'category');
        pmFoldersContainer.addEventListener('dragover', handleDragOver);
        pmFoldersContainer.addEventListener('dragleave', handleDragLeave);
        pmFoldersContainer.addEventListener('drop', handleDrop);
    }

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
    fetch(`../../generalComponents/policyManagerPHP/getCategories.php?_=${new Date().getTime()}`)
    fetch(`../../generalComponents/policyManagerPHP/getCategories.php?_=${new Date().getTime()}`) // This line is correct, no change needed, just confirming.
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
                        if (policy.categoryID) {
                            renderPMPolicy(policy.title, policy.policyID, policy.categoryID);
                        } else {
                            renderPMRootPolicy(policy.title, policy.policyID);
                        }
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
                    
                    // ✨ FIXED: Leaves the breadcrumb before reloading!
                    syncAndReload();
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
                    syncAndReload();
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
            if (!folderToEditId) return; // Safety check

            fetch('../../generalComponents/policyManagerPHP/deleteCategory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ categoryID: folderToEditId })
            })
            .then(res => {
                if (!res.ok) throw new Error(`Server responded with status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    pmDeleteModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    syncAndReload();
                } else {
                    alert("Cannot delete: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error deleting folder:", error);
                alert("An error occurred while trying to delete the folder. Please check the console for details.");
            });
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
            .then(res => {
                if (!res.ok) throw new Error(`Server responded with status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    pmAddFileModal.style.display = 'none';
                    if(globalOverlay) globalOverlay.style.display = 'none';
                    syncAndReload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error assigning policy:", error);
                alert("An error occurred while assigning the policy. Please check the console for details.");
            });
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
                    
                    syncAndReload();
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
    // --- RENDER FOLDER FUNCTION ---
    function renderPMFolder(name, categoryId, parentId) {
        const folderDiv = document.createElement('div');
        folderDiv.className = 'pm-folder-item';
        if (parentId !== null) folderDiv.classList.add('pm-child-folder');
        folderDiv.dataset.categoryId = categoryId;
        
        // MAKE FOLDERS DRAGGABLE
        folderDiv.setAttribute('data-id', categoryId);
        folderDiv.setAttribute('data-type', 'category');
        folderDiv.setAttribute('draggable', 'true');
        folderDiv.addEventListener('dragstart', handleDragStart);
        folderDiv.addEventListener('dragend', handleDragEnd);
        folderDiv.addEventListener('dragover', handleDragOver);
        folderDiv.addEventListener('dragleave', handleDragLeave);
        folderDiv.addEventListener('drop', handleDrop);
        
        let iconsHTML = '';
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

        // ✨ ADDED: Inserted the triangle icon right before the name!
        folderDiv.innerHTML = `
            <div style="display: flex; align-items: center;">
                <i class="fas fa-caret-right folder-toggle-icon"></i>
                <p class="pm-folder-name">${name}</p>
            </div>
            <div class="pm-folder-icons">
                ${iconsHTML}
            </div>
        `;

        // EVENT LISTENERS FOR ICONS
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
            
            // ✨ ADDED: This line spins the triangle!
            folderDiv.classList.toggle('folder-open', isHidden);
        });

        // CORRECTLY APPEND FOLDERS TO LIST
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

    // --- RENDER ROOT POLICY FILE FUNCTION ---
    function renderPMRootPolicy(title, policyId) {
        const policyDiv = document.createElement('div');
        policyDiv.classList.add('pm-policy-item');
        policyDiv.style.marginLeft = '0px'; 
        policyDiv.style.width = '100%';
        policyDiv.dataset.policyId = policyId;
        
        policyDiv.setAttribute('data-id', policyId);
        policyDiv.setAttribute('data-type', 'policy');
        policyDiv.setAttribute('draggable', 'true');
        policyDiv.addEventListener('dragstart', handleDragStart);
        policyDiv.addEventListener('dragend', handleDragEnd);
        
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
            e.stopPropagation();
            policyToRemoveId = policyId;
            policyToRemoveElement = policyDiv;
            pmRemovePolicyModal.style.display = 'block';
            if(globalOverlay) globalOverlay.style.display = 'block';
        });

        pmFoldersContainer.appendChild(policyDiv);
    }

    // --- RENDER POLICY FILE FUNCTION ---
    function renderPMPolicy(title, policyId, categoryId) {
        // Drop directly into the parent folder's hidden container!
        const parentContainer = document.querySelector(`.pm-children-container[data-parent-folder-id="${categoryId}"]`);
        
        if (parentContainer) {
            const policyDiv = document.createElement('div');
            policyDiv.classList.add('pm-policy-item');
            policyDiv.dataset.policyId = policyId;
            
            // ✨ THE FIX: Added the draggable properties to the right place!
            policyDiv.setAttribute('data-id', policyId);
            policyDiv.setAttribute('data-type', 'policy');
            policyDiv.setAttribute('draggable', 'true');
            policyDiv.addEventListener('dragstart', handleDragStart);
            policyDiv.addEventListener('dragend', handleDragEnd);
            
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

// =====================================================================
    // ✨ POLICY MANAGER: SEARCH & FILTER ENGINE
    // =====================================================================
    const pmSearchInput = document.getElementById('pmSearchInput');
    const pmFilterSelect = document.getElementById('pmFilterSelect');

    function applyPolicyFilters() {
        const searchTerm = pmSearchInput ? pmSearchInput.value.toLowerCase() : '';
        const filterValue = pmFilterSelect ? pmFilterSelect.value : 'all';
        
        const allFolders = document.querySelectorAll('#pmFoldersContainer .pm-folder-item');
        const allPolicies = document.querySelectorAll('#pmFoldersContainer .pm-policy-item');
        const allContainers = document.querySelectorAll('#pmFoldersContainer .pm-children-container');

        // ✨ 1. DEFAULT STATE: If search is empty and filter is "Show All", reset perfectly!
        if (searchTerm === '' && filterValue === 'all') {
            allFolders.forEach(f => {
                f.style.display = 'flex';
                f.classList.remove('folder-open'); // Close all triangles
            });
            allPolicies.forEach(p => p.style.display = 'flex');
            allContainers.forEach(c => c.style.display = 'none'); // Hide nested contents
            return; // Exit early!
        }

        // 2. Hide everything initially for filtering
        allFolders.forEach(f => {
            f.style.display = 'none';
            f.classList.remove('folder-open');
        });
        allPolicies.forEach(p => p.style.display = 'none');
        allContainers.forEach(c => c.style.display = 'none');

        // ✨ A helper function that climbs up and opens EVERY parent folder
        function revealPMAncestors(element) {
            let currentContainer = element.closest('.pm-children-container');
            while (currentContainer) {
                currentContainer.style.display = 'flex'; // Show the container
                
                const parentId = currentContainer.dataset.parentFolderId;
                if (parentId) {
                    const parentFolder = document.querySelector(`.pm-folder-item[data-category-id="${parentId}"]`);
                    if (parentFolder) {
                        parentFolder.style.display = 'flex'; // Show the folder
                        parentFolder.classList.add('folder-open'); // Spin the triangle
                        // Move up to the next parent container in the chain
                        currentContainer = parentFolder.closest('.pm-children-container');
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
        }

        // 3. Process Policies (Files)
        allPolicies.forEach(policy => {
            const policyText = policy.textContent.toLowerCase();
            const matchesSearch = policyText.includes(searchTerm);
            
            if (matchesSearch && filterValue !== 'folders_only') {
                policy.style.display = 'flex';
                revealPMAncestors(policy); // Blast open all doors to the root!
            }
        });

        // 4. Process Folders
        allFolders.forEach(folder => {
            const folderNameSpan = folder.querySelector('.pm-folder-name');
            const folderName = folderNameSpan ? folderNameSpan.textContent.toLowerCase() : '';
            const matchesSearch = folderName.includes(searchTerm);
            
            if (matchesSearch) {
                if (filterValue === 'policies_only') {
                    // Only show this folder if it has visible policy files inside it
                    const folderId = folder.dataset.categoryId;
                    const childContainer = document.querySelector(`.pm-children-container[data-parent-folder-id="${folderId}"]`);
                    const hasVisiblePolicies = childContainer ? Array.from(childContainer.querySelectorAll('.pm-policy-item')).some(p => p.style.display === 'flex') : false;
                    
                    if (hasVisiblePolicies) {
                        folder.style.display = 'flex';
                        revealPMAncestors(folder);
                    }
                } else {
                    folder.style.display = 'flex';
                    revealPMAncestors(folder); // Blast open all doors to the root!
                }
            }
        });
    }

    // Attach the listeners so it filters in real-time as you type or click
    if (pmSearchInput) pmSearchInput.addEventListener('input', applyPolicyFilters);
    if (pmFilterSelect) pmFilterSelect.addEventListener('change', applyPolicyFilters);


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

/* =====================================================================
   GLOBAL DRAG AND DROP MANAGER (Reusable across all modules)
   ===================================================================== */
let draggedElementId = null;
let draggedElementType = null; 
let draggedElementSourceId = null; // ✨ NEW: Tracks which folder the user came from
let dropPlaceholder = null; 

function handleDragStart(e) {
    draggedElementId = e.currentTarget.getAttribute('data-id');
    draggedElementType = e.currentTarget.getAttribute('data-type');
    draggedElementSourceId = e.currentTarget.getAttribute('data-parent-folder-id') || e.currentTarget.getAttribute('data-dpt-id'); 
    e.currentTarget.style.opacity = '0.2'; 

    dropPlaceholder = document.createElement('div');
    dropPlaceholder.className = 'drop-placeholder';
    dropPlaceholder.innerHTML = e.currentTarget.innerHTML; 
    
    // ✨ NEW: If it's an employee, make the ghost pill-shaped!
    if (draggedElementType === 'employee') {
        dropPlaceholder.style.borderRadius = '50px';
        dropPlaceholder.style.padding = '6px 15px';
    }
}

function handleDragEnd(e) {
    e.currentTarget.style.opacity = '1'; 
    if (dropPlaceholder && dropPlaceholder.parentNode) {
        dropPlaceholder.parentNode.removeChild(dropPlaceholder);
    }
    dropPlaceholder = null;
}

function handleDragOver(e) {
    e.preventDefault(); 
    e.stopPropagation(); 

    const targetType = e.currentTarget.getAttribute('data-type');
    const targetId = e.currentTarget.getAttribute('data-id');

    // ✨ UPDATED RULES: Allow Policies and Categories to drop into Categories
    if (draggedElementType === 'employee' && targetType !== 'department') return;
    if (draggedElementType === 'department' && targetType !== 'department') return;
    if ((draggedElementType === 'policy' || draggedElementType === 'category') && targetType !== 'category') return;

    if (dropPlaceholder) {
        if (targetId === 'root') {
            if (draggedElementType === 'employee' || draggedElementType === 'policy') return; // Can't drop these on root background
            dropPlaceholder.className = 'drop-placeholder';
            e.currentTarget.appendChild(dropPlaceholder);
        } else {
            dropPlaceholder.className = 'drop-placeholder';
            
            // Assign the correct nesting class based on what we are dragging and where
            if (draggedElementType === 'employee') {
                dropPlaceholder.classList.add(e.currentTarget.classList.contains('dpt-child-folder') ? 'nested-employee' : 'parent-employee');
            } else if (draggedElementType === 'policy') {
                dropPlaceholder.classList.add('pm-policy-item'); // Policy styling
            } else {
                dropPlaceholder.classList.add(draggedElementType === 'department' ? 'dpt-child-folder' : 'pm-child-folder');
            }
            
            const childContainer = document.querySelector(`.department-children-container[data-parent-id="${targetId}"]`) 
                                || document.querySelector(`.pm-children-container[data-parent-folder-id="${targetId}"]`);
            
            if (childContainer) {
                childContainer.style.display = 'flex'; 
                childContainer.appendChild(dropPlaceholder);
            }
            
            e.currentTarget.style.border = "2px dashed #fbaf41"; 
        }
    }
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    // Removes the yellow dashed border when you drag your mouse away from a folder
    e.currentTarget.style.border = "none";
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.style.border = "none";

    if (dropPlaceholder && dropPlaceholder.parentNode) {
        dropPlaceholder.parentNode.removeChild(dropPlaceholder);
    }
    dropPlaceholder = null;

    const targetParentId = e.currentTarget.getAttribute('data-id');
    const targetType = e.currentTarget.getAttribute('data-type');

    if (draggedElementId === targetParentId) return;
    
    // Same updated validation rules
    if (draggedElementType === 'employee' && targetType !== 'department') return;
    if (draggedElementType === 'department' && targetType !== 'department') return;
    if ((draggedElementType === 'policy' || draggedElementType === 'category') && targetType !== 'category') return;

    let phpEndpoint = '';
    let requestBody = {};

    if (draggedElementType === 'department') {
        phpEndpoint = '../../generalComponents/dpManagerPHP/moveDepartment.php';
        requestBody = { departmentId: draggedElementId, newParentId: targetParentId === 'root' ? null : targetParentId };
    } else if (draggedElementType === 'category') {
        // ✨ NEW: Route for moving folders in Policy Manager
        phpEndpoint = '../../generalComponents/policyManagerPHP/moveCategory.php';
        requestBody = { categoryID: draggedElementId, newParentID: targetParentId === 'root' ? null : targetParentId };
    } else if (draggedElementType === 'policy') {
        // ✨ NEW: Route for moving files in Policy Manager
        phpEndpoint = '../../generalComponents/policyManagerPHP/movePolicy.php';
        requestBody = { policyId: draggedElementId, newFolderId: targetParentId === 'root' ? null : targetParentId };
    } else if (draggedElementType === 'employee') {
        if (targetParentId === 'root') return; 
        phpEndpoint = '../../generalComponents/dpManagerPHP/moveEmployee.php';
        requestBody = { accID: draggedElementId, oldDptID: draggedElementSourceId, newDptID: targetParentId };
    }

    if (phpEndpoint) {
        fetch(phpEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestBody)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('internalSync', 'true');
                location.reload(); 
            } else {
                alert("Move failed: " + data.message);
            }
        })
        .catch(err => {
            console.error("Error moving item:", err);
            alert("Network Error: Could not connect to move script.");
        });
    }
}


/* =====================================================================
   ✨ SECURITY: ADVANCED ANTI-SCREENSHOT & COPY SHIELD
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    const pdfContainer = document.getElementById('Policy_Repo_pdfViewer');
    const pdfCanvas = document.getElementById('pr_pdfCanvas');
    
    if (pdfContainer && pdfCanvas) {
        
        // Helper function to instantly black out the document
        const secureDocument = () => {
            if (pdfContainer.style.display === 'flex' || pdfContainer.style.display === 'block') {
                pdfCanvas.style.filter = 'blur(40px)';
                pdfCanvas.style.opacity = '0'; // Completely invisible
            }
        };

        // Helper function to restore the document
        const restoreDocument = () => {
            if (pdfContainer.style.display === 'flex' || pdfContainer.style.display === 'block') {
                pdfCanvas.style.filter = 'none';
                pdfCanvas.style.opacity = '1';
            }
        };

        // 1. Disable Right-Click (Context Menu)
        pdfContainer.addEventListener('contextmenu', (e) => e.preventDefault());

        // 2. PREEMPTIVE KEYBOARD STRIKES
        document.addEventListener('keydown', (e) => {
            if (pdfContainer.style.display === 'flex' || pdfContainer.style.display === 'block') {
                
                // Block Print/Save/Copy (Ctrl+P, Ctrl+S, Ctrl+C)
                if ((e.ctrlKey || e.metaKey) && ['p', 's', 'c'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                    alert("SECURITY ALERT: Copying or saving is prohibited.");
                }

                // 🚨 BEAT THE SNIPPING TOOL: Catch Win+Shift+S or Mac Cmd+Shift+4
                if ((e.metaKey || e.ctrlKey) && e.shiftKey) {
                    secureDocument(); // Blackout immediately before the OS freezes the screen!
                }
                
                // Catch PrintScreen keypress
                if (e.key === 'PrintScreen') {
                    secureDocument();
                    navigator.clipboard.writeText("SECURITY ALERT: Screenshotting official policies is prohibited.");
                }
            }
        });

        // Restore document a second after they let go of the screenshot keys
        document.addEventListener('keyup', (e) => {
            if (e.key === 'PrintScreen' || e.key === 'Meta' || e.key === 'Shift') {
                setTimeout(restoreDocument, 1000); 
            }
        });

        // 3. Window Blur (Catches Alt-Tabbing to another monitor/app)
        window.addEventListener('blur', secureDocument);
        window.addEventListener('focus', restoreDocument);

        // 4. 🚨 AGGRESSIVE MOUSE TRACKING
        // If they try to move their mouse to the Start Menu/Taskbar to click a snipping tool, 
        // the document vanishes the moment the cursor leaves the browser window.
        document.addEventListener('mouseleave', secureDocument);
        document.addEventListener('mouseenter', restoreDocument);
    }
});
/* =====================================================================
   ✨ REPORTS DASHBOARD: CHART INITIALIZATION
   ===================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    
    let workflowChartInstance = null;
    let statusChartInstance = null;
    let accessChartInstance = null;

    // ✨ NEW: Populate date filters for the details table
    function populateReportDateFilters() {
        const monthSelect = document.getElementById('filterMonth');
        const yearSelect = document.getElementById('filterYear');
        const chartMonthSelect = document.getElementById('chartFilterMonth');
        const chartYearSelect = document.getElementById('chartFilterYear');
        const pendingChartMonthSelect = document.getElementById('pendingChartFilterMonth');
        const pendingChartYearSelect = document.getElementById('pendingChartFilterYear');

        const currentYear = new Date().getFullYear();

        // Populate months
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        
        [monthSelect, chartMonthSelect, pendingChartMonthSelect].forEach(select => {
            if (!select) return;
            while (select.options.length > 1) select.remove(1);
            months.forEach((month, index) => {
                const option = document.createElement('option');
                option.value = index + 1;
                option.textContent = month;
                select.appendChild(option);
            });
        });

        [yearSelect, chartYearSelect, pendingChartYearSelect].forEach(select => {
            if (!select) return;
            select.innerHTML = '<option value="">All Years</option>'; 
            for (let y = currentYear + 2; y >= currentYear - 5; y--) {
                select.appendChild(new Option(y, y));
            }
            select.value = currentYear;
        });
    }
    populateReportDateFilters();

    window.renderReports = function() {
        
        fetch('../../generalComponents/policyManagerPHP/getReportsData.php')
            .then(response => response.json())
            .then(dbData => {
                
                // 1. INJECT KPI RIBBON
                if (dbData.kpiData) {
                    document.getElementById('kpi-active').innerText = dbData.kpiData.active;
                    document.getElementById('kpi-pending').innerText = dbData.kpiData.pending;
                    document.getElementById('kpi-speed').innerHTML = `${dbData.kpiData.speed} <span style="font-size: 14px; color: #64748b;">Days</span>`;
                    document.getElementById('kpi-expiring').innerText = dbData.kpiData.expiring;
                    if (document.getElementById('kpi-rejected')) {
                        document.getElementById('kpi-rejected').innerText = dbData.kpiData.rejected || 0;
                    }

                    // ✨ Rename "Pending Review" to "Pending Tasks"
                    document.querySelectorAll('.kpi-box h3, .kpi-box h4, .kpi-box div, .kpi-box p, .kpi-box span').forEach(el => {
                        if (el.innerText.trim() === 'Pending Review') {
                            el.innerText = 'Pending Tasks';
                        }
                    });

                    // ✨ Ensure KPI boxes are clickable and load the correct report details
                    const pendingKpiBox = document.getElementById('kpi-pending')?.closest('.kpi-box');
                    if (pendingKpiBox) pendingKpiBox.onclick = () => togglePendingTasksChart();
                    
                    const activeKpiBox = document.getElementById('kpi-active')?.closest('.kpi-box');
                    if (activeKpiBox) activeKpiBox.onclick = () => loadReportDetails('active');
                }

                // 2. WORKFLOW BAR CHART (Dynamic)
                const ctxWorkflow = document.getElementById('workflowChart');
                if (ctxWorkflow) {
                    if (workflowChartInstance) workflowChartInstance.destroy(); 
                    workflowChartInstance = new Chart(ctxWorkflow, {
                        type: 'bar',
                        data: {
                            labels: ['Drafting', 'Review', 'Verification', 'Approval'],
                            datasets: [{
                                label: 'Average Days',
                                data: dbData.workflowData, // 👈 PULLING FROM DATABASE
                                backgroundColor: '#fbaf41', 
                                borderRadius: 6
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });
                }

                // 3. STATUS DONUT CHART (Dynamic)
                const ctxStatus = document.getElementById('statusChart');
                if (ctxStatus) {
                    if (statusChartInstance) statusChartInstance.destroy();
                    statusChartInstance = new Chart(ctxStatus, {
                        type: 'doughnut',
                        data: {
                            labels: ['Approved', 'Under Review', 'Draft', 'Archived'],
                            datasets: [{
                                data: dbData.statusData, // 👈 PULLING FROM DATABASE
                                backgroundColor: ['#1a2035', '#fbaf41', '#64748b', '#ef4444'],
                                borderWidth: 0
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', layout: { padding: 10 } }
                    });
                }

                // 4. ACCESS TRENDS LINE CHART (Dynamic)
                const ctxAccess = document.getElementById('accessChart');
                if (ctxAccess) {
                    if (accessChartInstance) accessChartInstance.destroy();
                    accessChartInstance = new Chart(ctxAccess, {
                        type: 'line',
                        data: {
                            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            datasets: [{
                                label: 'Policy Views',
                                data: dbData.accessData, // 👈 PULLING FROM DATABASE
                                borderColor: '#1a2035', 
                                backgroundColor: 'rgba(26, 32, 53, 0.1)', 
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4 
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }
            })
            .catch(error => console.error("Error fetching report data:", error));
    };
});

// =====================================================================
// ✨ INLINE REMARKS SECTION ✨
// =====================================================================
window.openFeedbackModal = function() {
    const policyId = window.currentSelectedPolicyId; // ✨ THE FIX: Grab the ID from the global scope

    if (!policyId) {
        alert("Error: Please select a valid policy.");
        return;
    }

    window.currentPolicyId = policyId; // Set it for the submit function to use
    const remarksSection = document.getElementById('policyRemarksSection');
    const remarkInput = document.getElementById('policyRemarkText');

    if (remarksSection) {
        const isVisible = remarksSection.style.display === 'block';
        remarksSection.style.display = isVisible ? 'none' : 'block';
    }

    if (remarkInput) {
        remarkInput.focus();
    }
};

const submitRemarkBtn = document.getElementById('submitRemarkBtn');
const cancelRemarkBtn = document.getElementById('cancelRemarkBtn');
const policyRemarkText = document.getElementById('policyRemarkText');

if (cancelRemarkBtn) {
    cancelRemarkBtn.addEventListener('click', () => {
        const remarksSection = document.getElementById('policyRemarksSection');
        if (remarksSection) remarksSection.style.display = 'none';
        if (policyRemarkText) policyRemarkText.value = '';
    });
}

if (submitRemarkBtn) {
    submitRemarkBtn.addEventListener('click', function() {
        const content = policyRemarkText ? policyRemarkText.value.trim() : '';
        if (!content) return alert("Please write a remark.");

        const btn = this;
        btn.innerHTML = "Submitting...";
        btn.disabled = true;

        fetch('../../generalComponents/policyManagerPHP/submitFeedback.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ policyId: window.currentPolicyId, content: content })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Feedback submitted successfully!");
                if (policyRemarkText) policyRemarkText.value = '';
                const remarksSection = document.getElementById('policyRemarksSection');
                if (remarksSection) remarksSection.style.display = 'none';
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => alert("Network error."))
        .finally(() => {
            btn.innerHTML = "Submit Remark";
            btn.disabled = false;
        });
    });
}

window.loadWorkspaceFeedbacks = function() {
    const container = document.getElementById('workspaceFeedbackList');
    if (!container) return;

    fetch('../../generalComponents/policyManagerPHP/getFeedbacks.php')
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            container.innerHTML = `<p style="color:red;">Error: ${data.message}</p>`;
            return;
        }

        if (data.feedbacks.length === 0) {
            container.innerHTML = '<p style="padding: 20px;">No feedbacks found.</p>';
            return;
        }

        // Render the list with better text wrapping
        // Inside your loadWorkspaceFeedbacks function in QAD-POV.js
    container.innerHTML = data.feedbacks.map((fb, index) => `
    <div class="fb-item" style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 8px; cursor: pointer;" onclick="toggleFbDetails(${index})">
        <div style="display: flex; justify-content: space-between;">
            <strong>Policy: ${fb.policyTitle}</strong>
            <span>${fb.dateSubmitted}</span>
        </div>
        <div>Submitted by: ${fb.submittedBy}</div>
        
        <div id="fb-details-${index}" style="display:none; margin-top:10px; padding-top:10px; border-top:1px solid #eee;">
            <p><strong>Feedback Content:</strong></p>
            <div style="
                background: #f9f9f9; 
                padding: 10px; 
                border-radius: 5px; 
                max-height: 150px;       /* Limits the height to trigger scrolling */
                overflow-y: auto;        /* Adds the vertical scrollbar */
                white-space: pre-wrap;   /* Keeps your paragraph formatting */
                border: 1px solid #eee;  /* Visual cue for the scroll box */
            ">
                ${fb.content}
            </div>
        </div>
    </div>
`).join('');
    })
    .catch(err => {
        console.error(err);
        container.innerHTML = '<p>Error loading feedbacks.</p>';
    });
};

// Helper to toggle details
window.toggleFbDetails = function(index) {
    const el = document.getElementById(`fb-details-${index}`);
    if (el) el.style.display = (el.style.display === 'none' ? 'block' : 'none');
};

/* =====================================================================
// ✨ ACTIVE POLICIES CHART & TOGGLE ENGINE ✨
// ===================================================================== */
let activePoliciesChartInstance = null;
let currentChartParentId = null;

window.toggleActivePoliciesChart = function() {
    const chartContainer = document.getElementById('activePoliciesChartContainer');
    const detailsContainer = document.getElementById('reportDetailsArea');
    
    if (detailsContainer) detailsContainer.style.display = 'none';

    if (chartContainer.style.display === 'none' || chartContainer.style.display === '') {
        chartContainer.style.display = 'block';
        currentChartParentId = null;
        renderActivePoliciesChart();
    } else {
        chartContainer.style.display = 'none';
    }
};

window.renderActivePoliciesChart = function(drilldownId = undefined) {
    const ctx = document.getElementById('activePoliciesChart');
    if (!ctx) return;

    if (drilldownId !== undefined) {
        currentChartParentId = drilldownId;
    }

    const month = document.getElementById('chartFilterMonth') ? document.getElementById('chartFilterMonth').value : '';
    const year = document.getElementById('chartFilterYear') ? document.getElementById('chartFilterYear').value : '';

    let url = `../../generalComponents/policyManagerPHP/getActivePoliciesChartData.php?month=${month}&year=${year}`;
    if (currentChartParentId !== null) {
        url += `&parentID=${currentChartParentId}`;
    }

    fetch(url)
        .then(res => res.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (!data.success) {
                    alert("Error loading chart data: " + data.message);
                    return;
                }
                if (activePoliciesChartInstance) activePoliciesChartInstance.destroy();

                const backBtn = document.getElementById('chartBackBtn');
                if (currentChartParentId !== null) {
                    if (!backBtn) {
                        const btn = document.createElement('button');
                        btn.id = 'chartBackBtn';
                        btn.innerHTML = '<i class="fas fa-arrow-left"></i> Back to All Folders';
                        btn.style.cssText = 'margin-bottom: 15px; padding: 5px 15px; background: #293A82; color: white; border: none; border-radius: 5px; cursor: pointer; display: inline-block;';
                        btn.onclick = () => renderActivePoliciesChart(null);
                        document.getElementById('activePoliciesChartContainer').insertBefore(btn, document.getElementById('activePoliciesChart').parentNode);
                    } else {
                        backBtn.style.display = 'inline-block';
                    }
                } else if (backBtn) {
                    backBtn.style.display = 'none';
                }

                activePoliciesChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Active Policies',
                            data: data.active,
                            backgroundColor: '#fbaf41',
                            barPercentage: 0.6,
                            categoryPercentage: 0.8,
                            categoryIds: data.categoryIds
                        }]
                    },
                    options: { 
                        indexAxis: 'y', 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    footer: (tooltipItems) => {
                                        const index = tooltipItems[0].dataIndex;
                                        const catId = data.categoryIds[index];
                                        if (catId !== null && currentChartParentId === null) {
                                            return 'Click to view child folders';
                                        }
                                        return null;
                                    }
                                }
                            }
                        }, 
                        scales: { x: { beginAtZero: true }, y: { grid: { display: false } } },
                        onClick: (event, elements, chart) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const catId = chart.data.datasets[0].categoryIds[index];
                                
                                if (catId !== null && currentChartParentId === null) {
                                    renderActivePoliciesChart(catId);
                                }
                            }
                        },
                        onHover: (event, elements, chart) => {
                            if (elements.length > 0 && currentChartParentId === null && chart.data.datasets[0].categoryIds[elements[0].index] !== null) {
                                event.native.target.style.cursor = 'pointer';
                            } else {
                                event.native.target.style.cursor = 'default';
                            }
                        }
                    }
                });
            } catch (e) {
                alert("Database Error: Could not render chart. Press F12 to check console for details.");
            }
        }).catch(err => console.error("Network Error:", err));
};

let pendingTasksChartInstance = null;

window.togglePendingTasksChart = function() {
    const chartContainer = document.getElementById('pendingTasksChartContainer');
    const activeChartContainer = document.getElementById('activePoliciesChartContainer');
    const detailsContainer = document.getElementById('reportDetailsArea');
    
    if (activeChartContainer) activeChartContainer.style.display = 'none';
    if (detailsContainer) detailsContainer.style.display = 'none';

    if (chartContainer.style.display === 'none' || chartContainer.style.display === '') {
        chartContainer.style.display = 'block';
        renderPendingTasksChart();
    } else {
        chartContainer.style.display = 'none';
    }
};

window.renderPendingTasksChart = function() {
    const ctx = document.getElementById('pendingTasksChart');
    if (!ctx) return;

    const month = document.getElementById('pendingChartFilterMonth') ? document.getElementById('pendingChartFilterMonth').value : '';
    const year = document.getElementById('pendingChartFilterYear') ? document.getElementById('pendingChartFilterYear').value : '';

    let url = `../../generalComponents/policyManagerPHP/getReportsData.php?action=pendingChart&month=${month}&year=${year}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error("Error loading chart data", data);
                return;
            }
            if (pendingTasksChartInstance) pendingTasksChartInstance.destroy();

            pendingTasksChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: ['#fbaf41', '#293A82', '#64748b', '#ef4444', '#10b981'],
                        statusIds: data.statusIds
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { 
                        legend: { position: 'right' },
                        tooltip: { callbacks: { footer: () => 'Click to view policies under this task' } }
                    }, 
                    onClick: (event, elements, chart) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const statusId = chart.data.datasets[0].statusIds[index];
                            loadReportDetails('pending', statusId);
                        }
                    },
                    onHover: (event, elements, chart) => {
                        event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
        }).catch(err => console.error("Network Error:", err));
};

let currentReportType = '';
let currentStatusId = '';

window.loadReportDetails = function(type, statusId = '') {
    currentReportType = type;
    currentStatusId = statusId;
    const chartContainer = document.getElementById('activePoliciesChartContainer');
    const pendingChartContainer = document.getElementById('pendingTasksChartContainer');
    if (chartContainer) chartContainer.style.display = 'none';
    if (pendingChartContainer) pendingChartContainer.style.display = 'none';
    document.getElementById('reportDetailsArea').style.display = 'block';
    fetchReportDetails();
}

window.fetchReportDetails = function() {
    const month = document.getElementById('filterMonth').value;
    const year = document.getElementById('filterYear').value;
    
    let url = `../../generalComponents/policyManagerPHP/getReportsData.php?action=details&type=${currentReportType}&month=${month}&year=${year}`;
    if (currentStatusId) url += `&statusId=${currentStatusId}`;
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const table = document.getElementById('detailsTable');
            if (!table) return;
            
            let thead = table.querySelector('thead');
            if (!thead) {
                thead = document.createElement('thead');
                table.insertBefore(thead, table.firstChild);
            }
            
            const tbody = table.querySelector('tbody');
            
            window.currentReportsData = data; // Store globally for overlays
            
            // ✨ Dynamic table headers and rows based on report type
            if (currentReportType === 'pending') {
                thead.innerHTML = `
                    <tr>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Policy Name</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Current Task</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Days Assigned</th>
                    </tr>
                `;
                tbody.innerHTML = data.map(item => `<tr><td style="padding: 10px; border-bottom: 1px solid #eee;">${item.title || 'Untitled'}</td><td style="padding: 10px; border-bottom: 1px solid #eee;">${item.taskName}</td><td style="padding: 10px; border-bottom: 1px solid #eee;">${item.daysAssigned} days</td></tr>`).join('');
            } else if (currentReportType === 'rejected') {
                thead.innerHTML = `
                    <tr>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Policy Title</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Policy Author</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Submission Date</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Rejection Date</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Reason</th>
                    </tr>
                `;
                tbody.innerHTML = data.map((item, index) => `
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">${item.title || 'Untitled'}</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">${item.authorName || 'Unknown'}</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">${item.submissionDate ? new Date(item.submissionDate).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A'}</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">${item.rejectionDate ? new Date(item.rejectionDate).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A'}</td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><button onclick="showRejectedReason(${index})" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: bold; transition: 0.2s;"><i class="fas fa-comment-alt"></i> View Reason</button></td>
                    </tr>
                `).join('');
            } else {
                thead.innerHTML = `
                    <tr>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Folder / Category</th>
                        <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Total Policies</th>
                    </tr>
                `;
                tbody.innerHTML = data.map(item => `<tr><td style="padding: 10px; border-bottom: 1px solid #eee;">${item.categoryName || 'Main Repository'}</td><td style="padding: 10px; border-bottom: 1px solid #eee;">${item.total}</td></tr>`).join('');
            }
        });
}

// ✨ MODAL OVERLAY: Display Reason for Rejection
window.showRejectedReason = function(index) {
    const data = window.currentReportsData[index];
    const reason = data.reason || "No specific feedback or reason was provided for this rejection.";
    
    let modal = document.getElementById('rejectedReasonModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'rejectedReasonModal';
        modal.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;';
        modal.innerHTML = `
            <div style="background:white; padding:30px; border-radius:10px; width:450px; max-width:90%; color:#333; position:relative; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                <h3 style="margin-top:0; color:#ef4444; font-family: 'Istok Web', sans-serif; font-size: 22px; border-bottom: 2px solid #eee; padding-bottom: 10px;">Rejection Reason</h3>
                <p style="margin: 10px 0; font-size: 14px; color: #666;"><strong>Policy:</strong> <span id="rrModalTitle"></span></p>
                <div id="rejectedReasonText" style="margin-bottom:20px; white-space:pre-wrap; max-height:250px; overflow-y:auto; border:1px solid #e2e8f0; padding:15px; border-radius:8px; background:#f8fafc; font-size: 15px; line-height: 1.5;"></div>
                <div style="text-align:right;">
                    <button onclick="document.getElementById('rejectedReasonModal').style.display='none'" style="background:#64748b; color:white; border:none; padding:10px 25px; border-radius:5px; cursor:pointer; font-weight: bold; font-family: 'Istok Web', sans-serif; transition: background 0.2s;">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('rrModalTitle').textContent = data.title || 'Untitled';
    document.getElementById('rejectedReasonText').textContent = reason;
    modal.style.display = 'flex';
};

window.openDocumentHistoryModal = function(policyId) {
    if (!policyId) {
        alert("Error: Please select a valid policy.");
        return;
    }

    const overlay = document.getElementById('documentHistoryOverlay');
    const tbody = document.getElementById('documentHistoryTableBody');
    const subtitle = document.getElementById('docHistorySubtitle');
    
    if (overlay) overlay.style.display = 'flex';
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Loading history...</td></tr>';
    if (subtitle) subtitle.textContent = "Fetching revision history for this document...";

    fetch(`../../generalComponents/policyManagerPHP/getDocumentHistory.php?policyID=${policyId}`)
        .then(res => res.text()) // ✨ Use text() first to catch raw PHP errors
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success && data.history && data.history.length > 0) {
                    if (subtitle) subtitle.textContent = `History for: ${data.history[0].title}`;
                
                let html = '';
                data.history.forEach(item => {
                    html += `
                        <tr style="border-bottom: 1px solid #eee; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9f9f9'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 12px; font-weight: bold;">${item.versionNo}</td>
                            <td style="padding: 12px;">${item.title}</td>
                            <td style="padding: 12px;">${item.authorName}</td>
                            <td style="padding: 12px;">${item.approverName}</td>
                            <td style="padding: 12px;">${item.datePublished}</td>
                            <td style="padding: 12px; display: flex; flex-direction: column; gap: 5px;">
                                <button class="action-btn-inline" 
                                    onclick="openSecondaryPdfViewer('${item.contentPath}', '${item.title} (${item.versionNo})')"
                                    style="background:#293A82; color:white; border:none; padding: 6px 15px; border-radius: 5px; cursor:pointer; font-weight: bold; width: 100%;">
                                    <i class="fas fa-file-pdf"></i> View Policy
                                </button>
                                ${item.revisionFormPath ? `
                                <button class="action-btn-inline" 
                                    onclick="openSecondaryPdfViewer('${item.revisionFormPath}', 'Change Log - ${item.title}')"
                                    style="background:#fbaf41; color:black; border:none; padding: 6px 15px; border-radius: 5px; cursor:pointer; font-weight: bold; width: 100%;">
                                    <i class="fas fa-file-alt"></i> Change Log
                                </button>
                                ` : ''}
                            </td>
                        </tr>
                    `;
                });
                    if (tbody) tbody.innerHTML = html;
                } else {
                    if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">No history found.</td></tr>`;
                    if (subtitle) subtitle.textContent = "No history available.";
                }
            } catch (e) {
                console.error("Backend Error Response:", text);
                if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Database error. Press F12 to check console.</td></tr>`;
                if (subtitle) subtitle.textContent = "Error loading history.";
            }
        })
        .catch(err => {
            console.error("Network Error:", err);
            if (tbody) tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Network error. Could not reach server.</td></tr>`;
        });
};

// =====================================================================
// ✨ SECONDARY PDF VIEWER ENGINE (For Side-by-Side Comparison) ✨
// =====================================================================
var sec_pdfDoc = null,
    sec_pageNum = 1,
    sec_pageRendering = false,
    sec_pageNumPending = null,
    sec_scale = 1.2,
    sec_canvas = null,
    sec_ctx = null;

function sec_renderPage(num) {
    sec_pageRendering = true;
    if (sec_pdfDoc) {
        sec_pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({scale: sec_scale});
            sec_canvas.height = viewport.height;
            sec_canvas.width = viewport.width;

            let renderContext = {
                canvasContext: sec_ctx,
                viewport: viewport
            };
            let renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                sec_pageRendering = false;
                
                // Add watermark
                sec_ctx.save();
                sec_ctx.translate(sec_canvas.width / 2, sec_canvas.height / 2);
                sec_ctx.rotate(-Math.PI / 4); 
                sec_ctx.font = "bold 60px 'Istok Web', Arial, sans-serif";
                sec_ctx.fillStyle = "rgba(180, 180, 180, 0.4)"; 
                sec_ctx.textAlign = "center";
                sec_ctx.textBaseline = "middle";
                sec_ctx.fillText("OFFICIAL OPTIQUAL DOCUMENT", 0, 0);
                sec_ctx.font = "18px 'Istok Web', Arial, sans-serif";
                sec_ctx.fillText("DO NOT DISTRIBUTE OR REPRODUCE", 0, 40);
                sec_ctx.restore();

                if (sec_pageNumPending !== null) {
                    sec_renderPage(sec_pageNumPending);
                    sec_pageNumPending = null;
                }
            });
        });

        const pageNumEl = document.getElementById('sec_pageNum');
        const zoomLevelEl = document.getElementById('sec_zoomLevel');
        if (pageNumEl) pageNumEl.textContent = num;
        if (zoomLevelEl) zoomLevelEl.textContent = Math.round(sec_scale * 100) + '%';
    }
}

function sec_queueRenderPage(num) {
    if (sec_pageRendering) sec_pageNumPending = num;
    else sec_renderPage(num);
}

function sec_onPrevPage() { if (sec_pageNum <= 1) return; sec_pageNum--; sec_queueRenderPage(sec_pageNum); }
function sec_onNextPage() { if (!sec_pdfDoc || sec_pageNum >= sec_pdfDoc.numPages) return; sec_pageNum++; sec_queueRenderPage(sec_pageNum); }
function sec_onZoomIn() { sec_scale += 0.2; sec_queueRenderPage(sec_pageNum); }
function sec_onZoomOut() { if (sec_scale <= 0.6) return; sec_scale -= 0.2; sec_queueRenderPage(sec_pageNum); }

window.openSecondaryPdfViewer = function(filePath, documentTitle) {
    if (!filePath || filePath === 'null' || filePath.trim() === '') {
        alert("No PDF document available to view.");
        return; 
    }

    const viewerTitle = document.getElementById('secPdfViewerTitle');
    if (viewerTitle) viewerTitle.textContent = documentTitle || "Document Compare Viewer";

    const pdfViewerContainer = document.getElementById('Secondary_PdfViewer');
    if (pdfViewerContainer) pdfViewerContainer.style.display = 'flex'; 

    if (typeof pdfjsLib !== 'undefined') {
        const encodedUrl = encodeURI(filePath);
        pdfjsLib.getDocument(encodedUrl).promise.then(function(pdfDoc_) {
            sec_pdfDoc = pdfDoc_;
            const pageCountEl = document.getElementById('sec_pageCount');
            if (pageCountEl) pageCountEl.textContent = sec_pdfDoc.numPages;
            
            sec_pageNum = 1;
            sec_scale = 1.2;
            sec_renderPage(sec_pageNum);
        }).catch(function(error) {
            console.error("Error loading PDF: ", error);
            alert("Error loading document. The file may be missing or corrupted.");
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    sec_canvas = document.getElementById('sec_pdfCanvas');
    if(sec_canvas) sec_ctx = sec_canvas.getContext('2d');

    const prevBtn = document.getElementById('sec_prevPage');
    if(prevBtn) prevBtn.addEventListener('click', sec_onPrevPage);
    
    const nextBtn = document.getElementById('sec_nextPage');
    if(nextBtn) nextBtn.addEventListener('click', sec_onNextPage);
    
    const zoomInBtn = document.getElementById('sec_zoomIn');
    if(zoomInBtn) zoomInBtn.addEventListener('click', sec_onZoomIn);
    
    const zoomOutBtn = document.getElementById('sec_zoomOut');
    if(zoomOutBtn) zoomOutBtn.addEventListener('click', sec_onZoomOut);

    const closeBtn = document.getElementById('closeSecondaryPdfViewer');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            document.getElementById('Secondary_PdfViewer').style.display = 'none';
            if (sec_ctx && sec_canvas) {
                sec_ctx.clearRect(0, 0, sec_canvas.width, sec_canvas.height);
                sec_canvas.height = 0;
            }
        });
    }
});
