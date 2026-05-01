    <!-- Footer -->
    <footer style="background:var(--azul); color:#cdd9f0; margin-top:60px;">
        <div class="container py-5">
            <div class="row">

                <!-- Marca -->
                <div class="col-md-4 mb-4">
                    <h5 style="color:#fff; font-weight:800; font-size:20px;">
                        <i class="bi bi-lightning-charge-fill" style="color:var(--amarillo);"></i>
                        Electro<span style="color:var(--amarillo);">hogar</span>
                    </h5>
                    <p style="font-size:13px; margin-top:10px; line-height:1.8;">
                        Tu tienda de confianza en electrodomésticos. Calidad garantizada, los mejores precios y atención personalizada.
                    </p>
                    <div class="d-flex mt-3">
                        <a href="#" style="color:#cdd9f0; font-size:22px; margin-right:14px;"><i class="bi bi-facebook"></i></a>
                        <a href="#" style="color:#cdd9f0; font-size:22px; margin-right:14px;"><i class="bi bi-instagram"></i></a>
                        <a href="#" style="color:#cdd9f0; font-size:22px; margin-right:14px;"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" style="color:#cdd9f0; font-size:22px;"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <!-- Links -->
                <div class="col-md-2 mb-4">
                    <h6 style="color:var(--amarillo); font-weight:700; text-transform:uppercase; font-size:12px; letter-spacing:1px;">Tienda</h6>
                    <ul class="list-unstyled mt-2" style="font-size:13px;">
                        <li class="mb-1"><a href="index.php?pagina=inicio" style="color:#cdd9f0; text-decoration:none;">Productos</a></li>
                        <li class="mb-1"><a href="index.php?pagina=carrito" style="color:#cdd9f0; text-decoration:none;">Mi carrito</a></li>
                        <li class="mb-1"><a href="index.php?pagina=pago" style="color:#cdd9f0; text-decoration:none;">Pagar</a></li>
                        <li class="mb-1"><a href="index.php?pagina=mi_cuenta" style="color:#cdd9f0; text-decoration:none;">Mis pedidos</a></li>
                    </ul>
                </div>

                <!-- Mi cuenta -->
                <div class="col-md-2 mb-4">
                    <h6 style="color:var(--amarillo); font-weight:700; text-transform:uppercase; font-size:12px; letter-spacing:1px;">Mi cuenta</h6>
                    <ul class="list-unstyled mt-2" style="font-size:13px;">
                        <li class="mb-1"><a href="index.php?pagina=login" style="color:#cdd9f0; text-decoration:none;">Iniciar sesión</a></li>
                        <li class="mb-1"><a href="index.php?pagina=registro" style="color:#cdd9f0; text-decoration:none;">Registrarse</a></li>
                        <li class="mb-1"><a href="index.php?pagina=mi_cuenta" style="color:#cdd9f0; text-decoration:none;">Mi perfil</a></li>
                    </ul>
                </div>

                <!-- Contacto -->
                <div class="col-md-4 mb-4">
                    <h6 style="color:var(--amarillo); font-weight:700; text-transform:uppercase; font-size:12px; letter-spacing:1px;">Contacto</h6>
                    <ul class="list-unstyled mt-2" style="font-size:13px; line-height:2;">
                        <li><i class="bi bi-telephone-fill mr-2" style="color:var(--amarillo);"></i>+591 7000-0000</li>
                        <li><i class="bi bi-envelope-fill mr-2" style="color:var(--amarillo);"></i>electrohogar@gmail.com</li>
                        <li><i class="bi bi-geo-alt-fill mr-2" style="color:var(--amarillo);"></i>Bolivia</li>
                        <li><i class="bi bi-clock-fill mr-2" style="color:var(--amarillo);"></i>Lun - Sáb: 8:00 - 20:00</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div style="background:rgba(0,0,0,.25); padding:12px 0; text-align:center; font-size:12px;">
            <span>&copy; <?php echo date('Y'); ?> Electrohogar. Todos los derechos reservados.</span>
            <span class="mx-2">|</span>
            <span>Hecho con <i class="bi bi-heart-fill" style="color:var(--amarillo);"></i> en Bolivia</span>
        </div>
    </footer>

    <script src="recursos/js/validacion.js"></script>

<?php if (isset($_SESSION['usuario']) && empty($_SESSION['es_admin'])): ?>
<?php include __DIR__ . '/chat_widget_cliente.php'; ?>
<?php endif; ?>
</body>
</html>
