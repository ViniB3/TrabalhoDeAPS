<?php
session_start();

// --- ENUMS E CLASSES AUXILIARES ---
class StatusMesa {
    const DISPONIVEL = 'Disponível';
    const ATENDIMENTO = 'Em atendimento';
    const FECHANDO = 'Aguardando Pagamento';
}

// --- ENTIDADES DO CRUD ---

class Produto {
    public $id;
    public $nome;
    public $categoria; // Novo campo 
    public $preco;
    public $quantidade; // Estoque 

    public function __construct($id, $nome, $categoria, $preco, $quantidade) {
        $this->id = $id;
        $this->nome = $nome;
        $this->categoria = $categoria;
        $this->preco = $preco;
        $this->quantidade = $quantidade;
    }
}

class Cliente { // 
    public $id;
    public $nome;
    public $cpf;
    public $telefone;

    public function __construct($id, $nome, $cpf, $telefone) {
        $this->id = $id;
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
    }
}

class Funcionario { // 
    public $id;
    public $nome;
    public $funcao; // [cite: 427]
    public $login;
    public $senha; // Apenas simulação

    public function __construct($id, $nome, $funcao, $login) {
        $this->id = $id;
        $this->nome = $nome;
        $this->funcao = $funcao;
        $this->login = $login;
        $this->senha = '1234'; // Padrão
    }
}

// --- CLASSES DE NEGÓCIO ---

class ItemPedido {
    public $produto;
    public $quantidade;
    public $observacao;
    public $subtotal;

    public function __construct(Produto $produto, $quantidade, $observacao = "") {
        $this->produto = $produto;
        $this->quantidade = $quantidade;
        $this->observacao = $observacao;
        $this->subtotal = $produto->preco * $quantidade;
    }
}

class Mesa {
    public $numero;
    public $status;
    public $itens = [];
    public $total = 0.0;
    public $metodoPagamento = null;

    public function __construct($numero) {
        $this->numero = $numero;
        $this->status = StatusMesa::DISPONIVEL;
    }

    public function abrir() {
        if ($this->status !== StatusMesa::DISPONIVEL) throw new Exception("Mesa ocupada.");
        $this->status = StatusMesa::ATENDIMENTO;
        $this->itens = [];
        $this->total = 0;
        $this->metodoPagamento = null;
    }

    public function adicionarItem(Produto $produto, $quantidade, $observacao) {
        if ($this->status !== StatusMesa::ATENDIMENTO) throw new Exception("Abra a mesa primeiro.");
        // Validação de Estoque Básico [cite: 418]
        if ($produto->quantidade < $quantidade) {
            throw new Exception("Estoque insuficiente! Disponível: {$produto->quantidade}");
        }
        
        $this->itens[] = new ItemPedido($produto, $quantidade, $observacao);
        
        // Baixa no estoque temporária (na memória da sessão)
        $produto->quantidade -= $quantidade; 
        
        $this->calcularTotal();
    }

    public function cancelarItem($index, $senhaGerente) {
        if ($senhaGerente !== 'admin') throw new Exception("Senha inválida.");
        if (isset($this->itens[$index])) {
            // Devolve ao estoque [cite: 66]
            $item = $this->itens[$index];
            $item->produto->quantidade += $item->quantidade;

            unset($this->itens[$index]);
            $this->itens = array_values($this->itens);
            $this->calcularTotal();
        }
    }

    private function calcularTotal() {
        $this->total = 0;
        foreach ($this->itens as $item) $this->total += $item->subtotal;
    }

    public function solicitarFechamento() {
        if (empty($this->itens)) throw new Exception("Mesa vazia.");
        $this->status = StatusMesa::FECHANDO;
    }

    public function confirmarPagamento($metodo) {
        if ($this->status !== StatusMesa::FECHANDO) throw new Exception("Solicite o fechamento primeiro.");
        $this->metodoPagamento = $metodo;
        
        $dadosVenda = [
            'mesa' => $this->numero,
            'total' => $this->total,
            'metodo' => $metodo,
            'hora' => date('H:i')
        ];

        $this->status = StatusMesa::DISPONIVEL;
        $this->itens = [];
        $this->total = 0;
        
        return $dadosVenda;
    }
}

class Caixa {
    public $aberto = false;
    public $saldoInicial = 0;
    public $eventoEspecial = "";
    public $vendasDoDia = [];
    public $historicoFechamentos = [];

    public function abrir($troco, $evento) {
        if ($this->aberto) throw new Exception("Caixa já aberto.");
        $this->saldoInicial = $troco;
        $this->eventoEspecial = $evento;
        $this->aberto = true;
        $this->vendasDoDia = [];
    }

    public function registrarVenda($venda) {
        $this->vendasDoDia[] = $venda;
    }

    public function fechar() {
        if (!$this->aberto) throw new Exception("Caixa não está aberto.");
        $totalVendas = array_sum(array_column($this->vendasDoDia, 'total'));
        
        $this->historicoFechamentos[] = [
            'data' => date('d/m/Y H:i'),
            'evento' => $this->eventoEspecial,
            'saldo_inicial' => $this->saldoInicial,
            'total_vendas' => $totalVendas,
            'qtd_pedidos' => count($this->vendasDoDia)
        ];

        $this->aberto = false;
        $this->eventoEspecial = "";
        $this->saldoInicial = 0;
    }
}

// --- INICIALIZAÇÃO DE DADOS (DATABASE SIMULADO) ---
if (!isset($_SESSION['sistema'])) {
    // Populando dados iniciais para não começar vazio
    $cardapioInicial = [
        1 => new Produto(1, 'Isca de Tilápia', 'Porções', 45.00, 50),
        2 => new Produto(2, 'Batata Frita', 'Porções', 25.00, 100),
        3 => new Produto(3, 'Torresmo', 'Porções', 30.00, 30),
        4 => new Produto(4, 'Cerveja 600ml', 'Bebidas', 14.00, 200),
        5 => new Produto(5, 'Refrigerante', 'Bebidas', 6.00, 150)
    ];

    $funcionariosIniciais = [
        1 => new Funcionario(1, 'Guilherme Nishida', 'Gerente', 'nishida'),
        2 => new Funcionario(2, 'João da Silva', 'Garçom', 'joao')
    ];

    $clientesIniciais = [
        1 => new Cliente(1, 'Cliente Balcão', '000.000.000-00', '44 99999-9999')
    ];

    $_SESSION['sistema'] = [
        'caixa' => new Caixa(),
        'mesas' => [1 => new Mesa(1), 2 => new Mesa(2), 3 => new Mesa(3), 4 => new Mesa(4)],
        'cardapio' => $cardapioInicial,
        'clientes' => $clientesIniciais,
        'funcionarios' => $funcionariosIniciais
    ];
}
?>