const sidebar = document.querySelector('.Sidebar');
const hamburgerIcon = document.getElementById('hamburger-icon');

    sidebar.addEventListener('mouseenter', () => {
        sidebar.classList.add('extended');
        hamburgerIcon.style.left = '2.5in';
    });

    sidebar.addEventListener('mouseleave', () => {
        sidebar.classList.remove('extended');
        hamburgerIcon.style.left = '0.8in';
    });
    hamburgerIcon.addEventListener('click', () => {
        const isExtended = greyLine.classList.toggle('extended');
        hamburgerIcon.style.left = isExtended ? '2.5in' : '0.8in';
    });

    const notifButton = document.getElementById('notifButton');
    const notificationOverlay = document.getElementById('popupOverlay');

    notifButton.addEventListener('click', () => {
        if (notificationOverlay.style.display === 'block') {
            notificationOverlay.style.display = 'none';
        } else {
            notificationOverlay.style.display = 'block';
        }
    });
   

    
    const signOutOverlay = document.getElementById('signOutOverlay');
    const userButton = document.getElementById('userButton');
    userButton.addEventListener('click', () => {
        signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById("signOutOverlay").addEventListener("click", function () {
        window.location.href = "landingPage.html";
    });

    const policyRepositoryPanel = document.getElementById('policy-repo-content');
    const policySubmissionPanel = document.getElementById('policy-submission-content');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const processTrackerPanel = document.querySelector('.Process-Tracker-Panel2');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

// Policy Repository
function showPolicyRepository() {
    console.log("Policy Repository Triggered");
    policyRepositoryPanel.style.display = 'block';
    policySubmissionPanel.style.display = 'none';
    departmentPanel.style.display = 'none'; 
    roleManagerPanel.style.display='none';
    policyManagerPanel.style.display='none';
    reportPanel.style.display='none';

}

// Policy Submission
function showPolicySubmission() {
    console.log("Policy Submission Triggered");

    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'flex';
    departmentPanel.style.display = 'none';
    roleManagerPanel.style.display='none';
    policyManagerPanel.style.display='none';
    reportPanel.style.display='none';

}

const cfOverlay = document.getElementById('confirm-dl');
const dlBtn = document.querySelector('.policy-submission-buttons button:first-child');

dlBtn.addEventListener('click', () => {
    cfOverlay.style.display = cfOverlay.style.display === 'block' ? 'none' : 'block';

});

document.getElementById("first-child").addEventListener("click", function () {
    cfOverlay.style.display = "none";
    alert("Download cancelled");

});

document.getElementById("last-child").addEventListener("click", function () {
    cfOverlay.style.display = "none";
    alert("Downloading template");
});

const submitOverlay = document.getElementById('submitOverlay');
const submitBtn = document.getElementById('submitBtn');
const cancelBtn = document.getElementById('cancelBtn');


submitButton.addEventListener('click', () => {
    submitOverlay.style.display = submitOverlay.style.display === 'block' ? 'none' : 'block';
});

document.getElementById("submitBtn").addEventListener("click", function () {
    submitOverlay.style.display = "none";

});

document.getElementById("cancelBtn").addEventListener("click", function () {
    submitOverlay.style.display = "none";
});
  

// Policy Repository
const parentFolders = document.querySelectorAll('.PR-Parent-Folders');

parentFolders.forEach(folder => {
    folder.addEventListener('click', () => {
        const parentId = folder.getAttribute('data-id');
        console.log('Clicked Parent ID:', parentId);

        // Hide all child folders
        const allChildFolders = document.querySelectorAll('.child-folders');
        allChildFolders.forEach(child => {
            child.style.display = 'none';
        });

        // Hide all policy folders
        const allPoliciesFolder = document.querySelectorAll('.Policies-Folder');
        allPoliciesFolder.forEach(policyFolder => {
            policyFolder.style.display = 'none';
        });

        // Show matching child folders
        const childToShow = document.querySelector(`.child-folders[data-parent-id='${parentId}']`);
        if (childToShow) {
            childToShow.style.display = 'flex';
        }
    });
});

//Policy Repository
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');

// Search function
function searchPolicies() {
    const searchTerm = searchInput.value.toLowerCase();

    // Get everything
    const parentFolders = document.querySelectorAll('.PR-Parent-Folders');
    const childFolders = document.querySelectorAll('.PR-Child-Folders');
    const policies = document.querySelectorAll('.PR-Policies');

    // First, hide everything
    parentFolders.forEach(parent => parent.style.display = 'none');
    document.querySelectorAll('.child-folders').forEach(childFolder => childFolder.style.display = 'none');
    childFolders.forEach(child => child.style.display = 'none');
    document.querySelectorAll('.Policies-Folder').forEach(policyFolder => policyFolder.style.display = 'none');
    policies.forEach(policy => policy.style.display = 'none');

    let found = false; // to know if there are any matches

    // Search in Parent Folders
    parentFolders.forEach(parent => {
        const parentName = parent.innerText.toLowerCase();
        if (parentName.includes(searchTerm)) {
            parent.style.display = 'flex';
            found = true;
        }
    });

    // Search in Child Folders
    childFolders.forEach(child => {
        const childName = child.innerText.toLowerCase();
        if (childName.includes(searchTerm)) {
            child.style.display = 'flex';
            const parentId = child.closest('.child-folders').getAttribute('data-parent-id');
            document.querySelector(`.PR-Parent-Folders[data-id='${parentId}']`).style.display = 'flex';
            document.querySelector(`.child-folders[data-parent-id='${parentId}']`).style.display = 'flex';
            found = true;
        }
    });

    // Search in Policies
    policies.forEach(policy => {
        const policyName = policy.innerText.toLowerCase();
        if (policyName.includes(searchTerm)) {
            policy.style.display = 'flex';

            const policiesFolder = policy.closest('.Policies-Folder');
            policiesFolder.style.display = 'flex';

            const childFolder = policiesFolder.previousElementSibling; // the previous .PS-Child-Folders
            if (childFolder) childFolder.style.display = 'flex';

            const parentId = childFolder.closest('.child-folders').getAttribute('data-parent-id');
            document.querySelector(`.PR-Parent-Folders[data-id='${parentId}']`).style.display = 'flex';
            document.querySelector(`.child-folders[data-parent-id='${parentId}']`).style.display = 'flex';

            found = true;
        }
    });

    if (!found) {
        console.log('No results found.');
    }
}

// Attach search on button click
searchButton.addEventListener('click', searchPolicies);

// Optional: Search also when typing (Enter key)
searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        searchPolicies();
    }
});

