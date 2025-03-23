const SelectionPane = document.getElementById('Selection_pane');
const hamburgerIcon = document.getElementById('hamburger-icon');

SelectionPane.addEventListener('mouseenter', () => {
    SelectionPane.classList.add('extended');
    hamburgerIcon.style.left = '2.5in';
});

SelectionPane.addEventListener('mouseleave', () => {
    SelectionPane.classList.remove('extended');
    hamburgerIcon.style.left = '0.8in';
});

hamburgerIcon.addEventListener('click', () => {
    const isExtended = SelectionPane.classList.toggle('extended');
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



