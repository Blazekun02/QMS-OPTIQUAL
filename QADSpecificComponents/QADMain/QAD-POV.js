//Show Modules
    const policyRepositoryPanel = document.getElementById('policy-repo-content');
    const policySubmissionPanel = document.getElementById('policy-submission-content');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const processTrackerPanel = document.querySelector('.Process-Tracker-Panel2'); // Ensure this matches the class in your HTML
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');
    const taskManagerPanel = document.querySelector('.Task-Manager-Panel');

        // Department Manager
    function showDepartmentManager() {    
        policyRepositoryPanel.style.display = 'none';
        policySubmissionPanel.style.display = 'none';
        departmentPanel.style.display = 'block';
        policyManagerPanel.style.display = 'none';
        processTrackerPanel.style.display = 'none';
        taskManagerPanel.style.display = 'none';
        
    }

    // Policy Repository
    function showPolicyRepository() {
        console.log("Policy Repository Triggered");
        policyRepositoryPanel.style.display = 'block';
        policySubmissionPanel.style.display = 'none';
        processTrackerPanel.style.display = 'none';
        departmentPanel.style.display = 'none'; 
        policyManagerPanel.style.display = 'none';
        taskManagerPanel.style.display = 'none';
    }

    // Policy Submission
    function showPolicySubmission() {
        console.log("Policy Submission Triggered");
        policyRepositoryPanel.style.display = 'none';
        policySubmissionPanel.style.display = 'flex';
        processTrackerPanel.style.display = 'none';
        departmentPanel.style.display = 'none';
        policyManagerPanel.style.display = 'none';
        taskManagerPanel.style.display = 'none';
    }

    // Process Tracker
    function showProcessTracker() {
        policyRepositoryPanel.style.display = 'none';
        policySubmissionPanel.style.display = 'none';
        processTrackerPanel.style.display = 'block';
        departmentPanel.style.display = 'none'; 
        policyManagerPanel.style.display = 'none';
        taskManagerPanel.style.display = 'none';
        
    }

    // Task Manager
    function showTaskManager() {
    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'none';
    processTrackerPanel.style.display = 'none';
    departmentPanel.style.display = 'none';
    policyManagerPanel.style.display = 'none';

    // Show the Task Manager panel
    taskManagerPanel.style.display = 'flex';
}

// Task Manager
function showTaskManager() {
    console.log('Task Manager Triggered');
    // Hide all other panels
    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'none';
    processTrackerPanel.style.display = 'none';
    departmentPanel.style.display = 'none';
    policyManagerPanel.style.display = 'none';

    // Show the Task Manager panel
    taskManagerPanel.style.display = 'flex';

    // Additional Task Manager-specific logic
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');

    taskManagerHeaderContainer.style.display = 'block'; // Show header
    taskManagerTable.style.display = 'table'; // Show the table
    introductionSection.style.display = 'none'; // Hide introduction section
}

// Add event listener for Task Manager menu item
document.addEventListener('DOMContentLoaded', () => {
    const taskManagerMenuItem = document.querySelector('.Sidebar-Menu .menu-icons:nth-child(4)');
    if (taskManagerMenuItem) {
        taskManagerMenuItem.addEventListener('click', showTaskManager);
    } else {
        console.error('Task Manager menu item not found.');
    }
});

