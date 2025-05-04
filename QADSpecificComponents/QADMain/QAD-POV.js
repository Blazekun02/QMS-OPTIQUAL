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
    

// Policy Repository
function showPolicyRepository() {
    console.log("Policy Repository Triggered");
    policyRepositoryPanel.style.display = 'block';
    policySubmissionPanel.style.display = 'none';
    departmentPanel.style.display = 'none'; 
}

// Policy Submission
function showPolicySubmission() {
    console.log("Policy Submission Triggered");

    // Safely get the department panel inside this function
    
    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'flex';
    departmentPanel.style.display = 'none';

    

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

//department Manager
document.addEventListener('DOMContentLoaded', () => {
    const addDepartmentButton = document.getElementById('addDepartmentButton');
    const assignNameContainer = document.getElementById('assignNameContainer');
    const overlay = document.getElementById('overlay');
    const cancelAssignNameButton = document.getElementById('cancelAssignName');
    const confirmAssignNameButton = document.getElementById('confirmAssignName');
    const departmentNameInput = document.getElementById('departmentNameInput');
    const departmentListContainer = document.getElementById('departmentListContainer');
    const assignRoleContainer = document.getElementById('assignRoleContainer');
    const departmentStructureContainer = document.getElementById('departmentStructureContainer');
    const cancelStructureButton = document.getElementById('cancelStructure');
    const confirmStructureButton = document.getElementById('confirmStructure');
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
    let departmentToDelete = null; // To store the department to be deleted
    let currentTargetDepartment = null; // To store the department being assigned a role
    let currentlyEditingRoleTextSpan = null; // To store the span being edited
    let roleToDelete = null; // To store the role to be deleted


    addDepartmentButton.addEventListener('click', () => {
        assignNameContainer.style.display = 'block';
        overlay.style.display = 'block';
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
        nameSpan.id = `department-name-${Date.now()}`; // Assign the unique ID FIRST
        departmentDiv.appendChild(nameSpan); // Then append

        const iconsDiv = document.createElement('div');
        iconsDiv.classList.add('department-icons');

        const addUserIcon = document.createElement('i');
        addUserIcon.classList.add('fas', 'fa-user-plus');
        addUserIcon.addEventListener('click', () => {
            assignRoleContainer.style.display = 'block';
            overlay.style.display = 'block';
            currentTargetDepartment = departmentDiv;
            assignRoleContainer.dataset.targetDepartment = departmentDiv;
        });
        iconsDiv.appendChild(addUserIcon);

        const structureIcon = document.createElement('i');
        structureIcon.classList.add('fas', 'fa-sitemap');
        structureIcon.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'block';
            overlay.style.display = 'block';
        });
        iconsDiv.appendChild(structureIcon);

        const editIcon = document.createElement('i');
        editIcon.classList.add('fas', 'fa-pencil-alt');
        editIcon.addEventListener('click', () => {
            renameDepartmentContainer.style.display = 'block';
            overlay.style.display = 'block';
            renameDepartmentInput.value = nameSpan.textContent;
            renameDepartmentContainer.dataset.targetDepartmentSpan = nameSpan.id;
            console.log('Stored targetSpanId in renameDepartmentContainer:', renameDepartmentContainer.dataset.targetDepartmentSpan);
        });
        iconsDiv.appendChild(editIcon);

        const deleteIcon = document.createElement('i');
        deleteIcon.classList.add('fas', 'fa-trash-alt');
        deleteIcon.addEventListener('click', () => {
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
    let currentlyEditingRole = null;

    if (cancelAssignRoleButton) {
        cancelAssignRoleButton.addEventListener('click', () => {
            assignRoleContainer.style.display = 'none';
            overlay.style.display = 'none';
            document.getElementById('positionInput').value = '';
            document.getElementById('nameInput').value = '';
            currentlyEditingRole = null; // Reset
        });
    }


// Automatically populate the "Name" input field when a checkbox is checked
const nameInput = document.getElementById('nameInput');
const accountCheckboxes = document.querySelectorAll('.scrollable-account-list input[type="checkbox"]');

accountCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        const selectedCheckboxes = document.querySelectorAll('.scrollable-account-list input[type="checkbox"]:checked');

        // Ensure only one checkbox is selected
        if (selectedCheckboxes.length > 1) {
            alert('You can only select one account.');
            checkbox.checked = false; // Uncheck the current checkbox
            return;
        }

        // Populate the "Name" input field with the selected account's name
        if (checkbox.checked) {
            const accountLabel = checkbox.nextElementSibling.textContent; // Get the label text
            const [fullName] = accountLabel.split(' ('); // Extract the name before the email
            nameInput.value = fullName.trim(); // Populate the name input field with the name only
        } else {
            nameInput.value = ''; // Clear the name input field if unchecked
        }
    });
});

 // Confirm Assign Role Button Logic
if (confirmAssignRoleButton) {
    confirmAssignRoleButton.addEventListener('click', () => {
        const positionInput = document.getElementById('positionInput');
        const position = positionInput.value.trim();
        const name = nameInput.value.trim();

        // Get all checked checkboxes
        const selectedAccounts = document.querySelectorAll('.scrollable-account-list input[type="checkbox"]:checked');

        // Validation for inputs and checkboxes
        if (!position) {
            alert('Please fill in the Position field.');
            return;
        }

        if (selectedAccounts.length === 0) {
            alert('Please select at least one account.');
            return;
        }

        if (selectedAccounts.length > 1) {
            alert('You can only select one account.');
            return;
        }

        // Proceed with assigning the role
        const selectedAccountLabel = selectedAccounts[0].nextElementSibling.textContent; // Get the label text
        const [fullName, email] = selectedAccountLabel.split(' ('); // Extract the name and email
        const emailOnly = email.replace(')', '').trim(); // Remove the closing parenthesis and trim

        const newRoleText = `${position} - ${fullName.trim()} (${emailOnly})`; // Use the name and email

        if (currentlyEditingRole) {
            // Update the existing role
            const roleTextSpan = currentlyEditingRole.querySelector('span');
            roleTextSpan.textContent = newRoleText;
            currentlyEditingRole = null; // Reset editing state
        } else {
            // Create a new role
            const assignedRoleDiv = document.createElement('div');
            assignedRoleDiv.classList.add('assigned-role-item');
            assignedRoleDiv.innerHTML = `
                <span>${newRoleText}</span>
                <div class="assigned-role-icons">
                    <i class="fas fa-pencil-alt edit-role-icon" title="Rename Role"></i>
                    <i class="fas fa-trash-alt delete-role-icon" title="Delete Role"></i>
                </div>
            `;
            const editRoleIcon = assignedRoleDiv.querySelector('.edit-role-icon');
            const deleteRoleIcon = assignedRoleDiv.querySelector('.delete-role-icon');
            const roleTextSpan = assignedRoleDiv.querySelector('span');

            editRoleIcon.addEventListener('click', () => {
                const currentRoleText = roleTextSpan.textContent;
                const [currentPosition, currentName] = currentRoleText.split(' - ');
                renameRoleInput.value = currentName;
                currentlyEditingRoleTextSpan = roleTextSpan;
                renameRoleContainer.style.display = 'block';
                overlay.style.display = 'block';
            });

            deleteRoleIcon.addEventListener('click', () => {
                deleteConfirmationContainer.style.display = 'block';
                overlay.style.display = 'block';
                roleToDelete = assignedRoleDiv;
            });

            currentTargetDepartment.parentNode.insertBefore(assignedRoleDiv, currentTargetDepartment.nextSibling);
        }
         // Reset the form and close the modal
         assignRoleContainer.style.display = 'none';
         overlay.style.display = 'none';
         positionInput.value = '';
         nameInput.value = '';
         selectedAccounts.forEach(account => account.checked = false); // Uncheck all checkboxes
         currentTargetDepartment = null;
     });
 }

    if (cancelStructureButton) {
        cancelStructureButton.addEventListener('click', () => {
            departmentStructureContainer.style.display = 'none';
            overlay.style.display = 'none';
            document.getElementById('structureNameInput').value = ''; // Clear the input
        });
    }

    if (confirmStructureButton) {
        confirmStructureButton.addEventListener('click', () => {
            const structureName = document.getElementById('structureNameInput').value.trim();

            if (structureName) {
                console.log('Structure Name:', structureName);
                departmentStructureContainer.style.display = 'none';
                overlay.style.display = 'none';
                document.getElementById('structureNameInput').value = ''; // Clear the input
            } else {
                alert('Please enter a structure name.');
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
            console.log('--- Confirm Rename Clicked ---'); // Added
            const newDepartmentName = renameDepartmentInput.value.trim();
            const targetSpanId = renameDepartmentContainer.dataset.targetDepartmentSpan;
            console.log('New department name:', newDepartmentName); // CHECKPOINT C (Relabeled)
            console.log('Retrieved targetSpanId:', targetSpanId); // CHECKPOINT B (Relabeled)

            console.log('Retrieved targetSpanId on confirm:', targetSpanId); 
            if (newDepartmentName && targetSpanId) {
                console.log('Both newDepartmentName and targetSpanId are truthy.'); // Added
                const targetNameSpan = document.getElementById(targetSpanId);
                console.log('Attempting to get element with ID:', targetSpanId); // Added
                console.log('Found targetNameSpan element:', targetNameSpan); // CHECKPOINT D (Relabeled)
                console.log('Found targetNameSpan element:', targetNameSpan);
                if (targetNameSpan) {
                    console.log('targetNameSpan element exists. Updating textContent.'); // Added
                    targetNameSpan.textContent = newDepartmentName;
                    renameDepartmentContainer.style.display = 'none';
                    overlay.style.display = 'none';
                    renameDepartmentInput.value = '';
                    renameDepartmentContainer.dataset.targetDepartmentSpan = '';
                    console.log('Department name updated successfully!'); // CHECKPOINT E (Relabeled)
                } else {
                    console.error('Target department name span NOT found!'); // CHECKPOINT F (Relabeled)
                    alert('Error updating department name.');
                }
            } else {
                console.log('Either newDepartmentName or targetSpanId is falsy.'); // Added
                if (!newDepartmentName) {
                    alert('Please enter a new department name.'); // CHECKPOINT G (Relabeled)
                } else {
                    console.log('targetSpanId is falsy:', targetSpanId); // Added
                    alert('Error: Target department information missing.'); // More informative alert
                }
            }
            console.log('--- Confirm Rename Click End ---'); // Added
        });
    }

    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
            departmentToDelete = null;
            roleToDelete = null;
        });
    }

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', () => {
            if (departmentToDelete) {
                console.log("Deleting department:", departmentToDelete);
    
                // Remove all assigned roles within the department
                const assignedRoles = departmentToDelete.querySelectorAll('.assigned-role-item');
                assignedRoles.forEach(role => {
                    console.log("Removing assigned role:", role);
                    role.remove(); // Remove each assigned role
                });
    
                // Remove the department itself
                departmentToDelete.remove();
                departmentToDelete = null;
    
                console.log("Department and all assigned roles deleted successfully.");
            } else if (roleToDelete) {
                console.log("Deleting role:", roleToDelete);
                roleToDelete.remove();
                roleToDelete = null;

                console.log("Role deleted successfully.");
            }
    
            // Close the delete confirmation modal
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
        });
    }
    
    
    if (cancelRenameRoleButton) {
        cancelRenameRoleButton.addEventListener('click', () => {
            renameRoleContainer.style.display = 'none';
            overlay.style.display = 'none';
            renameRoleInput.value = '';
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
                renameRoleContainer.style.display = 'none';
                overlay.style.display = 'none';
                renameRoleInput.value = '';
                currentlyEditingRoleTextSpan = null;
            } else {
                alert('Please enter a new role name.');
            }
        });
    }
});

function showDepartmentManager() {
    const policyRepositoryPanel = document.querySelector('.Policy-Repository-Panel');
    const policySubmissionPanel = document.querySelector('.Policy-Submission-Panel');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');

    if (policyRepositoryPanel) policyRepositoryPanel.style.display = 'none';
    if (policySubmissionPanel) policySubmissionPanel.style.display = 'none';
    if (departmentPanel) departmentPanel.style.display = 'block';
    if (policyManagerPanel) policyManagerPanel.style.display = 'none';
}


// Attach the function to the sidebar menu item
document.querySelector('.menu-icons:nth-child(1)').addEventListener('click', showPolicyRepository);
document.querySelector('.menu-icons:nth-child(2)').addEventListener('click', showPolicySubmission);
document.querySelector('.menu-icons:nth-child(6)').addEventListener('click', showDepartmentManager);
document.querySelector('.menu-icons:nth-child(7)');