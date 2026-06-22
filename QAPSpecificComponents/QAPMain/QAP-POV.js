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

function showWelcomePage() {
    hideAllPanels();
    const welcome = document.querySelector('#Welcome-Panel');
    if (welcome) welcome.style.display = 'flex';
    localStorage.setItem('activePanel', 'welcome');
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

    const sidebarLogo = document.querySelector('.Sidebar-Logo');
    if (sidebarLogo) sidebarLogo.addEventListener('click', showWelcomePage);

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
        else if (savedPanel === 'welcome') showWelcomePage();
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

// ✨ NEW: Sort & Filter Policies Engine ✨
document.addEventListener('DOMContentLoaded', () => {
    // 1. Tag all existing elements with their original index so we can restore "Default" order
    let globalIndex = 0;
    document.querySelectorAll('.Parent-Block, .PR-Child-Folders, .PR-Policies').forEach(el => {
        el.setAttribute('data-original-index', globalIndex++);
    });

    const prSearchContainer = document.querySelector('.PR-Search-Container');
    if (prSearchContainer && !document.getElementById('prSortSelect')) {
        const sortSelect = document.createElement('select');
        sortSelect.id = 'prSortSelect';
        sortSelect.style.padding = '0 15px';
        sortSelect.style.height = '34px';
        sortSelect.style.borderRadius = '20px';
        sortSelect.style.border = '1px solid #ccc';
        sortSelect.style.marginLeft = '10px';
        sortSelect.style.fontFamily = "'Istok Web', sans-serif";
        sortSelect.style.outline = 'none';
        sortSelect.style.backgroundColor = 'white';
        sortSelect.style.cursor = 'pointer';
        
        let wrapper = prSearchContainer.parentElement;
        if (wrapper.id !== 'prControlsWrapper') {
            wrapper = document.createElement('div');
            wrapper.id = 'prControlsWrapper';
            wrapper.style.display = 'flex';
            wrapper.style.alignItems = 'center';
            wrapper.style.flexWrap = 'wrap';
            wrapper.style.marginBottom = '10px';
            prSearchContainer.parentNode.insertBefore(wrapper, prSearchContainer);
            wrapper.appendChild(prSearchContainer);
            prSearchContainer.style.marginBottom = '0';
        }

        sortSelect.innerHTML = `
            <option value="default">Sort: Default</option>
            <option value="alpha_asc">Alphabetical (A-Z)</option>
            <option value="alpha_desc">Alphabetical (Z-A)</option>
            <option value="date_desc">Newest Uploads</option>
            <option value="date_asc">Oldest Uploads</option>
        `;
        
        sortSelect.addEventListener('change', () => {
            const sortValue = sortSelect.value;
            
            const sortNodes = (nodeList, type) => {
                return Array.from(nodeList).sort((a, b) => {
                    if (sortValue === 'default') {
                        return parseInt(a.getAttribute('data-original-index') || 0) - parseInt(b.getAttribute('data-original-index') || 0);
                    }
                    
                    if (type === 'policy') {
                        if (sortValue.startsWith('alpha')) {
                            const nameA = (a.querySelector('.PR-Policies-Name') || a).textContent.trim().toLowerCase();
                            const nameB = (b.querySelector('.PR-Policies-Name') || b).textContent.trim().toLowerCase();
                            return sortValue === 'alpha_asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
                        } else if (sortValue.startsWith('date')) {
                            const dateA = new Date(a.getAttribute('data-upload-date') || 0).getTime();
                            const dateB = new Date(b.getAttribute('data-upload-date') || 0).getTime();
                            return sortValue === 'date_desc' ? dateB - dateA : dateA - dateB;
                        }
                    } else if (type === 'folder') {
                        if (sortValue.startsWith('alpha')) {
                            const pA = a.querySelector('.PR-Parent-Folder-Name') || a.querySelector('p');
                            const pB = b.querySelector('.PR-Parent-Folder-Name') || b.querySelector('p');
                            const nameA = pA ? pA.textContent.trim().toLowerCase() : '';
                            const nameB = pB ? pB.textContent.trim().toLowerCase() : '';
                            return sortValue === 'alpha_asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
                        } else {
                            return parseInt(a.getAttribute('data-original-index') || 0) - parseInt(b.getAttribute('data-original-index') || 0);
                        }
                    }
                    return 0;
                });
            };

            // 1. Sort Parent Blocks
            const prFoldersContainer = document.querySelector('.PR-Folders');
            if (prFoldersContainer) {
                const parentBlocks = prFoldersContainer.querySelectorAll(':scope > .Parent-Block');
                const sortedParentBlocks = sortNodes(parentBlocks, 'folder');
                sortedParentBlocks.forEach(block => prFoldersContainer.appendChild(block));
                
                // Sort Main Repository Policies (Policies not in any folder)
                const globalPolicies = prFoldersContainer.querySelectorAll(':scope > .PR-Policies');
                if (globalPolicies.length > 0) {
                     const sortedGlobalPolicies = sortNodes(globalPolicies, 'policy');
                     sortedGlobalPolicies.forEach(pol => prFoldersContainer.appendChild(pol));
                }
            }

            // 2. Sort Child Folders and their immediate Policies
            document.querySelectorAll('.child-folders').forEach(childContainer => {
                const childFoldersAndPolicies = Array.from(childContainer.children);
                
                const childFolderPairs = [];
                for (let i = 0; i < childFoldersAndPolicies.length; i++) {
                    const el = childFoldersAndPolicies[i];
                    if (el.classList.contains('PR-Child-Folders')) {
                        const nextEl = childFoldersAndPolicies[i+1];
                        if (nextEl && nextEl.classList.contains('Policies-Folder')) {
                            childFolderPairs.push({ folder: el, content: nextEl });
                            i++; // Skip the Policies-Folder since we grouped it
                        } else {
                            childFolderPairs.push({ folder: el, content: null });
                        }
                    }
                }
                
                childFolderPairs.sort((a, b) => {
                    if (sortValue === 'default' || sortValue.startsWith('date')) {
                         return parseInt(a.folder.getAttribute('data-original-index') || 0) - parseInt(b.folder.getAttribute('data-original-index') || 0);
                    } else {
                         const nameA = a.folder.textContent.trim().toLowerCase();
                         const nameB = b.folder.textContent.trim().toLowerCase();
                         return sortValue === 'alpha_asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
                    }
                });
                
                childFolderPairs.forEach(pair => {
                    childContainer.appendChild(pair.folder);
                    if (pair.content) childContainer.appendChild(pair.content);
                });
                
                const directPolicies = childFoldersAndPolicies.filter(el => el.classList.contains('PR-Policies'));
                if (directPolicies.length > 0) {
                    const sortedDirectPolicies = sortNodes(directPolicies, 'policy');
                    sortedDirectPolicies.forEach(pol => childContainer.appendChild(pol));
                }
            });

            // 3. Sort Policies inside sub-folders
            document.querySelectorAll('.Policies-Folder').forEach(policiesContainer => {
                const policies = policiesContainer.querySelectorAll('.PR-Policies');
                const sortedPolicies = sortNodes(policies, 'policy');
                sortedPolicies.forEach(pol => policiesContainer.appendChild(pol));
            });
        });
        
        wrapper.appendChild(sortSelect);
    }
});

    // --- ARCHIVES BUTTON & MODAL SETUP ---
    document.addEventListener('DOMContentLoaded', () => {
        if (window.currentUserRoleID !== 2 && window.currentUserRoleID !== 3) return;
        const prSearchContainer = document.querySelector('.PR-Search-Container');
        if (prSearchContainer && !document.getElementById('prArchiveBtn')) {
            let controlsWrapper = prSearchContainer.parentElement;
            if (controlsWrapper.id !== 'prControlsWrapper') {
                controlsWrapper = document.createElement('div');
                controlsWrapper.id = 'prControlsWrapper';
                controlsWrapper.style.display = 'flex';
                controlsWrapper.style.alignItems = 'center';
                controlsWrapper.style.flexWrap = 'wrap';
                controlsWrapper.style.marginBottom = '10px';
                prSearchContainer.parentNode.insertBefore(controlsWrapper, prSearchContainer);
                controlsWrapper.appendChild(prSearchContainer);
                prSearchContainer.style.marginBottom = '0';
            }
            
            const archiveBtn = document.createElement('button');
            archiveBtn.innerHTML = '<i class="fas fa-archive"></i> View Archives';
            archiveBtn.id = 'prArchiveBtn';
            archiveBtn.style.backgroundColor = '#64748b';
            archiveBtn.style.color = 'white';
            archiveBtn.style.padding = '0 15px';
            archiveBtn.style.height = '34px';
            archiveBtn.style.borderRadius = '20px';
            archiveBtn.style.border = 'none';
            archiveBtn.style.cursor = 'pointer';
            archiveBtn.style.fontWeight = 'bold';
            archiveBtn.style.marginLeft = '10px';
            
            archiveBtn.addEventListener('click', openArchivesModal);
            controlsWrapper.appendChild(archiveBtn);
            
            const archiveModal = document.createElement('div');
            archiveModal.id = 'archivesModal';
            archiveModal.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1005; align-items:center; justify-content:center;';
            archiveModal.innerHTML = `
                <div style="background:white; padding:30px; border-radius:10px; width:750px; max-width:90%; max-height:80vh; display:flex; flex-direction:column; color:#333; position:relative; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <h2 style="margin-top:0; color:#293A82; font-family: 'Istok Web', sans-serif; font-size: 24px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                        <i class="fas fa-archive" style="color: #64748b; margin-right: 10px;"></i> Archived & Replaced Policies
                    </h2>
                    <div id="archivesListContainer" style="overflow-y:auto; flex-grow:1; margin-bottom:20px; padding-right:10px;">
                        <p>Loading archives...</p>
                    </div>
                    <div style="text-align:right;">
                        <button onclick="document.getElementById('archivesModal').style.display='none'" style="background:#64748b; color:white; border:none; padding:10px 25px; border-radius:5px; cursor:pointer; font-weight: bold; font-family: 'Istok Web', sans-serif; transition: background 0.2s;">Close</button>
                    </div>
                </div>
            `;
            document.body.appendChild(archiveModal);
        }
    });

    window.archiveAttributeEscape = function(value) {
        return String(value || '')
            .replace(/\\/g, '\\\\')
            .replace(/'/g, "\\'")
            .replace(/\r?\n/g, ' ');
    };

    window.sortArchivedItems = function(items, order = 'asc') {
        function parseVersion(version) {
            if (!version) return [0, 0, 0];
            const parts = version.toString().split('.').map(part => parseInt(part, 10) || 0);
            return [parts[0] || 0, parts[1] || 0, parts[2] || 0];
        }

        return items.slice().sort((a, b) => {
            const aVer = parseVersion(a.versionNo);
            const bVer = parseVersion(b.versionNo);
            for (let i = 0; i < 3; i++) {
                if (aVer[i] !== bVer[i]) {
                    return order === 'asc' ? aVer[i] - bVer[i] : bVer[i] - aVer[i];
                }
            }

            const aTime = new Date(a.dateUploaded).getTime() || 0;
            const bTime = new Date(b.dateUploaded).getTime() || 0;
            return order === 'asc' ? aTime - bTime : bTime - aTime;
        });
    };

    window.toggleArchiveFolder = function(button) {
        const folder = button.closest('.archive-folder');
        if (!folder) return;
        const content = folder.querySelector('.archive-folder-content');
        if (!content) return;

        const isOpen = content.style.display === 'block';
        content.style.display = isOpen ? 'none' : 'block';

        const icon = folder.querySelector('.archive-folder-toggle-icon');
        if (icon) {
            icon.className = isOpen ? 'fas fa-folder' : 'fas fa-folder-open';
        }
    };

    window.openArchiveHistory = function(rootPolicyID) {
        const modal = document.getElementById('archivesModal');
        if (modal) modal.style.display = 'none';
        if (typeof openDocumentHistoryModal === 'function') {
            openDocumentHistoryModal(rootPolicyID);
        }
    };

    window.renderArchiveGroups = function(container, archives, sortOrder = 'asc') {
        const groups = {};
        archives.forEach(item => {
            const key = item.rootPolicyID || item.policyID;
            if (!groups[key]) {
                groups[key] = {
                    rootPolicyID: key,
                    rootTitle: item.rootTitle || item.title || 'Untitled Policy',
                    items: []
                };
            }
            groups[key].items.push(item);
        });

        const groupKeys = Object.keys(groups).sort((a, b) => {
            const aTitle = groups[a].rootTitle.toLowerCase();
            const bTitle = groups[b].rootTitle.toLowerCase();
            return aTitle.localeCompare(bTitle);
        });

        let html = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; gap:12px; flex-wrap:wrap;">
                <div style="font-size:14px; color:#334155; font-weight:700;">Archive folders grouped by policy title</div>
                <div style="display:flex; align-items:center; gap:8px; font-size:14px; color:#334155;">
                    <label for="archiveSortSelect" style="font-weight:600;">Sort</label>
                    <select id="archiveSortSelect" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:6px; background:#fff; color:#0f172a;">
                        <option value="asc"${sortOrder === 'asc' ? ' selected' : ''}>Original → Latest</option>
                        <option value="desc"${sortOrder === 'desc' ? ' selected' : ''}>Latest → Original</option>
                    </select>
                </div>
            </div>
        `;

        groupKeys.forEach(key => {
            const group = groups[key];
            const sortedItems = window.sortArchivedItems(group.items, sortOrder);
            const rows = sortedItems.map(item => `
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:10px 12px; vertical-align:middle;">${item.versionNo ? 'v' + (String(item.versionNo).includes('.') ? item.versionNo : item.versionNo + '.0') : 'Original'}</td>
                    <td style="padding:10px 12px; vertical-align:middle;">${item.title}</td>
                    <td style="padding:10px 12px; vertical-align:middle;">${item.authorName || 'Unknown'}</td>
                    <td style="padding:10px 12px; vertical-align:middle;">${item.dateUploaded || 'N/A'}</td>
                    <td style="padding:10px 12px; vertical-align:middle; text-align:right; display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap;">
                        <button onclick="viewArchivedPolicy('${window.archiveAttributeEscape(item.contentPath)}', '${window.archiveAttributeEscape(item.title + ' (Archived)')}', '${window.archiveAttributeEscape(item.dateUploaded)}', ${item.policyID})" style="background:#293A82; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:700; white-space:nowrap;">
                            <i class="fas fa-file-pdf"></i> View
                        </button>
                        ${item.revisionFormPath ? `<button onclick="viewArchivedPolicy('${window.archiveAttributeEscape(item.revisionFormPath)}', '${window.archiveAttributeEscape('Change Log - ' + item.title)}', '${window.archiveAttributeEscape(item.dateUploaded)}', ${item.policyID})" style="background:#fbaf41; color:#111; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:700; white-space:nowrap;">
                            <i class="fas fa-file-alt"></i> Change Log
                        </button>` : ''}
                    </td>
                </tr>
            `).join('');

            html += `
                <div class="archive-folder" style="border:1px solid #d1d5db; border-radius:10px; margin-bottom:14px; overflow:hidden; background:#fff;">
                    <div class="archive-folder-header" style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px; cursor:pointer; background:#f8fafc;" onclick="window.toggleArchiveFolder(this)">
                        <div>
                            <div style="font-size:16px; font-weight:700; color:#0f172a;">${group.rootTitle}</div>
                            <div style="font-size:13px; color:#475569; margin-top:4px;">${sortedItems.length} archived revision${sortedItems.length !== 1 ? 's' : ''}</div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <button onclick="event.stopPropagation(); window.openArchiveHistory(${group.rootPolicyID});" style="background:#64748b; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer; font-weight:700; white-space:nowrap;">
                                <i class="fas fa-history"></i> Full History
                            </button>
                            <i class="archive-folder-toggle-icon fas fa-folder" style="font-size:18px; color:#64748b;"></i>
                        </div>
                    </div>
                    <div class="archive-folder-content" style="display:none; padding:0 18px 18px;">
                        <div style="overflow-x:auto; margin-top:16px;">
                            <table style="width:100%; border-collapse:collapse; font-family:'Istok Web', sans-serif; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left; padding:10px 12px; border-bottom:2px solid #e2e8f0;">Version</th>
                                        <th style="text-align:left; padding:10px 12px; border-bottom:2px solid #e2e8f0;">Title</th>
                                        <th style="text-align:left; padding:10px 12px; border-bottom:2px solid #e2e8f0;">Author</th>
                                        <th style="text-align:left; padding:10px 12px; border-bottom:2px solid #e2e8f0;">Published</th>
                                        <th style="text-align:right; padding:10px 12px; border-bottom:2px solid #e2e8f0;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;

        const select = document.getElementById('archiveSortSelect');
        if (select) {
            select.addEventListener('change', function() {
                window.renderArchiveGroups(container, archives, this.value);
            });
        }
    };

    window.openArchivesModal = function() {
        const modal = document.getElementById('archivesModal');
        const container = document.getElementById('archivesListContainer');
        if (!modal || !container) return;

        modal.style.display = 'flex';
        container.innerHTML = '<p style="text-align:center; padding: 20px;">Fetching archived policies...</p>';

        fetch('/qms_optiqual/generalComponents/policyManagerPHP/getArchivedPolicies.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (!Array.isArray(data.archives) || data.archives.length === 0) {
                        container.innerHTML = '<p style="text-align:center; padding: 20px; color: #666; font-weight: bold;">No archived policies found.</p>';
                        return;
                    }
                    window.renderArchiveGroups(container, data.archives, 'asc');
                } else {
                    container.innerHTML = `<p style="text-align:center; color:red; padding: 20px;">Error: ${data.message}</p>`;
                }
            })
            .catch(err => {
                console.error('Error fetching archives:', err);
                container.innerHTML = '<p style="text-align:center; color:red; padding: 20px;">Network error while fetching archives.</p>';
            });
    };

    window.viewArchivedPolicy = function(filePath, title, uploadDate, policyId) {
        const modal = document.getElementById('archivesModal');
        if (modal) modal.style.display = 'none';
        window.openCustomPdfViewer(filePath, title, uploadDate, policyId);
    };

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
            
            const savedPanel = localStorage.getItem('activePanel');
            if (savedPanel === 'workspace' && typeof showWorkspace === 'function') showWorkspace();
            else if (savedPanel === 'repository' && typeof showPolicyRepository === 'function') showPolicyRepository();
            else if (savedPanel === 'submission' && typeof showPolicySubmission === 'function') showPolicySubmission();
            else if (savedPanel === 'role' && typeof showRoleManager === 'function') showRoleManager();
            else if (savedPanel === 'information' && typeof showInformation === 'function') showInformation();
            else {
                const policyRepositoryPanel = document.getElementById('policy-repo-content');
                if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
            }
            
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

