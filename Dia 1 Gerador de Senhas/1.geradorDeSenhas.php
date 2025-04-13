<?php
session_start();

include 'config.php'; 

function gerarSenha($tamanho) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $senha = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $senha .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    return $senha;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["gerar"])) {
    $_SESSION["senha_gerada"] = gerarSenha(intval($_POST["tamanho"] ?? 12));
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["salvar"]) && isset($_SESSION["senha_gerada"])) {
    if (!isset($conn)) {
        die("Erro na conexão com o banco de dados.");
    }

    $stmt = $conn->prepare("INSERT INTO senhas (senha) VALUES (?)");
    $stmt->bind_param("s", $_SESSION["senha_gerada"]);

    if ($stmt->execute()) {
        echo "<p>Senha salva no banco: <strong>{$_SESSION["senha_gerada"]}</strong></p>";
        unset($_SESSION["senha_gerada"]); 
    } else {
        echo "<p>Erro ao salvar a senha: " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerador de Senhas</title>
</head>
<body>
    <form method="post">
        <label for="tamanho">Tamanho da senha:</label>
        <input type="number" name="tamanho" id="tamanho" value="12" min="6" max="30">
        <button type="submit" name="gerar">Gerar Senha</button>
    </form>

    <?php if (isset($_SESSION["senha_gerada"])): ?>
        <p>Senha gerada: <strong><?php echo $_SESSION["senha_gerada"]; ?></strong></p>
        <form method="post">
            <button type="submit" name="salvar">Salvar Senha</button>
        </form>
    <?php endif; ?>
</body>
</html>