<?php
$mensagem = "";
$nome = "";
$preco = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomeInformado = $_POST["nome"] ?? "";
    $precoInformado = $_POST["preco"] ?? "";
    $nome = is_string($nomeInformado) ? trim($nomeInformado) : "";
    $preco = is_string($precoInformado) ? $precoInformado : "";

    if ($nome === "") {
        $mensagem = "Erro: O nome do produto não pode estar vazio.";
    } elseif (!is_numeric($preco) || (float) $preco <= 0) {
        $mensagem = "Erro: O preço deve ser um número positivo.";
    } else {
        $servername = "localhost";
        $username = "root";
        $password = "Senai@118";
        $dbname = "exercicio";
        $conn = null;
        $stmt = null;

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli($servername, $username, $password, $dbname);
            $conn->set_charset("utf8mb4");
            $sql = "INSERT INTO produtos (nome, preco) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $precoNumerico = (float) $preco;
            $stmt->bind_param("sd", $nome, $precoNumerico);
            $stmt->execute();
            $mensagem = "Produto cadastrado com sucesso!";
        } catch (mysqli_sql_exception $erro) {
            $mensagem = "Não foi possível cadastrar o produto. Verifique as credenciais do MySQL e se o banco "
                . "exercicio contém a tabela produtos. Detalhe: " . $erro->getMessage();
        } finally {
            if ($stmt instanceof mysqli_stmt) {
                $stmt->close();
            }
            if ($conn instanceof mysqli) {
                $conn->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos</title>
</head>

<body>

    <h1>Cadastro de Produtos</h1>

    <form method="post">

        <label for="nome">Nome do Produto:</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome, ENT_QUOTES, "UTF-8") ?>" required>

        <br><br>

        <label for="preco">Preço:</label>
        <input type="number" name="preco" id="preco"
               step="0.01" min="0.01" value="<?= htmlspecialchars($preco, ENT_QUOTES, "UTF-8") ?>" required>

        <br><br>

        <button type="submit">Cadastrar Produto</button>

    </form>

    <br>

    <?php
    if ($mensagem !== "") {
        echo "<p>" . htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") . "</p>";
    }
    ?>

</body>
</html>