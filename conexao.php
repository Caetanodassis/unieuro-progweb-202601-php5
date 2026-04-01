<?php
session_start();

// 1. Captura os dados do formulário apenas se eles existirem (evita o Warning)
if (isset($_POST["username"])) {
    $_SESSION["username"] = $_POST["username"];
}
if (isset($_POST["password"])) {
    $_SESSION["password"] = $_POST["password"];
}
if (isset($_GET["tentativa"])) {
    $_SESSION["tentativa"] = $_GET["tentativa"];
}

// 2. Tenta conectar ao banco de dados
$host = "localhost";
$dbname = "produtos";
$user = "postgres";
$pass = "123456";

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass");

// 3. Verifica se a conexão falhou antes de tentar rodar qualquer query
if (!$conn) {
    die("Erro crítico: Não foi possível conectar ao banco '$dbname'. Verifique se ele existe no pgAdmin.");
}

// 4. Proteção contra acesso direto: se não houver usuário na sessão, volta pro login
if (!isset($_SESSION["username"])) {
    header("Location: login.php?msgerro=Sessão expirada ou inválida.");
    exit();
}

// 5. Busca o usuário usando parâmetros seguros (evita SQL Injection)
$sql = "SELECT * FROM usuario WHERE username = $1 AND password = $2";
$resultado = pg_query_params($conn, $sql, array($_SESSION["username"], $_SESSION["password"]));

// 6. Se o usuário não existir no banco, manda de volta para o login
if (!$linha = pg_fetch_assoc($resultado)) {
    session_destroy(); // Limpa a sessão se os dados estiverem errados
    header("Location: login.php?msgerro=Usuário ou senha incorretos!");
    exit();
}

// Se chegou aqui, a conexão deu certo e o usuário é válido!
?>