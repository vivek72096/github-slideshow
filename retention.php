<?php
session_start();
require_once __DIR__ . "/../config.php";

/* ===== KC FROM CLICK ===== */
if (isset($_GET['kc'])) {
    $_SESSION['kc_name'] = strtoupper(trim($_GET['kc']));
}

$kc = $_SESSION['kc_name'] ?? '';
$error = "";

/* ===== LOGIN PROCESS ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($kc === "") {
        $error = "KC not selected. Please go back and select KC.";
    } else {

        $sql = "SELECT * FROM kc_login 
                WHERE LOWER(username)=LOWER(?) 
                AND kc_batch=? 
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $kc);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {

            $row = $res->fetch_assoc();

            if ($password === $row['password']) {

                $_SESSION['kc_loggedin'] = true;
                $_SESSION['kc_user']     = $row['username'];
                $_SESSION['kc_role']     = $row['role'];
                $_SESSION['kc_name']     = $row['kc_batch'];

                header("Location: kc_home.php");
                exit;

            } else {
                $error = "Incorrect password. Please try again.";
            }

        } else {
            $error = "Invalid username or KC mismatch.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>KC Login - <?= htmlspecialchars($kc) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    background:linear-gradient(135deg,#e3f2fd,#ffffff);
    font-family:"Segoe UI",Roboto,Arial,sans-serif;
}

/* ===== TOP BAR ===== */
.top-bar{
    background:linear-gradient(90deg,#0d6efd,#0a58ca);
    color:#fff;
    padding:15px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 15px rgba(0,0,0,.25);
}
.top-bar .title{
    font-size:20px;
    font-weight:700;
}
.top-bar a{
    color:#fff;
    text-decoration:none;
    margin-left:12px;
    padding:6px 14px;
    border-radius:6px;
    background:rgba(255,255,255,.25);
    font-weight:600;
}
.top-bar a:hover{
    background:rgba(255,255,255,.4);
}

/* ===== LOGIN CARD ===== */
.login-card{
    max-width:420px;
    margin:70px auto;
    background:#fff;
    padding:30px;
    border-radius:18px;
    box-shadow:0 12px 35px rgba(0,0,0,.2);
}
.login-card h3{
    text-align:center;
    font-weight:900;
    color:#0d6efd;
}
.login-card .kc-name{
    text-align:center;
    font-size:15px;
    color:#555;
    margin-bottom:20px;
    font-weight:600;
}

/* ===== FOOTER ===== */
.footer{
    text-align:center;
    font-size:13px;
    color:#666;
    margin-top:30px;
}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="top-bar">
    <div class="title">KC Login Panel</div>
    <div>
        <a href="KC_master_list.php">⬅ KC List</a>
       <a href="/">🏠 Home</a>
    </div>
</div>


<!-- LOGIN BOX -->
<div class="login-card">

    <h3>Kaushal College Login</h3>
    <div class="kc-name">
        KC Selected: <strong><?= htmlspecialchars($kc ?: "Not Selected") ?></strong>
    </div>

    <form method="POST" autocomplete="off">

        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger small">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <button class="btn btn-primary w-100 fw-bold">
            🔐 Login to Dashboard
        </button>

    </form>

    <div class="footer">
        © <?= date("Y") ?> PREJHA | KC MIS System
    </div>

</div>

</body>
</html>
