<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lector RSS - Axio</title>
        <style>
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #E4CCE8; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            p.header { color: #66E9D9; font-weight: bold; margin: 0; }
        </style>
    </head>
    <body>
        <form action="index.php">
            <fieldset> 
                <legend>FILTRO</legend>
                <label>PERIODICO : </label>
                <select name="periodicos">
                    <option value="elpais" <?php if(isset($_REQUEST['periodicos']) && $_REQUEST['periodicos'] == 'elpais') echo 'selected'; ?>>El Pais</option>
                    <option value="elmundo" <?php if(isset($_REQUEST['periodicos']) && $_REQUEST['periodicos'] == 'elmundo') echo 'selected'; ?>>El Mundo</option>      
                </select> 
                <label>CATEGORIA : </label>
                <select name="categoria">
                    <option value=""></option>
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
                <label>FECHA : </label>
                <input type="date" name="fecha" value="<?php echo isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : ''; ?>">
                <label style="margin-left: 1vw;">BUSCAR : </label>
                <input type="text" name="buscar" value="<?php echo isset($_REQUEST['buscar']) ? $_REQUEST['buscar'] : ''; ?>">
                <input type="submit" name="filtrar" value="Filtrar">
            </fieldset>
        </form>
        
        <?php
        
        // Incluir los scripts que descargan las noticias nuevas
        require_once __DIR__ . "/../RSSElPais.php";
        require_once __DIR__ . "/../RSSElMundo.php";
        
        // Incluir la conexión (que crea la variable $link como objeto PDO)
        require_once __DIR__ . "/../conexionBBDD.php"; 
        
        // Función para mostrar la tabla
        function filtros($sql, $pdo){ 
            if ($pdo === false) {
                echo "<p>Error: No hay conexión a la base de datos.</p>";
                return;
            }
            
            try {
                $stmt = $pdo->query($sql);
                
                // Si no hay resultados
                if ($stmt->rowCount() == 0) {
                    echo "<tr><td colspan='6'>No se encontraron noticias con este filtro.</td></tr>";
                    return;
                }

                while ($arrayFiltro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";              
                        echo "<td>".$arrayFiltro['titulo']."</td>";
                        echo "<td>". substr(strip_tags($arrayFiltro['contenido']), 0, 100) . "...</td>"; // Contenido recortado
                        echo "<td>". substr(strip_tags($arrayFiltro['descripcion']), 0, 100) . "...</td>";                      
                        echo "<td>".$arrayFiltro['categoria']."</td>";                       
                        echo "<td><a href='".$arrayFiltro['link']."' target='_blank'>Ver noticia</a></td>";                              
                        
                        // Manejo seguro de la fecha
                        $fechaTexto = $arrayFiltro['fPubli'];
                        if ($fechaTexto) {
                            $fecha = date_create($fechaTexto);
                            $fechaConversion = date_format($fecha, 'd-M-Y');
                        } else {
                            $fechaConversion = "Sin fecha";
                        }
                        
                        echo "<td>".$fechaConversion."</td>";
                    echo "</tr>";  
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='6'>Error SQL: " . $e->getMessage() . "</td></tr>";
            }
        }
        
        // Verificar conexión
        if($link === false){ 
            printf("<p>Conexión fallida (Base de Datos no disponible)</p>");
        } else {
       
            echo "<table>";
            echo "<tr>
                    <th><p class='header'>TITULO</p></th>
                    <th><p class='header'>CONTENIDO</p></th>
                    <th><p class='header'>DESCRIPCIÓN</p></th>
                    <th><p class='header'>CATEGORÍA</p></th>
                    <th><p class='header'>ENLACE</p></th>
                    <th><p class='header'>FECHA</p></th>
                  </tr>";

            // Lógica de filtrado
            $sql = "";
            
            // Determinar tabla (El Pais por defecto)
            $tabla = "elpais";
            if (isset($_REQUEST['periodicos'])) {
                $p = strtolower(str_replace(' ', '', $_REQUEST['periodicos']));
                if ($p == "elmundo") $tabla = "elmundo";
            }

            // Construcción dinámica de la consulta
            // Usamos "fPubli" con comillas para Postgres
            $sql = "SELECT * FROM $tabla WHERE 1=1"; 

            if(isset($_REQUEST['filtrar'])){
                
                $cat = $_REQUEST['categoria'];
                $f = $_REQUEST['fecha'];
                $palabra = $_REQUEST['buscar'];

                if (!empty($cat)) {
                    $sql .= " AND categoria LIKE '%$cat%'";
                }
                if (!empty($f)) {
                    $sql .= " AND \"fPubli\" = '$f'";
                }
                if (!empty($palabra)) {
                    $sql .= " AND (descripcion LIKE '%$palabra%' OR titulo LIKE '%$palabra%')";
                }
            }
            
            // Ordenar por fecha descendente
            $sql .= " ORDER BY \"fPubli\" DESC LIMIT 50";

            // Ejecutar filtro
            filtros($sql, $link);
                  
            echo "</table>";   
        }
        ?>
    </body>
</html>