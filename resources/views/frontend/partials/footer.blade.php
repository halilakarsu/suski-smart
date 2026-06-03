<footer class="footer">
	<div class="container-fluid">
		<nav class="pull-left">
			<ul class="nav">
				<li class="nav-item">
					<a class="nav-link" href="https://suski.gov.tr">
						SMART ŞUSKİ PROJESİ Şuski Bilgi İşlem Daire Başkanlığı tarafın dan hazırlanmıştır
					</a>
				</li>

			</ul>
		</nav>
		<div class="copyright ml-auto">
			2026, made with <i class="fa fa-heart heart text-danger"></i> by <a href="https://www.suski.gov.tr">ŞUSKİ
			</a>
		</div>
	</div>
</footer>
</div>


</div>
<!--   Core JS Files   -->
<script src="/frontend/assets/js/core/jquery.3.2.1.min.js"></script>
<script src="/frontend/assets/js/core/popper.min.js"></script>
<script src="/frontend/assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="/frontend/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="/frontend/assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="/frontend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>


<!-- Chart JS -->
<script src="/frontend/assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="/frontend/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="/frontend/assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="/frontend/assets/js/plugin/datatables/datatables.min.js"></script>



<!-- jQuery Vector Maps -->
<script src="/frontend/assets/js/plugin/jqvmap/jquery.vmap.min.js"></script>
<script src="/frontend/assets/js/plugin/jqvmap/maps/jquery.vmap.world.js"></script>

<!-- Sweet Alert -->
<script src="/frontend/assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Atlantis JS -->
<script src="/frontend/assets/js/atlantis.min.js"></script>

<script>
	// Toast Mesajı Tanımlama (SweetAlert2)
	const Toast = Swal.mixin({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.addEventListener('mouseenter', Swal.stopTimer)
			toast.addEventListener('mouseleave', Swal.resumeTimer)
		}
	});

	// Laravel Session Success Mesajı
	@if (session('success'))
		Toast.fire({
			icon: 'success',
			title: '{{ session('success') }}'
		});
	@endif

	// Laravel Session Error Mesajı
	@if (session('error'))
		Toast.fire({
			icon: 'error',
			title: '{{ session('error') }}'
		});
	@endif
</script>
</body>

</html>