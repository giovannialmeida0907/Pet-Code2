<?php
require_once('conexao.php');

// A variável $table_name é carregada de conexao.php
global $table_name; 

// Inicie a sessão para mensagens de sucesso/erro, se necessário
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    
    $conn = conectar_banco();

    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $pergunta = $_POST['pergunta'] ?? '';
    $resposta = $_POST['resposta'] ?? '';
    
    $nome_pet = $_POST['nome_pet'] ?? '';
    $especie = $_POST['especie'] ?? '';
    $idade = (int)($_POST['idade'] ?? 0);

    if ($senha !== $confirmar_senha) {
        $message = "Erro: As senhas não coincidem.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Erro: Formato de e-mail inválido.";
        $message_type = "error";
    } elseif (empty($nome) || empty($email) || empty($senha)) {
        $message = "Erro: Por favor, preencha todos os campos obrigatórios.";
        $message_type = "error";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $check_sql = "SELECT id FROM $table_name WHERE email = ?";
        $stmt_check = $conn->prepare($check_sql);
        
        if ($stmt_check === false) {
             $message = "Erro na preparação da consulta de verificação: " . $conn->error;
             $message_type = "error";
        } else {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $message = "Erro: Este e-mail já está cadastrado.";
                $message_type = "error";
            } else {
                $sql = "INSERT INTO $table_name (nome, email, senha_hash, pergunta_seguranca, resposta_seguranca, nome_pet, especie, idade_pet) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    $message = "Erro na preparação da consulta de inserção: " . $conn->error;
                    $message_type = "error";
                } else {
                    $stmt->bind_param("sssssssi", $nome, $email, $senha_hash, $pergunta, $resposta, $nome_pet, $especie, $idade);

                    if ($stmt->execute()) {
                        $message = "Cadastro realizado com sucesso! Bem-vindo(a) à Pet-code.";
                        $message_type = "success";
                    } else {
                        $message = "Erro ao cadastrar: " . $stmt->error;
                        $message_type = "error";
                    }
                    $stmt->close();
                }
            }
            $stmt_check->close();
        }
    }

    if (isset($message)) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $message_type;
        // Redireciona para evitar reenvio do formulário
        header("Location: pagina_do_formulario.php"); 
        exit();
    }
    
    $conn->close();
}
?>