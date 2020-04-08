<!-- TODO Falta la validación de todos los campos (longitud, caracteres etc.) tanto en el lado del cliente como en el del servidor. -->
<?php
include './header.php';
if(isset($_SESSION['sesionIniciada'])){
  header("Location: ./");
}
?>
<form action="./signup" method="POST">
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="nombreRegistro">Nombre</label>
      <input type="text" class="form-control" name="nombreRegistro" id="nombreRegistro">
    </div>
    <div class="form-group col-md-6">
      <label for="apellidosRegistro">Apellidos</label>
      <input type="text" class="form-control" name="apellidosRegistro" id="apellidosRegistro">
    </div>
  </div>
  <div class="form-group">
    <label for="dniRegistro">DNI</label>
    <input type="text" class="form-control" name="dniRegistro" id="dniRegistro">
  </div>
  <div class="form-group">
    <label for="correoElectronicoRegistro">Correo electrónico</label>
    <input type="text" class="form-control" name="correoElectronicoRegistro" id="correoElectronicoRegistro">
  </div>
  <div class="form-group">
    <label for="contraseñaRegistro">Contraseña</label>
    <input type="password" class="form-control" name="contraseñaRegistro" id="contraseñaRegistro">
  </div>
  <div class="form-group">
    <label for="confirmaciónContraseñaRegistro">Repetir contraseña</label>
    <input type="password" class="form-control" name="confirmaciónContraseñaRegistro" id="confirmaciónContraseñaRegistro">
  </div>
  <input type="hidden" name="triedRegistro" id="triedRegistro" value="true">
  <button type="submit" class="btn btn-primary">Registrarse</button>
</form>
<?php
//Primero comprobamos si se han enviado datos desde el formulario para ver si se debe intentar insertar al cargar la página o no.
if(isset($_POST['triedRegistro'])  && $_POST['triedRegistro'] == true){
  //Si no se ha introducido algún campo, advertir al usuario. En caso contrario, procedemos con el registro.
  if (!isset($_POST['nombreRegistro'], $_POST['apellidosRegistro'], $_POST['dniRegistro'], $_POST['correoElectronicoRegistro'], $_POST['contraseñaRegistro'], $_POST['confirmaciónContraseñaRegistro'])) {
  	exit('Please fill both the username and password fields!');//TODO Echo Por favor rellena los campos, o algo así.
  } else {
    define('SERVIDOR_BD', 'localhost:3306');
    define('USUARIO_BD', 'webtintoreria');
    define('CONTRASENA_BD', 'lavanderia');
    define('NOMBRE_BD', 'tintoreria');

    $db = mysqli_connect(SERVIDOR_BD,USUARIO_BD,CONTRASENA_BD,NOMBRE_BD);
    session_start();

    if ($stmt = $db->prepare('SELECT nombre FROM Cuenta WHERE correoElectronico = ?')) {
    	$stmt->bind_param('s', $_POST['correoElectronicoRegistro']);
    	$stmt->execute();
    	$stmt->store_result();
    	// Store the result so we can check if the account exists in the database.
    	if ($stmt->num_rows > 0) {
    		// Username already exists
    		echo 'Este correo ya está en uso';
    	} else {
        if ($stmt = $db->prepare('INSERT INTO Cuenta (nombre, apellidos, correoElectronico, contraseña, dni) VALUES (?, ?, ?, ?, ?)')) {
        	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
        	$hashContraseña = password_hash($_POST['contraseñaRegistro'], PASSWORD_DEFAULT);
        	$stmt->bind_param('sssss', $_POST['nombreRegistro'], $_POST['apellidosRegistro'], $_POST['correoElectronicoRegistro'], $hashContraseña, $_POST['dniRegistro']);
        	$stmt->execute();
        	echo 'You have successfully registered, you can now login!';
        } else {
        	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
        	echo 'Could not prepare statement!';
        }
    	}
    	$stmt->close();
    } else {
    	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    	echo 'Could not prepare statement!';
    }
  }
  $db->close();
} else {
}

?>
<script src="./js/signup.js"></script>
<?php
include './footer.php';
?>