// Child Folders click
const childFolders = document.querySelectorAll('.PR-Child-Folders');

childFolders.forEach(childFolder => {
    childFolder.addEventListener('click', () => {
        const childId = childFolder.getAttribute('data-id');
        console.log('Clicked Child ID:', childId);

        // Hide all policy folders first
        const allPoliciesFolder = document.querySelectorAll('.Policies-Folder');
        allPoliciesFolder.forEach(policyFolder => {
            policyFolder.style.display = 'none';
        });

        // Show the policy folder for the clicked child
        const policiesFolderToShow = document.querySelector(`.Policies-Folder[data-pol-id='${childId}']`);
        if (policiesFolderToShow) {
            policiesFolderToShow.style.display = 'flex';
        }
    });
});

// Role Manager
const addUserButton = document.querySelector('.user-plus-icon'); // Target the plus icon
const addToTeamPopup = document.getElementById('addToTeamPopup');
const confirmAddPopup = document.getElementById('confirmAddPopup');
const overlay = document.querySelector('.RMoverlay'); // Target the overlay class
const cancelAddButton = document.getElementById('cancelAdd');
const addSelectedButton = document.getElementById('addSelected');
const confirmCancelButton = document.getElementById('confirmCancel');
const confirmAddButton = document.getElementById('confirmAdd');
const searchNameInput = document.getElementById('searchName');
const searchBySelect = document.getElementById('searchBy');
const suggestionsContainer = document.getElementById('suggestions');
let timeoutId;

if (addUserButton && addToTeamPopup && overlay) {
    addUserButton.addEventListener('click', () => {
        overlay.style.display = 'flex';
        addToTeamPopup.style.display = 'block';
    });
}

if (cancelAddButton && overlay && addToTeamPopup) {
    cancelAddButton.addEventListener('click', () => {
        overlay.style.display = 'none';
        addToTeamPopup.style.display = 'none';
        suggestionsContainer.innerHTML = '';
        searchNameInput.value = '';
    });
}

if (overlay && addToTeamPopup) {
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) {
            overlay.style.display = 'none';
            addToTeamPopup.style.display = 'none';
            suggestionsContainer.innerHTML = '';
            searchNameInput.value = '';
            confirmAddPopup.style.display = 'none'; // Also hide the confirm popup if open
        }
    });
}

if (addSelectedButton && addToTeamPopup && confirmAddPopup) {
    addSelectedButton.addEventListener('click', () => {
        addToTeamPopup.style.display = 'none';
        confirmAddPopup.style.display = 'block';
    });
}

if (confirmCancelButton && overlay && confirmAddPopup) {
    confirmCancelButton.addEventListener('click', () => {
        confirmAddPopup.style.display = 'none';
        addToTeamPopup.style.display = 'block'; // Optionally go back to the add team popup
    });
}

if (confirmAddButton && overlay && confirmAddPopup) {
    confirmAddButton.addEventListener('click', () => {
        // Here you would typically handle the logic to add the selected members
        console.log('Members confirmed and would be added!');
        overlay.style.display = 'none';
        confirmAddPopup.style.display = 'none';
        suggestionsContainer.innerHTML = '';
        searchNameInput.value = '';
    });
}


