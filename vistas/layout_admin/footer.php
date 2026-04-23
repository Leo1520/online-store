    </div><!-- /p-4 -->

</div><!-- /main-content -->

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios@1.7.2/dist/axios.min.js"></script>

<script>
function showToast(msg, type = 'success') {
    const bg = { success: '#28a745', error: '#dc3545', warning: '#F5A623', info: '#1B3A6B' };
    Swal.fire({
        toast: true, position: 'top-end', showConfirmButton: false,
        timer: 3000, timerProgressBar: true,
        icon: type, title: msg,
        background: bg[type] ?? bg.success, color: '#fff',
        iconColor: '#fff'
    });
}

function confirmDelete(msg, onConfirm) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: msg ?? 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => { if (result.isConfirmed) onConfirm(); });
}

function confirmAction(title, msg, onConfirm) {
    Swal.fire({
        title: title,
        text: msg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1B3A6B',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then(result => { if (result.isConfirmed) onConfirm(); });
}
</script>

</body>
</html>
