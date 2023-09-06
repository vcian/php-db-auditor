<div class="mx-2 my-1">
    <div class="space-x-1">
        TABLE NAME : <span
            class="px-1 bg-blue-500 text-black"><?php echo $data['table']; ?> ?></span>
    </div>
    <div class="flex space-x-1 mt-1">
        <span class="font-bold text-green">Columns</span>
        <span class="flex-1 content-repeat-[.] text-gray"></span>
        <span class="font-bold"><?php echo $data['field_count']; ?></span>
    </div>

    <div class="flex space-x-1">
        <span class="font-bold text-green">Table Size</span>
        <span class="flex-1 content-repeat-[.] text-gray"></span>
        <span class="font-bold"><?php echo $data['size']; ?></span>
    </div>

    <div class="mt-1">
        <div class="flex space-x-1">
            <span class="font-bold text-green">Fields</span>
            <span class="flex-1 content-repeat-[.] text-gray"></span>
            <span class="font-bold">Data Type</span>
        </div>

        <?php foreach($data['fields'] as $field) { ?>
            <div class="flex space-x-1">
                <span class="font-bold"><?php echo $field['COLUMN_NAME']; ?></span>
                <i class="text-blue"><?php echo $field['COLUMN_TYPE']; ?></i>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                <span class="font-bold text-green"><?php echo $field['DATA_TYPE']; ?></span>
            </div>
        <?php } ?>
    </div>

    <div class="mt-1">
        <?php foreach ($data['constrain'] as $key => $value) {
            if ($value) { ?>
                <div class="space-x-1 mt-1">
                    <span class="px-1 bg-green-500 text-black"><?php echo strtoupper($key); ?></span>
                </div>
            <?php
            foreach ($value as $constrainField) {
                    if ($key === 'foreign') { ?>
                        <div class="flex space-x-1">
                            <span class="font-bold"><?php echo $constrainField['column_name']; ?></span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                            <i class="text-blue"><?php echo $constrainField['foreign_table_name']; ?></i>
                            <span class="font-bold text-green"><?php echo $constrainField['foreign_column_name']; ?></span>
                        </div>
                    <?php } else { ?>
                        <div class="flex space-x-1">
                            <span class="font-bold"><?php echo $constrainField; ?></span>
                            <span class="flex-1 content-repeat-[.] text-gray"></span>
                        </div>
                        <?php
                        }
                    }
                }
            } ?>
    </div>
</div>