// department manager
document.addEventListener('DOMContentLoaded', () => {

    function generateStructureId() {
    return 'structure-' + Date.now() + '-' + Math.floor(Math.random() * 10000);
}
    const addDepartmentButton = document.getElementById('addDepartmentButton');
    const assignNameContainer = document.getElementById('assignNameContainer');
    const overlay = document.getElementById('oVerlay');
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
    const renameRoleContainer = document.getElementById('renameRoleContainer');
    const cancelRenameRoleButton = document.getElementById('cancelRenameRole');
    const confirmRenameRoleButton = document.getElementById('confirmRenameRoleButton');
    const renameRoleInput = document.getElementById('renameRoleInput');
    let departmentToDelete = null;
    let currentTargetDepartment = null;
    let currentlyEditingRoleTextSpan = null;
    let roleToDelete = null;
    let currentlyEditingRole = null;
    let activeDepartmentForStructure = null;
    let currentTargetStructure = null;
    let currentlyEditingStructureSpan = null;
    let itemToDelete = null;

    const accountList = document.querySelector('.scrollable-account-list');
    const nameDisplaySpan = document.getElementById('nameDisplay'); // Get the correct span

    if (accountList && nameDisplaySpan) {
        accountList.addEventListener('click', (event) => {
            const target = event.target;
            let fullInfo = '';
    
            if (target.type === 'radio' && target.nextElementSibling) {
                fullInfo = target.nextElementSibling.textContent;
            } else if (target.tagName === 'LABEL' && target.previousElementSibling && target.previousElementSibling.type === 'radio') {
                fullInfo = target.textContent;
            }
    
            if (fullInfo) {
                const nameParts = fullInfo.split(' (');
                nameDisplaySpan.textContent = nameParts[0].trim();
            } else {
                nameDisplaySpan.textContent = ''; // Clear if nothing selected
            }
        });
    }


    addDepartmentButton.addEventListener('click', () => {
        overlay.style.display = 'flex';
        assignNameContainer.style.display = 'block';
        
    });

    cancelAssignNameButton.addEventListener('click', () => {
        assignNameContainer.style.display = 'none';
        overlay.style.display = 'none';
        departmentNameInput.value = '';
    });

    confirmAssignNameButton.addEventListener('click', () => {
        const departmentName = departmentNameInput.value.trim();

        if (departmentName) {
            displayNewDepartment(departmentName);
            assignNameContainer.style.display = 'none';
            overlay.style.display = 'none';
            departmentNameInput.value = '';
        } else {
            alert('Please enter a department name.');
        }
    });

    function displayNewDepartment(name) {
        const departmentDiv = document.createElement('div');
        departmentDiv.classList.add('department-item');
        departmentDiv.dataset.departmentName = name;

        const nameSpan = document.createElement('span');
        nameSpan.textContent = name;
        nameSpan.id = `department-name-${Date.now()}`;
        departmentDiv.appendChild(nameSpan);

        const iconsDiv = document.createElement('div');
        iconsDiv.classList.add('department-icons');

        const addUserIcon = document.createElement('i');
        addUserIcon.classList.add('fas', 'fa-user-plus');
        addUserIcon.addEventListener('click', () => {
            assignRoleContainer.style.display = 'block';
            overlay.style.display = 'block';
            currentTargetDepartment = departmentDiv;
        });
        iconsDiv.appendChild(addUserIcon);

        const structureIcon = document.createElement('i');
        structureIcon.classList.add('fas', 'fa-sitemap');
        structureIcon.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'block';
            overlay.style.display = 'block';
            activeDepartmentForStructure = departmentDiv;
        });
        iconsDiv.appendChild(structureIcon);

        const editIcon = document.createElement('i');
        editIcon.classList.add('fas', 'fa-pencil-alt');
        editIcon.addEventListener('click', () => {
            renameDepartmentContainer.style.display = 'block';
            overlay.style.display = 'block';
            renameDepartmentInput.value = nameSpan.textContent;
            renameDepartmentContainer.dataset.targetDepartmentSpan = nameSpan.id;
        });
        iconsDiv.appendChild(editIcon);

        const deleteIcon = document.createElement('i');
        deleteIcon.classList.add('fas', 'fa-trash-alt');
        deleteIcon.addEventListener('click', () => {
            console.log('Delete button clicked for:', departmentDiv);
            deleteConfirmationContainer.style.display = 'block';
            overlay.style.display = 'block';
            departmentToDelete = departmentDiv;
        });
        iconsDiv.appendChild(deleteIcon);

        departmentDiv.appendChild(iconsDiv);
        departmentListContainer.appendChild(departmentDiv);
    }

    const cancelAssignRoleButton = document.getElementById('cancelAssignRole');
    const confirmAssignRoleButton = document.getElementById('confirmAssignRole');

    if (cancelAssignRoleButton) {
        cancelAssignRoleButton.addEventListener('click', () => {
            assignRoleContainer.style.display = 'none';
            overlay.style.display = 'none';
            positionInput.value = '';
            currentlyEditingRole = null;
            currentTargetDepartment = null;
            currentTargetStructure = null;
            document.querySelectorAll('.scrollable-account-list input[type="radio"]:checked').forEach(radio => radio.checked = false);
            accountSearchInput.value = '';
            accountListItems.forEach(item => item.style.display = '');
        });
    }

   if (confirmAssignRoleButton) {
    confirmAssignRoleButton.addEventListener('click', () => {
        const position = positionInput.value.trim();
        const selectedAccount = document.querySelector('.scrollable-account-list input[type="radio"]:checked');

        if (!position) {
            alert('Please fill in the Position field.');
            return;
        }

        if (!selectedAccount) {
            alert('Please select an account.');
            return;
        }

        const selectedAccountLabel = selectedAccount.nextElementSibling.textContent;
        const [fullName, email] = selectedAccountLabel.split(' (');
        const emailOnly = email.replace(')', '').trim();
        const newRoleText = `${position} - ${fullName.trim()} (${emailOnly})`;

        if (currentlyEditingRoleTextSpan) {
            currentlyEditingRoleTextSpan.textContent = newRoleText;
            currentlyEditingRoleTextSpan = null;
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
    
                    positionInput.value = currentPosition; // Populate the position input
    
                    // Select the currently assigned account in the radio list
                    document.querySelectorAll('.scrollable-account-list .account-item').forEach(item => {
                        const label = item.querySelector('label').textContent;
                        if (label.startsWith(currentFullName.trim())) {
                            item.querySelector('input[type="radio"]').checked = true;
                            const nameDisplaySpan = document.getElementById('nameDisplay');
                            if (nameDisplaySpan) {
                                const nameParts = label.split(' (');
                                nameDisplaySpan.textContent = nameParts[0].trim();
                            }
                        } else {
                            item.querySelector('input[type="radio"]').checked = false;
                        }
                    });
    
                    currentlyEditingRoleTextSpan = roleTextSpan; // Set the target span for editing
                    assignRoleContainer.style.display = 'block'; // Show the Assign Role modal
                    overlay.style.display = 'block';
                });
    
                deleteRoleIcon.addEventListener('click', () => {
                    console.log('Deleting role initiated for:', assignedRoleDiv);
                    deleteConfirmationContainer.style.display = 'block';
                    overlay.style.display = 'block';
                    roleToDelete = assignedRoleDiv;
                });
    
                 if (currentTargetDepartment) {
                const departmentId = currentTargetDepartment.dataset.departmentId;
                assignedRoleDiv.dataset.departmentId = departmentId;
                currentTargetDepartment.parentNode.insertBefore(assignedRoleDiv, currentTargetDepartment.nextSibling);
                currentTargetDepartment = null;
            } else if (currentTargetStructure) {
                const structureId = currentTargetStructure.dataset.structureId;
                assignedRoleDiv.dataset.parentStructureId = structureId;
                assignedRoleDiv.classList.add('child-role');
                currentTargetStructure.parentNode.insertBefore(assignedRoleDiv, currentTargetStructure.nextSibling);
                currentTargetStructure = null;
            }
        }
    
           assignRoleContainer.style.display = 'none';
        overlay.style.display = 'none';
        positionInput.value = '';
        document.querySelectorAll('.scrollable-account-list input[type="radio"]').forEach(radio => radio.checked = false);
        const nameDisplaySpan = document.getElementById('nameDisplay');
        if (nameDisplaySpan) nameDisplaySpan.textContent = '';
        const accountSearchInput = document.getElementById('accountSearch');
        const accountListItems = document.querySelectorAll('#assignRoleContainer .scrollable-account-list .account-item');
        if (accountSearchInput) accountSearchInput.value = '';
        if (accountListItems) accountListItems.forEach(item => item.style.display = '');
    });
}

    const accountSearchInput = document.getElementById('accountSearch');
    const accountListItems = document.querySelectorAll('#assignRoleContainer .scrollable-account-list .account-item');

    if (accountSearchInput) {
        accountSearchInput.placeholder = "\uD83D\uDD0E Search name here";

        accountSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            accountListItems.forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                if (label.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });

            const scrollableList = document.querySelector('#assignRoleContainer .scrollable-account-list');
            if (scrollableList) {
                scrollableList.scrollTop = 0;
            }
        });
    }

    if (cancelStructureButton) {
        cancelStructureButton.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'none';
            overlay.style.display = 'none';
            structureNameInput.value = '';
            activeDepartmentForStructure = null;
            currentlyEditingStructureSpan = null;
        });
    }

    if (confirmStructureButton) {
    confirmStructureButton.addEventListener('click', () => {
        const structureName = structureNameInput.value.trim();

        if (structureName && activeDepartmentForStructure) {
            const departmentId = activeDepartmentForStructure.dataset.departmentId;
            const structureDiv = document.createElement('div');
            structureDiv.classList.add('department-structure-item');
            const structureId = generateStructureId();
            structureDiv.dataset.structureId = structureId;
            structureDiv.dataset.departmentId = departmentId;

            // If adding as a subfolder, set parent id
            if (activeDepartmentForStructure.classList.contains('department-structure-item')) {
                structureDiv.dataset.parentId = activeDepartmentForStructure.dataset.structureId;
            }

            structureDiv.innerHTML = `
                <span>${structureName}</span>
                <div class="structure-icons">
                    <i class="fas fa-user-plus add-structure-user" title="Add User"></i>
                    <i class="fas fa-pencil-alt edit-structure-icon" title="Edit Structure"></i>
                    <i class="fas fa-trash-alt delete-structure-icon" title="Delete Structure"></i>
                </div>
            `;
    
                const addUserIconStructure = structureDiv.querySelector('.add-structure-user');
                addUserIconStructure.addEventListener('click', () => {
                    assignRoleContainer.style.display = 'block';
                    overlay.style.display = 'block';
                    currentTargetStructure = structureDiv;
                });
    
    
                const editStructureIcon = structureDiv.querySelector('.edit-structure-icon');
                const deleteStructureIcon = structureDiv.querySelector('.delete-structure-icon');
                const structureNameSpan = structureDiv.querySelector('span');
    
                editStructureIcon.addEventListener('click', () => {
                    const currentStructureName = structureNameSpan.textContent;
                    structureNameInput.value = currentStructureName;
                    departmentStructureContainer.style.display = 'block';
                    overlay.style.display = 'block';
                    currentlyEditingStructureSpan = structureNameSpan;
                    activeDepartmentForStructure = structureDiv.parentNode; // Set parent department for editing
                });
    
                deleteStructureIcon.addEventListener('click', () => {
                    console.log('Deleting structure item initiated for:', structureDiv); // ADD THIS
                    deleteConfirmationContainer.style.display = 'block';
                    overlay.style.display = 'block';
                    itemToDelete = structureDiv;
                });
    
              let insertionPoint = activeDepartmentForStructure.nextSibling;
            let lastRole = activeDepartmentForStructure;
            while (insertionPoint && (insertionPoint.classList.contains('assigned-role-item') || insertionPoint.classList.contains('department-structure-item'))) {
                if (insertionPoint.classList.contains('assigned-role-item')) {
                    lastRole = insertionPoint;
                }
                insertionPoint = insertionPoint.nextSibling;
            }
            activeDepartmentForStructure.parentNode.insertBefore(structureDiv, lastRole.nextSibling);

            departmentStructureContainer.style.display = 'none';
            overlay.style.display = 'none';
            structureNameInput.value = '';
            activeDepartmentForStructure = null;

        } else if (!structureName && currentlyEditingStructureSpan) {
            alert('Please enter a new structure name.');
        } else if (!structureName) {
            alert('Please enter a structure name.');
        } else if (!activeDepartmentForStructure) {
            alert('Error: No department selected for structure.');
            departmentStructureContainer.style.display = 'none';
            overlay.style.display = 'none';
            structureNameInput.value = '';
        }
    });
}

    if (cancelRenameButton) {
        cancelRenameButton.addEventListener('click', () => {
            renameDepartmentContainer.style.display = 'none';
            overlay.style.display = 'none';
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
                if (targetNameSpan) {
                    targetNameSpan.textContent = newDepartmentName;
                    renameDepartmentContainer.style.display = 'none';
                    overlay.style.display = 'none';
                    renameDepartmentInput.value = '';
                    renameDepartmentContainer.dataset.targetDepartmentSpan = '';
                } else {
                    alert('Error updating department name.');
                }
            } else {
                if (!newDepartmentName) {
                    alert('Please enter a new department name.');
                } else {
                    alert('Error: Target department information missing.');
                }
            }
        });
    }

    const cancelDeleteButton = document.getElementById('cancelDelete');
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
            departmentToDelete = null;
            roleToDelete = null;
            itemToDelete = null;
        });
    }

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', () => {
            if (departmentToDelete) {
                const departmentIdToDelete = departmentToDelete.dataset.departmentId;
                const rolesToDelete = document.querySelectorAll(`.assigned-role-item[data-department-id="${departmentIdToDelete}"]`);
                const structuresToDelete = document.querySelectorAll(`.department-structure-item[data-department-id="${departmentIdToDelete}"]`);
    
                rolesToDelete.forEach(role => role.remove());
                structuresToDelete.forEach(structure => recursivelyDeleteStructureItem(structure)); // Use recursive delete for structures
                departmentToDelete.remove();
                departmentToDelete = null;
            } else if (roleToDelete) {
                roleToDelete.remove();
                roleToDelete = null;
            } else if (itemToDelete) {
                recursivelyDeleteStructureItem(itemToDelete);
                itemToDelete = null;
            }
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
        });
    }
    
    function recursivelyDeleteStructureItem(itemToDelete) {
    const structureIdToDelete = itemToDelete.dataset.structureId;

    // Delete only roles directly under this structure
    const childRolesToDelete = document.querySelectorAll(`.assigned-role-item[data-parent-structure-id="${structureIdToDelete}"]`);
    childRolesToDelete.forEach(role => role.remove());

    // Recursively delete only structures directly under this structure
    const childStructuresToDelete = document.querySelectorAll(`.department-structure-item[data-parent-id="${structureIdToDelete}"]`);
    childStructuresToDelete.forEach(child => recursivelyDeleteStructureItem(child));

    // Remove the current structure
    itemToDelete.remove();
}

