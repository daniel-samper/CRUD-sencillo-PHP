<html>
<head>
    <meta charset="UTF-8">
    <title>LMSGI | Práctica 6.4</title>
    <style>
        table, td {
            border: 1px solid;
        }
    </style>
</head>
<body>

<?php
// Recoge los parámetros del POST
$dni = (isset($_POST['dni'])? $_POST['dni'] : "");
$nombre = (isset($_POST['nombre'])? $_POST['nombre'] : "");
$localidad = (isset($_POST['localidad'])? $_POST['localidad'] : "");
$fecha_nacimiento = (isset($_POST['fecha_nacimiento'])? $_POST['fecha_nacimiento'] : "");

?>

<h1>Práctica 6.4 LMSGI</h1>

<form action="practica6.4.php" method="post">
    <fieldset>
        <legend>Filtros de búsqueda</legend>
        <label for="dni">DNI: </label>
        <input type="text" name="dni" value="<?php echo $dni?>" />
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" value="<?php echo $nombre?>" />
        <label for="localidad">Localidad: </label>
        <input type="text" name="localidad" value="<?php echo $localidad?>" />
        <label for="fecha_nacimiento">F. Nacimiento: </label>
        <input type="text" name="fecha_nacimiento" value="<?php echo $fecha_nacimiento?>" />

        <input type="submit" value="Enviar" />
        <input type="reset" value="Limpiar" />
    </fieldset>

</form>

<?php
try {
    $con = new PDO('mysql:host=localhost;dbname=universidad;charset=UTF8', 'root', '');
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT * FROM alumno where true'; // se le añade el true para que siempre lleve where y poder añadri filtros de forma cómoda
    $filters = array();
    if (!empty($dni)){
        $sql .= " and dni like :dni";
        $filters[":dni"] = "%".$dni."%";
    }
    if (!empty($nombre)) {
        $sql .= " and nombre like :nombre";
        $filters[":nombre"] = "%".$nombre."%";
    }
    if (!empty($localidad)) {
        $sql .= " and localidad like :localidad";
        $filters[":localidad"] = "%".$localidad."%";
    }
    if (!empty($fecha_nacimiento)) {
        $sql .= " and fecha_nacimiento like :fecha_nacimiento";
        $filters[":fecha_nacimiento"] = "%".$fecha_nacimiento."%";
    }

    $stmt = $con->prepare($sql);
    $stmt->execute($filters);

    // Devuelve un array multidimensional con todos los datos de la query
    $arrAlumnos = array();
    $arrAlumnos = $stmt->fetchAll();

    $stmt = null;
    $con = null;

} catch(PDOException $e) {

    echo 'Error: ' . $e->getMessage();
    $stmt = null;
    $con = null;
}
?>

<br/>
<caption>Listado de alumnos</caption>
<table>
    <tr>
        <th>DNI</th>
        <th>Nombre</th>
        <th>Apellido 1</th>
        <th>Apellido 2</th>
        <th>Localidad</th>
        <th>F. Nacimiento</th>
    </tr>
    <?php
    foreach ($arrAlumnos as $alumno){

        echo "<tr>";
        echo "<td>" . $alumno['DNI'] . "</td>"; // Se recogen con mayúscula porque están definidos así en BD y no hemos puesto alias
        echo "<td>" . $alumno['NOMBRE'] . "</td>";
        echo "<td>" . $alumno['APELLIDO_1'] . "</td>";
        echo "<td>" . $alumno['APELLIDO_2'] . "</td>";
        echo "<td>" . $alumno['LOCALIDAD'] . "</td>";
        echo "<td>" . $alumno['FECHA_NACIMIENTO'] . "</td>";
        echo "</tr>";
    }
    ?>
</table>


</body>
</html>

