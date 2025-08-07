# Parking Management System (PMS)

A web-based application for managing parking spots, user registrations, and parking reservations with real-time location tracking.

## Features

- **User Authentication**
  - Login/Logout for users and admins
  - Role-based access control (User/Admin)
  - Secure password hashing

- **User Features**
  - Dashboard with current time display
  - Find nearest parking spot using geolocation
  - Edit user profile (name, vehicle details)
  - View parking spot details on an interactive map

- **Admin Features**
  - Manage parking spots (add/edit/remove)
  - View usage statistics
  - Manage user accounts

- **Technical Features**
  - Dijkstra's algorithm for finding shortest path to parking
  - Real-time distance calculation using Haversine formula
  - Responsive Bootstrap-based UI
  - Secure session management

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5, Leaflet.js
- **Backend**: PHP, MySQL
- **APIs**: OpenStreetMap, Nominatim (for reverse geocoding)
