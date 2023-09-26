<div class="mt-1">
    TABLE NAME : <span class="px-2 font-bold bg-blue text-white"> <?php echo str_replace('_', ' ', $tableStatus['table']); ?> </span>
    <?php if (!empty($tableStatus['table_comment'])): ?>
        <div class="mt-0">
            <span class="text-white mt-1">suggestion(s)</span>
        </div>
        <ol class='mt-1 ml-1'>
            <?php foreach ($tableStatus['table_comment'] as $commentKey => $comment): ?>
                <li>
                    <span class="text-yellow"><?php echo $comment; ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
    <div class="mt-1">
        <table class="w-full">
            <thead>
                <tr>
                    <th> field name</th>
                    <th> standard check</th>
                    <th> datatype </th>
                    <th> size </th>
                    <th> suggestion(s)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tableStatus['fields'] as $key => $field): ?>
                    <tr>
                        <?php if (!empty($field)): ?>
                            <?php if ((isset($field['suggestion']) && isset($field['datatype']) && count($field) === 2) || count($field) === 1): ?>
                                <td><?php echo $key; ?></td>
                                <td class="text-green">✓</td>
                            <?php else: ?>
                                <td class="text-red"><?php echo $key; ?></td>
                                <td class="text-red">✗</td>
                            <?php endif; ?>

                            <td><?php echo $field['datatype']['data_type'] ?? "-"; ?></td>
                            <td><?php echo $field['datatype']['size'] ?? "-"; ?></td>
                            <?php
                                if (isset($field['datatype'])) {
                                    unset($field['datatype']);
                                }
                            ?>
                            <?php foreach ($field as $key => $fieldComment): ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <?php if ($key === 'suggestion'): ?>
                                        <td class="text-yellow flex"><?php echo $fieldComment; ?></td>
                                    <?php else: ?>
                                        <td class="text-red flex"><?php echo $fieldComment; ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <td><?php echo $key; ?></td>
                            <td class="text-green">✓</td>
                            <td><?php echo $field['datatype']['data_type'] ?? "-"; ?></td>
                            <td><?php echo $field['datatype']['size'] ?? "-"; ?></td>
                            <td>-</td>
                        <?php endif; ?>
                    </tr>
                    <?php unset($field['datatype']); ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
