</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanilla-tilt@1.7.2/dist/vanilla-tilt.min.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
// Hide preloader after page fully loads
window.addEventListener("load", function(){
  const pre = document.getElementById("preloader");
  if(pre) { pre.classList.add("preloader--hidden"); setTimeout(()=>pre.remove(),600); }
});
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
