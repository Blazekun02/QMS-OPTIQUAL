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

    document.getElementById('signOutLink').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent default link behavior
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/qms_optiqual/auth/sign_out/signoutBE.php';
    document.body.appendChild(form);
    form.submit();
});

//Show Modules
    const policyRepositoryPanel = document.getElementById('policy-repo-content');
    const policySubmissionPanel = document.getElementById('policy-submission-content');
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    const processTrackerPanel = document.querySelector('.Process-Tracker-Panel2');
    const policyManagerPanel = document.querySelector('.Policy-Manager-Panel');

        // Department Manager
    function showDepartmentManager() {    
        policyRepositoryPanel.style.display = 'none';
        policySubmissionPanel.style.display = 'none';
        departmentPanel.style.display = 'block';
        policyManagerPanel.style.display = 'none';
        processTrackerPanel.style.display = 'none';
 
    }

    // Policy Repository
    function showPolicyRepository() {
        console.log("Policy Repository Triggered");
        policyRepositoryPanel.style.display = 'block';
        policySubmissionPanel.style.display = 'none';
        processTrackerPanel.style.display = 'none';
        departmentPanel.style.display = 'none'; 
        policyManagerPanel.style.display = 'none';
    }

    // Policy Submission
    function showPolicySubmission() {
        console.log("Policy Submission Triggered");
        policyRepositoryPanel.style.display = 'none';
        policySubmissionPanel.style.display = 'flex';
        processTrackerPanel.style.display = 'none';
        departmentPanel.style.display = 'none';
        policyManagerPanel.style.display = 'none';
    }

    // Process Tracker
    function showProcessTracker() {
        policyRepositoryPanel.style.display = 'none';
        policySubmissionPanel.style.display = 'none';
        processTrackerPanel.style.display = 'block';
        departmentPanel.style.display = 'none'; 
        policyManagerPanel.style.display = 'none';
        
    }

