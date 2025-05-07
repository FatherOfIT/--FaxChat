<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $success = 'رمز عبور با موفقیت تغییر کرد';
                } else {
                    $error = 'خطا در تغییر رمز عبور';
                }
            } else {
                $error = 'رمز عبور جدید و تکرار آن مطابقت ندارند';
            }
        } else {
            $error = 'رمز عبور فعلی اشتباه است';
        }
    } elseif (isset($_POST['update_settings'])) {
        $theme = $_POST['theme'];
        $notifications = isset($_POST['notifications']) ? 1 : 0;
        $sound = isset($_POST['sound']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE users SET theme = ?, notifications = ?, sound = ? WHERE id = ?");
        $stmt->bind_param("siii", $theme, $notifications, $sound, $user_id);
        
        if ($stmt->execute()) {
            $success = 'تنظیمات با موفقیت بروزرسانی شد';
            $user['theme'] = $theme;
            $user['notifications'] = $notifications;
            $user['sound'] = $sound;
        } else {
            $error = 'خطا در بروزرسانی تنظیمات';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیمات - گروه پیامرسان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="<?php echo $user['profile_picture'] ?? 'assets/images/default-avatar.png'; ?>" 
                             class="rounded-circle profile-picture" alt="Profile">
                        <h5 class="mt-2"><?php echo htmlspecialchars($user['username']); ?></h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home"></i> خانه
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user"></i> پروفایل
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="stickers.php">
                                <i class="fas fa-sticky-note"></i> استیکرها
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="settings.php">
                                <i class="fas fa-cog"></i> تنظیمات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> خروج
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تنظیمات</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row">
                    <!-- Change Password -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">تغییر رمز عبور</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">رمز عبور فعلی</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">رمز عبور جدید</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">تکرار رمز عبور جدید</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">تغییر رمز عبور</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">تنظیمات عمومی</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="theme" class="form-label">تم</label>
                                        <select class="form-select" id="theme" name="theme">
                                            <option value="light" <?php echo $user['theme'] === 'light' ? 'selected' : ''; ?>>روشن</option>
                                            <option value="dark" <?php echo $user['theme'] === 'dark' ? 'selected' : ''; ?>>تیره</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifications" name="notifications" 
                                                   <?php echo $user['notifications'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="notifications">اعلان‌ها</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="sound" name="sound" 
                                                   <?php echo $user['sound'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="sound">صدا</label>
                                        </div>
                                    </div>
                                    <button type="submit" name="update_settings" class="btn btn-primary">ذخیره تنظیمات</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Theme switcher
            $('#theme').change(function() {
                const theme = $(this).val();
                $('body').removeClass('bg-light bg-dark').addClass(theme === 'dark' ? 'bg-dark' : 'bg-light');
            });
        });
    </script>
</body>
</html> 