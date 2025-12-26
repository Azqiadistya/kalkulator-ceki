<?php
session_start();

if (!isset($_SESSION['names'])) {
    header('Location: setup.php');
    exit;
}

$names = $_SESSION['names'];
$inputs = $_SESSION['inputs'] ?? ($_SESSION['scores'] ?? array_fill(0, 4, 0));
$history = $_SESSION['history'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = isset($_POST['name_index']) ? (int)$_POST['name_index'] : -1;
    $value = isset($_POST['value']) ? (int)$_POST['value'] : 0;
    $op = isset($_POST['op']) ? $_POST['op'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $newName = isset($_POST['new_name']) ? trim($_POST['new_name']) : '';

    if ($action === 'reset_table') {
        $_SESSION['history'] = [];
    } elseif ($action === 'restart') {
        session_unset();
        session_destroy();
        header('Location: setup.php');
        exit;
    } elseif ($action === 'edit_name' && $index >= 0 && $index < 4 && $newName !== '') {
        $names[$index] = $newName;
        $_SESSION['names'] = $names;
    } elseif ($action === 'reset_calc') {
        $inputs = array_fill(0, 4, 0);
        $_SESSION['inputs'] = $inputs;
    } elseif ($action === 'save') {
        $base = $history ? $history[count($history) - 1] : array_fill(0, 4, 0);
        $newRow = [];
        for ($i = 0; $i < 4; $i++) {
            $newRow[] = (int)$base[$i] + (int)$inputs[$i];
        }
        $history[] = $newRow;
        $_SESSION['history'] = $history;
        $inputs = array_fill(0, 4, 0);
        $_SESSION['inputs'] = $inputs;
    } elseif ($index >= 0 && $index < 4 && $value >= 0 && ($op === 'plus' || $op === 'minus')) {
        if ($op === 'plus') {
            $inputs[$index] = $value;
        } else {
            $inputs[$index] = -$value;
        }

        $_SESSION['inputs'] = $inputs;
    }

    header('Location: index.php');
    exit;
}

$latest = $inputs;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg: #0f3b2a;
            --card: #fbf7ef;
            --ink: #1f1f1f;
            --accent: #1c6b4a;
            --accent-2: #b7332a;
            --muted: #5f5f5f;
        }
        body {
            font-family: "Georgia", "Times New Roman", serif;
            background:
                linear-gradient(rgba(251, 247, 239, 0.78), rgba(251, 247, 239, 0.78)),
                url("assets/card-bg.jpg") center / cover no-repeat fixed;
            color: var(--ink);
            margin: 0;
            padding: 32px;
        }
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
        header {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            margin: 0;
            font-size: 28px;
        }
        a {
            color: var(--accent);
            text-decoration: none;
            font-weight: bold;
        }
        .card {
            background: var(--card);
            border: 2px solid #e2d7c7;
            box-shadow: 10px 10px 0 rgba(7, 19, 13, 0.55);
            padding: 20px;
            position: relative;
        }
        .card::before {
            content: "";
            position: absolute;
            top: 12px;
            right: 12px;
            width: 48px;
            height: 64px;
            border: 1px solid #d7cabb;
            background:
                linear-gradient(135deg, #ffffff 0 55%, #f1e8db 55% 100%);
            box-shadow: 3px 3px 0 rgba(0, 0, 0, 0.08);
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='48' height='64' viewBox='0 0 48 64'><rect x='0' y='0' width='48' height='64' fill='none'/><path d='M9 16 C9 12 13 10 16 13 C19 10 23 12 23 16 C23 21 16 25 16 25 C16 25 9 21 9 16 Z' fill='%23b7332a'/><path d='M28 44 C28 40 32 38 35 41 C38 38 42 40 42 44 C42 49 35 53 35 53 C35 53 28 49 28 44 Z' fill='%230f3b2a'/></svg>");
            background-repeat: no-repeat;
            background-position: center 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        .table-scroll {
            max-height: 264px;
            overflow-y: auto;
            border: 1px solid #e0d0b8;
        }
        .table-scroll table {
            border: none;
        }
        .table-scroll th,
        .table-scroll td {
            border: 1px solid #e0d0b8;
        }
        th, td {
            border: 1px solid #e0d0b8;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #f5e9d5;
        }
        .name-btn {
            background: none;
            border: none;
            padding: 0;
            font: inherit;
            color: inherit;
            cursor: pointer;
        }
        .name-btn:hover {
            text-decoration: underline;
        }
        .muted {
            color: var(--muted);
            font-size: 14px;
        }
        .calc {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        input {
            width: 100%;
            padding: 10px 12px;
            font-size: 16px;
            border: 1px solid #d6c4aa;
            background: #fff;
            color: var(--ink);
        }
        .calc-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: end;
        }
        .calc-actions {
            display: flex;
            gap: 8px;
        }
        button {
            padding: 10px 16px;
            font-size: 18px;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .plus {
            background: var(--accent);
        }
        .minus {
            background: var(--accent-2);
        }
        .scores {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: 12px;
        }
        .save-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }
        .save {
            background: #1c6b4a;
            padding: 10px 18px;
            font-size: 16px;
        }
        .reset-calc {
            background: #9a2a0d;
            padding: 10px 18px;
            font-size: 16px;
        }
        .score-item {
            background: #fff6e6;
            border: 1px solid #ead7bf;
            padding: 8px 10px;
            font-size: 14px;
        }
        .table-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
        }
        .reset {
            background: #9a2a0d;
            padding: 8px 14px;
            font-size: 14px;
        }
        .restart {
            background: #1c6b4a;
            padding: 8px 14px;
            font-size: 14px;
        }
        @media (max-width: 900px) {
            .wrap {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="card">
            <h2>Kalkulator</h2>
            <div class="calc">
                <?php foreach ($names as $i => $name): ?>
                    <form method="post" class="calc-row">
                        <label>
                            <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                            <input type="number" name="value" min="0" step="1" required>
                        </label>
                        <div class="calc-actions">
                            <input type="hidden" name="name_index" value="<?php echo $i; ?>">
                            <button type="submit" name="op" value="plus" class="plus">+</button>
                            <button type="submit" name="op" value="minus" class="minus">-</button>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
            <div class="scores">
                <?php foreach ($names as $i => $name): ?>
                    <div class="score-item">
                        <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo (int)$latest[$i]; ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="post" class="save-row">
                <input type="hidden" name="action" value="save">
                <div class="calc-actions">
                    <button type="submit" class="save">Simpan</button>
                    <button type="submit" name="action" value="reset_calc" class="reset-calc">Reset</button>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Ceki-nya Indonesia</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($names as $i => $name): ?>
                                <th>
                                    <button type="button" class="name-btn" data-index="<?php echo $i; ?>">
                                        <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                    </button>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$history): ?>
                            <tr>
                                <td colspan="4" class="muted">Belum ada data.</td>
                            </tr>
                    <?php else: ?>
                        <?php foreach (array_reverse($history) as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo (int)$value; ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-actions">
                <form method="post">
                    <input type="hidden" name="action" value="restart">
                    <button type="submit" class="restart">Mulai Ulang Permainan</button>
                </form>
                <form method="post">
                    <input type="hidden" name="action" value="reset_table">
                    <button type="submit" class="reset">Reset Tabel</button>
                </form>
            </div>
        </section>
    </div>
    <form method="post" id="edit-name-form">
        <input type="hidden" name="action" value="edit_name">
        <input type="hidden" name="name_index" id="edit-name-index" value="">
        <input type="hidden" name="new_name" id="edit-name-value" value="">
    </form>
    <script>
        document.querySelectorAll('.name-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var currentName = button.textContent.trim();
                var newName = window.prompt('Edit nama:', currentName);
                if (newName === null) {
                    return;
                }
                newName = newName.trim();
                if (newName === '') {
                    return;
                }
                if (!window.confirm('Simpan perubahan nama?')) {
                    return;
                }
                document.getElementById('edit-name-index').value = button.getAttribute('data-index');
                document.getElementById('edit-name-value').value = newName;
                document.getElementById('edit-name-form').submit();
            });
        });
    </script>
</body>
</html>
