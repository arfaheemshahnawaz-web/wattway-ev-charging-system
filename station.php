<?php
require_once 'auth.php';
require_login('operator'); 
require_once 'db.php';

$operator_id = $_SESSION['user']['id'] ?? 0;

// Fetch all stations added by this operator
$stmt = $pdo->prepare("SELECT * FROM tbl_stations WHERE added_by = ? and status = 'active' ORDER BY station_id DESC");
$stmt->execute([$operator_id]);
$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stations_json = json_encode($stations);

$operator_name = $_SESSION['user']['name'] ?? 'Operator';
include 'partials/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container text-center hero-content">
        <h1 class="headline display-5 fw-bold">
            WattWay — 
            <span style="background:linear-gradient(90deg,var(--accent1),var(--accent2));
                         -webkit-background-clip:text;
                         -webkit-text-fill-color:transparent;">
                Welcome, <?php echo htmlspecialchars($operator_name); ?>!
            </span>
        </h1>
        <p class="hero-sub">Manage your EV charging stations, slots, and driver assignments professionally.</p>
    </div>
    <div class="floating-circles"><span></span><span></span><span></span><span></span><span></span></div>
</section>

<div class="container my-5">
    <button class="btn btn-brand mb-4" id="addStationBtn">Add New Station</button>

    <div class="row">
        <?php foreach($stations as $station): ?>
        <div class="col-lg-6 mb-4 station-card" data-id="<?php echo $station['station_id']; ?>">
            <div class="card glass p-3">
                <h5 class="fw-bold"><?php echo htmlspecialchars($station['station_name']); ?></h5>
                <p><?php echo htmlspecialchars($station['address']); ?></p>
                <p><strong>Plug:</strong> <?php echo htmlspecialchars($station['plug_type']); ?></p>
                <p><strong>Speed:</strong> <?php echo htmlspecialchars($station['charging_speed']); ?></p>
                <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($station['pricing']); ?>/kWh</p>

                <hr>
<button class="btn btn-sm btn-brand updateStationBtn mt-2" data-id="<?php echo $station['station_id']; ?>">Update</button>
<button class="btn btn-sm btn-brand viewFeedbackBtn mt-2" data-id="<?php echo $station['station_id']; ?>">View Feedback</button>
<button class="btn btn-sm btn-brand viewSlotsBtn mt-2" data-id="<?php echo $station['station_id']; ?>">View / Manage Slots</button>

            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ================== Modals ================== -->

<!-- Add Station Modal -->
<div class="modal fade" id="addStationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Add New Station</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Station Name</label><input type="text" id="new_station_name" class="form-control"></div>
        <div class="mb-3"><label>Address</label><input type="text" id="new_station_address" class="form-control"></div>
        <div class="mb-3">
            <label>Plug Type</label>
            <select id="new_station_plug" class="form-select">
                <option value="">Select Plug Type</option>
                <option value="Type1">Type 1</option>
                <option value="Type2">Type 2</option>
                <option value="CCS">CCS</option>
                <option value="CHAdeMO">CHAdeMO</option>
                <option value="GB/T">GB/T</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Charging Speed</label>
            <select id="new_station_speed" class="form-select">
                <option value="">Select Speed</option>
                <option value="Slow">Slow</option>
                <option value="Fast">Fast</option>
                <option value="Rapid">Rapid</option>
            </select>
        </div>
        <div class="mb-3"><label>Pricing (₹/kWh)</label><input type="number" id="new_station_price" class="form-control"></div>
        <div class="mb-3">
            <label>Latitude & Longitude</label>
            <div class="input-group">
                <input type="text" id="new_station_lat" class="form-control" placeholder="Latitude">
                <input type="text" id="new_station_lng" class="form-control" placeholder="Longitude">
                <button class="btn btn-outline-primary" id="useCurrentLocation">Use Current Location</button>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-brand" id="saveNewStation">Add Station</button>
      </div>
    </div>
  </div>
</div>

