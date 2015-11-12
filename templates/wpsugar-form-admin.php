<h2><?= $form['admin_title'] ?></h2>
<?php if ($results): ?>
    <table id="<?= $form['name'] ?>" class="wp-list-table widefat fixed posts" cellspacing="0" cellpadding="0">
        <thead><tr>
            <?php foreach($fields as $field): ?>
                <th><strong><?= $field['label'] ?></strong></th>
            <?php endforeach; ?>
        </tr></thead>

        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <?php foreach($fields as $field): ?>
                        <td><?= $result[$field['name']] ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><i>Список пуст</i></p>
<?php endif; ?>