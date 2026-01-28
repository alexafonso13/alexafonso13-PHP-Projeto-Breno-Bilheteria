<?php
require 'connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'apagar') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare('DELETE FROM utilizadores WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    header('Location: inserir.php');
    exit;
}


$editUser = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM utilizadores WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $editUser = $stmt->fetch();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar') {
    $id = (int) ($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $documento = trim($_POST['documento_identificacao'] ?? '');

    if ($id > 0) {
        $stmt = $db->prepare('UPDATE utilizadores SET nome=:nome, email=:email, telefone=:telefone, documento_identificacao=:documento WHERE id=:id');
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $db->prepare('INSERT INTO utilizadores (nome,email,telefone,documento_identificacao) VALUES (:nome,:email,:telefone,:documento)');
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();
    }

    header('Location: inserir.php');
    exit;
}

$utilizadores = $db->query('SELECT * FROM utilizadores ORDER BY id DESC')->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Utilizadores</title>
</head>
<body>
    <p><a href="index.php">Início</a> | <a href="evento.php">Eventos</a></p>

    <h1>Utilizadores</h1>

    <h3><?php echo $editUser ? 'Editar' : 'Novo'; ?></h3>
    <form method="post">
        <input type="hidden" name="acao" value="guardar">
        <input type="hidden" name="id" value="<?php echo (int) ($editUser['id'] ?? 0); ?>">
        <p>Nome: <input name="nome" required value="<?php echo htmlspecialchars($editUser['nome'] ?? ''); ?>"></p>
        <p>Email: <input type="email" name="email" required value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>"></p>
        <p>Telefone: <input name="telefone" value="<?php echo htmlspecialchars($editUser['telefone'] ?? ''); ?>"></p>
        <p>Documento: <input name="documento_identificacao" required value="<?php echo htmlspecialchars($editUser['documento_identificacao'] ?? ''); ?>"></p>
        <button>Guardar</button>
        <?php if ($editUser): ?> <a href="inserir.php">Cancelar</a><?php endif; ?>
    </form>

    <h3>Lista</h3>
    <table border="1" cellpadding="4">
        <tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Documento</th><th>Ações</th></tr>
        <?php foreach ($utilizadores as $u): ?>
            <tr>
                <td><?php echo (int) $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['nome']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['telefone']); ?></td>
                <td><?php echo htmlspecialchars($u['documento_identificacao']); ?></td>
                <td>
                    <a href="inserir.php?edit=<?php echo (int) $u['id']; ?>">editar</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Apagar?');">
                        <input type="hidden" name="acao" value="apagar">
                        <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                        <button type="submit">apagar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

