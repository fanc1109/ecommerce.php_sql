<?php
require_once "auth.php";
require_once "config.php";
 
$nome = $endereco = $salario = $foto = $setor_id = "";
$nome_erro = $endereco_erro = $salario_erro = $foto_erro = $setor_erro = "";

// Carrega a lista de setores
$setores = [];
$res_setores = mysqli_query($link, "SELECT * FROM setores ORDER BY nome ASC");
if ($res_setores) {
    while ($s = mysqli_fetch_assoc($res_setores)) {
        $setores[] = $s;
    }
}
 
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];
    $foto_atual = $_POST["foto_atual"];
    
    // Validações
    $input_nome = trim($_POST["nome"]);
    if (empty($input_nome)) {
        $nome_erro = "Por favor, insira um nome.";
    } else {
        $nome = $input_nome;
    }

    $input_setor = trim($_POST["setor_id"]);
    if (empty($input_setor)) {
        $setor_erro = "Por favor, selecione um setor.";
    } else {
        $setor_id = $input_setor;
    }
    
    $input_endereco = trim($_POST["endereco"]);
    if (empty($input_endereco)) {
        $endereco_erro = "Por favor, insira um endereço.";     
    } else {
        $endereco = $input_endereco;
    }
    
    $input_salario = trim($_POST["salario"]);
    if (empty($input_salario) || !ctype_digit($input_salario)) {
        $salario_erro = "Por favor, insira um salário válido.";     
    } else {
        $salario = $input_salario;
    }
    
    // Processa foto
    $foto_nome = $foto_atual;
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $extensoes_permitidas = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES["foto"]["name"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (array_key_exists($ext, $extensoes_permitidas)) {
            $foto_nome = uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto_nome)) {
                if ($foto_atual != "default-avatar.png" && file_exists("uploads/" . $foto_atual)) {
                    unlink("uploads/" . $foto_atual);
                }
            }
        }
    }
    
    // Atualização com setor_id
    if (empty($nome_erro) && empty($endereco_erro) && empty($salario_erro) && empty($setor_erro)) {
        $sql = "UPDATE funcionarios SET nome=?, endereco=?, salario=?, foto=?, setor_id=? WHERE id=?";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssisii", $param_nome, $param_endereco, $param_salario, $param_foto, $param_setor_id, $param_id);
            
            $param_nome = $nome;
            $param_endereco = $endereco;
            $param_salario = $salario;
            $param_foto = $foto_nome;
            $param_setor_id = $setor_id;
            $param_id = $id;
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Ops! Algo deu errado.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
} else {
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        
        $sql = "SELECT * FROM funcionarios WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            $param_id = $id;
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $nome = $row["nome"];
                    $endereco = $row["endereco"];
                    $salario = $row["salario"];
                    $foto = $row["foto"];
                    $setor_id = $row["setor_id"];
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
    <title>Atualizar Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Atualizar Registro</h2>
        <p class="text-sm text-gray-500 mb-6">Edite os valores abaixo.</p>

        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto de Perfil</label>
                <div class="flex items-center gap-4 mb-2">
                    <img src="uploads/<?php echo (!empty($foto) && file_exists('uploads/' . $foto)) ? $foto : 'default-avatar.png'; ?>" class="w-12 h-12 rounded-full object-cover border">
                    <input type="file" name="foto" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" class="w-full px-3 py-2 border rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Setor</label>
                <select name="setor_id" class="w-full px-3 py-2 border rounded-lg border-gray-300">
                    <option value="">Selecione um Setor</option>
                    <?php foreach ($setores as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ($setor_id == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <textarea name="endereco" rows="3" class="w-full px-3 py-2 border rounded-lg border-gray-300"><?php echo htmlspecialchars($endereco); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salário</label>
                <input type="text" name="salario" value="<?php echo htmlspecialchars($salario); ?>" class="w-full px-3 py-2 border rounded-lg border-gray-300">
            </div>

            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="hidden" name="foto_atual" value="<?php echo $foto; ?>"/>
            
            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar Alterações" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>