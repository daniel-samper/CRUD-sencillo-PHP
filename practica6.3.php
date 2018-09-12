<html>
<head>
    <meta charset="UTF-8">
    <title>LMSGI | Práctica 6.3</title>
    <style>
        table, td {
            border: 1px solid;
        }
    </style>
</head>
<body>

<h1>Práctica 6.3 LMSGI</h1>

<?php
try {
    $con = new PDO('mysql:host=localhost;dbname=universidad;charset=UTF8', 'root', '');
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $con->prepare('SELECT * FROM alumno');
    $stmt->execute();

    $arrAlumnos = array();
    // Lo obtiene uno a uno, por lo que habría que recorrer todos los registros
    //while( $datos = $stmt->fetch() ) // Cuidado porque devuelve por índice y asociativo si no se indica lo contrario
    //    echo $datos[0] . " -  " . $datos['DNI'] .'<br/>';

    // Devuelve un array multidimensional con todos los datos de la query
    $arrAlumnos = $stmt->fetchAll();

    // Para poder ver el contenido del array, tal cual lo devuelve PDO
    //echo "<pre>";
    //print_r($arrAlumnos);
    //echo "</pre>";

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

