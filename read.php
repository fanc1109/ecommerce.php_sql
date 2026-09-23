<?php
// Exige autenticação para acessar a página
require_once "auth.php";

// Verifica se o ID do produto foi passado no parâmetro GET na URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Carrega a conexão com o banco de dados
    require_once "config.php";

    // SQL preparado para consultar na tabela Produto pelo id_produto
    $sql = "SELECT * FROM Produto WHERE id_produto = ?";

    // Prepara a consulta
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Associa o ID informado ao parâmetro "i" (integer) da SQL
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        // Atribui o ID filtrado à variável
        $param_id = trim($_GET["id"]);

        // Executa a instrução preparada
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            // Verifica se retornou exatamente 1 produto
            if (mysqli_num_rows($result) == 1) {
                // Obtém o registro como um array associativo
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                // Redireciona para página de erro se o ID for inválido
                header("location: error.php");
                exit();
            }
        } else {
            echo "Ops! Algo deu errado ao tentar carregar o produto.";
        }
        // Fecha a declaração iniciada
        mysqli_stmt_close($stmt);
    }
    // Fecha a conexão com o MySQL
    mysqli_close($link);
} else {
    // Redireciona para página de erro se nenhum ID for enviado
    header("location: error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200 space-y-6">
        <h1 class="text-2xl font-bold text-gray-800 border-b pb-4">Detalhes do Produto</h1>

        <!-- Foto do produto -->
        <div class="flex justify-center">
            <?php 
            $caminho_foto = (!empty($row["foto_produto"]) && file_exists("uploads/" . $row["foto_produto"])) 
                ? "uploads/" . $row["foto_produto"] 
                : "https://via.placeholder.com/150?text=Sem+Foto";
            ?>
            <img src="<?php echo $caminho_foto; ?>" alt="Foto do produto" class="w-32 h-32 rounded-lg object-cover border border-gray-300">
        </div>

        <div class="space-y-4">
            <!-- Código do Produto -->
            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Código (ID)</label>
                <p class="text-lg text-gray-800 font-semibold"><?php echo $row["id_produto"]; ?></p>
            </div>

            <!-- Nome do Produto -->
            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Nome do Produto</label>
                <p class="text-lg text-gray-800 font-semibold"><?php echo htmlspecialchars($row["nome_produto"]); ?></p>
            </div>

            <!-- Preço do Produto -->
            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Preço</label>
                <p class="text-lg text-gray-800 font-semibold">R$ <?php echo number_format($row["preco_produto"], 2, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Botão para retornar ao painel principal -->
        <div class="pt-4">
            <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition">Voltar</a>
        </div>
    </div>
</body>
</html>