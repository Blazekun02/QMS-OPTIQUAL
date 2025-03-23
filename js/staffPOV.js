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



    <!-- js for filter -->
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

2

    function showProcessTracker() {
        document.getElementById('policies-repository-content').style.display = 'none';
        document.getElementById('policy-submission-content').style.display = 'none';
        document.querySelector('.process-tracker').style.display = 'flex';
    }


    document.querySelector('.process-tracker').style.display = 'none';

document.addEventListener('DOMContentLoaded', function() {
  const submissionsButton = document.getElementById('processTrackerSub-btn');
  const table = document.querySelector('.process-tracker-table');

  if (submissionsButton && table) {
    submissionsButton.addEventListener('click', function() {
      if (this.textContent === 'Submissions') {
        this.textContent = 'Feedbacks';

        table.innerHTML = `
          <tHead>
            <tr>
              <th>Policy Reported</th>
              <th>Date Submitted</th>
              <th>Status</th>
            </tr>
          </tHead>
          <tBody>
            <tr>
              <td>Student Handbook</td>
              <td>10/24/24</td>
              <td class="status-reviewed">Reviewed</td>
            </tr>
            <tr>
              <td>Organizational Profile</td>
              <td>03/03/24</td>
              <td class="status-verified">Verified</td>
            </tr>
            <tr>
              <td>Student Dress Code</td>
              <td>06/07/24</td>
              <td class="status-approved">Approved</td>
            </tr>
          </tBody>
        `;
      } else {
        this.textContent = 'Submissions';
        // Change table back to original columns
        table.innerHTML = `
          <tHead>
            <tr>
              <th>Policy Title</th>
              <th>Date Submitted</th>
              <th>Version no.</th>
              <th>Status</th>
            </tr>
          </tHead>
          <tBody>
            <tr>
              <td>Student Handbook</td>
              <td>10/24/24</td>
              <td>5.0</td>
              <td class="status-reviewed">Reviewed</td>
            </tr>
            <tr>
              <td>Organizational Profile</td>
              <td>03/03/24</td>
              <td>3.0</td>
              <td class="status-verified">Verified</td>
            </tr>
            <tr>
              <td>Student Dress Code</td>
              <td>06/07/24</td>
              <td>4.0</td>
              <td class="status-approved">Approved</td>
            </tr>
          </tBody>
        `;
      }
    });
  }
});

