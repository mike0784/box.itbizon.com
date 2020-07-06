<br>
<table class="table">
    <thead>
        <tr>
            <td>Название</td>
            <td>Описание</td>
            <td>Длительность выполнения</td>
            <td>Просроченных дней</td>
            <td>Дата начала</td>
            <td>Дата дедлайна</td>
        </tr>
    </thead>
    <tbody>
    <?php while ($task = $tasksList->Fetch()): ?>
        <tr>
            <td><?= $task['TITLE'] ?></td>
            <td><?= $task['DESCRIPTION'] ?></td>
            <td><?= (new \DateTime())->diff((new \Datetime($task['DATE_START'])))->format('%a'); ?></td>
            <td>
                <?= (new \DateTime())->diff((new \Datetime($task['DEADLINE'])))->format('%a'); ?>
            </td>
            <td><?= $task['DATE_START'] ?></td>
            <td><?= $task['DEADLINE'] ?></td>
        </tr>
    <? endwhile; ?>
    </tbody>
</table>