const submitForm = document.querySelector('#submitOverlay form');
const formSubmitBtn = document.getElementById('submitBtn');
if (submitForm && formSubmitBtn) {
    submitForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const titleInput = document.getElementById('policyTitle');
        const title = titleInput ? titleInput.value.trim() : '';
        const isRevCheckbox = document.querySelector('input[name="isRevision"]');
        
        // File validation: Only PDF and under 2MB
        const fileInput = document.querySelector('input[name="policyFile"]');
        if (fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileSizeMB = file.size / (1024 * 1024);
            const fileName = file.name.toLowerCase();
            
            if (!fileName.endsWith('.pdf')) {
                alert("Only PDF files are accepted.");
                return;
            }
            if (fileSizeMB > 2) {
                alert("File size must be 2MB and below.");
                return;
            }
        }
        
        const logFileInput = document.querySelector('input[name="changeLogFile"]');
        if (logFileInput && logFileInput.files.length > 0) {
            const logFile = logFileInput.files[0];
            const logFileSizeMB = logFile.size / (1024 * 1024);
            const logFileName = logFile.name.toLowerCase();
            
            if (!logFileName.endsWith('.pdf')) {
                alert("Only PDF files are accepted for the revision log.");
                return;
            }
            if (logFileSizeMB > 2) {
                alert("Revision log file size must be 2MB and below.");
                return;
            }
        }
        
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

/* =====================================================================
// ✨ INLINE REMARKS SECTION ✨
// ===================================================================== */
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
        container.innerHTML = `
            <div style="margin-bottom: 15px; text-align: right;">
                <button onclick="expandAllFeedbackReplies(event)" style="background: #293A82; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-reply-all"></i> Reply to all Feedbacks
                </button>
            </div>
        ` + data.feedbacks.map((fb, index) => {
            const badge = fb.fbType == 2 ? '<span style="background-color: #ef4444; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: 10px;">REJECTED</span>' : '<span style="background-color: #10b981; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: 10px;">GENERAL</span>';
            return `
        <div class="fb-item" style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 8px; cursor: pointer;" onclick="toggleFbDetails(${index})">
            <div style="display: flex; justify-content: space-between;">
                <strong>Policy: ${fb.policyTitle || 'Untitled'} ${badge}</strong>
                <span>${fb.dateSubmitted}</span>
            </div>
            <div>Submitted by: ${fb.submittedBy}</div>
            
            <div id="fb-details-${index}" style="display:none; margin-top:10px; padding-top:10px; border-top:1px solid #eee;" onclick="event.stopPropagation()">
                <p><strong>Feedback Content:</strong></p>
                <div style="background: #f9f9f9; padding: 10px; border-radius: 5px; max-height: 150px; overflow-y: auto; white-space: pre-wrap; border: 1px solid #eee;">
                    ${fb.content}
                </div>
                ${fb.replyContent ? `
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ccc;">
                        <p style="margin:0 0 5px 0; font-weight:bold; color: #293A82; font-size: 14px;">
                            <i class="fas fa-level-up-alt fa-rotate-90" style="margin-right: 8px;"></i>Response from ${fb.replierName || 'Author'} on ${new Date(fb.dateReplied).toLocaleDateString()}:
                        </p>
                        <div style="background: #eef2ff; padding: 12px; border-radius: 5px; white-space: pre-wrap; border-left: 3px solid #293A82;">${fb.replyContent}</div>
                    </div>
                ` : `
                    <div style="text-align: right; margin-top: 15px;" id="reply-container-${fb.feedbackID}" class="reply-container-block" data-fbid="${fb.feedbackID}">
                        <button onclick="showReplyForm(${fb.feedbackID})" style="background: #fbaf41; color: black; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                            <i class="fas fa-reply"></i> Address Feedback
                        </button>
                    </div>
                `}
            </div>
        </div>
            `;
        }).join('');
    })
    .catch(err => {
        console.error(err);
        container.innerHTML = '<p>Error loading feedbacks.</p>';
    });
};

