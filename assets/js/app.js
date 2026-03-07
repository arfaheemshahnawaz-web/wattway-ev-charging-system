
window.WattWay = (() => {
  const fetchJSON = (url, opts={}) => fetch(url, opts).then(r => r.json());
  function renderStationList(el, stations) {
    el.innerHTML = stations.map(s => `
      <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-semibold">${s.name}</div>
          <div class="small text-muted">${s.address}</div>
          <div class="small"><span class="badge text-bg-dark me-1">${s.plug_type}</span><span class="badge text-bg-secondary">${s.watt_kw} kW</span>
          ${s.approved==1?'<span class="badge badge-approve ms-1">Approved</span>':'<span class="badge badge-pending ms-1">Pending</span>'}
          </div>
        </div>
        ${s.station_id ? `<a class="btn btn-sm btn-outline-light" target="_blank" href="https://www.google.com/maps?q=${s.lat},${s.lng}">Open</a>`:''}
      </div>`).join('') || '<div class="text-muted">No records</div>';
  }

  function makeMap(divId) {
    const map = L.map(divId).setView([20.5937, 78.9629], 5); // India
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' })
      .on('tileload', function(){ // when first tile loads remove loading class
        const el = document.getElementById(divId);
        if(el) el.classList.remove('loading');
      }).addTo(map);
    return map;
  }

  // create a custom pulsing icon by using a divIcon
  function createPulseIcon(){
    const html = '<div class="custom-marker"><div class="pulse"></div></div>';
    return L.divIcon({ className: '', html: html, iconSize: [46,46], iconAnchor: [23,46]});
  }

  function applyFilters(baseUrl, controls) {
    const q = new URLSearchParams();
    let searchEl = null, plugEl = null, wattEl = null;
    try {
      if (controls && controls.searchInput) searchEl = document.querySelector(controls.searchInput);
      if (controls && controls.plugSelect) plugEl = document.querySelector(controls.plugSelect);
      if (controls && controls.wattSelect) wattEl = document.querySelector(controls.wattSelect);
    } catch(e) { /* ignore invalid selectors */ }
    if (searchEl?.value) q.set('q', searchEl.value);
    if (plugEl?.value) q.set('plug', plugEl.value);
    if (wattEl?.value) q.set('watt', wattEl.value);
    return baseUrl + (q.toString() ? ('?' + q.toString()) : '');
  }

  function initHomeMap(divId, apiUrl, controls) {
    const map = makeMap(divId);
    let markersLayer = L.layerGroup().addTo(map);
    const icon = createPulseIcon();
    const load = () => {
      fetchJSON(applyFilters(apiUrl, controls)).then(data => {
        markersLayer.clearLayers();
        data.forEach(s => {
          const m = L.marker([s.lat, s.lng], {icon}).addTo(markersLayer);
          m.bindPopup(`<b>${s.name}</b><br>${s.address}<br><span class="badge text-bg-dark">${s.plug_type}</span> <span class="badge text-bg-secondary">${s.watt_kw} kW</span>`);
        });
        if (data.length) map.fitBounds(data.map(s => [s.lat, s.lng]), { padding: [40,40] });
        setTimeout(()=>{ try{ map.invalidateSize(); }catch(e){} }, 600);
      }).catch(err => console.error(err));
    };
    document.querySelector(controls.filterBtn).addEventListener('click', load);
    load();
  }

  function initDriverMap(divId, apiUrl, controls) {
    const map = makeMap(divId);
    let markersLayer = L.layerGroup().addTo(map);
    let routingControl = null;
    const startRouting = (from, to) => {
      if (routingControl) map.removeControl(routingControl);
      routingControl = L.Routing.control({ waypoints: [L.latLng(from[0], from[1]), L.latLng(to[0], to[1])], routeWhileDragging: false }).addTo(map);
    };
    let myLoc = null;
    const icon = createPulseIcon();
    const load = () => {
      fetchJSON(applyFilters(apiUrl, controls)).then(data => {
        markersLayer.clearLayers();
        data.forEach(s => {
          const m = L.marker([s.lat, s.lng], {icon}).addTo(markersLayer);
          m.bindPopup(`<b>${s.name}</b><br>${s.address}<br><button class="btn btn-sm btn-cta mt-1" data-lat="${s.lat}" data-lng="${s.lng}">Route here</button>`);
          m.on('popupopen', () => {
            const btn = document.querySelector('.leaflet-popup-content button');
            if (btn) btn.addEventListener('click', () => {
              if (!myLoc) alert('Click "Use my location" first.');
              else startRouting(myLoc, [parseFloat(btn.dataset.lat), parseFloat(btn.dataset.lng)]);
            });
          });
        });
        if (data.length) map.fitBounds(data.map(s => [s.lat, s.lng]), { padding: [40,40] });
        setTimeout(()=>{ try{ map.invalidateSize(); }catch(e){} }, 600);
      }).catch(err => console.error(err));
    };
    document.querySelector(controls.filterBtn).addEventListener('click', load);
    document.querySelector(controls.locateBtn).addEventListener('click', () => {
      map.locate({ setView: true, maxZoom: 13 });
    });
    map.on('locationfound', (e) => {
      myLoc = [e.latlng.lat, e.latlng.lng];
      L.marker(e.latlng).addTo(map).bindPopup('You are here').openPopup();
    });
    map.on('locationerror', () => alert('Unable to access location.'));
    load();
  }

  function initStationDashboard() {
    const form = document.getElementById('stationForm');
    const msg = document.getElementById('stationMsg');
    const listEl = document.getElementById('myStations');
    const loadMine = () => fetchJSON('api/my_stations.php').then(data => renderStationList(listEl, data));
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      fetch('api/register_station.php', { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
          msg.innerHTML = `<div class="alert ${res.ok?'alert-success':'alert-danger'}">${res.msg}</div>`;
          if (res.ok) { form.reset(); loadMine(); }
        });
    });
    loadMine();
  }

  function initAdminDashboard() {
    const pendingEl = document.getElementById('pendingStations');
    const allEl = document.getElementById('allStations');
    const drvEl = document.getElementById('driversList');
    const opEl = document.getElementById('operatorsList');
    const load = () => {
      fetchJSON('api/admin_list.php?type=pending').then(data => {
        pendingEl.innerHTML = data.map(s => `
          <div class="border rounded p-2 mb-2">
            <div class="d-flex justify-content-between">
              <div><b>${s.name}</b> <span class="badge text-bg-secondary">${s.watt_kw} kW</span> <span class="badge text-bg-dark">${s.plug_type}</span><br><span class="small text-muted">${s.address}</span></div>
              <div class="d-flex gap-1">
                <button data-id="${s.station_id}" data-action="approve" class="btn btn-sm btn-success">Approve</button>
                <button data-id="${s.station_id}" data-action="reject" class="btn btn-sm btn-danger">Reject</button>
              </div>
            </div>
          </div>`).join('') || '<div class="text-muted">No pending stations</div>';
        pendingEl.querySelectorAll('button').forEach(btn => btn.addEventListener('click', () => {
          const fd = new FormData(); fd.append('id', btn.dataset.id); fd.append('action', btn.dataset.action);
          fetch('api/admin_action.php', { method:'POST', body: fd }).then(()=>load());
        }));
      });
      fetchJSON('api/admin_list.php?type=allstations').then(data => renderStationList(allEl, data));
      fetchJSON('api/admin_list.php?type=drivers').then(data => {
        drvEl.innerHTML = data.map(u => `<div class="border rounded p-2 mb-2"><b>${u.name}</b><br><span class="small text-muted">${u.email}</span></div>`).join('');
      });
      fetchJSON('api/admin_list.php?type=operators').then(data => {
        opEl.innerHTML = data.map(u => `<div class="border rounded p-2 mb-2"><b>${u.name}</b><br><span class="small text-muted">${u.email}</span></div>`).join('');
      });
    };
    load();
  }

  return { initHomeMap, initDriverMap, initStationDashboard, initAdminDashboard };
})();

// Auto-initialize home map if present (provides graceful default controls)
document.addEventListener('DOMContentLoaded', () => {
  try{
    const el = document.getElementById('homeMap');
    if (el && window.WattWay) {
      // call initHomeMap with no controls (applyFilters can handle it)
      window.WattWay.initHomeMap?.('homeMap', 'api/get_stations.php', {});
    }
  } catch(e){ console.warn('auto init home map failed', e); }
});
