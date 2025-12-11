<?php
require_once 'classes.php';

$sistema = &$_SESSION['sistema'];
$msg = "";
$tipo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $acao = $_POST['acao'];

        // ==========================================
        //  CRUDs (BACKOFFICE)
        // ==========================================

        // --- CRUD PRODUTOS (ESTOQUE) [cite: 415] ---
        if ($acao === 'salvar_produto') {
            $id = !empty($_POST['id']) ? intval($_POST['id']) : (empty($sistema['cardapio']) ? 1 : max(array_keys($sistema['cardapio'])) + 1);
            $nome = $_POST['nome'];
            $cat = $_POST['categoria'];
            $preco = floatval($_POST['preco']);
            $qtd = intval($_POST['quantidade']);

            $sistema['cardapio'][$id] = new Produto($id, $nome, $cat, $preco, $qtd);
            $msg = "Produto salvo com sucesso!";
            $tipo = "success";
        }
        elseif ($acao === 'excluir_produto') {
            $id = intval($_POST['id']);
            unset($sistema['cardapio'][$id]);
            $msg = "Produto removido!";
            $tipo = "success";
        }

        // --- CRUD CLIENTES [cite: 421] ---
        elseif ($acao === 'salvar_cliente') {
            $id = !empty($_POST['id']) ? intval($_POST['id']) : (empty($sistema['clientes']) ? 1 : max(array_keys($sistema['clientes'])) + 1);
            $sistema['clientes'][$id] = new Cliente($id, $_POST['nome'], $_POST['cpf'], $_POST['telefone']);
            $msg = "Cliente salvo com sucesso!";
            $tipo = "success";
        }
        elseif ($acao === 'excluir_cliente') {
            $id = intval($_POST['id']);
            unset($sistema['clientes'][$id]);
            $msg = "Cliente removido!";
            $tipo = "success";
        }

        // --- CRUD FUNCIONÁRIOS [cite: 426] ---
        elseif ($acao === 'salvar_funcionario') {
            $id = !empty($_POST['id']) ? intval($_POST['id']) : (empty($sistema['funcionarios']) ? 1 : max(array_keys($sistema['funcionarios'])) + 1);
            $sistema['funcionarios'][$id] = new Funcionario($id, $_POST['nome'], $_POST['funcao'], $_POST['login']);
            $msg = "Funcionário salvo com sucesso!";
            $tipo = "success";
        }
        elseif ($acao === 'excluir_funcionario') {
            $id = intval($_POST['id']);
            unset($sistema['funcionarios'][$id]);
            $msg = "Funcionário removido!";
            $tipo = "success";
        }

        // ==========================================
        //  FRENTE DE CAIXA (EXISTENTE)
        // ==========================================
        
        elseif ($acao === 'abrir_caixa') {
            $sistema['caixa']->abrir(floatval($_POST['troco']), $_POST['evento_especial']);
            $msg = "Caixa aberto!";
            $tipo = "success";
        }
        elseif ($acao === 'fechar_caixa') {
            $sistema['caixa']->fechar();
            $msg = "Caixa fechado!";
            $tipo = "warning";
        }
        elseif ($acao === 'abrir_mesa') {
            if (!$sistema['caixa']->aberto) throw new Exception("Caixa fechado!");
            $sistema['mesas'][intval($_POST['mesa_id'])]->abrir();
            $msg = "Mesa aberta.";
            $tipo = "success";
        }
        elseif ($acao === 'adicionar_item') {
            $idMesa = intval($_POST['mesa_id']);
            $prodNome = $_POST['produto'];
            
            // Find product by name
            $idProd = null;
            foreach ($sistema['cardapio'] as $id => $prod) {
                if ($prod->nome === $prodNome) {
                    $idProd = $id;
                    break;
                }
            }
            if ($idProd === null) throw new Exception("Produto inválido.");
            
            $sistema['mesas'][$idMesa]->adicionarItem(
                $sistema['cardapio'][$idProd], 
                intval($_POST['quantidade']), 
                $_POST['observacao']
            );
            $msg = "Item adicionado.";
            $tipo = "success";
        }
        elseif ($acao === 'cancelar_item') {
            $sistema['mesas'][intval($_POST['mesa_id'])]->cancelarItem(intval($_POST['item_index']), $_POST['senha_gerente']);
            $msg = "Item cancelado.";
            $tipo = "success";
        }
        elseif ($acao === 'fechar_mesa') {
            $sistema['mesas'][intval($_POST['mesa_id'])]->solicitarFechamento();
            $msg = "Aguardando pagamento.";
            $tipo = "warning";
        }
        elseif ($acao === 'pagar_mesa') {
            $venda = $sistema['mesas'][intval($_POST['mesa_id'])]->confirmarPagamento($_POST['metodo_pagamento']);
            $sistema['caixa']->registrarVenda($venda);
            $msg = "Mesa finalizada!";
            $tipo = "success";
        }

    } catch (Exception $e) {
        $msg = "Erro: " . $e->getMessage();
        $tipo = "error";
    }
}
?>