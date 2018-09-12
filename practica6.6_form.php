<?php
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LMSGI | Práctica 6.6 | Form</title>
    <style>
        .msg{
            color: red;
        }
    </style>
</head>
<body>

<?php

// Recoge los parámetros del POST
$dni = (isset($_REQUEST['dni'])? $_REQUEST['dni'] : "");
$nombre = (isset($_REQUEST['nombre'])? $_REQUEST['nombre'] : "");
$apellido_1 = (isset($_REQUEST['apellido_1'])? $_REQUEST['apellido_1'] : "");
$apellido_2 = (isset($_REQUEST['apellido_2'])? $_REQUEST['apellido_2'] : "");
$localidad = (isset($_REQUEST['localidad'])? $_REQUEST['localidad'] : "");
$fecha_nacimiento = (isset($_POST['fecha_nacimiento'])? $_POST['fecha_nacimiento'] : null);

// Recogida de acciones
$insert = (isset($_POST['insert'])? true : false);
$update = (isset($_POST['update'])? true : false);

try {
    $con = new PDO('mysql:host=localhost;dbname=universidad;charset=UTF8', 'root', '');
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // INSERTAR ALUMNO SI PROCEDE
    if (!empty($dni) && $insert) {
        echo "INSERT";
        $stmt = $con->prepare('INSERT into alumno (dni, nombre, apellido_1, apellido_2, localidad, fecha_nacimiento) values (:dni, :nombre, :apellido_1, :apellido_2, :localidad, :fecha_nacimiento)');
        $rows = $stmt->execute(array(
            ':dni' => $dni,
            ':nombre' => $nombre,
            ':apellido_1' => $apellido_1,
            ':apellido_2' => $apellido_2,
            ':localidad' => $localidad,
            ':fecha_nacimiento' => $fecha_nacimiento
        ));

        if ($rows > 0) {
            echo "<br/>";
            printf('<div class="msg">Alumno %s %s %s insertado correctamente.</div>', $dni, $nombre, $apellido_1);
        }
    }

    // OBTENCIÓN ALUMNO para MODIFICACIÓN
    if (!empty($dni) && !$update && !$insert) {


        $sql = 'SELECT * FROM alumno where dni=:dni';
        //echo $sql;exit();

        $stmt = $con->prepare($sql);
        $stmt->execute(array(':dni' => $dni));

        // Devuelve un array asociativo y por índices numérico
        $alumno = array();
        $alumno = $stmt->fetch();

        if (!empty($alumno)){
            $dni = $alumno['DNI'];
            $nombre = $alumno['NOMBRE'];
            $apellido_1 = $alumno['APELLIDO_1'];
            $apellido_2 = $alumno['APELLIDO_2'];
            $localidad = $alumno['LOCALIDAD'];
            $fecha_nacimiento = $alumno['FECHA_NACIMIENTO'];
        }
    }

    // Modifica el registro
    if (!empty($dni) && $update){
        echo "UPDATE";
        $stmt = $con->prepare('UPDATE alumno set dni=:dni, nombre=:nombre, apellido_1=:apellido_1, apellido_2=:apellido_2, localidad=:localidad, fecha_nacimiento=:fecha_nacimiento where dni=:dni');
        $rows = $stmt->execute(array(
            ':dni' => $dni,
            ':nombre' => $nombre,
            ':apellido_1' => $apellido_1,
            ':apellido_2' => $apellido_2,
            ':localidad' => $localidad,
            ':fecha_nacimiento' => $fecha_nacimiento
        ));

        if ($rows > 0) {
            echo "<br/>";
            printf('<div class="msg">Alumno %s %s %s modificado correctamente.</div>', $dni, $nombre, $apellido_1);
        }
    }

    // Cierre de conexiones
    $stmt = null;
    $con = null;

} catch(PDOException $e) {

    echo 'Error: ' . $e->getMessage();
    $stmt = null;
    $con = null;
}
?>

<h1>Práctica 6.6 LMSGI Form</h1>

<a href="practica6.6.php">Volver al listado</a>
<br/>
<br/>

<form id="formulario" action="practica6.6_form.php" method="post">
    <fieldset>
        <legend>Formulario Alumno</legend>

        <label for="dni">DNI: </label>
        <input type="text" name="dni" value="<?php echo $dni?>" /> <br/>
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" value="<?php echo $nombre?>" /> <br/>
        <label for="apellido_1">Primer apellido: </label>
        <input type="text" name="apellido_1" value="<?php echo $apellido_1?>" /> <br/>
        <label for="apellido_2">Segundo apellido: </label>
        <input type="text" name="apellido_2" value="<?php echo $apellido_2?>" /> <br/>
        <label for="localidad">Localidad: </label>
        <input type="text" name="localidad" value="<?php echo $localidad?>" /> <br/>
        <label for="fecha_nacimiento">F. Nacimiento: </label>
        <input type="text" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento?>" /> <br/><br/>

        <input type="submit" value="Insertar" name="insert" />
        <input type="submit" value="Modificar" name="update" />
        <input type="reset" value="Cancelar" name="cancelar" />
    </fieldset>

</form>

</body>
</html>
