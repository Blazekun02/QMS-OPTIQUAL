const sidebar = document.querySelector('.Sidebar');
const dataPrivacyIcon = document.getElementById('dataPrivacyIcon');
const mainContentDataPrivacy = document.getElementById('mainContentDataPrivacy');
const roleManagerIcon = document.getElementById('roleManagerIcon'); // Get the new icon
const mainContentRoleManager = document.getElementById('mainContentRoleManager'); // Get the new content div

const openDPAEditPopupBtn = document.getElementById('openDPAEditPopup');
const editDPAOverlay = document.getElementById('editDPAOverlay');
const dpaEditTextarea = document.getElementById('dpaEditTextarea');
const cancelDPAEditBtn = document.getElementById('cancelDPAEdit');
const submitDPAEditBtn = document.getElementById('submitDPAEdit');
const currentDPAContent = document.getElementById('currentDPAContent');

const confirmUpdateOverlay = document.getElementById('confirmUpdateOverlay');
const noConfirmUpdateBtn = document.getElementById('noConfirmUpdate');
const yesConfirmUpdateBtn = document.getElementById('yesConfirmUpdate');

let newDPAText = '';

function hideAllMainContents() {
    if (mainContentDataPrivacy) {
        mainContentDataPrivacy.style.display = 'none';
    }
    // Hide the new role manager content as well
    if (mainContentRoleManager) {
        mainContentRoleManager.style.display = 'none';
    }
}

function showMainContent(contentElement) {
    hideAllMainContents();
    if (contentElement) {
        contentElement.style.display = 'flex';
        // document.body.style.overflow = 'hidden'; // Only hide body overflow for popups, not main content
    } else {
        // document.body.style.overflow = ''; // Restore body overflow only if no main content or popup is visible
    }
}

sidebar.addEventListener('mouseenter', () => {
    sidebar.classList.add('extended');
    document.body.classList.add('sidebar-extended');
});

sidebar.addEventListener('mouseleave', () => {
    sidebar.classList.remove('extended');
    document.body.classList.remove('sidebar-extended');
});

function toggleSidebar() {
    sidebar.classList.toggle('extended');
    document.body.classList.toggle('sidebar-extended');
}

const notifButton = document.getElementById('notifButton');
const notificationOverlay = document.getElementById('popupOverlay'); // This ID seems incorrect from your CSS, it's for dpa edit. Let's assume it's for general notifications.

if (notifButton && notificationOverlay) {
    notifButton.addEventListener('click', () => {
        // Check if other overlays are open and close them
        if (signOutOverlay.style.display === 'block') {
            signOutOverlay.style.display = 'none';
        }
        notificationOverlay.style.display = notificationOverlay.style.display === 'block' ? 'none' : 'block';
    });
}

const signOutOverlay = document.getElementById('signOutOverlay');
const userButton = document.getElementById('userButton');
if (userButton && signOutOverlay) {
    userButton.addEventListener('click', () => {
        // Check if other overlays are open and close them
        if (notificationOverlay && notificationOverlay.style.display === 'block') {
            notificationOverlay.style.display = 'none';
        }
        signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
    });
}

if (signOutOverlay) {
    signOutOverlay.addEventListener("click", function () {
        window.location.href = "landingPage.html";
    });
}

