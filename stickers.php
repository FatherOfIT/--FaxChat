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

// Get all stickers
$stmt = $conn->prepare("SELECT * FROM stickers ORDER BY created_at DESC");
$stmt->execute();
$stickers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$success = '';
$error = '';

// Handle sticker upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sticker'])) {
    $name = trim($_POST['name']);
    $file = $_FILES['sticker'];
    
    if ($file['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = 'assets/uploads/stickers/' . $new_filename;
            
            if (!file_exists('assets/uploads/stickers')) {
                mkdir('assets/uploads/stickers', 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $stmt = $conn->prepare("INSERT INTO stickers (name, file_path, user_id) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $name, $upload_path, $user_id);
                
                if ($stmt->execute()) {
                    $success = 'استیکر با موفقیت آپلود شد';
                    header('Location: stickers.php');
                    exit();
                } else {
                    $error = 'خطا در ذخیره استیکر';
                }
            } else {
                $error = 'خطا در آپلود فایل';
            }
        } else {
            $error = 'فرمت فایل نامعتبر است';
        }
    } else {
        $error = 'خطا در آپلود فایل';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استیکرها - گروه پیامرسان</title>
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
                            <a class="nav-link active" href="stickers.php">
                                <i class="fas fa-sticky-note"></i> استیکرها
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
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
                    <h1 class="h2">استیکرها</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadStickerModal">
                        <i class="fas fa-plus"></i> آپلود استیکر جدید
                    </button>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php foreach ($stickers as $sticker): ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($sticker['file_path']); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($sticker['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($sticker['name']); ?></h5>
                                    <button class="btn btn-sm btn-primary copy-sticker" 
                                            data-sticker-path="<?php echo htmlspecialchars($sticker['file_path']); ?>">
                                        <i class="fas fa-copy"></i> کپی لینک
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Upload Sticker Modal -->
    <div class="modal fade" id="uploadStickerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">آپلود استیکر جدید</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">نام استیکر</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="sticker" class="form-label">فایل استیکر</label>
                            <input type="file" class="form-control" id="sticker" name="sticker" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">آپلود</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.copy-sticker').click(function() {
                const stickerPath = $(this).data('sticker-path');
                navigator.clipboard.writeText(stickerPath).then(function() {
                    alert('لینک استیکر کپی شد');
                });
            });
        });
    </script>
</body>
</html> 