<?php
// Inclui o arquivo que valida se o usuário está logado
require_once "auth.php";
// Inclui a conexão com o banco de dados MySQL
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"> <!-- Define a codificação de caracteres do documento -->
    <title>Painel de Controle</title> <!-- Título exibido na aba do navegador -->
    <script src="https://cdn.tailwindcss.com"></script> <!-- Importa o CSS do Tailwind -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Importa ícones -->
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <!-- Barra superior de autenticação -->
    <header class="bg-slate-900 text-white border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-store text-blue-500"></i> <!-- Ícone da loja -->
                <span class="font-bold text-sm tracking-wide">Sistema do E-commerce</span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <!-- Exibe o nome do usuário armazenado na sessão -->
                <span class="text-slate-300">Olá, <strong class="text-white"><?php echo htmlspecialchars($_SESSION["admin_nome"] ?? 'Usuário'); ?></strong></span>
                <!-- Botão de Logout -->
                <a href="logout.php" class="bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white px-3 py-1.5 rounded-md transition flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Sair
                </a>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <div class="max-w-5xl mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Produtos Cadastrados</h2> <!-- Título da seção -->
            <!-- Botão que direciona para a criação de um novo produto -->
            <a href="create_produto.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition inline-flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Adicionar Novo Produto
            </a>
        </div>

        <?php
        // Instrução SQL para listar todos os produtos do banco por ordem decrescente
        $sql = "SELECT * FROM Produto ORDER BY id_produto DESC";
        // Executa a consulta
        if ($result = mysqli_query($link, $sql)) {
            // Verifica se a tabela possui ao menos 1 registro
            if (mysqli_num_rows($result) > 0) {
                echo '<div class="overflow-x-auto rounded-lg border border-gray-200">';
                echo '<table class="w-full border-collapse text-left text-sm text-gray-600">';
                echo '<thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">';
                echo '<tr>';
                echo '<th class="px-4 py-3 border-b">#</th>'; // Coluna ID
                echo '<th class="px-4 py-3 border-b">Nome do Produto</th>'; // Coluna Nome
                echo '<th class="px-4 py-3 border-b">Valor do Produto</th>'; // Coluna Preço
                echo '<th class="px-4 py-3 border-b text-center">Ações</th>'; // Coluna Ações
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="divide-y divide-gray-200">';

                // Percorre todos os produtos encontrados no banco
                while ($row = mysqli_fetch_array($result)) {
                    // Verifica se a foto existe no servidor; caso contrário, define uma imagem padrão
                    $foto_path = (!empty($row['foto_produto']) && file_exists('uploads/' . $row['foto_produto'])) 
                        ? 'uploads/' . $row['foto_produto'] 
                        : 'https://via.placeholder.com/40';

                    echo '<tr class="hover:bg-gray-50 transition">';
                    // Exibe o ID do produto
                    echo '<td class="px-4 py-3 font-medium text-gray-900">' . $row['id_produto'] . '</td>';
                    // Exibe a imagem miniatura e o nome do produto
                    echo '<td class="px-4 py-3 flex items-center gap-3">';
                    echo '<img src="' . $foto_path . '" class="w-9 h-9 rounded-full object-cover border border-gray-200">';
                    echo htmlspecialchars($row['nome_produto']);
                    echo '</td>';
                    // Exibe o preço formatado em Real (R$)
                    echo '<td class="px-4 py-3">R$ ' . number_format($row['preco_produto'], 2, ',', '.') . '</td>';
                    // Renderiza os botões com parâmetros GET direcionando para read.php, update.php e delete.php
                    echo '<td class="px-4 py-3 text-center space-x-3">';
                    echo '<a href="read.php?id=' . $row['id_produto'] . '" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-eye"></i></a>';
                    echo '<a href="update.php?id=' . $row['id_produto'] . '" class="text-amber-600 hover:text-amber-800"><i class="fa-solid fa-pencil"></i></a>';
                    echo '<a href="delete.php?id=' . $row['id_produto'] . '" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></a>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                // Libera o espaço de memória utilizado pelo resultado
                mysqli_free_result($result);
            } else {
                // Mensagem para catálogo vazio
                echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">Nenhum produto cadastrado no momento.</div>';
            }
        } else {
            echo "Ops! Algo deu errado ao buscar os produtos.";
        }
        // Encerra a conexão com o banco de dados
        mysqli_close($link);
        ?>
    </div>
</body>
</html>