function showIntroduction(policyTitle, policyContent, pdfPath) {
    const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
    const taskManagerTable = document.querySelector('.task-manager-table');
    const introductionSection = document.querySelector('.introduction-section');
    const introductionTitleElement = introductionSection.querySelector('.introduction-title');
    const introductionContentElement = introductionSection.querySelector('.introduction-content');
    const policyFeedbackContent = document.getElementById('policyFeedbackContent'); // Get the placeholder
    const pdfViewerContainer = document.querySelector('.pdfViewerContainer'); // Get the PDF viewer container
    const viewPolicyButton = document.getElementById('viewPolicyButton'); // Get the View Policy button
    const introductionContent = document.querySelector('.introduction-content'); // Get the initial content

    introductionTitleElement.textContent = policyTitle;
    introductionContentElement.textContent = policyContent;

    taskManagerHeaderContainer.style.display = 'none'; // Hide header and line
    taskManagerTable.style.display = 'none';
    pdfViewerContainer.style.display = 'none'; // Hide the PDF viewer container
    introductionSection.style.display = 'block';
    policyFeedbackContent.style.display = 'block'; // Show the placeholder  
    introductionContent.style.display = 'block';
    viewPolicyButton.textContent = 'View Policy';

    // Add event listener to dynamically load the PDF when "View Policy" is clicked
    let isPolicyVisible = false;
    viewPolicyButton.addEventListener('click', function () {
        if (!isPolicyVisible) {
            introductionContent.style.display = 'none';
            pdfViewerContainer.style.display = 'block'; // Show the PDF viewer
            policyFeedbackContent.style.display = 'none';
            viewPolicyButton.textContent = 'View Feedback Report';
            isPolicyVisible = true;

            // Dynamically load the PDF into the viewer
            const pdfUrl = `${pdfPath}`; // Adjust the path as needed
            pdfjsLib.getDocument(pdfUrl).promise.then(pdfDoc_ => {
                pdfDoc = pdfDoc_;
                document.getElementById('pageCount').textContent = pdfDoc.numPages;
                renderPage(1); // Render the first page
            });
        } else {
            introductionContent.style.display = 'block';
            pdfViewerContainer.style.display = 'none'; // Hide the PDF viewer
            policyFeedbackContent.style.display = 'block';
            viewPolicyButton.textContent = 'View Policy';
            isPolicyVisible = false;
        }
    });
}   
    


document.querySelector('.menu-icons:nth-child(1)').addEventListener('click', showPolicyRepository);
document.querySelector('.menu-icons:nth-child(2)').addEventListener('click', showPolicySubmission);
document.querySelector('.menu-icons:nth-child(3)').addEventListener('click', showProcessTracker);
document.querySelector('.menu-icons:nth-child(4)').addEventListener('click', showTaskManager);
document.querySelector('.menu-icons:nth-child(6)').addEventListener('click', showDepartmentManager);
document.querySelector('.menu-icons:nth-child(7)');

//Show Modules End
document.addEventListener('DOMContentLoaded', () => {
    const closePdfViewerButton = document.getElementById('closePdfViewer');
    const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
    const policyRepositoryPanel = document.getElementById('policy-repo-content');

    // Add event listener to close the PDF Viewer
    closePdfViewerButton.addEventListener('click', () => {
        pdfViewerContainer.style.display = 'none'; // Hide the PDF Viewer
        policyRepositoryPanel.style.display = 'block'; // Show the Policy Repository
    });
});

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
   

