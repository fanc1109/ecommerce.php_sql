<?php
// Carrega o arquivo de validação de login e o arquivo de banco de dados
require_once "auth.php";
require_once "config.php";

// Inicializa variáveis do formulário e de mensagens de erro
$nome = $preco = $foto = "";
$nome_erro = $preco_erro = "";

// Executa a lógica de atualização quando o formulário é enviado via POST
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Obtém o ID do produto e o nome da foto atual via campo oculto
    $id = $_POST["id"];
    $foto_atual = $_POST["foto_atual"];

    // Validação do campo nome_produto
    $input_nome = trim($_POST["nome_produto"]);
    if (empty($input_nome)) {
        $nome_erro = "Por favor, insira o nome do produto.";
    } else {
        $nome = $input_nome;
    }

    // Validação do campo preco_produto
    $input_preco = trim($_POST["preco_produto"]);
    if (empty($input_preco) || !is_numeric($input_preco)) {
        $preco_erro = "Por favor, insira um preço válido.";
    } else {
        $preco = $input_preco;
    }

    // Processamento do upload da foto
    $foto_nome = $foto_atual; // Por padrão mantém a foto que já existia
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $extensoes_permitidas = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES["foto"]["name"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // Garante que o formato da imagem é válido
        if (array_key_exists($ext, $extensoes_permitidas)) {
            $foto_nome = uniqid() . "." . $ext; // Gera nome único para o arquivo
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto_nome)) {
                // Remove a foto antiga do servidor se uma nova for enviada com sucesso
                if (!empty($foto_atual) && file_exists("uploads/" . $foto_atual)) {
                    unlink("uploads/" . $foto_atual);
                }
            }
        }
    }

    // Se os campos forem válidos, faz o UPDATE no MySQL
    if (empty($nome_erro) && empty($preco_erro)) {
        $sql = "UPDATE Produto SET nome_produto = ?, preco_produto = ?, foto_produto = ? WHERE id_produto = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Associa os valores ("s" = string, "d" = decimal/double, "s" = string, "i" = int)
            mysqli_stmt_bind_param($stmt, "sdsi", $param_nome, $param_preco, $param_foto, $param_id);

            // Passa os valores validados aos parâmetros
            $param_nome = $nome;
            $param_preco = $preco;
            $param_foto = $foto_nome;
            $param_id = $id;

            // Executa a instrução preparada
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php"); // Redireciona para o painel em caso de sucesso
                exit();
            } else {
                echo "Ops! Algo deu errado ao atualizar o registro.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
} else {
    // Se a requisição for do tipo GET, busca os dados vigentes para preencher os inputs
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        $sql = "SELECT * FROM Produto WHERE id_produto = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            $param_id = $id;

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    // Preenche as variáveis com os dados do banco
                    $nome = $row["nome_produto"];
                    $preco = $row["preco_produto"];
                    $foto = $row["foto_produto"];
                } else {
                    header("location: error.php");
                    exit();
                }
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    } else {
        header("location: error.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Atualizar Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Atualizar Produto</h2>
        <p class="text-sm text-gray-500 mb-6">Altere os dados desejados do produto.</p>

        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
            <!-- Exibição da foto atual e campo de upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto do Produto</label>
                <div class="flex items-center gap-4 mb-2">
                    <img src="<?php echo (!empty($foto) && file_exists('uploads/' . $foto)) ? 'uploads/' . $foto : 'https://via.placeholder.com/80'; ?>" class="w-16 h-16 rounded-lg object-cover border">
                    <input type="file" name="foto" accept="image/*" class="text-sm text-gray-500">
                </div>
            </div>

            <!-- Input do Nome -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                <input type="text" name="nome_produto" value="<?php echo htmlspecialchars($nome); ?>" class="w-full px-3 py-2 border rounded-lg border-gray-300">
                <span class="text-xs text-red-500"><?php echo $nome_erro; ?></span>
            </div>

            <!-- Input do Preço -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço (Ex: 99.90)</label>
                <input type="text" name="preco_produto" value="<?php echo htmlspecialchars($preco); ?>" class="w-full px-3 py-2 border rounded-lg border-gray-300">
                <span class="text-xs text-red-500"><?php echo $preco_erro; ?></span>
            </div>

            <!-- Envio de dados ocultos -->
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="hidden" name="foto_atual" value="<?php echo $foto; ?>"/>

            <!-- Botões de ação -->
            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar Alterações" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>