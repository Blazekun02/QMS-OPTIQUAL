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

    

// Role Manager

function showDepartmentManager() {
    const departmentPanel = document.querySelector('.Department-Manager-Panel');
    
    // Toggle visibility
    if (departmentPanel.style.display === 'none' || departmentPanel.style.display === '') {
        departmentPanel.style.display = 'block';
    } else {
        departmentPanel.style.display = 'none';
    }
}

// Attach the function to the sidebar menu item
document.querySelector('.menu-icons:nth-child(6)').addEventListener('click', showDepartmentManager);