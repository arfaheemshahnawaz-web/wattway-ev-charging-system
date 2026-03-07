<?php
require_once 'db.php'; // Ensure database connection is included

// 1. FETCH APPROVED STATION DATA
// Only select stations that have been approved ('active')
$stmt = $pdo->prepare("
    SELECT station_id, station_name, address, latitude, longitude, plug_type, charging_speed, pricing 
    FROM tbl_stations 
    WHERE status = 'active'
");
$stmt->execute();
$approved_stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Encode the PHP array into a JSON string for JavaScript consumption
$stations_json = json_encode($approved_stations);

?>
<?php include 'partials/header.php'; ?>
<head>
<!-- Leaflet CSS -->
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    </head>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20n6a32kM6s51V1pT0qJ8g612tVp6bH7w0N7F0K+8qU="
    crossorigin=""></script>
<div id="preloader">
    <div class="preloader__box"></div>
</div>

<section class="hero py-5">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <div class="animate__animated animate__fadeInLeft">
                    <h1 class="headline display-5 fw-bold">WattWay — <span style="background:linear-gradient(90deg,var(--accent1),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent">Powering the Future of EV Travel</span></h1>
                    <p class="sub mt-3">Discover verified charging stations, plan routes, and manage charging locations — all in a beautiful, fast interface.</p>
                    <div class="mt-4 d-flex gap-2">
                        <a class="btn btn-cta" href="register_driver.php">Join as Driver</a>
                        <a class="btn btn-cta" href="register_station.php">Register Station</a>
                    </div>
                    <div class="mt-4 small text-muted">Tip: Click a role card to learn more and quick-login.</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div id="homeMap" class="loading"></div>
            </div>
        </div>
    </div>
</section>

<section class="roles py-5">
    <div class="container">
        <h2 class="text-center mb-5 animate__animated animate__fadeInUp">Choose your role</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="role-card" data-tilt data-tilt-scale="1.02" onclick="location.href='register_driver.php'">
                    <div class="icon mb-2">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path fill="white" d="M5 11h14v2H5zM3 7h2v10H3zM19 7h2v10h-2z"/></svg>
                    </div>
                    <h3>Driver</h3>
                    <p>Find nearby charging stations, filter by plug type and power, and get turn-by-turn directions directly from your location.</p>
                    <div class="mt-3 text-center"><a class="btn btn-cta" href="login.php?role=driver">Get started</a></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="role-card" data-tilt data-tilt-scale="1.02" onclick="location.href='register_station.php'">
                    <div class="icon mb-2" style="background:linear-gradient(135deg,var(--accent3),var(--accent4));">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path fill="white" d="M6 2h9a1 1 0 011 1v2h2v13a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1zM8 6h5v2H8z"/></svg>
                    </div>
                    <h3>Station Operator</h3>
                    <p>Register and manage charging points. Submit stations for admin approval and monitor bookings and details.</p>
                    <div class="mt-3 text-center"><a class="btn btn-cta" href="login.php?role=operator">Get started</a></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="role-card" data-tilt data-tilt-scale="1.02" onclick="location.href='login.php?role=admin'">
                    <div class="icon mb-2" style="background:linear-gradient(135deg,#7950f2,#ff6a00);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path fill="white" d="M12 2a7 7 0 00-7 7v4H4l4 6h8l4-6h-1V9a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3>Admin</h3>
                    <p>Approve or reject stations, manage users, and keep the platform running smoothly and safely.</p>
                    <div class="mt-3 text-center"><a class="btn btn-cta" href="login.php?role=admin">Get started</a></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="card glass p-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h4 class="mb-2">Why WattWay?</h4>
                    <p class="small text-muted">Fast, reliable, and designed for EV drivers and operators. Real-time maps, powerful filters and an approval workflow for verified stations.</p>
                </div>
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="p-3" style="min-width:180px">
                            <div class="fw-semibold">Find Quickly</div>
                            <div class="small text-muted">Search by address, name or plug type.</div>
                        </div>
                        <div class="p-3" style="min-width:180px">
                            <div class="fw-semibold">Plan Route</div>
                            <div class="small text-muted">Get directions from your current location to a station.</div>
                        </div>
                        <div class="p-3" style="min-width:180px">
                            <div class="fw-semibold">Secure Approvals</div>
                            <div class="small text-muted">Operators submit stations and admins approve to ensure quality.</div>
                        </div>
                        <div class="p-3" style="min-width:180px">
                            <div class="fw-semibold">Modern UI</div>
                            <div class="small text-muted">Animations, glassmorphism and responsive layouts for a premium feel.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // 2. PASS PHP DATA TO JAVASCRIPT
    const APPROVED_STATIONS_DATA = <?php echo $stations_json; ?>;
    let homeMap = null; // Variable to hold the map object
    
    function initMap() {
        const mapEl = document.getElementById('homeMap');
        if (!mapEl) return;

        // 3. MAP INITIALIZATION (Using a generic library example like Leaflet)
        
        // Define default center (e.g., a relevant city center or default view)
        const defaultCenter = [20.5937, 78.9629]; // Example: Center of India (Latitude, Longitude)
        
        // Initialize the map (assuming Leaflet is included)
        // If you are using Google Maps, replace this with your Google Maps initialization code.
        homeMap = L.map('homeMap').setView(defaultCenter, 5); // 5 is a zoom level

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(homeMap);

        // Remove loading class once map is initialized
        mapEl.classList.remove('loading');


        // 4. ADD MARKERS FOR APPROVED STATIONS
        APPROVED_STATIONS_DATA.forEach(station => {
            const lat = parseFloat(station.latitude);
            const lng = parseFloat(station.longitude);

            if (lat && lng) {
                // Create the popup content
                const popupContent = `
                    <h6>${station.station_name}</h6>
                    <p>${station.address}</p>
                    <small>Plug: ${station.plug_type} | Speed: ${station.charging_speed}</small>
                    <br><small>Price: $${parseFloat(station.pricing).toFixed(2)}/kWh</small>
                    <br><a href="station_details.php?id=${station.station_id}">View Details</a>
                `;

                L.marker([lat, lng])
                    .addTo(homeMap)
                    .bindPopup(popupContent);
            }
        });
        
        // Optionally adjust map bounds to fit all markers if needed
        if (APPROVED_STATIONS_DATA.length > 0) {
             const bounds = APPROVED_STATIONS_DATA.map(s => [parseFloat(s.latitude), parseFloat(s.longitude)]).filter(c => !isNaN(c[0]));
             if (bounds.length > 0) {
                 homeMap.fitBounds(bounds, { padding: [50, 50] });
             }
        }
    }


    // DOM Content Loaded Handler
    document.addEventListener('DOMContentLoaded', function(){
        // Initialize VanillaTilt for role cards
        if(window.VirtualTilt === undefined && window.VanillaTilt){
            document.querySelectorAll('[data-tilt]').forEach(el => VanillaTilt.init(el, { max: 12, speed: 450, glare: true, 'max-glare': 0.18 }));
        }

        // Initialize Map
        initMap();
    });
</script>

<?php include 'partials/footer.php'; ?>