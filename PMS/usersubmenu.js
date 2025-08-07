
    document.addEventListener("DOMContentLoaded", function() {
        const userManagementLink = document.getElementById('userManagementLink');
        const userManagementSubmenu = document.getElementById('userManagementSubmenu');

        userManagementLink.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default anchor behavior
            // Toggle the display property
            if (userManagementSubmenu.style.display === 'none' || userManagementSubmenu.style.display === '') {
                userManagementSubmenu.style.display = 'flex';
            } else {
                userManagementSubmenu.style.display = 'none';
            }
        });
    });
