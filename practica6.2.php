<html>
<head>
    <meta charset="UTF-8">
    <title>LMSGI | Práctica 6.2</title>
</head>
<body>

<h1>Práctica 6.2 LMSGI</h1>

<?php
// Recoge los parámetros del POST, si no tiene los inicializa a 2
$rows = 2;
$cols = 2;
if (isset($_POST['rows']))
    if ($_POST['rows']>0)
        $rows = $_POST['rows'];
    else
        $rows = 2;

if (isset($_POST['cols']))
    if ($_POST['cols']>0)
        $cols = $_POST['cols'];
    else
        $cols = 2;

?>

<form action="practica6.2.php" method="post">
    <fieldset>
        <legend>Datos para la tabla</legend>
        <label for="rows">Filas: </label>
        <input type="text" name="rows" value="<?php echo $rows?>" />
        <label for="cols">Columnas: </label>
        <input type="text" name="cols" value="<?php echo $cols?>" />

        <input type="submit" value="Enviar" />
    </fieldset>

</form>

<br/>

<table style="border: 1px solid">
    <tr>
        <?php
        for($i=1; $i<=$cols; $i++){
            echo "<th>Campo $i</th>";
        }
        ?>
    </tr>
    <?php
    for($i=1; $i<=$rows; $i++){

        echo "<tr>";

        for($j=1; $j<=$cols; $j++){
            echo "<td style=\"border: 1px solid\">fila " . $i . " columna " . $j ."</td>";
        }

        echo "</tr>";
    }
    ?>

</table>


</body>
</html>

