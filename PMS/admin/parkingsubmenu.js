document.addEventListener("DOMContentLoaded", function() {
    const parkingManagementLink = document.getElementById('parkingManagementLink');
    const parkingManagementSubmenu = document.getElementById('parkingManagementSubmenu');

    parkingManagementLink.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default link behavior
        console.log("Parking Management Link clicked"); // Debugging line

        // Check if submenu is visible
        const isVisible = parkingManagementSubmenu.style.display === 'block';
        console.log("Current submenu visibility: " + parkingManagementSubmenu.style.display);

        // Toggle the submenu visibility
        parkingManagementSubmenu.style.display = isVisible ? 'none' : 'block';
        console.log("New submenu visibility: " + parkingManagementSubmenu.style.display); // Debugging line
    });
});
