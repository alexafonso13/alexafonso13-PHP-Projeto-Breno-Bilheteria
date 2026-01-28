<?php
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'apagar') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare('DELETE FROM eventos WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    header('Location: evento.php');
    exit;
}


$editEvento = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM eventos WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $editEvento = $stmt->fetch();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar') {
    $id = (int) ($_POST['id'] ?? 0);

    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $local = trim($_POST['local'] ?? '');
    $data_evento = trim($_POST['data_evento'] ?? '');
    $duracao_min = (int) ($_POST['duracao_min'] ?? 0);
    $capacidade = (int) ($_POST['capacidade'] ?? 0);
    $tipo_lugares = $_POST['tipo_lugares'] ?? 'geral';

    if ($id > 0) {
        $stmt = $db->prepare('UPDATE eventos SET titulo=:titulo, descricao=:descricao, local=:local, data_evento=:data_evento, duracao_min=:duracao_min, capacidade=:capacidade, tipo_lugares=:tipo_lugares WHERE id=:id');
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':local', $local);
        $stmt->bindParam(':data_evento', $data_evento);
        $stmt->bindParam(':duracao_min', $duracao_min, PDO::PARAM_INT);
        $stmt->bindParam(':capacidade', $capacidade, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_lugares', $tipo_lugares);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $db->prepare('INSERT INTO eventos (titulo, descricao, local, data_evento, duracao_min, capacidade, tipo_lugares) VALUES (:titulo,:descricao,:local,:data_evento,:duracao_min,:capacidade,:tipo_lugares)');
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':local', $local);
        $stmt->bindParam(':data_evento', $data_evento);
        $stmt->bindParam(':duracao_min', $duracao_min, PDO::PARAM_INT);
        $stmt->bindParam(':capacidade', $capacidade, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_lugares', $tipo_lugares);
        $stmt->execute();
    }

    header('Location: evento.php');
    exit;
}

$eventos = $db->query('SELECT * FROM eventos ORDER BY data_evento DESC')->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Eventos</title>
</head>
<body>
    <p><a href="index.php">Início</a> | <a href="inserir.php">Utilizadores</a></p>

    <h1>Eventos</h1>

    <h3><?php echo $editEvento ? 'Editar' : 'Novo'; ?></h3>
    <form method="post">
        <input type="hidden" name="acao" value="guardar">
        <input type="hidden" name="id" value="<?php echo (int) ($editEvento['id'] ?? 0); ?>">
        <p>Título: <input name="titulo" required value="<?php echo htmlspecialchars($editEvento['titulo'] ?? ''); ?>"></p>
        <p>Local: <input name="local" required value="<?php echo htmlspecialchars($editEvento['local'] ?? ''); ?>"></p>
        <p>Data/hora: <input type="datetime-local" name="data_evento" required value="<?php echo isset($editEvento['data_evento']) ? str_replace(' ', 'T', substr($editEvento['data_evento'], 0, 16)) : ''; ?>"></p>
        <p>Duração (min): <input type="number" name="duracao_min" min="0" required value="<?php echo htmlspecialchars($editEvento['duracao_min'] ?? ''); ?>"></p>
        <p>Capacidade: <input type="number" name="capacidade" min="1" required value="<?php echo htmlspecialchars($editEvento['capacidade'] ?? ''); ?>"></p>
        <p>Tipo lugares:
            <select name="tipo_lugares">
                <option value="geral" <?php echo (($editEvento['tipo_lugares'] ?? 'geral') === 'geral') ? 'selected' : ''; ?>>geral</option>
                <option value="numerado" <?php echo (($editEvento['tipo_lugares'] ?? 'geral') === 'numerado') ? 'selected' : ''; ?>>numerado</option>
            </select>
        </p>
        <p>Descrição:<br><textarea name="descricao" rows="3" cols="60"><?php echo htmlspecialchars($editEvento['descricao'] ?? ''); ?></textarea></p>
        <button>Guardar</button>
        <?php if ($editEvento): ?> <a href="evento.php">Cancelar</a><?php endif; ?>
    </form>

    <h3>Lista</h3>
    <table border="1" cellpadding="4">
        <tr>
            <th>ID</th><th>Título</th><th>Local</th><th>Data</th><th>Cap.</th><th>Tipo</th><th>Ações</th>
        </tr>
        <?php foreach ($eventos as $ev): ?>
            <tr>
                <td><?php echo (int) $ev['id']; ?></td>
                <td><?php echo htmlspecialchars($ev['titulo']); ?></td>
                <td><?php echo htmlspecialchars($ev['local']); ?></td>
                <td><?php echo htmlspecialchars($ev['data_evento']); ?></td>
                <td><?php echo (int) $ev['capacidade']; ?></td>
                <td><?php echo htmlspecialchars($ev['tipo_lugares']); ?></td>
                <td>
                    <a href="evento.php?edit=<?php echo (int) $ev['id']; ?>">editar</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Apagar?');">
                        <input type="hidden" name="acao" value="apagar">
                        <input type="hidden" name="id" value="<?php echo (int)$ev['id']; ?>">
                        <button type="submit">apagar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
