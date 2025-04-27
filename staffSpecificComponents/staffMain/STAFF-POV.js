
// JS FOR SIDEBAR
const greyLine = document.getElementById('grey-line');
const hamburgerIcon = document.getElementById('hamburger-icon');


greyLine.addEventListener('mouseenter', () => {
    greyLine.classList.add('extended');
    hamburgerIcon.style.left = '14.5vw';
});

greyLine.addEventListener('mouseleave', () => {
    greyLine.classList.remove('extended');
    hamburgerIcon.style.left = '5.5vw';
});

hamburgerIcon.addEventListener('click', () => {
        const isExtended = greyLine.classList.toggle('extended');
        hamburgerIcon.style.left = isExtended ? '13vw' : '4vw';
    });




// JS for notif and sign out

const notificationOverlay = document.getElementById('notificationOverlay');
const notifButton = document.getElementById('notifButton');
notifButton.addEventListener('click', () => {
    notificationOverlay.style.display = notificationOverlay.style.display === 'block' ? 'none' : 'block';
});

const signOutOverlay = document.getElementById('signOutOverlay');
const userButton = document.getElementById('userButton');
userButton.addEventListener('click', () => {
    signOutOverlay.style.display = signOutOverlay.style.display === 'block' ? 'none' : 'block';
});

document.getElementById("signOutOverlay").addEventListener("click", function () {
    window.location.href = "landingPage.html";
});





// js for policy repository

function showPoliciesRepository() {
document.getElementById('policies-repository-content').style.display = 'flex';
document.getElementById('policy-submission-content').style.display='none';
document.querySelector('.process-tracker').style.display='none';
document.querySelector('.task-manager').style.display = 'none';
document.querySelector('.information').style.display = 'none';
}

// Placeholder data for the policy repository
const policyCategoriesData = [
{
    name: "Academic Services",
    subcategories: [
        {
            name: "Guidance Office",
            nestedSubcategories: ["Counseling Services", "Workshops"]
        },
        {
            name: "Office Guidelines",
            nestedSubcategories: ["Office Hours", "Client Assistance", "Appointment and Scheduling"]
        },
        {
            name: "Organizational Profile",
            nestedSubcategories: ["Vision", "Mission", "Core Values", "Academic Offerings"]
        },
        {
            name: "Discipline Office",
            nestedSubcategories: ["Confidentiality and Records Policy", "Dress Code and Decorum Policy"]
        },
        {
            name: "Blue Slip procedures",
            nestedSubcategories: ["No ID, No Entry", "Monday to Thursday", "Friday and Saturday", "Foot Wear"]
        },
        {
            name: "Behavioral sanctions",
            nestedSubcategories: ["Verbal Warning", "Bullying", "Cheating"]
        },
        {
            name: "Office of the Registrar",
            nestedSubcategories: ["Online Transactions Policy", "ID Verification Policy", "Enrollment and Registration Policy"]
        }
    ]
},
{
    name: "School Programs",
    subcategories: [
        {
            name: "School of Engineering",
            nestedSubcategories: ["Computer Engineering", "Civil Engineering", "Electronics Engineering"]
        },
        {
            name: "School of Computing and Information Technology",
            nestedSubcategories: ["Computer Science", "Information Technology", "Associate in Computer Technology", "AI Manifesto", "soCIT Digi-X"]
        },
        {
            name: "School of Management",
            nestedSubcategories: ["Accountancy", "Business Administration", "Tourism Management", "Finance Management", "Management Accounting"]
        },
        {
            name: "School of Multimedia Arts",
            nestedSubcategories: ["Multimedia and Arts", "Arts in Psychology"]
        },
        {
            name: "School of Architecture",
            nestedSubcategories: [] // No nested subcategories for this one in your HTML
        },
        {
            name: "Senior High School",
            nestedSubcategories: ["Stem", "ABM", "HUMMS"]
        },
        {
            name: "Graduate School",
            nestedSubcategories: ["Master of Science in Computer Science", "Master in Information System", "Master in Information Technology", "Master of Engineering Major in Computer Engineering", "Master in Game Design", "Master in Management"]
        }
    ]
}
];

