<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LMSGI | Práctica 6.6</title>
    <style>
        table, td {
            border: 1px solid;
        }
        .msg{
            color: red;
        }
    </style>
    <script>

        function delete_alumno(dni) {
            document.getElementById("alumno_deleted").value = dni;
            document.getElementById("formulario").submit();
        }

        function insert_alumno(){
            window.open("practica6.6_form.php", "_self");
        }

        function update_alumno(dni){
            window.open("practica6.6_form.php?dni="+dni, "_self");
        }
    </script>
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
$alumno_dni_deleted = (isset($_POST['alumno_deleted'])? $_POST['alumno_deleted'] : "");

// Limpiar filtros
// Los filtros enviados no se pueden eliminar sin javascript porque la página ha sido enviado al cliente
//  con el valor por defecto puesto en el value por la variable y al hacer reset pone el valor por defecto, el enviado

?>

<h1>Práctica 6.6 LMSGI CRUD</h1>

<form id="formulario" action="practica6.6.php" method="post">
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

    <input type="hidden" id="alumno_deleted" name="alumno_deleted" value="" />

    <?php
    try {
        $con = new PDO('mysql:host=localhost;dbname=universidad;charset=UTF8', 'root', '');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ELIMINAR ALUMNO SI PROCEDE
        if (!empty($alumno_dni_deleted)) {
            $stmt = $con->prepare('DELETE FROM alumno WHERE dni = :dni');
            $rows = $stmt->execute(array(':dni' => $alumno_dni_deleted));

            if ($rows > 0) {
                echo "<br/>";
                printf('<div class="msg">%s Alumno %s eliminado correctamente.</div>', $rows, $alumno_dni_deleted);
            }
        }

        // CALCULO NUM ALUMNOS
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

        // OBTENCIÓN ALUMNOS
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
            <th>Acciones</th>
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
            echo "<td>";
            echo "<input type='button' value='-' name='delete' onclick='delete_alumno(\"".$alumno['DNI']."\")' />";
            echo "<input type='button' value='+' name='insert' onclick='insert_alumno()' />";
            echo "<input type='button' value='Editar' name='update' onclick='update_alumno(\"".$alumno['DNI']."\")' />";
            echo "</td>";
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

