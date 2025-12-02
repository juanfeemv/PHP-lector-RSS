<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Lector RSS</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background-color: white;
                color: #333;
            }

            h1 {
                text-align: center;
                color: #444;
            }

            form {
                background-color: #f2f2f2;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                text-align: center; 
            }

            input, select {
                padding: 5px;
                margin: 5px;
            }

            input[type="submit"] {
                cursor: pointer;
                background-color: #ddd;
                border: 1px solid #999;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th, td {
                border: 1px solid #ccc;
                padding: 10px;
                text-align: left;
                font-size: 14px;
            }

            th {
                background-color: #e0e0e0;
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            a {
                color: #0066cc;
                text-decoration: none;
            }
            
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>

        <h1>Lector de Noticias </h1>

        <div style="display:none;">
            <?php
            ob_start(); 
            require_once __DIR__ . "/../RSSElPais.php";
            require_once __DIR__ . "/../RSSElMundo.php";
            ob_end_clean(); 
            ?>
        </div>

        <form action="index.php" method="GET">
            <label>Periódico:</label>
            <select name="periodicos">
                <option value="elpais" <?php if(isset($_REQUEST['periodicos']) && $_REQUEST['periodicos'] == 'elpais') echo 'selected'; ?>>El País</option>
                <option value="elmundo" <?php if(isset($_REQUEST['periodicos']) && $_REQUEST['periodicos'] == 'elmundo') echo 'selected'; ?>>El Mundo</option>      
            </select> 

            <label>Categoría:</label>
            <select name="categoria">
                <option value="">Todas</option>
                <option value="Política">Política</option>
                <option value="Deportes">Deportes</option>
                <option value="Ciencia">Ciencia</option>
                <option value="España">España</option>
                <option value="Economía">Economía</option>
                <option value="Música">Música</option>
                <option value="Cine">Cine</option>
                <option value="Europa">Europa</option>
                <option value="Justicia">Justicia</option>                
            </select>

            <label>Fecha:</label>
            <input type="date" name="fecha" value="<?php echo isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : ''; ?>">

            <label>Buscar:</label>
            <input type="text" name="buscar" value="<?php echo isset($_REQUEST['buscar']) ? $_REQUEST['buscar'] : ''; ?>">
            
            <input type="submit" name="filtrar" value="Filtrar">
        </form>
        
        <?php
        require_once __DIR__ . "/../conexionBBDD.php"; 
        
        function filtros($sql, $pdo){ 
            if ($pdo === false) {
                echo "<p style='color:red; text-align:center;'>Error de conexión.</p>";
                return;
            }
            try {
                $stmt = $pdo->query($sql);
                if ($stmt->rowCount() == 0) {
                    echo "<p style='text-align:center;'>No hay resultados.</p>";
                    return;
                }
                while ($arrayFiltro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";              
                        echo "<td><b>".$arrayFiltro['titulo']."</b></td>";
                        echo "<td>". substr(strip_tags($arrayFiltro['contenido']), 0, 100) . "...</td>";
                        echo "<td>". substr(strip_tags($arrayFiltro['descripcion']), 0, 100) . "...</td>";                      
                        echo "<td>".$arrayFiltro['categoria']."</td>";                       
                        echo "<td><a href='".$arrayFiltro['link']."' target='_blank'>Leer</a></td>";                              
                        
                        $fechaTexto = $arrayFiltro['fPubli'];
                        $fechaConversion = $fechaTexto ? date_format(date_create($fechaTexto), 'd/m/Y') : "-";
                        
                        echo "<td>".$fechaConversion."</td>";
                    echo "</tr>";  
                }
            } catch (PDOException $e) {
                echo "<p>Error SQL.</p>";
            }
        }
        
        if($link !== false){ 
            echo "<table>";
            echo "<tr>
                    <th>TÍTULO</th>
                    <th>CONTENIDO</th>
                    <th>DESCRIPCIÓN</th>
                    <th>CATEGORÍA</th>
                    <th>ENLACE</th>
                    <th>FECHA</th>
                  </tr>";

            $tabla = "elpais";
            if (isset($_REQUEST['periodicos'])) {
                $p = strtolower(str_replace(' ', '', $_REQUEST['periodicos']));
                if ($p == "elmundo") $tabla = "elmundo";
            }

            $sql = "SELECT * FROM $tabla WHERE 1=1"; 

            if(isset($_REQUEST['filtrar'])){
                $cat = $_REQUEST['categoria'];
                $f = $_REQUEST['fecha'];
                $palabra = $_REQUEST['buscar'];

                if (!empty($cat)) $sql .= " AND categoria LIKE '%$cat%'";
                if (!empty($f)) $sql .= " AND \"fPubli\" = '$f'";
                if (!empty($palabra)) $sql .= " AND (descripcion LIKE '%$palabra%' OR titulo LIKE '%$palabra%')";
            }
            
            $sql .= " ORDER BY \"fPubli\" DESC LIMIT 50";
            filtros($sql, $link);
            echo "</table>";   
        }
        ?>
    </body>
</html>