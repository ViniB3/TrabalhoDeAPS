<?php require_once 'controlador.php'; 
$mensagem = isset($msg) ? $msg : '';
$tipoMsg = isset($tipo) ? $tipo : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Petiscaria Nishida 2.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
    <h1>Petiscaria Nishida</h1>
    <h2><a href="admin.php" style="color: white; margin-left: 20px; font-weight: bold;">[ Acessar Administração ]</a></h2>
    <div>
        Status Caixa: 
        <span class="badge <?php echo $sistema['caixa']->aberto ? 'verde' : 'vermelho'; ?>">
            <?php echo $sistema['caixa']->aberto ? 'ABERTO' : 'FECHADO'; ?>
        </span>
        <?php if($sistema['caixa']->aberto && $sistema['caixa']->eventoEspecial): ?>
            <span class="badge azul">Evento: <?php echo $sistema['caixa']->eventoEspecial; ?></span>
        <?php endif; ?>
    </div>
</div>

<?php if ($mensagem): ?>
    <div class="alerta <?php echo $tipoMsg; ?>"><?php echo $mensagem; ?></div>
<?php endif; ?>

<div class="container">
    
    <div class="panel">
        <h2>Painel do Gerente</h2>
        <?php if (!$sistema['caixa']->aberto): ?>
            <form method="POST">
                <input type="hidden" name="acao" value="abrir_caixa">
                <div class="form-group">
                    <label>Fundo de Troco (R$):</label>
                    <input type="number" name="troco" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Evento Especial (Opcional):</label>
                    <input type="text" name="evento_especial" placeholder="Ex: Dia dos Namorados">
                </div>
                <button type="submit" class="btn-primary">Abrir Caixa </button>
            </form>
            
            <?php if (!empty($sistema['caixa']->historicoFechamentos)): ?>
                <h3>Histórico de Fechamentos</h3>
                <ul>
                    <?php foreach ($sistema['caixa']->historicoFechamentos as $h): ?>
                        <li><?php echo "{$h['data']} - Vendas: R$ " . number_format($h['total_vendas'], 2, ',', '.') . " ({$h['evento']})"; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        <?php else: ?>
            <p><strong>Vendas Hoje:</strong> <?php echo count($sistema['caixa']->vendasDoDia); ?> pedidos.</p>
            <form method="POST">
                <input type="hidden" name="acao" value="fechar_caixa">
                <button type="submit" class="btn-danger">Fechar Caixa</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="mesas-grid">
        <?php foreach ($sistema['mesas'] as $id => $mesa): ?>
            <div class="mesa-card <?php echo $mesa->status == 'Disponível' ? 'livre' : 'ocupada'; ?>">
                <h3>Mesa <?php echo $id; ?></h3>
                <small><?php echo $mesa->status; ?></small>

                <?php if ($mesa->status == 'Disponível'): ?>
                    <form method="POST">
                        <input type="hidden" name="acao" value="abrir_mesa">
                        <input type="hidden" name="mesa_id" value="<?php echo $id; ?>">
                        <button class="btn-success" <?php echo !$sistema['caixa']->aberto ? 'disabled' : ''; ?>>
                            Abrir Mesa
                        </button>
                    </form>

                <?php elseif ($mesa->status == 'Em atendimento'): ?>
                    <div class="lista-itens">
                        <?php foreach ($mesa->itens as $idx => $item): ?>
                            <div class="item-row">
                                <span><?php echo "{$item->quantidade}x {$item->produto->nome}"; ?></span>
                                <span class="preco">R$ <?php echo $item->subtotal; ?></span>
                                <?php if($item->observacao): ?>
                                    <br><small>Obs: <?php echo $item->observacao; ?></small>
                                <?php endif; ?>
                                
                                <form method="POST" style="margin-top:5px;">
                                    <input type="hidden" name="acao" value="cancelar_item">
                                    <input type="hidden" name="mesa_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="item_index" value="<?php echo $idx; ?>">
                                    <input type="password" name="senha_gerente" placeholder="Senha (admin)" size="8">
                                    <button class="btn-danger-sm">X</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="total">Total: R$ <?php echo number_format($mesa->total, 2); ?></div>

                    <form method="POST" class="form-pedido">
                        <input type="hidden" name="acao" value="adicionar_item">
                        <input type="hidden" name="mesa_id" value="<?php echo $id; ?>">
                        <select name="produto" required>
                            <?php foreach ($sistema['cardapio'] as $p): ?>
                                <option value="<?php echo $p->nome; ?>"><?php echo "{$p->nome} (R$ {$p->preco})"; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="quantidade" value="1" min="1" style="width:40px">
                        <input type="text" name="observacao" placeholder="Obs: Sem cebola..." style="width:100%; margin-top:5px;">
                        <button class="btn-primary">Adicionar</button>
                    </form>

                    <form method="POST" style="margin-top:10px;">
                        <input type="hidden" name="acao" value="fechar_mesa">
                        <input type="hidden" name="mesa_id" value="<?php echo $id; ?>">
                        <button class="btn-warning">Fechar Conta</button>
                    </form>

                <?php elseif ($mesa->status == 'Aguardando Pagamento'): ?>
                    <div class="total-final">A Pagar: R$ <?php echo number_format($mesa->total, 2); ?></div>
                    <form method="POST">
                        <input type="hidden" name="acao" value="pagar_mesa">
                        <input type="hidden" name="mesa_id" value="<?php echo $id; ?>">
                        <label>Forma de Pagamento:</label>
                        <select name="metodo_pagamento" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão Crédito">Cartão Crédito</option>
                            <option value="Cartão Débito">Cartão Débito</option>
                            <option value="PIX">PIX</option>
                        </select>
                        <button class="btn-success" style="margin-top:10px; width:100%">Finalizar</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>