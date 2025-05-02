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
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    

// Policy Repository
function showPolicyRepository() {
    console.log("Policy Repository Triggered");
    policyRepositoryPanel.style.display = 'block';
    policySubmissionPanel.style.display = 'none';
    departmentPanel.style.display = 'none'; 
    policyManagerPanel.style.display = 'none';
}

// Policy Submission
function showPolicySubmission() {
    console.log("Policy Submission Triggered");

    // Safely get the department panel inside this function
    
    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'flex';
    departmentPanel.style.display = 'none';
    policyManagerPanel.style.display = 'none';
    

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
const parentFolders = document.querySelectorAll('.PS-Parent-Folders');

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
    const parentFolders = document.querySelectorAll('.PS-Parent-Folders');
    const childFolders = document.querySelectorAll('.PS-Child-Folders');
    const policies = document.querySelectorAll('.PS-Policies');

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
            document.querySelector(`.PS-Parent-Folders[data-id='${parentId}']`).style.display = 'flex';
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
            document.querySelector(`.PS-Parent-Folders[data-id='${parentId}']`).style.display = 'flex';
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
const childFolders = document.querySelectorAll('.PS-Child-Folders');

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
    let departmentToDelete = null; // To store the department to be deleted
    let currentTargetDepartment = null; // To store the department being assigned a role

    


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
        departmentDiv.appendChild(nameSpan);
        nameSpan.id = `department-name-${Date.now()}`; // Assign a unique ID
        console.log('Generated nameSpan.id:', nameSpan.id); // CHECKPOINT 1

        const iconsDiv = document.createElement('div');
        iconsDiv.classList.add('department-icons');

        const addUserIcon = document.createElement('i');
        addUserIcon.classList.add('fas', 'fa-user-plus');
        addUserIcon.addEventListener('click', () => {
            assignRoleContainer.style.display = 'block';
            overlay.style.display = 'block';
            currentTargetDepartment = departmentDiv; // ADD THIS LINE!
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
            console.log('Stored targetSpanId in renameDepartmentContainer:', renameDepartmentContainer.dataset.targetDepartmentSpan); // CHECKPOINT 2
        });
        iconsDiv.appendChild(editIcon);

        const deleteIcon = document.createElement('i');
        deleteIcon.classList.add('fas', 'fa-trash-alt');
        deleteIcon.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'block';
            overlay.style.display = 'block';
            departmentToDelete = departmentDiv; // Store the department to delete
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
            document.getElementById('positionInput').value = '';
            document.getElementById('nameInput').value = '';
        });
    }
    if (confirmAssignRoleButton) {
        confirmAssignRoleButton.addEventListener('click', () => {
            const position = document.getElementById('positionInput').value.trim();
            const name = document.getElementById('nameInput').value.trim();

            if (position && name && currentTargetDepartment) {
                const assignedRoleDiv = document.createElement('div');
                assignedRoleDiv.classList.add('assigned-role-item');
                assignedRoleDiv.innerHTML = `
                    <span>${position} - ${name}</span>
                    <div class="assigned-role-icons">
                        <i class="fas fa-pencil-alt"></i>
                        <i class="fas fa-trash-alt"></i>
                    </div>
                `;

                // Insert the assigned role div AFTER the target department
                currentTargetDepartment.parentNode.insertBefore(assignedRoleDiv, currentTargetDepartment.nextSibling);

                assignRoleContainer.style.display = 'none';
                overlay.style.display = 'none';
                document.getElementById('positionInput').value = '';
                document.getElementById('nameInput').value = '';
                currentTargetDepartment = null; // Clear the target department
            } else {
                alert('Please fill in both Position and Name.');
            }
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
            const newDepartmentName = renameDepartmentInput.value.trim();
            const targetSpanId = renameDepartmentContainer.dataset.targetDepartmentSpan;
            console.log('Retrieved targetSpanId on confirm:', targetSpanId); // CHECKPOINT 3
            if (newDepartmentName && targetSpanId) {
                const targetNameSpan = document.getElementById(targetSpanId);
                console.log('Found targetNameSpan element:', targetNameSpan); // CHECKPOINT 4
                if (targetNameSpan) {
                    targetNameSpan.textContent = newDepartmentName;
                    renameDepartmentContainer.style.display = 'none';
                    overlay.style.display = 'none';
                    renameDepartmentInput.value = '';
                    renameDepartmentContainer.dataset.targetDepartmentSpan = '';
                } else {
                    console.error('Target department name span not found!');
                    alert('Error updating department name.');
                }
            } else if (!newDepartmentName) {
                alert('Please enter a new department name.');
            }
        });
    }
    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', () => {
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
            departmentToDelete = null; // Clear the stored department
        });
    }

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', () => {
            if (departmentToDelete) {
                departmentToDelete.remove(); // Delete the stored department
            }
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
            departmentToDelete = null; // Clear the stored department
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
document.querySelector('.menu-icons:nth-child(1)')
document.querySelector('.menu-icons:nth-child(2)')
document.querySelector('.menu-icons:nth-child(6)').addEventListener('click', showDepartmentManager);
document.querySelector('.menu-icons:nth-child(7)')