<div class="w-auto m-1">
    <?php
    $success = 0;
    $error = 0;
    ?>
    <div class="mt-1">
        <div class="flex space-x-1">
            <span class="font-bold text-green">TABLE NAME</span>
            <span class="flex-1 content-repeat-[.] text-gray"></span>
            <span class="font-bold">Standardization</span>
        </div>
        <?php foreach ($tableStatus as $table): ?>
            <div class="flex space-x-1">
                <span><?php echo $table['name']; ?></span>
                <i class="text-blue">(<?php echo $table['size']; ?> MB)</i>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                <?php if ($table['status']): ?>
                    <?php $success++; ?>
                    <b><span class="font-bold text-green">✓</span></b>
                <?php else: ?>
                    <?php $error++; ?>
                    <b><span class="font-bold text-red">✗</span></b>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="mt-1">
            <span class="px-2 bg-green ml-5 text-black"><?php echo $success; ?> </span> <span class="text-green ml-1"> TABLE
                PASSED ✓</span>
        </div>
        <div class="mt-1">
            <span class="px-2 bg-red ml-5 text-white"><?php echo $error; ?></span> <span class="text-red ml-1"> TABLE
                FAILED ✗</span>
        </div>
    </div>
</div>
