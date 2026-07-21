<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students</title>

    <style>
        body{
            font-family:Arial,sans-serif;
            margin:40px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,
        td{
            padding:10px;
            border:1px solid #ddd;
        }

        th{
            background:#f4f4f4;
        }

        h1{
            margin-bottom:25px;
        }
    </style>
</head>

<body>

<h1>Students</h1>

<table>

<thead>

<tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Classroom</th>
</tr>

</thead>

<tbody>

<?php foreach ($students->items() as $student): ?>

<tr>

<td><?= $student['id']; ?></td>

<td><?= htmlspecialchars($student['first_name']); ?></td>

<td><?= htmlspecialchars($student['last_name']); ?></td>

<td><?= htmlspecialchars((string)($student['classroom_id'] ?? '-')); ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<p>

Page

<?= $students->currentPage(); ?>

of

<?= $students->lastPage(); ?>

</p>

</body>

</html>