window.expandAllFeedbackReplies = function(e) {
    if (e) e.stopPropagation();
    document.querySelectorAll('[id^="fb-details-"]').forEach(el => el.style.display = 'block');
    document.querySelectorAll('.reply-container-block').forEach(container => {
        const fbId = container.getAttribute('data-fbid');
        if (fbId) showReplyForm(fbId);
    });
};

// Helper to toggle details
window.toggleFbDetails = function(index) {
    const el = document.getElementById(`fb-details-${index}`);
    if (el) el.style.display = (el.style.display === 'none' ? 'block' : 'none');
};

window.showReplyForm = function(feedbackID) {
    if (event) event.stopPropagation(); 
    const container = document.getElementById(`reply-container-${feedbackID}`);
    if (!container) return;

    container.innerHTML = `
        <textarea id="reply-text-${feedbackID}" placeholder="Type your reply to the feedback submitter..." style="width: 100%; min-height: 80px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; font-family: inherit; resize: vertical;"></textarea>
        <div style="text-align: right;">
            <button onclick="cancelReply(${feedbackID})" style="background: #6c757d; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px; font-weight: bold;">Cancel</button>
            <button id="submit-reply-btn-${feedbackID}" onclick="submitFeedbackReply(${feedbackID})" style="background: #293A82; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">Submit Reply</button>
        </div>
    `;
    const textarea = document.getElementById(`reply-text-${feedbackID}`);
    if (textarea) textarea.focus();
};

