<?php

    class Estoque{
        public $id;
        public $nome_produto;
        public $descricao;
        public $categoria;
        public $fornecedor;
        public $quantidade_em_estoque;
        public $preco_custo;
        public $data_entrada;
        public $data_saida;
        public $responsavel;
        public $observacoes;
    }


    interface EstoqueDao{    
        public function insert(Estoque $e);
        public function findById($id);
        public function update(Estoque $e);
    }



?>