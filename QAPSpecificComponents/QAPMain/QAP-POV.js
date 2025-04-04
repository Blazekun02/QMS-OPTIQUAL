const greyLine = document.getElementById('grey-line');
const hamburgerIcon = document.getElementById('hamburger-icon');

    greyLine.addEventListener('mouseenter', () => {
        greyLine.classList.add('extended');
        hamburgerIcon.style.left = '2.5in';
    });

    greyLine.addEventListener('mouseleave', () => {
        greyLine.classList.remove('extended');
        hamburgerIcon.style.left = '0.8in';
    });

    hamburgerIcon.addEventListener('click', () => {
            const isExtended = greyLine.classList.toggle('extended');
            hamburgerIcon.style.left = isExtended ? '2.5in' : '0.8in';
        });



// <!-- JS for notif and sign out -->

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



// <!-- js for policy repository -->

    function showPoliciesRepository() {
        document.getElementById('policies-repository-content').style.display = 'flex';
    }
    document.querySelectorAll('.category').forEach(category => {
        category.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });

    document.querySelectorAll('.subcategory').forEach(subcategory => {
        subcategory.addEventListener('click', function() {
            const subcategoryName = this.getAttribute('data-subcategory');
            // Handle the subcategory click, e.g., display policies for the selected department
            console.log(`Subcategory clicked: ${subcategoryName}`);
            // You can add logic here to load and display the relevant policies
        });
    });



// <!-- js for filter -->

        const filterButton = document.getElementById('filterButton');
        const filterOverlay = document.getElementById('filterOverlay');
        const cancelFilter = document.getElementById('cancelFilter');
        const applyFilter = document.getElementById('applyFilter');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortFilter = document.getElementById('sortFilter');

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

}

// <!-- js for policy submission -->

    function showPolicySubmission() {
        document.getElementById('policies-repository-content').style.display = 'none';
        document.getElementById('policy-submission-content').style.display = 'flex';
    }

    const cfOverlay = document.getElementById('confirm-dl');
    const dlBtn = document.getElementById('.policy-submission-buttons button:first-child');

    dlBtn.addEventListener('click', () => {
        cfOverlay.style.display = cfOverlay.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById("first-child").addEventListener("click", function () {
        cfOverlay.style.display = "none";
    });

    document.getElementById("last-child").addEventListener("click", function () {
        cfOverlay.style.display = "none";
    });

    
