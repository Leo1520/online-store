/**
 * Utilidades de validación de formularios.
 * Uso: importar este archivo y llamar a iniciarValidacion(formulario, reglas)
 */

var Validacion = (function () {

    // --- Utilidades internas ---

    function marcarError(campo, mensaje) {
        campo.classList.add('is-invalid');
        campo.classList.remove('is-valid');
        var fb = campo.nextElementSibling;
        if (fb && fb.classList.contains('invalid-feedback')) {
            fb.textContent = mensaje;
        } else {
            fb = document.createElement('div');
            fb.className = 'invalid-feedback';
            fb.textContent = mensaje;
            campo.parentNode.insertBefore(fb, campo.nextSibling);
        }
    }

    function marcarOk(campo) {
        campo.classList.remove('is-invalid');
        campo.classList.add('is-valid');
    }

    function limpiar(campo) {
        campo.classList.remove('is-invalid', 'is-valid');
    }

    // --- Reglas predefinidas ---

    var reglas = {
        requerido: function (v) {
            return v.trim() !== '' ? null : 'Este campo es obligatorio.';
        },
        minLen: function (n) {
            return function (v) {
                return v.trim().length >= n ? null : 'Minimo ' + n + ' caracteres.';
            };
        },
        maxLen: function (n) {
            return function (v) {
                return v.trim().length <= n ? null : 'Maximo ' + n + ' caracteres.';
            };
        },
        soloDigitos: function (v) {
            return /^\d+$/.test(v.trim()) ? null : 'Solo se permiten numeros.';
        },
        email: function (v) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? null : 'Correo electronico no valido.';
        },
        numeroPositivo: function (v) {
            return parseFloat(v) > 0 ? null : 'Debe ser un numero mayor a cero.';
        },
        seleccionValida: function (v) {
            return parseInt(v) > 0 ? null : 'Seleccione una opcion valida.';
        },
        soloLetrasEspacios: function (v) {
            return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(v.trim()) ? null : 'Solo se permiten letras.';
        },
        alphanumerico: function (v) {
            return /^[a-zA-Z0-9_]+$/.test(v.trim()) ? null : 'Solo letras, numeros y guion bajo.';
        },
    };

    // --- Función principal ---

    /**
     * @param {HTMLFormElement} form
     * @param {Object} config  clave: nombre del campo, valor: array de funciones validadoras
     * @param {Object} [opciones]  { soloSubmit: false }
     */
    function iniciar(form, config, opciones) {
        opciones = opciones || {};

        function validarCampo(campo) {
            var nombre = campo.name || campo.id;
            var fns = config[nombre];
            if (!fns) { limpiar(campo); return true; }

            for (var i = 0; i < fns.length; i++) {
                var error = fns[i](campo.value);
                if (error) { marcarError(campo, error); return false; }
            }
            marcarOk(campo);
            return true;
        }

        // Eventos en tiempo real
        if (!opciones.soloSubmit) {
            Object.keys(config).forEach(function (nombre) {
                var campos = form.querySelectorAll('[name="' + nombre + '"], #' + nombre);
                campos.forEach(function (campo) {
                    campo.addEventListener('input', function () { validarCampo(campo); });
                    campo.addEventListener('blur',  function () { validarCampo(campo); });
                });
            });
        }

        // Validación al enviar
        form.addEventListener('submit', function (e) {
            var valido = true;
            Object.keys(config).forEach(function (nombre) {
                var campos = form.querySelectorAll('[name="' + nombre + '"]');
                campos.forEach(function (campo) {
                    if (!validarCampo(campo)) valido = false;
                });
            });
            if (!valido) {
                e.preventDefault();
                var primero = form.querySelector('.is-invalid');
                if (primero) primero.focus();
            }
        });
    }

    return { iniciar: iniciar, reglas: reglas };
}());
