<?php
require_once "auth.php";
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    require_once "config.php";
    
    // Consulta JOIN buscando dados da tabela setores
    $sql = "SELECT f.*, s.nome AS setor_nome 
            FROM funcionarios f 
            INNER JOIN setores s ON f.setor_id = s.id 
            WHERE f.id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = trim($_GET["id"]);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Ops! Algo deu errado.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
} else {
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200 space-y-6">
        <h1 class="text-2xl font-bold text-gray-800 border-b pb-4">Detalhes do Funcionário</h1>
        
        <div class="flex justify-center">
            <?php 
                $caminho_foto = (!empty($row["foto"]) && file_exists("uploads/" . $row["foto"])) 
                    ? "uploads/" . $row["foto"] 
                    : "https://via.placeholder.com/150?text=Sem+Foto";
            ?>
            <img src="<?php echo $caminho_foto; ?>" alt="Foto de <?php echo htmlspecialchars($row["nome"]); ?>" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500 shadow-md">
        </div>

        <div class="space-y-4">
            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Nome</label>
                <p class="text-lg text-gray-800 font-semibold"><?php echo htmlspecialchars($row["nome"]); ?></p>
            </div>

            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Setor</label>
                <p class="text-lg text-blue-600 font-semibold"><?php echo htmlspecialchars($row["setor_nome"]); ?></p>
            </div>

            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Endereço</label>
                <p class="text-lg text-gray-800 font-semibold"><?php echo htmlspecialchars($row["endereco"]); ?></p>
            </div>

            <div class="border-b border-gray-100 pb-2">
                <label class="block text-xs uppercase text-gray-400 font-bold mb-1">Salário</label>
                <p class="text-lg text-gray-800 font-semibold">R$ <?php echo number_format($row["salario"], 2, ',', '.'); ?></p>
            </div>
        </div>

        <div class="pt-4">
            <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition">Voltar</a>
        </div>
    </div>
</body>
</html>