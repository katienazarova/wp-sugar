<input type="hidden" name="meta_box_nonce" value="<?= $nonce ?>" />
<p style="padding:10px 0 0 0;">Заполните дополнительные свойства.</p>

<table class="form-table">
    <?php foreach ($fields as $field): ?>
        <?php $meta = get_post_meta($post->ID, $field['id'], true); ?>
        
        <tr style="border-top: 1px solid #eeeeee;">
            <th style="width: 25%">
                <label for="<?= $field['id'] ?>">
                    <strong><?= $field['name'] ?></strong>
                    <span style="display: block; color: #999; margin: 5px 0 0 0; line-height: 16px; font-size: 11px; font-style: italic;"><?= $field['desc'] ?></span>
                </label>
            </th>
            <td>
                <?php if ($field['type'] === 'text'): ?>
                    <input type="text" name="<?= $field['id'] ?>" id="<?= $field['id'] ?>" value="<?= $meta ? $meta : stripslashes(htmlspecialchars(($field['std']), ENT_QUOTES))?>" size="30" style="width:75%; margin-right: 20px; float:left;" />
                <?php elseif ($field['type'] === 'textarea'): ?>
                    <textarea name="<?= $field['id'] ?>" id="<?= $field['id'] ?>" rows="8" cols="5" style="width:75%; margin-right: 20px; float:left;"><?= $meta ? $meta : $field['std'] ?></textarea>
                <?php elseif ($field['type'] === 'select'): ?>
                    <select id="<?= $field['id'] ?>" name="<?= $field['id'] ?>">
                        <?php foreach ($field['options'] as $key => $value): ?>
                            <option <?php if ($meta == $key): ?>selected="selected"<?php endif; ?> value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>