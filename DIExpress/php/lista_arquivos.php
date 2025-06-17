<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <title>Vídeos editados</title>
        <style>
             .fundo {
            background-image: url("../assets/fundo.png");
            background-size: cover; 
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .info {           
            width: fit-content;            
        }
        .video{
            padding: 2px;
        }
        .date{
            padding: 2px;
        }

            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                background-color: rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(5px);
            }

            h1 {
                text-align: center;
                color: #555;
            }

            ul {
                list-style: none;
                padding: 0;
            }

            li {
                display: flex;
                padding: 10px 15px;
                margin: 5px 0;
                background-color:rgba(255, 255, 255, 0.7);
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            li:hover {
                padding: 10px 15px;
                margin: 5px 0;
                background-color: rgba(193, 193, 193, 0.7);
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
        <script>
            function orderBy() {
                const list = document.getElementById('sortable-list');
                const items = Array.from(list.getElementsByTagName('li'));
                // Ordenar os elementos com base no atributo datetime em ordem decrescente
                items.sort((a, b) => {
                    const dateA = a.getAttribute('datetime');
                    const dateB = b.getAttribute('datetime');
                    return dateB - dateA; // Decrescente
                });
                // Reorganizar a lista no DOM
                list.innerHTML = ''; // Limpa a lista
                items.forEach(item => list.appendChild(item)); // Adiciona os itens ordenados
            }
            function download (file,name) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'video', true);
                xhr.responseType = 'blob';
                xhr.onload = function() {
                  const blob = xhr.response;
                  const url = URL.createObjectURL(blob);
                  const a = document.createElement('a');
                  a.href = file;
                  a.download = name;
                  a.click();
                };
                xhr.send();
            }
        </script>

    </head>
   <body class="fundo" onload="orderBy()">
    <div class="container">
        <h1>Vídeos editados</h1>
        <ul id="sortable-list">        
            <?php
            // Defina o caminho da pasta que contém os arquivos
            $folderPath = "../output";
            $cpf = "";

            // Obtenha o termo de pesquisa (filtro) se houver
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

            // Função para listar os arquivos da pasta
            function listFiles($folderPath, $searchTerm) {
                $files = scandir($folderPath);
                $fileList = [];   
                // Filtrar arquivos
                foreach ($files as $file) {
                    // Ignorar '.' e '..'
                    if ($file != "." && $file != ".." && str_starts_with($file, $searchTerm)) {
                          $fileList[] = $file;
                    }
                }
                return $fileList;
            }

            // Obter a lista de arquivos filtrados
            $fileList = listFiles($folderPath, $cpf);
            foreach ($fileList as $key => $value) {
                    echo "<li datetime='".filectime($folderPath."/".$value)."' onclick=\"download('".$folderPath."/".$value."','".$value."')\">
                            <div class='info'>
                                <div class='video'>                                
                                <span class='glyphicon glyphicon-download-alt'></span> "
                                .$value."
                                </div>
                                <div class='date'>
                                <span class='glyphicon glyphicon-time'></span> "
                                    .date("d/m/Y H:i:s", filectime($folderPath."/".$value))."
                                </div>
                            </div>
                         </li>\n";
            }
//<a href='".$folderPath."/".$value."'>".$value."</a>
            ?>
        </ul>
    </div>  
   </body>
</html>
