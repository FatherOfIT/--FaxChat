<?php
session_start();
require_once 'config/database.php';
require_login();

$user = get_logged_in_user();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گروه پیامرسان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="<?php echo $user['theme'] === 'dark' ? 'dark-theme' : ''; ?>">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="<?php echo $user['profile_picture'] ?? 'assets/images/default-avatar.png'; ?>" 
                             class="rounded-circle profile-picture" alt="Profile">
                        <h5 class="mt-2"><?php echo htmlspecialchars($user['username']); ?></h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
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
                    <h1 class="h2">گفتگو</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="theme-toggle">
                                <i class="fas fa-moon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Container -->
                <div class="chat-container">
                    <div class="messages" id="messages">
                        <!-- Messages will be loaded here -->
                    </div>
                </div>

                <!-- Message Input -->
                <div class="message-input">
                    <form id="message-form" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" class="form-control" id="message-input" placeholder="پیام خود را بنویسید...">
                            <button type="button" class="btn btn-outline-secondary" id="sticker-button" data-bs-toggle="modal" data-bs-target="#stickerModal">
                                <i class="fas fa-sticky-note"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="record-button">
                                <i class="fas fa-microphone"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary d-none" id="stop-button">
                                <i class="fas fa-stop"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary d-none" id="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Sticker Modal -->
    <div class="modal fade" id="stickerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">انتخاب استیکر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="sticker-grid">
                        <!-- Stickers will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 