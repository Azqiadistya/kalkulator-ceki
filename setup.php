<?php
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $names = [];
    for ($i = 1; $i <= 4; $i++) {
        $key = 'name' . $i;
        $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
        if ($value === '') {
            $errors[] = "Nama ke-$i wajib diisi.";
        }
        $names[] = $value;
    }

    if (!$errors) {
        $_SESSION['names'] = $names;
        $_SESSION['inputs'] = array_fill(0, 4, 0);
        $_SESSION['history'] = [];
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pemain</title>
    <style>
        :root {
            --bg: #0f3b2a;
            --card: #fbf7ef;
            --ink: #1f1f1f;
            --accent: #b7332a;
            --muted: #5f5f5f;
        }
        body {
            font-family: "Georgia", "Times New Roman", serif;
            background:
                linear-gradient(rgba(251, 247, 239, 0.78), rgba(251, 247, 239, 0.78)),
                url("assets/card-bg.jpg") center / cover no-repeat fixed;
            color: var(--ink);
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: var(--card);
            padding: 32px;
            border: 2px solid #e2d7c7;
            box-shadow: 12px 12px 0 rgba(7, 19, 13, 0.55);
            width: min(560px, 90vw);
            position: relative;
        }
        .card::before {
            content: "";
            position: absolute;
            top: 16px;
            right: 16px;
            width: 56px;
            height: 72px;
            border: 1px solid #d7cabb;
            background:
                linear-gradient(135deg, #ffffff 0 55%, #f1e8db 55% 100%);
            box-shadow: 3px 3px 0 rgba(0, 0, 0, 0.08);
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='56' height='72' viewBox='0 0 56 72'><rect x='0' y='0' width='56' height='72' fill='none'/><path d='M11 18 C11 14 16 12 19 16 C22 12 27 14 27 18 C27 24 19 28 19 28 C19 28 11 24 11 18 Z' fill='%23b7332a'/><path d='M33 50 C33 46 38 44 41 48 C44 44 49 46 49 50 C49 56 41 60 41 60 C41 60 33 56 33 50 Z' fill='%230f3b2a'/></svg>");
            background-repeat: no-repeat;
            background-position: center 10px;
        }
        h1 {
            margin: 0 0 16px;
            font-size: 28px;
            letter-spacing: 0.5px;
        }
        p {
            margin: 0 0 24px;
            color: var(--muted);
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
                max-width: 320px;
                margin: 0 auto;
            }
            .actions {
                justify-content: stretch;
                max-width: 320px;
                margin: 0 auto;
            }
            button {
                width: 100%;
            }
        }
        label {
            font-size: 14px;
            color: var(--muted);
        }
        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d8c8b1;
            background: #fff;
            color: var(--ink);
            font-size: 16px;
        }
        .actions {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }
        button {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 18px;
            font-size: 16px;
            cursor: pointer;
        }
        .error {
            background: #fff3ef;
            border: 1px solid #f0b7a7;
            color: #9a2a0d;
            padding: 10px 12px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Daftar User</h1>
        <p>Masukkan 4 nama peserta terlebih dahulu.</p>

        <?php if ($errors): ?>
            <div class="error">
                <?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="grid">
                <div>
                    <label for="name1">Nama 1</label>
                    <input id="name1" name="name1" type="text" value="<?php echo isset($_POST['name1']) ? htmlspecialchars($_POST['name1'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                </div>
                <div>
                    <label for="name2">Nama 2</label>
                    <input id="name2" name="name2" type="text" value="<?php echo isset($_POST['name2']) ? htmlspecialchars($_POST['name2'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                </div>
                <div>
                    <label for="name3">Nama 3</label>
                    <input id="name3" name="name3" type="text" value="<?php echo isset($_POST['name3']) ? htmlspecialchars($_POST['name3'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                </div>
                <div>
                    <label for="name4">Nama 4</label>
                    <input id="name4" name="name4" type="text" value="<?php echo isset($_POST['name4']) ? htmlspecialchars($_POST['name4'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                </div>
            </div>
            <div class="actions">
                <button type="submit">Simpan &amp; Masuk</button>
            </div>
        </form>
    </div>
</body>
</html>