function generatePolicyRepository(data) {
const repositoryContent = document.getElementById('policies-repository-content');
const searchContainer = repositoryContent.querySelector('.search-container');
const blackLine = repositoryContent.querySelector('.blackLine');
let insertionPoint = blackLine ? blackLine.nextSibling : searchContainer ? searchContainer.nextSibling : repositoryContent.firstChild;


const existingCategories = repositoryContent.querySelectorAll('.category');
existingCategories.forEach(category => category.remove());

data.forEach(categoryData => {
    const categoryDiv = document.createElement('div');
    categoryDiv.classList.add('category');
    categoryDiv.dataset.category = categoryData.name.toLowerCase().replace(/ /g, '-');
    categoryDiv.innerHTML = `
        ${categoryData.name}
        <i class="fas fa-chevron-right expand-icon"></i>
        <div class="category-content"></div>
    `;
    repositoryContent.insertBefore(categoryDiv, insertionPoint);
    insertionPoint = categoryDiv.nextSibling;

    const categoryContentDiv = categoryDiv.querySelector('.category-content');
    categoryData.subcategories.forEach(subcategoryData => {
        const subcategoryDiv = document.createElement('div');
        subcategoryDiv.classList.add('subcategory');
        subcategoryDiv.dataset.subcategory = subcategoryData.name.toLowerCase().replace(/ /g, '-');
        subcategoryDiv.innerHTML = `
            ${subcategoryData.name}
            <i class="fas fa-chevron-right expand-icon"></i>
            <div class="nested-subcategory-content"></div>
        `;
        categoryContentDiv.appendChild(subcategoryDiv);

        const nestedContentDiv = subcategoryDiv.querySelector('.nested-subcategory-content');
        subcategoryData.nestedSubcategories.forEach(nestedSubcategoryName => {
            const nestedSubcategoryDiv = document.createElement('div');
            nestedSubcategoryDiv.classList.add('nested-subcategory');
            nestedSubcategoryDiv.dataset.nestedSubcategory = nestedSubcategoryName.toLowerCase().replace(/ /g, '-');
            nestedSubcategoryDiv.textContent = nestedSubcategoryName;
            nestedContentDiv.appendChild(nestedSubcategoryDiv);
        });
    });
});

attachPolicyRepositoryEventListeners();
}

function attachPolicyRepositoryEventListeners() {
document.querySelectorAll('.category').forEach(category => {
    category.addEventListener('click', function() {
        this.classList.toggle('expanded');
    });
});

document.querySelectorAll('.subcategory').forEach(subcategory => {
    subcategory.addEventListener('click', function(event) {
        event.stopPropagation();
        this.classList.toggle('expanded');

        const parentCategoryContent = this.closest('.category-content');
        if (parentCategoryContent) {
            parentCategoryContent.querySelectorAll('.subcategory.expanded').forEach(otherSubcategory => {
                if (otherSubcategory !== this) {
                    otherSubcategory.classList.remove('expanded');
                }
            });
        }
    });
});
}




// js for filter in Pol Rep

function filterSubcategories(category, sort) {
const subcategories = document.querySelectorAll('.subcategory');
subcategories.forEach(subcategory => {
    const subcategoryName = subcategory.getAttribute('data-subcategory');
    const showCategory = !category || category === subcategoryName;

    if (showCategory) {
        subcategory.style.display = 'flex';
    } else {
        subcategory.style.display = 'none';
    }
});

if (sort) {
    console.log(`Sorting by: ${sort} (Not yet implemented)`);
}
}

