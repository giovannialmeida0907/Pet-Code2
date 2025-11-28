<?php
require_once('conexao.php');

$conn = conectar_banco();
global $table_name;

echo "<h2>Usuários no Banco de Dados</h2>";

$result = $conn->query("SELECT * FROM $table_name");
if ($result->num_rows > 0) {
    echo "<table border='1' style='width:100%'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Senha</th><th>Tamanho</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td style='font-family: monospace;'>" . htmlspecialchars($row['senha']) . "</td>";
        echo "<td>" . strlen($row['senha']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum usuário encontrado.</p>";
}

$conn->close();
?>