window.cancelReply = function(feedbackID) {
    if (event) event.stopPropagation();
    const container = document.getElementById(`reply-container-${feedbackID}`);
    if (!container) return;
    container.innerHTML = `
        <button onclick="showReplyForm(${feedbackID})" style="background: #fbaf41; color: black; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">
           <i class="fas fa-reply"></i> Address Feedback
        </button>
    `;
};

window.submitFeedbackReply = function(feedbackID) {
    if (event) event.stopPropagation();
    const replyContent = document.getElementById(`reply-text-${feedbackID}`).value.trim();
    if (!replyContent) {
        alert("Please enter a reply.");
        return;
    }

    const btn = document.getElementById(`submit-reply-btn-${feedbackID}`);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    fetch('../../generalComponents/policyManagerPHP/submitFeedbackReply.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ feedbackID: feedbackID, replyContent: replyContent })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Reply submitted successfully!');
            if (typeof loadWorkspaceFeedbacks === 'function') {
                loadWorkspaceFeedbacks();
            }
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = 'Submit Reply';
        }
    })
    .catch(err => {
        console.error(err);
        alert("A network error occurred.");
        btn.disabled = false;
        btn.innerHTML = 'Submit Reply';
    });
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
                            <td style="padding: 12px; font-weight: bold;">${item.versionNo ? (String(item.versionNo).includes('.') ? item.versionNo : item.versionNo + '.0') : 'Original'}</td>
                            <td style="padding: 12px;">${item.title}</td>
                            <td style="padding: 12px;">${item.authorName}</td>
                            <td style="padding: 12px;">${item.approverName}</td>
                            <td style="padding: 12px;">${item.datePublished}</td>
                            <td style="padding: 12px; display: flex; flex-direction: column; gap: 5px;">
                                <button class="action-btn-inline" 
                                    onclick="openSecondaryPdfViewer('${item.contentPath}', '${item.title} (v${item.versionNo ? (String(item.versionNo).includes('.') ? item.versionNo : item.versionNo + '.0') : 'Original'})')"
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

