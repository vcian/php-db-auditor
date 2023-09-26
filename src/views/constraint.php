<div class="mx-2 my-1">
    <div class="space-x-1">
        TABLE NAME : <span class="px-1 bg-blue-500 text-black"><?= $data['table'] ?></span>
    </div>

    <div class="flex space-x-1 mt-1">
        <span class="font-bold text-green">Columns</span>
        <span class="flex-1 content-repeat-[.] text-gray"></span>
        <span class="font-bold"><?= $data['field_count'] ?></span>
    </div>

    <div class="flex space-x-1">
        <span class="font-bold text-green">Table Size</span>
        <span class="flex-1 content-repeat-[.] text-gray"></span>
        <span class="font-bold"><?= $data['size'] ?></span>
    </div>

    <div class="mt-1">
        <div class="flex space-x-1">
            <span class="font-bold text-green">Fields</span>
            <span class="flex-1 content-repeat-[.] text-gray"></span>
            <span class="font-bold">Data Type</span>
        </div>

        <?php foreach ($data['fields'] as $field): ?>
            <div class="flex space-x-1">
                <span class="font-bold"><?= $field['COLUMN_NAME'] ?></span>
                <i class="text-blue"><?= $field['COLUMN_TYPE'] ?></i>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                <span class="font-bold text-green"><?= $field['DATA_TYPE'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-1">
        <?php foreach ($data['constrain'] as $key => $value): ?>
            <?php if ($value): ?>
                <div class="space-x-1 mt-1">
                    <span class="px-1 bg-green-500 text-black"><?= strtoupper($key) ?></span>
                </div>
                <?php foreach ($value as $constrainField): ?>
                    <?php if ($key === 'foreign'): ?>
                        <div class="flex space-x-1">
                            <span class="font-bold"><?= $constrainField['column_name'] ?></span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <i class="text-blue"><?= $constrainField['foreign_table_name'] ?></i>
                            <span class="font-bold text-green"><?= $constrainField['foreign_column_name'] ?></span>
                        </div>
                    <?php else: ?>
                        <div class="flex space-x-1">
                            <span class="font-bold"><?= $constrainField ?></span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