<!-- Update Station Modal -->
<div class="modal fade" id="updateStationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Update Station</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="update_station_id">
        <div class="mb-3"><label>Plug Type</label>
            <select id="update_station_plug" class="form-select">
                <option value="Type1">Type 1</option>
                <option value="Type2">Type 2</option>
                <option value="CCS">CCS</option>
                <option value="CHAdeMO">CHAdeMO</option>
                <option value="GB/T">GB/T</option>
            </select>
        </div>
        <div class="mb-3"><label>Charging Speed</label>
            <select id="update_station_speed" class="form-select">
                <option value="Slow">Slow</option>
                <option value="Fast">Fast</option>
                <option value="Rapid">Rapid</option>
            </select>
        </div>
        <div class="mb-3"><label>Pricing (₹/kWh)</label><input type="number" id="update_station_price" class="form-control"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-brand" id="saveUpdateStation">Update Station</button>
      </div>
    </div>
  </div>
</div>

<!-- View Feedback Modal -->
<div class="modal fade" id="viewFeedbackModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Station Feedback</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="feedbackList" class="list-group"></div>
      </div>
    </div>
  </div>
</div>

<!-- Slots Modal -->
<div class="modal fade" id="slotsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Manage Slots</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm table-bordered" id="slotsTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Available</th>
                    <th>Driver</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="mt-2">
            <input type="date" id="slotDate" class="form-control form-control-sm d-inline w-50">
            <input type="time" id="slotTime" class="form-control form-control-sm d-inline w-25">
            <button class="btn btn-sm btn-brand" id="addSlotModalBtn">Add Slot</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Assign Driver Modal (used inside slots modal only) -->
<div class="modal fade" id="assignDriverModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Assign Driver & Confirm OTP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="assign_slot_id">
        <div class="mb-3">
            <label>Driver</label>
            <select id="assign_driver" class="form-select"><option value="">Select Driver</option></select>
        </div>
        <div class="mb-3"><label>OTP</label><input type="text" id="assign_otp" class="form-control" placeholder="Enter OTP"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-brand" id="confirmAssignDriver">Confirm Assignment</button>
      </div>
    </div>
  </div>
</div>
<!-- Edit Slot Modal -->
<div class="modal fade" id="editSlotModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Edit Slot</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_slot_id">
        <div class="mb-3">
            <label>Date</label>
            <input type="date" id="edit_slot_date" class="form-control">
        </div>
        <div class="mb-3">
            <label>Time</label>
            <input type="time" id="edit_slot_time" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-brand" id="saveEditSlotBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>



