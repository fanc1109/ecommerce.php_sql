<?php
//require_once "auth.php";
require_once "config.php";

$nome_produto = $preco_produto = $foto_produto = "";
$nome_produto_erro = $preco_produto_erro = $foto_produto_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validação do nome_produto
    $input_nome_produto = trim($_POST["nome_produto"]);
    if (empty($input_nome_produto)) {
        $nome_produto_erro = "Por favor, insira o nome do produto.";
    } else {
        $nome_produto = $input_nome_produto;
    }
    
    // Upload da Foto
    $foto_produto = "default-avatar.png";
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $extensoes_permitidas = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp");
        $fileproduto = $_FILES["foto"]["name"];
        $ext = pathinfo($fileproduto, PATHINFO_EXTENSION);

        if (!array_key_exists($ext, $extensoes_permitidas)) {
            $foto_produto_erro = "Formato de imagem inválido.";
        } elseif ($_FILES["foto"]["size"] > 2 * 1024 * 1024) {
            $foto_produto_erro = "Imagem excede o tamanho limite de 2MB.";
        } else {
            $foto_produto = uniqid() . "." . $ext;
            move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto_produto);
        }
    }

    // Validação do preco_produto
    $input_preco_produto = trim($_POST["preco_produto"]);
    if (empty($input_preco_produto) || !ctype_digit($input_preco_produto)) {
        $preco_produto_erro = "Por favor, insira um preço de produto válido (número inteiro).";     
    } else {
        $preco_produto = $input_preco_produto;
    }
    
    // Inserção no Banco
    if (empty($nome_produto_erro) && empty($preco_produto_erro) && empty($foto_produto_erro) && empty($setor_erro)) {
        $sql = "INSERT INTO produto (nome_produto, preco_produto, foto_produto) VALUES (?, ?, ?)";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sds", $param_nome_produto, $param_preco_produto, $param_foto_produto);
            
            $param_nome_produto = $nome_produto;
            $param_preco_produto = $preco_produto;
            $param_foto_produto = $foto_produto;
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Ops! Algo deu errado. Tente novamente.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Cadastrar Produto</h2>
        <p class="text-sm text-gray-500 mb-6">Preencha os campos abaixo para salvar o produto.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do produto</label>
                <input type="text" name="nome_produto" value="<?php echo $nome_produto; ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($nome_produto_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($nome_produto_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $nome_produto_erro; ?></span>
                <?php endif; ?>
            </div>
             <div class="form-group">
                <label>Foto do produto</label>
                <input type="file" name="foto_produto" accept="image/*">
                <?php if(!empty($foto_produto_erro)): ?>
                    <span class="erro"><?php echo $foto_produto_erro; ?></span>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor do produto</label>
                <input type="text" name="preco_produto" value="<?php echo $preco_produto; ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($preco_produto_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($preco_produto_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $preco_produto_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>