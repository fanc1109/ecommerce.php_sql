<?php
require_once "auth.php";
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    require_once "config.php";
    
    $sql = "DELETE FROM funcionarios WHERE id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = trim($_POST["id"]);
        
        if (mysqli_stmt_execute($stmt)) {
            header("location: index.php");
            exit();
        } else {
            echo "Ops! Algo deu errado.";
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
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
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Excluir Registro</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-4">
                <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                <p class="font-medium mb-4">Tem certeza de que deseja excluir este registro de funcionário?</p>
                <div class="flex gap-3">
                    <input type="submit" value="Sim, Excluir" class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition cursor-pointer">
                    <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg transition">Não, Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>