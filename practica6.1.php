<html>
<head>
    <script type="text/javascript" src="scripts/funciones.js"></script>
</head>
<body>

<h1>Práctica 6.1 LMSGI</h1>

<?php
// Inicialización de variables
$rows = 6;
$cols = 4;
?>


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