if (departmentToDelete) {
    const departmentIdToDelete = departmentToDelete.dataset.departmentId;
    // Delete only top-level structures (no parent)
    const structuresToDelete = document.querySelectorAll(`.department-structure-item[data-department-id="${departmentIdToDelete}"]:not([data-parent-id])`);
    structuresToDelete.forEach(structure => recursivelyDeleteStructureItem(structure));
    // Delete roles directly under department
    const rolesToDelete = document.querySelectorAll(`.assigned-role-item[data-department-id="${departmentIdToDelete}"]`);
    rolesToDelete.forEach(role => role.remove());
    departmentToDelete.remove();
    departmentToDelete = null;
}



    if (confirmRenameRoleButton) {
        confirmRenameRoleButton.addEventListener('click', () => {
            const newRoleName = renameRoleInput.value.trim();
            const newRolePosition = document.getElementById('renamePositionInput').value.trim(); // Get the new position
    
            if (newRoleName && currentlyEditingRoleTextSpan) {
                currentlyEditingRoleTextSpan.textContent = `${newRolePosition} - ${newRoleName}`; // Use the new position
                renameRoleContainer.style.display = 'none';
                overlay.style.display = 'none';
                renameRoleInput.value = '';
                document.getElementById('renamePositionInput').value = ''; // Clear the position input
                currentlyEditingRoleTextSpan = null;
            } else {
                alert('Please enter a new role name.');
            }
        });
    }

 

    if (accountSearchInput) {
        accountSearchInput.placeholder = "\uD83D\uDD0E Search name here";

        accountSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            accountListItems.forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                if (label.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });

            const scrollableList = document.querySelector('#assignRoleContainer .scrollable-account-list');
            if (scrollableList) {
                scrollableList.scrollTop = 0;
            }
        });
    }
});

function showDepartmentManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');
    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'block';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';
}


const policyRepositoryMenuItem = document.querySelector('.menu-icons:nth-child(1)');
if (policyRepositoryMenuItem) {
    policyRepositoryMenuItem.addEventListener('click', showPolicyRepository);
}

const policySubmissionMenuItem = document.querySelector('.menu-icons:nth-child(2)');
if (policySubmissionMenuItem) {
    policySubmissionMenuItem.addEventListener('click', showPolicySubmission);
}

const roleManagerMenuItem = document.querySelector('.menu-icons:nth-child(5)');
    if(roleManagerMenuItem){
        roleManagerMenuItem.addEventListener('click', showRoleManager);
}


const departmentManagerMenuItem = document.querySelector('.menu-icons:nth-child(6)');
if (departmentManagerMenuItem) {
    departmentManagerMenuItem.addEventListener('click', showDepartmentManager);
}

const policyManagerMenuItem = document.querySelector('.menu-icons:nth-child(7)');
if (policyManagerMenuItem) {
    policyManagerMenuItem.addEventListener('click', showPolicyManager);
}

const reportsMenuItem = document.querySelector('.menu-icons:nth-child(8)');
if (reportsMenuItem) {
    reportsMenuItem.addEventListener('click', showReports);
}