document.addEventListener('DOMContentLoaded', function() {
generatePolicyRepository(policyCategoriesData);

const filterButton = document.getElementById('filterButton');
const filterOverlay = document.getElementById('filterOverlay');
const cancelFilter = document.getElementById('cancelFilter');
const applyFilter = document.getElementById('applyFilter');
const categoryFilter = document.getElementById('categoryFilter');
const sortFilter = document.getElementById('sortFilter');

if (filterButton && filterOverlay && cancelFilter && applyFilter && categoryFilter && sortFilter) {
    filterButton.addEventListener('click', () => {
        filterOverlay.style.display = 'flex';
    });

    cancelFilter.addEventListener('click', () => {
        filterOverlay.style.display = 'none';
    });

    applyFilter.addEventListener('click', () => {
        const selectedCategory = categoryFilter.value;
        const selectedSort = sortFilter.value;

        filterSubcategories(selectedCategory, selectedSort);
        filterOverlay.style.display = 'none';
    });
}
});



// js for policy submission

function showPolicySubmission() {
    document.getElementById('policies-repository-content').style.display = 'none';
    document.getElementById('policy-submission-content').style.display = 'flex';
    document.querySelector('.process-tracker').style.display= 'none';
     document.querySelector('.task-manager').style.display = 'none';
     document.querySelector('.information').style.display = 'none';
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




// js for process tracker

function showProcessTracker() {
document.getElementById('policies-repository-content').style.display = 'none';
document.getElementById('policy-submission-content').style.display = 'none';
document.querySelector('.process-tracker').style.display = 'flex';
document.querySelector('.task-manager').style.display = 'none';
document.querySelector('.information').style.display = 'none';
}

// Sample process tracker data for Submissions view
const submissionsData = [
{
    policyTitle: "Student Handbook",
    dateSubmitted: "10/24/24",
    version: "5.0",
    status: "Reviewed"
},
{
    policyTitle: "Organizational Profile",
    dateSubmitted: "03/03/24",
    version: "3.0",
    status: "Verified"
},
{
    policyTitle: "Student Dress Code",
    dateSubmitted: "06/07/24",
    version: "4.0",
    status: "Approved"
}
];

// Sample process tracker data for Feedbacks view
const feedbacksData = [
{
    policyReported: "Student Handbook",
    dateSubmitted: "10/24/24",
    status: "Reviewed"
},
{
    policyReported: "Organizational Profile",
    dateSubmitted: "03/03/24",
    status: "Submitted"
},
{
    policyReported: "Student Dress Code",
    dateSubmitted: "06/07/24",
    status: "Addressed"
}
];

function populateProcessTrackerTable(data, isFeedbackView = false) {
const tableBody = document.getElementById('processTrackerTableBody');
tableBody.innerHTML = ''; // Clear existing rows

data.forEach(item => {
    const row = tableBody.insertRow();

    if (isFeedbackView) {
        const policyCell = row.insertCell();
        policyCell.textContent = item.policyReported;

        const dateCell = row.insertCell();
        dateCell.textContent = item.dateSubmitted;

        const statusCell = row.insertCell();
        statusCell.textContent = item.status;
        statusCell.className = `status-${item.status.toLowerCase()}`; // Apply status class
    } else {
        const titleCell = row.insertCell();
        titleCell.textContent = item.policyTitle;

        const dateCell = row.insertCell();
        dateCell.textContent = item.dateSubmitted;

        const versionCell = row.insertCell();
        versionCell.textContent = item.version;

        const statusCell = row.insertCell();
        statusCell.textContent = item.status;
        statusCell.className = `status-${item.status.toLowerCase()}`; // Apply status class
    }
});
}

document.addEventListener('DOMContentLoaded', function() {
const submissionsButton = document.getElementById('processTrackerSub-btn');

// Initial population of the table with Submissions data
populateProcessTrackerTable(submissionsData);

if (submissionsButton) {
    submissionsButton.addEventListener('click', function() {
        if (this.textContent === 'Submissions') {
            this.textContent = 'Feedbacks';
            populateProcessTrackerTable(feedbacksData, true);
            // Update table headers for Feedbacks view
            const tableHeaderRow = document.querySelector('.process-tracker-table thead tr');
            tableHeaderRow.innerHTML = `
                <th>Policy Reported</th>
                <th>Date Submitted</th>
                <th>Status</th>
            `;
        } else {
            this.textContent = 'Submissions';
            populateProcessTrackerTable(submissionsData);
            // Update table headers for Submissions view
            const tableHeaderRow = document.querySelector('.process-tracker-table thead tr');
            tableHeaderRow.innerHTML = `
                <th>Policy Title</th>
                <th>Date Submitted</th>
                <th>Version no.</th>
                <th>Status</th>
            `;
        }
    });
}
});




//this is for the js of task manager

function showTaskManager() {
document.getElementById('policies-repository-content').style.display = 'none';
document.getElementById('policy-submission-content').style.display = 'none';
document.querySelector('.process-tracker').style.display = 'none';
document.querySelector('.task-manager').style.display = 'flex';
const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
const taskManagerTable = document.querySelector('.task-manager-table');
const introductionSection = document.querySelector('.introduction-section');
taskManagerHeaderContainer.style.display = 'block'; // Show header and line
taskManagerTable.style.display = 'table'; // Show the table initially
introductionSection.style.display = 'none'; // Ensure introduction is hidden initially
document.querySelector('.information').style.display = 'none';
}

function showIntroduction(policyTitle, policyContent) {
const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
const taskManagerTable = document.querySelector('.task-manager-table');
const introductionSection = document.querySelector('.introduction-section');
const introductionTitleElement = introductionSection.querySelector('.introduction-title');
const introductionContentElement = introductionSection.querySelector('.introduction-content');

introductionTitleElement.textContent = policyTitle;
introductionContentElement.textContent = policyContent;

taskManagerHeaderContainer.style.display = 'none'; // Hide header and line
taskManagerTable.style.display = 'none';
introductionSection.style.display = 'block';
}

function showTable() {
const taskManagerHeaderContainer = document.querySelector('.task-manager-header-container');
const taskManagerTable = document.querySelector('.task-manager-table');
const introductionSection = document.querySelector('.introduction-section');

taskManagerHeaderContainer.style.display = 'block';
taskManagerTable.style.display = 'table';
introductionSection.style.display = 'none';
}

function showReplyModal() {
const replyOverlay = document.getElementById('replyOverlay');
if (replyOverlay) {
    replyOverlay.style.display = 'flex';
}
}

function closeReplyModal() {
const replyOverlay = document.getElementById('replyOverlay');
if (replyOverlay) {
    replyOverlay.style.display = 'none';
}
}

function showConfirmReply() {
const confirmReplyOverlay = document.getElementById('confirmReplyOverlay');
if (confirmReplyOverlay) {
    confirmReplyOverlay.style.display = 'flex';
}
}

function closeConfirmReply() {
const confirmReplyOverlay = document.getElementById('confirmReplyOverlay');
if (confirmReplyOverlay) {
    confirmReplyOverlay.style.display = 'none';
}
}

function handleReplyConfirmation() {
alert("Reply Confirmed!");
closeConfirmReply();
closeReplyModal();
}

function showRevisionModal() {
const revisionOverlay = document.getElementById('revisionOverlay');
if (revisionOverlay) {
    revisionOverlay.style.display = 'flex';
}
}

function closeRevisionModal() {
const revisionOverlay = document.getElementById('revisionOverlay');
if (revisionOverlay) {
    revisionOverlay.style.display = 'none';
}
}

// Sample task data
const taskData = [
{
    policyTitle: "Student Handbook",
    author: "Taylor Swift",
    dateSubmitted: "10/24/24",
    version: "5.0",
    status: "For Verification",
    description: "This is the detailed description for the Student Handbook policy."
},
{
    policyTitle: "Organizational Profile",
    author: "Sabrina Carpenter",
    dateSubmitted: "03/03/24",
    version: "3.0",
    status: "For Approval",
    description: "Details about the Organizational Profile."
},
{
    policyTitle: "Student Dress Code",
    author: "Ariana Grande",
    dateSubmitted: "06/07/24",
    version: "4.0",
    status: "For Revision",
    description: "Information regarding the Student Dress Code."
},
{
    policyTitle: "Cafeteria Guidelines",
    author: "Chapell Roan",
    dateSubmitted: "08/28/24",
    version: "2.3",
    status: "Revision Request",
    description: "Guidelines for the Cafeteria."
}
];

function populateTaskTable(tasks) {
const tableBody = document.getElementById('taskTableBody');
tableBody.innerHTML = ''; // Clear any existing rows

tasks.forEach(task => {
    const row = tableBody.insertRow();
    row.onclick = function() {
        showIntroduction(task.policyTitle, task.description);
    };

    const titleCell = row.insertCell();
    titleCell.textContent = task.policyTitle;

    const authorCell = row.insertCell();
    authorCell.textContent = task.author;

    const dateCell = row.insertCell();
    dateCell.textContent = task.dateSubmitted;

    const versionCell = row.insertCell();
    versionCell.textContent = task.version;

    const statusCell = row.insertCell();
    statusCell.textContent = task.status;
});
}

document.addEventListener('DOMContentLoaded', function() {
const menuIcon = document.querySelector('.menu-icon');
const menuDropdown = document.querySelector('.menu-dropdown');
const replyButton = menuDropdown ? menuDropdown.querySelector('.dropdown-button:nth-child(1)') : null;
const replyOverlay = document.getElementById('replyOverlay');
const submitReplyButton = replyOverlay ? replyOverlay.querySelector('.reply-content .submit-reply-button') : null;
const confirmReplyOverlay = document.getElementById('confirmReplyOverlay');
const revisionButton = menuDropdown ? menuDropdown.querySelector('.dropdown-button:nth-child(2)') : null;
const revisionOverlay = document.getElementById('revisionOverlay');
const revisionPopupContent = document.getElementById('revisionPopupContent'); // Target the correct content div
const cancelRevisionButton = document.getElementById('cancelRevision');
const viewPolicyButton = document.getElementById('viewPolicyButton'); // Get the View Policy button
const policyContentPlaceholder = document.getElementById('policyContentPlaceholder'); // Get the placeholder
const introductionContent = document.querySelector('.introduction-content'); // Get the initial content
let isPolicyVisible = false;

if (menuIcon && menuDropdown) {
    menuIcon.addEventListener('click', function(event) {
        event.stopPropagation();
        menuDropdown.style.display = (menuDropdown.style.display === 'block') ? 'none' : 'block';
    });

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.header-actions')) {
            menuDropdown.style.display = 'none';
        }
    });
}

