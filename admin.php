<?php require_once 'controlador.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Administração - Petiscaria Nishida</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
    <h1>Petiscaria Nishida - Controle</h1>
    <a href="index.php" class="btn-warning" style="width: auto; padding: 5px 15px; text-decoration: none; color: black;">Voltar para o Caixa</a>
</div>

<?php if ($msg): ?>
    <div class="alerta <?php echo $tipo; ?>"><?php echo $msg; ?></div>
<?php endif; ?>

<div class="container">

    <div class="panel">
        <h2>Gestão de Estoque e Produtos</h2>
        <form method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <input type="hidden" name="acao" value="salvar_produto">
            <input type="hidden" name="id" value=""> <div style="display: flex; gap: 10px;">
                <input type="text" name="nome" placeholder="Nome do Produto" required>
                <input type="text" name="categoria" placeholder="Categoria" required>
                <input type="number" name="preco" step="0.01" placeholder="Preço (R$)" required>
                <input type="number" name="quantidade" placeholder="Qtd Estoque" required>
                <button type="submit" class="btn-success" style="width: 150px;">Salvar</button>
            </div>
        </form>

        <table width="100%" border="1" style="border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #eee;">
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th> <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sistema['cardapio'] as $prod): ?>
                <tr>
                    <td><?php echo $prod->id; ?></td>
                    <td><?php echo $prod->nome; ?></td>
                    <td><?php echo $prod->categoria; ?></td>
                    <td>R$ <?php echo number_format($prod->preco, 2); ?></td>
                    <td><?php echo $prod->quantidade; ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir?');">
                            <input type="hidden" name="acao" value="excluir_produto">
                            <input type="hidden" name="id" value="<?php echo $prod->id; ?>">
                            <button class="btn-danger-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Gestão de Clientes</h2>
        <form method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <input type="hidden" name="acao" value="salvar_cliente">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="nome" placeholder="Nome do Cliente" required>
                <input type="text" name="cpf" placeholder="CPF">
                <input type="text" name="telefone" placeholder="Telefone">
                <button type="submit" class="btn-success" style="width: 150px;">Salvar</button>
            </div>
        </form>

        <table width="100%" border="1" style="border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #eee;">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sistema['clientes'] as $cli): ?>
                <tr>
                    <td><?php echo $cli->id; ?></td>
                    <td><?php echo $cli->nome; ?></td>
                    <td><?php echo $cli->cpf; ?></td>
                    <td><?php echo $cli->telefone; ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir?');">
                            <input type="hidden" name="acao" value="excluir_cliente">
                            <input type="hidden" name="id" value="<?php echo $cli->id; ?>">
                            <button class="btn-danger-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Gestão de Funcionários</h2>
        <form method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <input type="hidden" name="acao" value="salvar_funcionario">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="nome" placeholder="Nome" required>
                <select name="funcao" required>
                    <option value="Gerente">Gerente</option>
                    <option value="Caixa">Caixa</option>
                    <option value="Atendente">Atendente</option>
                </select>
                <input type="text" name="login" placeholder="Login Sistema" required>
                <button type="submit" class="btn-success" style="width: 150px;">Salvar</button>
            </div>
        </form>

        <table width="100%" border="1" style="border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #eee;">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Função</th>
                    <th>Login</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sistema['funcionarios'] as $func): ?>
                <tr>
                    <td><?php echo $func->id; ?></td>
                    <td><?php echo $func->nome; ?></td>
                    <td><?php echo $func->funcao; ?></td>
                    <td><?php echo $func->login; ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir?');">
                            <input type="hidden" name="acao" value="excluir_funcionario">
                            <input type="hidden" name="id" value="<?php echo $func->id; ?>">
                            <button class="btn-danger-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>