document.addEventListener('DOMContentLoaded', (event) => {
    if (dataPrivacyIcon) {
        dataPrivacyIcon.addEventListener('click', () => {
            showMainContent(mainContentDataPrivacy);
            // Ensure body scroll is normal when showing main content
            document.body.style.overflow = '';
            if (currentDPAContent && currentDPAContent.innerHTML.trim() === '<p></p>' || currentDPAContent.innerHTML.trim() === '') {
                currentDPAContent.innerHTML = '<p>Initial DPA Text: This document outlines Asia Pacific College\'s policies regarding the collection, processing, and management of employee personal information, in accordance with Republic Act 10173, the Data Privacy Act (DPA) of 2012. It covers data privacy principles, data subject rights, security measures, and procedures for data breaches and inquiries. By continuing to use our services, you acknowledge and agree to the terms herein. For more details, please refer to the complete DPA policy available through official channels.</p>';
            }
        });
    }

    // Event listener for the new Role Manager icon
    if (roleManagerIcon) {
        roleManagerIcon.addEventListener('click', () => {
            showMainContent(mainContentRoleManager);
            // Ensure body scroll is normal when showing main content
            document.body.style.overflow = '';
        });
    }

    // This is the key part for showing the edit popup when the 'Update' button is clicked
    if (openDPAEditPopupBtn && editDPAOverlay && dpaEditTextarea && currentDPAContent) {
        openDPAEditPopupBtn.addEventListener('click', () => {
            dpaEditTextarea.value = currentDPAContent.textContent.trim();
            editDPAOverlay.style.display = 'flex'; // Show the edit popup overlay
            document.body.style.overflow = 'hidden'; // Hide body scrollbar when popup is active
        });
    }

    if (cancelDPAEditBtn && editDPAOverlay) {
        cancelDPAEditBtn.addEventListener('click', () => {
            editDPAOverlay.style.display = 'none';
            dpaEditTextarea.value = '';
            // Only restore body scrollbar if no other main content is visible
            if (mainContentDataPrivacy.style.display !== 'flex' && mainContentRoleManager.style.display !== 'flex') {
                 document.body.style.overflow = '';
            } else if (mainContentDataPrivacy.style.display === 'flex' || mainContentRoleManager.style.display === 'flex') {
                document.body.style.overflow = ''; // Main content will handle its own overflow
            }
        });
    }

    if (submitDPAEditBtn && editDPAOverlay && confirmUpdateOverlay) {
        submitDPAEditBtn.addEventListener('click', () => {
            newDPAText = dpaEditTextarea.value.trim();
            if (newDPAText) {
                editDPAOverlay.style.display = 'none';
                confirmUpdateOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Keep body overflow hidden for confirmation popup
            } else {
                alert('DPA content cannot be empty. Please enter some text.');
            }
        });
    }

    if (noConfirmUpdateBtn && confirmUpdateOverlay && editDPAOverlay) {
        noConfirmUpdateBtn.addEventListener('click', () => {
            confirmUpdateOverlay.style.display = 'none';
            editDPAOverlay.style.display = 'flex'; // Go back to edit popup
            newDPAText = '';
        });
    }

    if (yesConfirmUpdateBtn && confirmUpdateOverlay && currentDPAContent) {
        yesConfirmUpdateBtn.addEventListener('click', () => {
            currentDPAContent.innerHTML = `<p>${newDPAText}</p>`;
            confirmUpdateOverlay.style.display = 'none';
            dpaEditTextarea.value = '';
            newDPAText = '';
            alert('Data Privacy Agreement updated successfully!');
            document.body.style.overflow = ''; // Restore body scrollbar on final submit
        });
    }

    // Initial hiding of overlays and ensuring correct body overflow state
    if (editDPAOverlay) {
        editDPAOverlay.style.display = 'none';
    }
    if (confirmUpdateOverlay) {
        confirmUpdateOverlay.style.display = 'none';
    }
    if (signOutOverlay) {
        signOutOverlay.style.display = 'none';
    }
    // Ensure notificationOverlay is initially hidden if it exists
    if (notificationOverlay) {
        notificationOverlay.style.display = 'none';
    }

    // Set initial display for main contents
    if (mainContentDataPrivacy) {
        mainContentDataPrivacy.style.display = 'none'; // Initially hide data privacy
    }
    if (mainContentRoleManager) {
        mainContentRoleManager.style.display = 'none'; // Initially hide role manager
    }

    // If no specific content is shown on load, ensure body scroll is default
    if (!mainContentDataPrivacy.style.display || mainContentDataPrivacy.style.display === 'none' &&
        !mainContentRoleManager.style.display || mainContentRoleManager.style.display === 'none') {
        document.body.style.overflow = '';
    }
});