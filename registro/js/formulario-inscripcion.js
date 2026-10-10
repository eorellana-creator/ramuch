(function ($) {
  'use strict';

  function alternarCamposCondicionales() {
    var esEstudiante = $('#tipo_socio').val() === 'estudiante';
    $('#certificado-container').toggle(esEstudiante);
    $('#archivo').prop('required', esEstudiante);

    var pertenecioClub = $('input[name="otro_club_montanismo"]:checked').val() === '1';
    $('#nombre-club-container').toggle(pertenecioClub);
    $('#nombre_otro_club').prop('required', pertenecioClub);

    var otraMotivacion = $('input[name="motivaciones[]"][value="otro"]').is(':checked');
    $('#motivacion-otro-container').toggle(otraMotivacion);
    $('#motivacion_otro').prop('required', otraMotivacion);

    var otraReferencia = $('#referencia').val() === 'Otro';
    $('#otro-referencia-container').toggle(otraReferencia);
    $('#otro_referencia').prop('required', otraReferencia);
  }

  function mostrarError(mensaje) {
    $('#alerta-invalido').html(
      $('<div>', {
        'class': 'alert alert-danger',
        'role': 'alert',
        'text': mensaje
      })
    );
  }

  window.mailExiste = function (campo) {
    if (!campo.value) return;
    $.get('mail_existe.php?email=' + encodeURIComponent(campo.value)).done(function (respuesta) {
      if (respuesta.split('|')[1] === '1') {
        $('#myModal2').modal('show');
        campo.value = '';
      }
    });
  };

  window.rutExiste = function (campo) {
    if (!campo.value || $(campo).hasClass('rutnovalido')) return;
    $.get('rut_existe.php?rut=' + encodeURIComponent(campo.value)).done(function (respuesta) {
      if (respuesta.split('|')[1] === '1') {
        $('#myModal').modal('show');
        campo.value = '';
      }
    });
  };

  window.validaCertificado = function (campo) {
    if (!campo.value) return true;
    var extensiones = ['pdf', 'jpg', 'jpeg', 'png'];
    if ($.inArray(campo.value.split('.').pop().toLowerCase(), extensiones) === -1) {
      $('#myModal3').modal('show');
      $(campo).val('');
      return false;
    }
    return true;
  };

  window.enviar = function () {
    var formulario = document.getElementById('formulario');
    var password = $('#password').val();
    var confirmacion = $('#password2').val();
    var motivaciones = $('input[name="motivaciones[]"]:checked');

    $('.is-invalid').removeClass('is-invalid');
    $('.error-pass, .error-email2').html('');

    if (!formulario.checkValidity()) {
      formulario.reportValidity();
      mostrarError('Debe completar todos los campos obligatorios.');
      return false;
    }
    if ($('#rut').hasClass('rutnovalido')) {
      mostrarError('Debe ingresar un RUT válido.');
      return false;
    }
    if ($('#email').val().toLowerCase() !== $('#email2').val().toLowerCase()) {
      $('#email2').addClass('is-invalid');
      $('.error-email2').html('Los correos ingresados no coinciden.');
      mostrarError('Los correos ingresados deben ser iguales.');
      return false;
    }
    if (password !== confirmacion) {
      mostrarError('Las contraseñas no coinciden.');
      return false;
    }
    if (password.length < 8 || !/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
      mostrarError('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.');
      return false;
    }
    if (motivaciones.length > 3) {
      mostrarError('Puedes seleccionar como máximo tres motivaciones para ingresar a RAMUCH.');
      return false;
    }
    if (typeof grecaptcha === 'undefined' || grecaptcha.getResponse().length === 0) {
      mostrarError('Por favor, completa el captcha.');
      return false;
    }

    var boton = $('#btn-crear-cuenta');
    boton.prop('disabled', true).text('Enviando...');
    $('#alerta-invalido').html('');

    $.ajax({
      url: 'envia.php',
      type: 'POST',
      dataType: 'json',
      data: new FormData(formulario),
      cache: false,
      contentType: false,
      processData: false
    }).done(function (respuesta) {
      if (!respuesta.success) {
        mostrarError(respuesta.message || 'No fue posible completar el registro.');
        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
        boton.prop('disabled', false).text('Crear cuenta');
        return;
      }
      $('#contenido').html("<div style='width:100%; text-align:center; padding:30px 0 90px'><strong>Hemos enviado un correo para verificar tu cuenta.<br>Sigue sus instrucciones para terminar el registro.<br><br>Muchas gracias.<br>RAMUCH.</strong></div>");
      window.parent.parent.scrollTo(0, 0);
    }).fail(function (xhr) {
      var mensaje = xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message
        : 'No fue posible completar el registro. Intenta nuevamente.';
      mostrarError(mensaje);
      if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
      boton.prop('disabled', false).text('Crear cuenta');
    });
    return false;
  };

  $(function () {
    $('#rut').off('.rut').rut({formatOn: 'keyup blur', validateOn: 'blur'})
      .on('rutInvalido.formulario', function () {
        $(this).addClass('rutnovalido is-invalid');
        $('#errorrut2').html('Rut inválido. Debe ingresar un RUT válido.');
      })
      .on('rutValido.formulario', function () {
        $(this).removeClass('rutnovalido is-invalid');
        $('#errorrut2').html('');
      });

    $('#referencia, #tipo_socio').on('change.formulario', alternarCamposCondicionales);
    $('input[name="otro_club_montanismo"], input[name="motivaciones[]"]').on('change.formulario', function () {
      var seleccionadas = $('input[name="motivaciones[]"]:checked');
      if (seleccionadas.length > 3) {
        this.checked = false;
        mostrarError('Puedes seleccionar como máximo tres motivaciones.');
      }
      alternarCamposCondicionales();
    });
    $('#telefono').on('input.formulario', function () {
      this.value = this.value.replace(/\D/g, '');
    });
    $('#formulario').off('submit.formulario').on('submit.formulario', function (evento) {
      evento.preventDefault();
      window.enviar();
    });
    alternarCamposCondicionales();
  });
})(jQuery);
