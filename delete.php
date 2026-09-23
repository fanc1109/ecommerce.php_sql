<?php
// Exige autenticação
require_once "auth.php";

// Processa a remoção do registro após a confirmação via POST
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Carrega o arquivo de conexão
    require_once "config.php";

    // Comando SQL DELETE apontando para a tabela Produto
    $sql = "DELETE FROM Produto WHERE id_produto = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Vincula o parâmetro ao ID do produto ("i" = integer)
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        // Define a variável do parâmetro com o valor vindo do POST
        $param_id = trim($_POST["id"]);

        // Executa a instrução preparada
        if (mysqli_stmt_execute($stmt)) {
            // Redireciona para o painel principal em caso de sucesso
            header("location: index.php");
            exit();
        } else {
            echo "Ops! Algo deu errado ao tentar excluir o produto.";
        }
        // Fecha o stmt
        mysqli_stmt_close($stmt);
    }
    // Fecha o link com o banco de dados
    mysqli_close($link);
} else {
    // Se a chamada for via GET, verifica se há id_produto na URL
    if (empty(trim($_GET["id"]))) {
        header("location: error.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Excluir Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Excluir Produto</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-4">
                <!-- Armazena o id enviado no GET para repassá-lo via POST -->
                <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                <p class="font-medium mb-4">Tem certeza de que deseja excluir este produto do catálogo?</p>
                <div class="flex gap-3">
                    <!-- Confirmar exclusão -->
                    <input type="submit" value="Sim, Excluir" class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg cursor-pointer">
                    <!-- Cancelar operação -->
                    <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg">Não, Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>