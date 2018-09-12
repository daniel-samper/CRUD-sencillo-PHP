<html>
<head>
    <meta charset="UTF-8">
    <title>LMSGI | Práctica 6.5</title>
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

// Recogida de acciones
$pagina = (isset($_POST['pagina'])? $_POST['pagina'] : 1);
$num_registros = (isset($_POST['num_registros'])? $_POST['num_registros'] : 10);
$primero = (isset($_POST['primero'])? true : false);
$ultimo = (isset($_POST['ultimo'])? true : false);
$siguiente = (isset($_POST['siguiente'])? true : false);
$anterior = (isset($_POST['anterior'])? true : false);
$mostrar = (isset($_POST['mostrar'])? true : false);

// Limpiar filtros
// Los filtros enviados no se pueden eliminar sin javascript porque la página ha sido enviado al cliente
//  con el valor por defecto puesto en el value por la variable y al hacer reset pone el valor por defecto, el enviado

?>

<h1>Práctica 6.5 LMSGI</h1>

<form action="practica6.5.php" method="post">
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

        <input type="submit" value="Enviar" name="enviar" />
        <input type="reset" value="Limpiar" name="limpiar" />
    </fieldset>


    <?php
    try {
        $con = new PDO('mysql:host=localhost;dbname=universidad;charset=UTF8', 'root', '');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cálculo del número de regístros
        $sql = "select count(*) from alumno where true"; // se le añade el true para que siempre lleve where y poder añadri filtros de forma cómoda
        $sql_filters = ""; // Se crea la sql de filtros para reutilizarla
        $filters = array();
        if (!empty($dni)){
            $sql_filters .= " and dni like :dni";
            $filters[":dni"] = "%".$dni."%";
        }
        if (!empty($nombre)) {
            $sql_filters .= " and nombre like :nombre";
            $filters[":nombre"] = "%".$nombre."%";
        }
        if (!empty($localidad)) {
            $sql_filters .= " and localidad like :localidad";
            $filters[":localidad"] = "%".$localidad."%";
        }
        if (!empty($fecha_nacimiento)) {
            $sql_filters .= " and fecha_nacimiento like :fecha_nacimiento";
            $filters[":fecha_nacimiento"] = "%".$fecha_nacimiento."%";
        }
        $sql .= $sql_filters;
        $stmt = $con->prepare($sql);
        $stmt->execute($filters);
        $result = $stmt->fetch();
        $total_registros = $result[0];

        // Cálculo de Acciones
        $paginas = ceil($total_registros/$num_registros);
        if ($primero) $pagina = 1;
        if ($ultimo) $pagina = $paginas;
        if ($siguiente && $pagina<$paginas) $pagina++;
        if ($anterior && $pagina>1) $pagina--;
        if ($mostrar) $pagina = 1;

        // Obtención de datos
        $sql = 'SELECT * FROM alumno where true'; // se le añade el true para que siempre lleve where y poder añadri filtros de forma cómoda
        $sql .= $sql_filters;

        if ($num_registros != "todos")
            $sql .= " limit ".($num_registros*($pagina-1)) .",". $num_registros;
        //echo $sql;exit();

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

    <br/>

    <div>
        <input type="submit" value="<<" name="primero" /> &nbsp;
        <input type="submit" value="<" name="anterior" /> &nbsp;
        <input type="text" name="pagina" value="<?php echo $pagina?>" /> &nbsp;
        <input type="submit" value=">" name="siguiente" /> &nbsp;
        <input type="submit" value=">>" name="ultimo" /> &nbsp;
        <label for="num_registros">Registros por página: </label>
        <select name="num_registros">
            <option value="10" <?php echo ($num_registros==10? "selected":"")?>>10</option>
            <option value="15" <?php echo ($num_registros==15? "selected":"")?>>15</option>
            <option value="20" <?php echo ($num_registros==20? "selected":"")?>>20</option>
            <option value="todos" <?php echo ($num_registros=="todos"? "selected":"")?>>todos</option>
        </select>
        <input type="submit" value="Mostrar" name="mostrar" />
        <br/><br/>
        <span>Núm. Registros: <?php echo $total_registros?></span> &nbsp;
        <span>Página <?php echo $pagina ."/". $paginas?></span>
    </div>

</form>


</body>
</html>

