<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vòng Quay May Mắn - Bảo trì</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .maintenance-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }

        .maintenance-icon {
            font-size: 5rem;
            color: #ffc107;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .maintenance-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #343a40;
        }

        .maintenance-message {
            font-size: 1.2rem;
            max-width: 600px;
            margin-bottom: 2rem;
            color: #6c757d;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <i class="fas fa-cog maintenance-icon animate__animated animate__pulse animate__infinite"></i>
        <h1 class="maintenance-title animate__animated animate__fadeIn">Đang bảo trì</h1>
        <p class="maintenance-message animate__animated animate__fadeIn">
            Vòng quay may mắn hiện đang được bảo trì hoặc chưa được kích hoạt.
            Vui lòng quay lại sau. Chúng tôi xin lỗi vì sự bất tiện này.
        </p>
        <a href="/" class="btn btn-primary animate__animated animate__fadeIn">
            <i class="fas fa-sync-alt"></i> Tải lại trang
        </a>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Vòng Quay May Mắn. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