//Sign Out Overlay
    const signOutOverlay = document.getElementById('signOutOverlay');
    const userButton = document.getElementById('userButton');
    userButton.addEventListener('click', () => {
        signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById("signOutOverlay").addEventListener("click", function () {
        window.location.href = "landingPage.html";
    });

//Confirm Download Overlay
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

//Submit Overlay
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


// Policy Search function
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');

function searchPolicies() {
    const searchTerm = searchInput.value.toLowerCase();

    // Get everything
    const parentFolders = document.querySelectorAll('.PR-Parent-Folders');
    const childFolders = document.querySelectorAll('.PR-Child-Folders');
    const policies = document.querySelectorAll('.PR-Policies');

    // Hide All Folders
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
    const positionInput = document.getElementById('positionInput');
    const nameInput = document.getElementById('nameInput');
    const departmentStructureContainer = document.getElementById('departmentStructureContainer');
    const cancelStructureButton = document.getElementById('cancelStructure');
    const confirmStructureButton = document.getElementById('confirmStructure');
    const structureNameInput = document.getElementById('structureNameInput'); // Get the structure name input
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
    let currentlyEditingRole = null;
    let activeDepartmentForStructure = null; // To track which department's structure icon was clicked
  


// Fetch and display all departments from DB on page load
    fetch('../../generalComponents/dpManagerPHP/getDepartments.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.departments)) {
            data.departments.forEach(dep => {
                displayNewDepartment(dep.dptName, dep.dptID);
            });
        }
    });
  
    addDepartmentButton.addEventListener('click', () => {
     assignNameContainer.style.display = 'block';
     overlay.style.display = 'block';
    });
  
    cancelAssignNameButton.addEventListener('click', () => {
     assignNameContainer.style.display = 'none';
     overlay.style.display = 'none';
     departmentNameInput.value = '';
    });
  
    // confirmAssignNameButton.addEventListener('click', () => {
    //  const departmentName = departmentNameInput.value.trim();
  
    //  if (departmentName) {
    //   displayNewDepartment(departmentName);
    //   assignNameContainer.style.display = 'none';
    //   overlay.style.display = 'none';
    //   departmentNameInput.value = '';
    //  } else {
    //   alert('Please enter a department name.');
    //  }
    // });

    confirmAssignNameButton.addEventListener('click', () => {
    const departmentName = departmentNameInput.value.trim();

    if (departmentName) {
        fetch('../../generalComponents/dpManagerPHP/addDepartment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ departmentName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayNewDepartment(departmentName, data.departmentId); // Only display if saved successfully
                assignNameContainer.style.display = 'none';
                overlay.style.display = 'none';
                departmentNameInput.value = '';
            } else {
                alert('Failed to add department: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error adding department: ' + error);
        });
    } else {
        alert('Please enter a department name.');
    }
    });
  
    function displayNewDepartment(name, id = null) {
    const departmentDiv = document.createElement('div');
    departmentDiv.classList.add('department-item');
    departmentDiv.dataset.departmentName = name;
    if (id) departmentDiv.dataset.departmentId = id; // <-- Always set departmentId if available

    const nameSpan = document.createElement('span');
    nameSpan.textContent = name;
    nameSpan.id = `department-name-${id ? id : Date.now()}`; // Use id if available, else fallback
    departmentDiv.appendChild(nameSpan)
  
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
      activeDepartmentForStructure = departmentDiv; // Store the clicked department
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
  
    if (cancelAssignRoleButton) {
     cancelAssignRoleButton.addEventListener('click', () => {
      assignRoleContainer.style.display = 'none';
      overlay.style.display = 'none';
      positionInput.value = '';
      nameInput.value = '';
      currentlyEditingRole = null;
      document.querySelectorAll('.scrollable-account-list input[type="checkbox"]:checked').forEach(checkbox => checkbox.checked = false);
     });
    }
  
    if (confirmAssignRoleButton) {
     confirmAssignRoleButton.addEventListener('click', () => {
      const position = positionInput.value.trim();
      const name = nameInput.value.trim();
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
        nameInput.value = currentFullName.trim();
  
        document.querySelectorAll('.scrollable-account-list .account-item').forEach(item => {
         const label = item.querySelector('label').textContent;
         if (label.startsWith(currentFullName.trim())) {
          item.querySelector('input[type="radio"]').checked = true;
         } else {
          item.querySelector('input[type="radio"]').checked = false;
         }
        });
  
        currentlyEditingRole = assignedRoleDiv;
        assignRoleContainer.style.display = 'block';
        overlay.style.display = 'block';
       });
  
       deleteRoleIcon.addEventListener('click', () => {
        deleteConfirmationContainer.style.display = 'block';
        overlay.style.display = 'block';
        roleToDelete = assignedRoleDiv;
       });
  
       currentTargetDepartment.parentNode.insertBefore(assignedRoleDiv, currentTargetDepartment.nextSibling);
      }
  
      assignRoleContainer.style.display = 'none';
      overlay.style.display = 'none';
      positionInput.value = '';
      nameInput.value = '';
      selectedAccount.checked = false;
      currentTargetDepartment = null;
     });
    }
  
    const accountRadios = document.querySelectorAll('.scrollable-account-list input[type="radio"]');
  
    accountRadios.forEach(radio => {
     radio.addEventListener('change', () => {
      if (radio.checked) {
       const accountLabel = radio.nextElementSibling.textContent;
       const [fullName] = accountLabel.split(' (');
       nameInput.value = fullName.trim();
      }
     });
    });
  
    if (cancelStructureButton) {
     cancelStructureButton.addEventListener('click', () => {
      departmentStructureContainer.style.display = 'none';
      overlay.style.display = 'none';
      structureNameInput.value = '';
      activeDepartmentForStructure = null; // Clear the active department
     });
    }
  
    if (confirmStructureButton) {
     confirmStructureButton.addEventListener('click', () => {
      const structureName = structureNameInput.value.trim();
  
      if (structureName && activeDepartmentForStructure) {
       const structureDiv = document.createElement('div');
       structureDiv.classList.add('department-structure-item'); // Add a class for styling
       structureDiv.textContent = `- ${structureName}`; // Display with a hyphen for indentation
  
       // Insert the new structure div after the active department
       activeDepartmentForStructure.parentNode.insertBefore(structureDiv, activeDepartmentForStructure.nextSibling);
  
       departmentStructureContainer.style.display = 'none';
       overlay.style.display = 'none';
       structureNameInput.value = '';
       activeDepartmentForStructure = null; // Clear the active department
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
  
    // if (confirmRenameButton) {
    //  confirmRenameButton.addEventListener('click', () => {
    //   console.log('--- Confirm Rename Clicked ---');
    //   const newDepartmentName = renameDepartmentInput.value.trim();
    //   const targetSpanId = renameDepartmentContainer.dataset.targetDepartmentSpan;
    //   console.log('New department name:', newDepartmentName);
    //   console.log('Retrieved targetSpanId:', targetSpanId);
  
    //   if (newDepartmentName && targetSpanId) {
    //    console.log('Both newDepartmentName and targetSpanId are truthy.');
    //    const targetNameSpan = document.getElementById(targetSpanId);
    //    console.log('Attempting to get element with ID:', targetSpanId);
    //    console.log('Found targetNameSpan element:', targetNameSpan);
    //    if (targetNameSpan) {
    //     console.log('targetNameSpan element exists. Updating textContent.');
    //     targetNameSpan.textContent = newDepartmentName;
    //     renameDepartmentContainer.style.display = 'none';
    //     overlay.style.display = 'none';
    //     renameDepartmentInput.value = '';
    //     renameDepartmentContainer.dataset.targetDepartmentSpan = '';
    //     console.log('Department name updated successfully!');
    //    } else {
    //     console.error('Target department name span NOT found!');
    //     alert('Error updating department name.');
    //    }
    //   } else {
    //    console.log('Either newDepartmentName or targetSpanId is falsy.');
    //    if (!newDepartmentName) {
    //     alert('Please enter a new department name.');
    //    } else {
    //     console.log('targetSpanId is falsy:', targetSpanId);
    //     alert('Error: Target department information missing.');
    //    }
    //   }
    //   console.log('--- Confirm Rename Click End ---');
    //  });
    // }


    if (confirmRenameButton) {
    confirmRenameButton.addEventListener('click', () => {
        console.log('--- Confirm Rename Clicked ---');
        const newDepartmentName = renameDepartmentInput.value.trim();
        const targetSpanId = renameDepartmentContainer.dataset.targetDepartmentSpan;

        if (newDepartmentName && targetSpanId) {
            const targetNameSpan = document.getElementById(targetSpanId);
            const departmentDiv = targetNameSpan.closest('.department-item');
            const departmentId = departmentDiv ? departmentDiv.dataset.departmentId : null;

            if (!departmentId) {
                alert('Error: Department ID not found.');
                return;
            }

            // Send AJAX request to update department name in the database
            fetch('../../generalComponents/dpManagerPHP/renameDepartment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ departmentId, newDepartmentName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the department name in the UI
                    targetNameSpan.textContent = newDepartmentName;
                    renameDepartmentContainer.style.display = 'none';
                    overlay.style.display = 'none';
                    renameDepartmentInput.value = '';
                    renameDepartmentContainer.dataset.targetDepartmentSpan = '';
                    console.log('Department name updated successfully!');
                } else {
                    alert('Failed to rename department: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error renaming department: ' + error);
            });
        } else {
            alert('Please enter a new department name.');
        }
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
  
    // if (confirmDeleteButton) {
    //  confirmDeleteButton.addEventListener('click', () => {
    //   if (departmentToDelete) {
    //    console.log("Deleting department:", departmentToDelete);
    //    const assignedRoles = departmentToDelete.querySelectorAll('.assigned-role-item');
    //    assignedRoles.forEach(role => {
    //     console.log("Removing assigned role:", role);
    //     role.remove();
    //    });
    //    departmentToDelete.remove();
    //    departmentToDelete = null;
    //    console.log("Department and all assigned roles deleted successfully.");
    //   } else if (roleToDelete) {
    //    console.log("Deleting role:", roleToDelete);
    //    roleToDelete.remove();
    //    roleToDelete = null;
    //    console.log("Role deleted successfully.");
    //   }
    //   deleteConfirmationContainer.style.display = 'none';
    //   overlay.style.display = 'none';
    //  });
    // }
  
    if (confirmDeleteButton) {
    confirmDeleteButton.addEventListener('click', () => {
        if (departmentToDelete) {
            // Get the department ID from the dataset
            const departmentId = departmentToDelete.dataset.departmentId;
            if (!departmentId) {
                alert('Department ID not found. Cannot delete.');
                return;
            }
            fetch('../../generalComponents/dpManagerPHP/deleteDepartment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ departmentId }) // Send dptID, not name
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove assigned roles
                    const assignedRoles = departmentToDelete.querySelectorAll('.assigned-role-item');
                    assignedRoles.forEach(role => role.remove());
                    // Remove department from DOM
                    departmentToDelete.remove();
                    departmentToDelete = null;
                    console.log("Department and all assigned roles deleted successfully.");
                } else {
                    alert('Failed to delete department: ' + (data.message || 'Unknown error'));
                }
                deleteConfirmationContainer.style.display = 'none';
                overlay.style.display = 'none';
            })
            .catch(error => {
                alert('Error deleting department: ' + error);
                deleteConfirmationContainer.style.display = 'none';
                overlay.style.display = 'none';
            });
        } else if (roleToDelete) {
            // ...existing code for deleting a role...
            roleToDelete.remove();
            roleToDelete = null;
            console.log("Role deleted successfully.");
            deleteConfirmationContainer.style.display = 'none';
            overlay.style.display = 'none';
        }
        
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
  








// Refresh the page every 5 seconds if there are updates
let lastUpdate = null;

function checkForUpdates() {
    fetch('../../generalComponents/Refresh/Policy_Repo_Refresh.php')
        .then(response => response.text())
        .then(timestamp => {
            if (lastUpdate === null) {
                lastUpdate = timestamp;
            } else if (lastUpdate !== timestamp) {
                location.reload(); // Page refresh
            }
        });
}

// Check every 5 seconds
setInterval(checkForUpdates, 5000);

document.querySelectorAll('.PR-Policies').forEach(policy => {
    policy.addEventListener('click', function () {
        const filePath = policy.getAttribute('data-file'); // Get the file path from the data-file attribute
        console.log('File path:', filePath); // Debugging

        const pdfViewerContainer = document.getElementById('Policy_Repo_pdfViewer');
        pdfViewerContainer.style.display = 'block'; // Show the PDF viewer

        if (typeof loadPDF === 'function') {
            loadPDF(filePath); // Dynamically load the PDF
        } else {
            console.error("loadPDF function not found.");
        }

        policyRepositoryPanel.style.display = 'none';
    });
    
});



// const PRpdfUrl = filePath;
//         pdfjsLib.getDocument(url).promise.then(pdfDoc => {
//             window.pdfDoc = pdfDoc_;
//             console.log('PDF loaded:', pdfDoc); // Debugging
//             renderPage(1);
//         })

// url