if (replyButton && replyOverlay) {
    replyButton.addEventListener('click', function() {
        menuDropdown.style.display = 'none';
        showReplyModal();
    });
}

if (submitReplyButton && confirmReplyOverlay) {
    submitReplyButton.addEventListener('click', function() {
        showConfirmReply();
    });
}

if (revisionButton && revisionOverlay && revisionPopupContent) { // Ensure revisionPopupContent exists
    revisionButton.addEventListener('click', function() {
        menuDropdown.style.display = 'none';
        showRevisionModal();
    });
}

if (cancelRevisionButton && revisionOverlay) {
    cancelRevisionButton.addEventListener('click', function() {
        revisionOverlay.style.display = 'none';
    });
}

const downloadChangeRequestButton = document.querySelector('.dropdown-button:nth-child(3)'); // Target the "Download Change Request Form" button
const downloadConfirmationOverlay = document.getElementById('downloadConfirmationOverlay');
const downloadConfirmNoButton = document.getElementById('downloadConfirmNo');
const downloadConfirmYesButton = document.getElementById('downloadConfirmYes');

if (downloadChangeRequestButton && downloadConfirmationOverlay) {
    downloadChangeRequestButton.addEventListener('click', function() {
        const menuDropdown = document.querySelector('.menu-dropdown');
        if (menuDropdown) {
            menuDropdown.style.display = 'none'; // Close the dropdown
        }
        downloadConfirmationOverlay.style.display = 'flex';
    });
}

