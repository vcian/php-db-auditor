<div class="mt-1">
    <div class="mt-1">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-green"> Database Name</th>
                    <th class="text-green"> Size</th>
                    <th class="text-green"> Table Count </th>
                    <th class="text-green"> Engine </th>
                    <th class="text-green"> Character Set</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td><?php echo $data['databaseName']; ?></td>
                        <td><?php echo $data['databaseSize']; ?></td>
                        <td><?php echo $data['tablistCount']; ?></td>
                        <td><?php echo $data['databaseEngine']; ?></td>
                        <td><?php echo $data['databaseName']; ?></td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>