<!-- ================== JS ================== -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const STATIONS = <?php echo $stations_json; ?>;

    // ---------- Add Station ----------
    const addStationModal = new bootstrap.Modal(document.getElementById('addStationModal'));
    document.getElementById('addStationBtn').addEventListener('click', () => addStationModal.show());

    document.getElementById('useCurrentLocation').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                document.getElementById('new_station_lat').value = pos.coords.latitude;
                document.getElementById('new_station_lng').value = pos.coords.longitude;
            }, () => alert('Could not get location'));
        } else alert('Geolocation not supported');
    });

    document.getElementById('saveNewStation').addEventListener('click', () => {
        const data = {
            station_name: document.getElementById('new_station_name').value,
            address: document.getElementById('new_station_address').value,
            plug_type: document.getElementById('new_station_plug').value,
            charging_speed: document.getElementById('new_station_speed').value,
            pricing: document.getElementById('new_station_price').value,
            latitude: document.getElementById('new_station_lat').value,
            longitude: document.getElementById('new_station_lng').value
        };
        axios.post('api/add_station.php', data).then(res => {
            alert(res.data.message);
            if (res.data.success) location.reload();
        });
    });

    // ---------- Update Station ----------
    const updateModal = new bootstrap.Modal(document.getElementById('updateStationModal'));
    document.addEventListener('click', e => {
        if (e.target.classList.contains('updateStationBtn')) {
            const stationId = e.target.dataset.id;
            const station = STATIONS.find(s => s.station_id == stationId);
            if (!station) return;
            document.getElementById('update_station_id').value = station.station_id;
            document.getElementById('update_station_plug').value = station.plug_type;
            document.getElementById('update_station_speed').value = station.charging_speed;
            document.getElementById('update_station_price').value = station.pricing;
            updateModal.show();
        }
    });

    document.getElementById('saveUpdateStation').addEventListener('click', () => {
        const data = {
            station_id: document.getElementById('update_station_id').value,
            plug_type: document.getElementById('update_station_plug').value,
            charging_speed: document.getElementById('update_station_speed').value,
            pricing: document.getElementById('update_station_price').value
        };
        axios.post('api/update_station.php', data).then(res => {
            alert(res.data.message);
            if (res.data.success) location.reload();
        });
    });

    // ---------- View Feedback ----------
    const feedbackModal = new bootstrap.Modal(document.getElementById('viewFeedbackModal'));
    document.addEventListener('click', e => {
        if (e.target.classList.contains('viewFeedbackBtn')) {
            const stationId = e.target.dataset.id;
            axios.get(`api/get_feedback.php?station_id=${stationId}`).then(res => {
                const feedbackList = document.getElementById('feedbackList');
                feedbackList.innerHTML = '';
                if (res.data.length === 0) {
                    feedbackList.innerHTML = '<p class="text-muted text-center">No feedback yet.</p>';
                } else {
                    res.data.forEach(fb => {
                        const stars = fb.rating ? '⭐'.repeat(fb.rating) : '';
                        feedbackList.insertAdjacentHTML('beforeend', `
                            <div class="list-group-item">
                                <h6>${fb.user_name || 'Anonymous'}</h6>
                                <p>${fb.feedback_text}</p>
                                <small class="text-muted">${stars} — ${new Date(fb.created_at).toLocaleString()}</small>
                            </div>
                        `);
                    });
                }
                feedbackModal.show();
            });
        }
    });

    // ---------- Slots ----------
    const slotsModal = new bootstrap.Modal(document.getElementById('slotsModal'));

    function openSlotModal(stationId) {
        slotsModal.show();
        const tbody = document.querySelector('#slotsTable tbody');
        tbody.dataset.stationId = stationId;

        axios.get(`api/get_slots.php?station_id=${stationId}`)
            .then(res => {
                tbody.innerHTML = '';
                res.data.forEach(slot => {
                    const driver = slot.driver_name || '-';
                    const available = slot.is_available === 'Yes' ? 'Yes' : 'No';

                    let actions = '';
                    if (slot.booking_id && !slot.assigned_driver_id) {
                        actions = `<button class="btn btn-sm btn-info assignDriverBtn" data-slot-id="${slot.availability_id}" data-booking-otp="${slot.booking_otp}">Confirm & Assign</button>`;
                    } else if (slot.assigned_driver_id) {
                        actions = `<button class="btn btn-sm btn-danger removeSlotBtn" data-slot-id="${slot.availability_id}">Remove Slot</button>`;
                    } else {
                        actions = `<button class="btn btn-sm btn-warning editSlotBtn" 
                                       data-slot-id="${slot.availability_id}" 
                                       data-date="${slot.visit_date}" 
                                       data-time="${slot.slot_time}">Edit</button>
                                   <button class="btn btn-sm btn-danger deleteSlotBtn" data-slot-id="${slot.availability_id}">Delete</button>`;
                    }

                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${slot.visit_date}</td>
                            <td>${slot.slot_time}</td>
                            <td>${available}</td>
                            <td>${driver}</td>
                            <td>${actions}</td>
                        </tr>
                    `);
                });
            })
            .catch(err => console.error(err));
    }

    document.addEventListener('click', e => {
        if (e.target.classList.contains('viewSlotsBtn')) {
            const stationId = e.target.dataset.id;
            openSlotModal(stationId);
        }
    });

    // ---------- Slot Actions (Add/Edit/Delete/Assign) ----------
    document.getElementById('addSlotModalBtn').addEventListener('click', () => {
        const tbody = document.querySelector('#slotsTable tbody');
        const stationId = tbody.dataset.stationId;
        const date = document.getElementById('slotDate').value;
        let time = document.getElementById('slotTime').value;

        if (!date || !time) return alert('Select date & time');
        if (time.length === 5) time += ':00';

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('station_id', stationId);
        formData.append('visit_date', date);
        formData.append('slot_time', time);

        axios.post('api/add_slot.php', formData)
            .then(res => {
                alert(res.data.message);
                if (res.data.success) {
                    document.getElementById('slotDate').value = '';
                    document.getElementById('slotTime').value = '';
                    openSlotModal(stationId);
                }
            }).catch(err => console.error(err));
    });

    document.addEventListener('click', e => {
        const btn = e.target;

        // Delete Slot
        if (btn.classList.contains('deleteSlotBtn')) {
            if (!confirm('Are you sure you want to delete this slot?')) return;
            const slotId = btn.dataset.slotId;
            const formData = new FormData();
            formData.append('availability_id', slotId);

            axios.post('api/delete_slot.php', formData)
                .then(res => {
                    alert(res.data.message);
                    const stationId = document.querySelector('#slotsTable tbody').dataset.stationId;
                    openSlotModal(stationId);
                }).catch(err => console.error(err));
        }

        // Remove Slot
        if (btn.classList.contains('removeSlotBtn')) {
            if (!confirm('Are you sure you want to remove this slot?')) return;
            const slotId = btn.dataset.slotId;
            const formData = new FormData();
            formData.append('availability_id', slotId);

            axios.post('api/remove_slot.php', formData)
                .then(res => {
                    alert(res.data.message);
                    const stationId = document.querySelector('#slotsTable tbody').dataset.stationId;
                    openSlotModal(stationId);
                }).catch(err => console.error(err));
        }

        // Edit Slot → Open Modal
        if (btn.classList.contains('editSlotBtn')) {
            const slotId = btn.dataset.slotId;
            const date = btn.dataset.date;
            const time = btn.dataset.time;

            document.getElementById('edit_slot_id').value = slotId;
            document.getElementById('edit_slot_date').value = date;
            document.getElementById('edit_slot_time').value = time;

            new bootstrap.Modal(document.getElementById('editSlotModal')).show();
        }

        // Assign Driver → Open Modal
        if (btn.classList.contains('assignDriverBtn')) {
            const slotId = btn.dataset.slotId;
            document.getElementById('assign_slot_id').value = slotId;
            document.getElementById('assign_otp').value = '';

            const driverSelect = document.getElementById('assign_driver');
            driverSelect.innerHTML = '<option value="">Select Driver</option>';

            axios.get(`api/get_drivers.php?slot_id=${slotId}`)
                .then(res => {
                    res.data.forEach(d => {
                        driverSelect.insertAdjacentHTML(
                            'beforeend',
                            `<option value="${d.driver_id}" data-otp="${d.otp || ''}">${d.name}</option>`
                        );
                    });
                    new bootstrap.Modal(document.getElementById('assignDriverModal')).show();
                })
                .catch(err => {
                    console.error(err);
                    alert('Failed to fetch drivers');
                });
        }
    });

    // ---------- Save Edited Slot ----------
    document.getElementById('saveEditSlotBtn').addEventListener('click', () => {
        const slotId = document.getElementById('edit_slot_id').value;
        let date = document.getElementById('edit_slot_date').value;
        let time = document.getElementById('edit_slot_time').value;

        if (!date || !time) return alert('Select date & time');
        if (time.length === 5) time += ':00';

        const formData = new FormData();
        formData.append('availability_id', slotId);
        formData.append('visit_date', date);
        formData.append('slot_time', time);

        axios.post('api/update_slot.php', formData)
            .then(res => {
                alert(res.data.message);
                const stationId = document.querySelector('#slotsTable tbody').dataset.stationId;
                openSlotModal(stationId);
                bootstrap.Modal.getInstance(document.getElementById('editSlotModal')).hide();
            }).catch(err => console.error(err));
    });

    // ---------- Assign Driver ----------
    document.getElementById('assign_driver').addEventListener('change', function () {
        const otp = this.options[this.selectedIndex].dataset.otp || '';
        document.getElementById('assign_otp').value = otp;
    });

    document.getElementById('confirmAssignDriver').addEventListener('click', () => {
        const slotId = document.getElementById('assign_slot_id').value.trim();
        const driverId = document.getElementById('assign_driver').value.trim();
        const otp = document.getElementById('assign_otp').value.trim();

        if (!slotId || !driverId || !otp) return alert('Please select a driver and enter OTP');

        const slotBtn = document.querySelector(`.assignDriverBtn[data-slot-id="${slotId}"]`);
        const bookingOtp = slotBtn.dataset.bookingOtp;

        if (otp !== bookingOtp) return alert('OTP does not match the booking OTP');

        axios.post('api/confirm_driver.php', { availability_id: slotId, driver_id: driverId, otp })
            .then(res => {
                alert(res.data.message);
                if (res.data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('assignDriverModal')).hide();
                    const stationId = document.querySelector('#slotsTable tbody').dataset.stationId;
                    openSlotModal(stationId);
                }
            }).catch(err => console.error(err));
    });

});
</script>


<?php include 'partials/footer.php'; ?>