if (downloadConfirmNoButton && downloadConfirmationOverlay) {
    downloadConfirmNoButton.addEventListener('click', function() {
        downloadConfirmationOverlay.style.display = 'none';
    });
}

if (downloadConfirmYesButton && downloadConfirmationOverlay) {
    downloadConfirmYesButton.addEventListener('click', function() {
        downloadConfirmationOverlay.style.display = 'none'; // Hide the confirmation
        alert('Download initiated!'); // Show the alert message
        // In a real application, you would trigger the download here
    });
}

// View Policy Button Functionality
if (viewPolicyButton && policyContentPlaceholder && introductionContent) {
    viewPolicyButton.addEventListener('click', function() {
        if (!isPolicyVisible) {
            introductionContent.style.display = 'none';
            policyContentPlaceholder.style.display = 'block';
            viewPolicyButton.textContent = 'View Feedback Report';
            isPolicyVisible = true;
        } else {
            introductionContent.style.display = 'block';
            policyContentPlaceholder.style.display = 'none';
            viewPolicyButton.textContent = 'View Policy';
            isPolicyVisible = false;
        }
    });
}

// Populate the task table on load
populateTaskTable(taskData);
});

//overlay for the submission confirmation in request for revision button

document.addEventListener('DOMContentLoaded', function () {
const submitRevisionConfirmationOverlay = document.getElementById('submitRevisionConfirmationOverlay');
const revisionConfirmNoButton = document.getElementById('revisionConfirmNo');
const revisionConfirmYesButton = document.getElementById('revisionConfirmYes');
const triggerButton = document.getElementById('submitRevision'); // Correctly target the submit button in the revision modal
const revisionOverlay = document.getElementById('revisionOverlay'); // Get the revision overlay

// Show the overlay
if (triggerButton && submitRevisionConfirmationOverlay && revisionOverlay) {
    triggerButton.addEventListener('click', function () {
        revisionOverlay.style.display = 'none'; // Hide the revision modal
        submitRevisionConfirmationOverlay.style.display = 'flex'; // Show the confirmation overlay
    });
}

// Hide the overlay when "No" is clicked
if (revisionConfirmNoButton && submitRevisionConfirmationOverlay) {
    revisionConfirmNoButton.addEventListener('click', function () {
        submitRevisionConfirmationOverlay.style.display = 'none';
        showRevisionModal();
    });
}

 if (revisionConfirmYesButton && submitRevisionConfirmationOverlay) {
    revisionConfirmYesButton.addEventListener('click', function () {
        submitRevisionConfirmationOverlay.style.display = 'none';
        alert('Submission confirmed!');
    });
}
});



// this is for the js of information
function showInformation() {
    document.getElementById('policies-repository-content').style.display = 'none';
    document.getElementById('policy-submission-content').style.display = 'none';
    document.querySelector('.process-tracker').style.display = 'none';
    document.querySelector('.task-manager').style.display = 'none';
    document.querySelector('.information').style.display = 'flex';
}


//also for information but the js for the clicking of folders

document.addEventListener('DOMContentLoaded', function() {
const policyRepositoryCategory = document.querySelector('.moduleCategory[data-category="policyRepository"]');
const nestedContent = document.querySelector('.nested-moduleSubcategory-content');
const expandIcon = policyRepositoryCategory.querySelector('.expand-icon');

policyRepositoryCategory.addEventListener('click', function() {
    if (nestedContent.style.display === 'none' || nestedContent.style.display === '') {
        nestedContent.style.display = 'block';
        policyRepositoryCategory.classList.add('expanded');
    } else {
        nestedContent.style.display = 'none';
        policyRepositoryCategory.classList.remove('expanded');
    }
});
});