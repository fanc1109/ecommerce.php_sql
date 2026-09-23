<?php
// Exige autenticação de acesso
require_once "auth.php";
// Carrega o arquivo de conexão MySQL
require_once "config.php";

// Inicializa variáveis dos campos e mensagens de erro
$pedido_id = $produto_id = $quantidade = "";
$pedido_id_erro = $produto_id_erro = $quantidade_erro = "";

// Consulta todos os pedidos cadastrados para popular o campo <select>
$pedidos = [];
$res_pedidos = mysqli_query($link, "SELECT id_pedido FROM Pedido ORDER BY id_pedido DESC");
if ($res_pedidos) {
    while ($p = mysqli_fetch_assoc($res_pedidos)) {
        $pedidos[] = $p; // Guarda os registros no array
    }
}

// Consulta todos os produtos cadastrados para popular o campo <select>
$produtos = [];
$res_produtos = mysqli_query($link, "SELECT id_produto, nome_produto FROM Produto ORDER BY nome_produto ASC");
if ($res_produtos) {
    while ($prod = mysqli_fetch_assoc($res_produtos)) {
        $produtos[] = $prod; // Guarda os registros no array
    }
}

// Executa o bloco de código quando o formulário for enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validação da opção de Pedido selecionada
    if (empty(trim($_POST["pedido_id"]))) {
        $pedido_id_erro = "Por favor, selecione um pedido.";
    } else {
        $pedido_id = trim($_POST["pedido_id"]);
    }

    // Validação da opção de Produto selecionada
    if (empty(trim($_POST["produto_id"]))) {
        $produto_id_erro = "Por favor, selecione um produto.";
    } else {
        $produto_id = trim($_POST["produto_id"]);
    }

    // Validação da Quantidade informada
    if (empty(trim($_POST["quantidade"]))) {
        $quantidade_erro = "Por favor, insira a quantidade.";
    } else {
        $quantidade = trim($_POST["quantidade"]);
    }

    // Grava na tabela pivô Contem se não houver erros
    if (empty($pedido_id_erro) && empty($produto_id_erro) && empty($quantidade_erro)) {
        // Query ajustada para inserir na tabela Contem com suas chaves estrangeiras
        $sql = "INSERT INTO Contem (Pedido_id_pedido, Produto_id_produto, quantidade_contem) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Vincula os parâmetros ("i" = int, "i" = int, "s" = string)
            mysqli_stmt_bind_param($stmt, "iis", $param_pedido, $param_produto, $param_qtd);

            // Atribui as variáveis de envio
            $param_pedido = $pedido_id;
            $param_produto = $produto_id;
            $param_qtd = $quantidade;

            // Executa a declaração no banco
            if (mysqli_stmt_execute($stmt)) {
                // Redireciona para a página principal após vincular o item
                header("location: index.php");
                exit();
            } else {
                echo "Ops! Algo deu errado ao associar o produto ao pedido.";
            }
            // Fecha a declaração
            mysqli_stmt_close($stmt);
        }
    }
    // Encerra a conexão com o MySQL
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Item ao Pedido</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Adicionar Item ao Pedido</h2>
        <p class="text-sm text-gray-500 mb-6">Associe produtos e quantidades aos pedidos do sistema.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-4">
            <!-- Seleção do Pedido -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Selecione o Pedido</label>
                <select name="pedido_id" class="w-full px-3 py-2 border rounded-lg border-gray-300">
                    <option value="">Selecione...</option>
                    <?php foreach ($pedidos as $p): ?>
                        <option value="<?php echo $p['id_pedido']; ?>" <?php echo ($pedido_id == $p['id_pedido']) ? 'selected' : ''; ?>>
                            Pedido #<?php echo $p['id_pedido']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="text-xs text-red-500"><?php echo $pedido_id_erro; ?></span>
            </div>

            <!-- Seleção do Produto -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Selecione o Produto</label>
                <select name="produto_id" class="w-full px-3 py-2 border rounded-lg border-gray-300">
                    <option value="">Selecione...</option>
                    <?php foreach ($produtos as $prod): ?>
                        <option value="<?php echo $prod['id_produto']; ?>" <?php echo ($produto_id == $prod['id_produto']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($prod['nome_produto']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="text-xs text-red-500"><?php echo $produto_id_erro; ?></span>
            </div>

            <!-- Campo Quantidade -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                <input type="text" name="quantidade" value="<?php echo htmlspecialchars($quantidade); ?>" class="w-full px-3 py-2 border rounded-lg border-gray-300" placeholder="Ex: 3">
                <span class="text-xs text-red-500"><?php echo $quantidade_erro; ?></span>
            </div>

            <!-- Botões de ação -->
            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar Item" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>