function showPolicyRepository() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showPolicySubmission() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');


    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'block';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showPolicyManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'block';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showRoleManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='block';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showReports() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'block';

}




// POLICY MANAGER
document.addEventListener('DOMContentLoaded', () => {

      function generateUniqueId() {
        return 'policy-' + Date.now() + '-' + Math.floor(Math.random() * 10000);
    }
    const addPolicyButton = document.getElementById('addPolicyButton');
    const newPolicyModal = document.getElementById('newPolicyModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const cancelNewPolicyButton = document.getElementById('cancelNewPolicy');
    const confirmNewPolicyButton = document.getElementById('confirmNewPolicy');
    const policyNameInput = document.getElementById('policyNameInput');
    const policyItemsContainer = document.getElementById('policyItemsContainer');
    const uploadPolicyModal = document.getElementById('uploadPolicyModal');
    const cancelUploadPolicyButton = document.getElementById('cancelUploadPolicy');
    const confirmUploadPolicyButton = document.getElementById('confirmUploadPolicy');
    const policyTitleInput = document.getElementById('policyTitle');
    const policyStructureModal = document.getElementById('policyStructureModal');
    const cancelStructureViewButton = document.getElementById('cancelStructureView');
    const confirmStructureViewButton = document.getElementById('confirmStructureView');
    const policyStructureNameInput = document.getElementById('policyStructureNameInput');
    const renamePolicyModal = document.getElementById('renamePolicyModal');
    const cancelRenamePolicyButton = document.getElementById('cancelRenamePolicy');
    const confirmRenamePolicyButton = document.getElementById('confirmRenamePolicy');
    const renamePolicyInput = document.getElementById('renamePolicyInput');
    const deletePolicyModal = document.getElementById('deletePolicyModal');
    const cancelDeletePolicyButton = document.getElementById('cancelDeletePolicy');
    const confirmDeletePolicyButton = document.getElementById('confirmDeletePolicy');
    let policyToDelete = null;
    let currentTargetPolicy = null;
    let activePolicyForStructure = null;
    let currentlyEditingStructureSpan = null;
    let itemToDelete = null;

    addPolicyButton.addEventListener('click', () => {
        modalOverlay.style.display = 'flex';
        newPolicyModal.style.display = 'block';
    });

    cancelNewPolicyButton.addEventListener('click', () => {
        newPolicyModal.style.display = 'none';
        modalOverlay.style.display = 'none';
        policyNameInput.value = '';
    });

    confirmNewPolicyButton.addEventListener('click', () => {
        const policyName = policyNameInput.value.trim();
        if (policyName) {
            displayNewPolicy(policyName);
            newPolicyModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            policyNameInput.value = '';
        } else {
            alert('Please enter a policy name.');
        }
    });

    function displayNewPolicy(name) {
        const policyDiv = document.createElement('div');
        policyDiv.classList.add('policyRecord');    
        const policyId = generateUniqueId();
        policyDiv.dataset.policyId = policyId;
        policyDiv.dataset.policyName = name;
        const nameSpan = document.createElement('span');
        nameSpan.textContent = name;
        nameSpan.id = `policy-name-${Date.now()}`;
        policyDiv.appendChild(nameSpan);

        const iconsDiv = document.createElement('div');
        iconsDiv.classList.add('recordActions');

        const uploadIcon = document.createElement('i');
        uploadIcon.classList.add('fas', 'fa-file-alt', 'upload-policy-icon');
        uploadIcon.title = 'Upload Policy';
        uploadIcon.addEventListener('click', () => {
            uploadPolicyModal.style.display = 'block';
            modalOverlay.style.display = 'block';
            currentTargetPolicy = policyDiv;
        });
        iconsDiv.appendChild(uploadIcon);

        const structureIcon = document.createElement('i');
        structureIcon.classList.add('fas', 'fa-folder', 'view-structure-icon');
        structureIcon.title = 'View Structure';
        structureIcon.addEventListener('click', () => {
            policyStructureModal.style.display = 'block';
            modalOverlay.style.display = 'block';
            activePolicyForStructure = policyDiv;
        });
        iconsDiv.appendChild(structureIcon);

        const editIcon = document.createElement('i');
        editIcon.classList.add('fas', 'fa-pencil-alt', 'rename-icon');
        editIcon.title = 'Rename Policy';
        editIcon.addEventListener('click', () => {
            renamePolicyModal.style.display = 'block';
            modalOverlay.style.display = 'block';
            renamePolicyInput.value = nameSpan.textContent;
            renamePolicyModal.dataset.targetPolicySpan = nameSpan.id;
        });
        iconsDiv.appendChild(editIcon);

        const deleteIcon = document.createElement('i');
        deleteIcon.classList.add('fas', 'fa-trash-alt', 'delete-icon');
        deleteIcon.title = 'Delete Policy';
        deleteIcon.addEventListener('click', () => {
            console.log('Delete button clicked for:', policyDiv);
            deletePolicyModal.style.display = 'block';
            modalOverlay.style.display = 'block';
            policyToDelete = policyDiv;
        });
        iconsDiv.appendChild(deleteIcon);

        policyDiv.appendChild(iconsDiv);
    policyItemsContainer.prepend(policyDiv);
         const uploadedFilesContainer = document.createElement('div');
        uploadedFilesContainer.classList.add('uploaded-files-container');
        policyDiv.appendChild(uploadedFilesContainer);
    }


    if (cancelUploadPolicyButton) {
        cancelUploadPolicyButton.addEventListener('click', () => {
            uploadPolicyModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            policyTitleInput.value = '';
            document.querySelector('#uploadPolicyModal input[type="file"]').value = '';
        });
    }

  if (confirmUploadPolicyButton) {
    confirmUploadPolicyButton.addEventListener('click', () => {
        const uploadPolicyModal = document.getElementById('uploadPolicyModal');
        const policyTitleInput = uploadPolicyModal.querySelector('input[name="policyTitle"], #policyTitle');
        const fileInput = uploadPolicyModal.querySelector('input[type="file"]');
        const policyTitle = policyTitleInput ? policyTitleInput.value.trim() : '';
        const fileSelected = fileInput && fileInput.files && fileInput.files.length > 0;

        if (!policyTitle) {
            alert('Please input a policy title.');
            return;
        }
        if (!fileSelected) {
            alert('Please select a policy file.');
            return;
        }

        if (currentTargetPolicy) {
            const file = fileInput.files[0];
          const fileDiv = document.createElement('div');
        fileDiv.classList.add('uploaded-policy-item');
        fileDiv.dataset.parentId = currentTargetPolicy.dataset.policyId; // <-- This line is crucial!
        fileDiv.innerHTML = `<span>${policyTitle}: ${file.name}</span>`;

            const actionsDiv = document.createElement('div');
            actionsDiv.classList.add('upload-actions');

            const editIcon = document.createElement('i');
            editIcon.classList.add('fas', 'fa-pencil-alt', 'edit-upload-icon');
            editIcon.title = 'Edit Uploaded Policy';
            // Add event listener for edit if needed

            const deleteIcon = document.createElement('i');
            deleteIcon.classList.add('fas', 'fa-trash-alt', 'delete-upload-icon');
            deleteIcon.title = 'Delete Uploaded Policy';
            // Add event listener for delete if needed

            actionsDiv.appendChild(editIcon);
            actionsDiv.appendChild(deleteIcon);

            fileDiv.appendChild(actionsDiv);

            currentTargetPolicy.parentNode.insertBefore(fileDiv, currentTargetPolicy.nextSibling);
        }

        uploadPolicyModal.style.display = 'none';
        modalOverlay.style.display = 'none';
        policyTitleInput.value = '';
        fileInput.value = '';
        currentTargetPolicy = null;
    });
}

    if (cancelStructureViewButton) {
        cancelStructureViewButton.addEventListener('click', () => {
            policyStructureModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            policyStructureNameInput.value = '';
            activePolicyForStructure = null;
            currentlyEditingStructureSpan = null;
        });
    }

    if (confirmStructureViewButton) {
    confirmStructureViewButton.addEventListener('click', () => {
        const structureName = policyStructureNameInput.value.trim();

        if (structureName && activePolicyForStructure) {
            const policyId = activePolicyForStructure.dataset.policyId;

            if (currentlyEditingStructureSpan) {
                currentlyEditingStructureSpan.textContent = structureName;
                policyStructureModal.style.display = 'none';
                modalOverlay.style.display = 'none';
                policyStructureNameInput.value = '';
                currentlyEditingStructureSpan = null;
                activePolicyForStructure = null;
                return;
            }

        const structureDiv = document.createElement('div');
        structureDiv.classList.add('policyStructureItem');
        const structureId = generateUniqueId();
        structureDiv.dataset.policyId = structureId;
        structureDiv.dataset.parentId = activePolicyForStructure.dataset.policyId;

           structureDiv.innerHTML = `
            <span>${structureName}</span>
            <div class="structureIcons">
                <i class="fas fa-file-alt upload-policy-icon" title="Upload Policy"></i>
                <i class="fas fa-pencil-alt edit-structure-icon" title="Edit Structure"></i>
                <i class="fas fa-trash-alt delete-structure-icon" title="Delete Structure"></i>
            </div>
        `;

            const editStructureIcon = structureDiv.querySelector('.edit-structure-icon');
            const deleteStructureIcon = structureDiv.querySelector('.delete-structure-icon');
            const structureNameSpan = structureDiv.querySelector('span');
            const uploadIcon = structureDiv.querySelector('.upload-policy-icon');

            uploadIcon.addEventListener('click', () => {
                uploadPolicyModal.style.display = 'block';
                modalOverlay.style.display = 'block';
                currentTargetPolicy = structureDiv;
            });

            editStructureIcon.addEventListener('click', () => {
                const currentStructureName = structureNameSpan.textContent;
                policyStructureNameInput.value = currentStructureName;
                policyStructureModal.style.display = 'block';
                modalOverlay.style.display = 'block';
                currentlyEditingStructureSpan = structureNameSpan;
                activePolicyForStructure = structureDiv.parentNode;
            });

            deleteStructureIcon.addEventListener('click', () => {
                console.log('Deleting structure item initiated for:', structureDiv);
                deletePolicyModal.style.display = 'block';
                modalOverlay.style.display = 'block';
                itemToDelete = structureDiv;
            });

            // Find the first uploaded policy item that is a direct sibling
            let insertionPoint = activePolicyForStructure.nextSibling;
            while (insertionPoint && !insertionPoint.classList.contains('uploaded-policy-item')) {
                insertionPoint = insertionPoint.nextSibling;
            }

            // Insert the new subfolder before the first uploaded policy item (or after the main folder if none exist)
            activePolicyForStructure.parentNode.insertBefore(structureDiv, insertionPoint);

            policyStructureModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            policyStructureNameInput.value = '';
            activePolicyForStructure = null;
        } else if (!structureName && currentlyEditingStructureSpan) {
            alert('Please enter a new structure name.');
        } else if (!structureName) {
            alert('Please enter a structure name.');
        } else if (!activePolicyForStructure) {
            alert('Error: No policy selected for structure.');
            policyStructureModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            policyStructureNameInput.value = '';
        }
    });
}

    if (cancelRenamePolicyButton) {
        cancelRenamePolicyButton.addEventListener('click', () => {
            renamePolicyModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            renamePolicyInput.value = '';
            renamePolicyModal.dataset.targetPolicySpan = '';
        });
    }

    if (confirmRenamePolicyButton) {
        confirmRenamePolicyButton.addEventListener('click', () => {
            const newPolicyName = renamePolicyInput.value.trim();
            const targetSpanId = renamePolicyModal.dataset.targetPolicySpan;

            if (newPolicyName && targetSpanId) {
                const targetNameSpan = document.getElementById(targetSpanId);
                if (targetNameSpan) {
                    targetNameSpan.textContent = newPolicyName;
                    renamePolicyModal.style.display = 'none';
                    modalOverlay.style.display = 'none';
                    renamePolicyInput.value = '';
                    renamePolicyModal.dataset.targetPolicySpan = '';
                } else {
                    alert('Error updating policy name.');
                }
            } else {
                if (!newPolicyName) {
                    alert('Please enter a new policy name.');
                } else {
                    alert('Error: Target policy information missing.');
                }
            }
        });
    }

    if (cancelDeletePolicyButton) {
        cancelDeletePolicyButton.addEventListener('click', () => {
            deletePolicyModal.style.display = 'none';
            modalOverlay.style.display = 'none';
            policyToDelete = null;
            itemToDelete = null;
        });
    }

    if (confirmDeletePolicyButton) {
        confirmDeletePolicyButton.addEventListener('click', () => {
            if (policyToDelete) {
                recursivelyDeletePolicyItem(policyToDelete);
                policyToDelete = null;
            } else if (itemToDelete) {
                recursivelyDeleteStructureItem(itemToDelete);
                itemToDelete = null;
            }
            deletePolicyModal.style.display = 'none';
            modalOverlay.style.display = 'none';
        });
    }

    function recursivelyDeletePolicyItem(itemToDelete) {
    const policyIdToDelete = itemToDelete.dataset.policyId;

    // Delete all subfolders (policyStructureItem) with this as parent
    const childStructures = document.querySelectorAll(`.policyStructureItem[data-parent-id="${policyIdToDelete}"]`);
    childStructures.forEach(child => recursivelyDeletePolicyItem(child));

    // Delete all uploaded files under this folder
    const uploadedFiles = document.querySelectorAll(`.uploaded-policy-item[data-parent-id="${policyIdToDelete}"]`);
    uploadedFiles.forEach(file => file.remove());

    // Remove the current item
    itemToDelete.remove();
}

// When confirming delete:
if (confirmDeletePolicyButton) {
    confirmDeletePolicyButton.addEventListener('click', () => {
        if (policyToDelete) {
            recursivelyDeletePolicyItem(policyToDelete);
            policyToDelete = null;
        } else if (itemToDelete) {
            recursivelyDeletePolicyItem(itemToDelete);
            itemToDelete = null;
        }
        deletePolicyModal.style.display = 'none';
        modalOverlay.style.display = 'none';
    });
}
});

function showDepartmentManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');
    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'block';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';
}


function showPolicyRepository() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'block';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showPolicySubmission() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');


    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'block';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showPolicyManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'block';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showRoleManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='block';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'none';

}

function showReports() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const roleManagerPanel = document.querySelector('.Role-Manager-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const reportPanel = document.querySelector('.reports-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (roleManagerPanel) roleManagerPanel.style.display='none';
    if (departmentPanel) departmentPanel.style.display = 'none';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
    if (reportPanel) reportPanel.style.display= 'block';

}


// reports
  document.addEventListener('DOMContentLoaded', () => {
        const reportItems = document.querySelectorAll('.report-item');

        reportItems.forEach(item => {
            const header = item.querySelector('.report-header');
            const content = item.querySelector('.report-content');
            const arrowIcon = item.querySelector('.arrow-icon');

            // Initially hide all content areas
            content.style.display = 'none';

            header.addEventListener('click', () => {
                // Toggle the display of the content area
                content.style.display = content.style.display === 'none' ? 'block' : 'none';

                // Toggle the arrow icon direction
                arrowIcon.classList.toggle('fa-caret-right');
                arrowIcon.classList.toggle('fa-caret-down');
            });
        });
    });