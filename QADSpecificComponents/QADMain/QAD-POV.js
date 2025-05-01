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

function showDepartmentManager() {
    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'none';
    departmentPanel.style.display = 'block';
    policyManagerPanel.style.display = 'none';
}

function showPolicyManager() {
    policyRepositoryPanel.style.display = 'none';
    policySubmissionPanel.style.display = 'none';
    departmentPanel.style.display = 'none';
    policyManagerPanel.style.display = 'flex';
}


// Attach the function to the sidebar menu item
document.querySelector('.menu-icons:nth-child(1)').addEventListener('click', showPolicyRepository);
document.querySelector('.menu-icons:nth-child(2)').addEventListener('click', showPolicySubmission);
document.querySelector('.menu-icons:nth-child(6)').addEventListener('click', showDepartmentManager);
document.querySelector('.menu-icons:nth-child(7)').addEventListener('click', showPolicyManager);


document.addEventListener('DOMContentLoaded', () => {

const addDepartmentButton = document.getElementById('addDepartmentButton');
const assignNamePanel = document.getElementById('assignNamePanel');
const cancelBtn = document.getElementById('cancelBtn');

    // Show the Assign Name panel when the Add Department button is clicked
    addDepartmentButton.addEventListener('click', () => {
        assignNamePanel.style.display = 'flex'; // Show the panel
    });

    // Hide the Assign Name panel when the Cancel button is clicked
    cancelBtn.addEventListener('click', () => {
        console.log("Cancel button clicked");
        assignNamePanel.style.display = 'none'; // Hide the panel
    });

});