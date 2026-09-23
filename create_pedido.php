<?php
// Garante que apenas usuários logados acessem a página
require_once "auth.php";
// Carrega o arquivo com as credenciais do banco MySQL
require_once "config.php";

// Inicializa variáveis do formulário e de erros
$forma_pagamento = $valor_total = ""; // Variáveis de dados
$forma_pagamento_erro = $valor_total_erro = ""; // Variáveis de validação

// Processa o envio do formulário via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validação do campo forma_pagamento
    $input_forma = trim($_POST["forma_pagamento"]);
    if (empty($input_forma)) {
        $forma_pagamento_erro = "Por favor, selecione a forma de pagamento.";
    } else {
        $forma_pagamento = $input_forma;
    }

    // Validação do campo valor_total_pedido
    $input_valor = trim($_POST["valor_total_pedido"]);
    if (empty($input_valor) || !is_numeric($input_valor)) {
        $valor_total_erro = "Por favor, informe um valor total válido.";
    } else {
        $valor_total = $input_valor;
    }

    // Se não houver erros nos inputs, grava na tabela Pedido
    if (empty($forma_pagamento_erro) && empty($valor_total_erro)) {
        // Query de inserção ajustada com a data atual (NOW()) e a chave estrangeira Usuario_id_usuario
        $sql = "INSERT INTO Pedido (forma_pagamento, data_pedido, valor_total_pedido, Usuario_id_usuario) VALUES (?, NOW(), ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Vincula os parâmetros ("s" = string, "d" = double/decimal, "i" = int)
            mysqli_stmt_bind_param($stmt, "sdi", $param_forma, $param_valor, $param_usuario);

            // Define os valores que vão para a consulta SQL
            $param_forma = $forma_pagamento;
            $param_valor = $valor_total;
            $param_usuario = $_SESSION["admin_id"]; // Pega o ID do usuário atualmente autenticado

            // Executa a instrução preparada
            if (mysqli_stmt_execute($stmt)) {
                // Redireciona para o painel principal após cadastrar o pedido
                header("location: index.php");
                exit();
            } else {
                echo "Ops! Algo deu errado ao registrar o pedido.";
            }
            // Fecha a instrução preparada
            mysqli_stmt_close($stmt);
        }
    }
    // Fecha a conexão com o banco
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Pedido</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Novo Pedido</h2>
        <p class="text-sm text-gray-500 mb-6">Cadastre as informações do pedido.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-4">
            <!-- Seleção da Forma de Pagamento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                <select name="forma_pagamento" class="w-full px-3 py-2 border rounded-lg border-gray-300">
                    <option value="">Selecione a forma de pagamento...</option>
                    <option value="Cartão de Crédito" <?php echo ($forma_pagamento == 'Cartão de Crédito') ? 'selected' : ''; ?>>Cartão de Crédito</option>
                    <option value="Pix" <?php echo ($forma_pagamento == 'Pix') ? 'selected' : ''; ?>>Pix</option>
                    <option value="Boleto" <?php echo ($forma_pagamento == 'Boleto') ? 'selected' : ''; ?>>Boleto</option>
                </select>
                <span class="text-xs text-red-500"><?php echo $forma_pagamento_erro; ?></span>
            </div>

            <!-- Campo de Valor Total -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total (R$)</label>
                <input type="text" name="valor_total_pedido" value="<?php echo htmlspecialchars($valor_total); ?>" class="w-full px-3 py-2 border rounded-lg border-gray-300" placeholder="Ex: 150.00">
                <span class="text-xs text-red-500"><?php echo $valor_total_erro; ?></span>
            </div>

            <!-- Botões de ação -->
            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar Pedido" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2 rounded-lg cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>