/* =====================================================================
   ✨ AUTOMATIC BACKGROUND REFRESH ENGINE ✨
   (Lightweight Polling for Tasks & Notifications)
   ===================================================================== */
let lastDashboardKpiState = {
    taskState: null
};

function startDashboardPolling() {
    setInterval(() => {
        fetch('../../generalComponents/policyManagerPHP/getDashboardSync.php')
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                // 1. Check Task State
                if (data.taskState !== lastDashboardKpiState.taskState) {
                    if (lastDashboardKpiState.taskState !== null) {
                        // Refresh Tasks via existing data JS function
                        if (typeof fetchTasksAndPopulate === 'function') {
                            fetchTasksAndPopulate();
                        }
                    }
                    lastDashboardKpiState.taskState = data.taskState;
                }

                // 2. Update Notifications silently
                const notifs = data.notifications;
                if (notifs) {
                    const unreadList = document.getElementById('notif-unread-list');
                    const readList = document.getElementById('notif-read-list');
                    
                    if (unreadList && notifs.unreadHtml && unreadList.innerHTML !== notifs.unreadHtml) {
                        unreadList.innerHTML = notifs.unreadHtml;
                        if (typeof updateNotifBadgeCount === 'function') updateNotifBadgeCount();
                    }
                    
                    if (readList && notifs.readHtml && readList.innerHTML !== notifs.readHtml) {
                        readList.innerHTML = notifs.readHtml;
                    }
                }
            })
            .catch(err => { /* Ignore silent errors */ });
    }, 7000); 
}

document.addEventListener("DOMContentLoaded", function() {
    startDashboardPolling();
});