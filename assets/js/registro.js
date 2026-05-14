function checkPasswordMatch() {
  const password = document.getElementById('contrasena');
  const confirm = document.getElementById('contrasenaconfirm');
  const message = document.getElementById('message');

  if (password.value === confirm.value) {
    message.style.color = 'green';
    message.innerHTML = 'Las contraseñas son iguales';
  } else {
    message.style.color = 'red';
    message.innerHTML = 'Las constraseñas no son iguales';
  }
}

function checkEmailMatch() {
  const email = document.getElementById('correo');
  const confirm = document.getElementById('correoconfirm');
  const message = document.getElementById('message');

  if (email.value === confirm.value) {
    message.style.color = 'green';
    message.innerHTML = 'Los correos son iguales';
  } else {
    message.style.color = 'red';
    message.innerHTML = 'Los correos no son iguales';
  }
}

