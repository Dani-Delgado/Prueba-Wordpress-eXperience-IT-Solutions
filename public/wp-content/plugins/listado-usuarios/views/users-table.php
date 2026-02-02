<?php
// $users viene del handler AJAX
if (!isset($users)) $users = [];
?>

<table>
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Apellido 1</th>
            <th>Apellido 2</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo esc_html($user['username']); ?></td>
                    <td><?php echo esc_html($user['name']); ?></td>
                    <td><?php echo esc_html($user['surname1']); ?></td>
                    <td><?php echo esc_html($user['surname2']); ?></td>
                    <td><?php echo esc_html($user['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No hay resultados</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
