<style>
    .ws-error-message {
        color: #ff0000;
    }
</style>

<div id="<?= $form['name'] ?>_wrapper">
    <form id="<?= $form['name'] ?>" <?php if (isset($form['form_class']) && $form['form_class']): ?>class="<?= $form['form_class'] ?>"<?php endif; ?>>
        <?php if (isset($form['fieldsets']) && is_array($form['fieldsets'])): ?>
            <?php foreach ($form['fieldsets'] as $fieldset): ?>
                <fieldset <?php if (isset($fieldset['fieldset_class']) && $fieldset['fieldset_class']): ?>class="<?= $fieldset['fieldset_class'] ?>"<?php endif; ?>>
                    <?php foreach ($fieldset['fields'] as $field): ?>
                        <?php
                            if ($field['type'] === 'textarea') {
                                $input = "<textarea name='{$field['name']}'></textarea>";
                            } else if ($field['type'] === 'text') {
                                $input = "<input type='text' name='{$field['name']}'>";
                            }
                        ?>

                        <?= str_replace(
                            array('%label%', '%field%'),
                            array(WPSugar\Localization::getMessage($field['label']), $input),
                            $form['field_template']
                        ); ?>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($form['fields']) && isset($form['fields'])): ?>
            <?php foreach ($form['fields'] as $field): ?>
                <?php
                    if ($field['type'] === 'textarea') {
                        $input = "<textarea name='{$field['name']}'></textarea>";
                    } else if ($field['type'] === 'text') {
                        $input = "<input type='text' name='{$field['name']}'>";
                    }
                ?>

                <?= str_replace(
                    array('%label%', '%field%'),
                    array(WPSugar\Localization::getMessage($field['label']), $input),
                    $form['field_template']
                ); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </form>
    <?= str_replace(
        array('%id%'),
        array($form['name'] . '_btn'),
        WPSugar\Localization::getTranslatedContent($form['submit_button'])
    ); ?>
</div>

<script>
(function($) {
    $(function() {
        var form = {
            name: '<?= $form['name'] ?>'
        };
        <?php if (isset($form['success_callback'])): ?>
            form.success_callback = '<?= $form['success_callback'] ?>';
        <?php endif; ?>

        $('#' + form.name + '_btn').on('click', function(event) {
            event.preventDefault();
            $('.ws-error-message, .ws-success-message').remove();
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                data: 'action=' + form.name + '&' + $('#' + form.name).serialize(),
                success: function(result) {
                    $('#' + form.name + '_wrapper').html('<div class="ws-success-message">' + result + '</div>');
                    if (form.success_callback && typeof window[form.success_callback] === 'function') {
                        window[form.success_callback]();
                    }
                },
                error: function(result) {
                    try {
                        var errors = JSON.parse(result.responseText);
                        if (errors && typeof errors === 'object') {
                            for (var key in errors) {
                                $('#' + form.name + ' *[name=' + key + ']')
                                    .before('<div class="ws-error-message">' + errors[key] + '</div>');
                            }
                        }
                    } catch(e) {}
                }
            });
        });
    });
})(jQuery);
</script>