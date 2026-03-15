<?php
$conn = mysqli_connect("localhost", "root", "", "db_gadget");
$logs = mysqli_query($conn, "SELECT * FROM log_chat ORDER BY waktu DESC");

// Hitung Statistik untuk Grafik di Skripsi
$stat = mysqli_query($conn, "SELECT sentimen, COUNT(*) as jumlah FROM log_chat GROUP BY sentimen");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Pasek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow-sm">
        <h3>📊 Statistik Sentimen Pelanggan</h3>
        <div class="row mb-4">
            <?php while($s = mysqli_fetch_assoc($stat)): ?>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h6><?= $s['sentimen'] ?></h6>
                    <h2 class="text-primary"><?= $s['jumlah'] ?></h2>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <h3>📝 Log Percakapan & Deep Thinking</h3>
        <table class="table table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Waktu</th>
                    <th>User Query</th>
                    <th>Sentimen</th>
                    <th>AI Thinking</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($logs)): ?>
                <tr>
                    <td><small><?= $row['waktu'] ?></small></td>
                    <td><?= $row['user_query'] ?></td>
                    <td>
                        <span class="badge bg-<?= ($row['sentimen']=='Positif'?'success':($row['sentimen']=='Negatif'?'danger':'secondary')) ?>">
                            <?= $row['sentimen'] ?>
                        </span>
                    </td>
                    <td><small class="text-muted"><?= $row['ai_thought'] ?></small></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>