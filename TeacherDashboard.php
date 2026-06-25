<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background: #8B0000 !important;
            color: white !important;
            text-align: center;
            padding: 20px;
            border: none;
        }
        .card-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .btn-primary, .btn-success, .btn-info, .btn-danger {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-primary {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
        }
        .btn-primary:hover {
            background: #6d0000 !important;
            border-color: #6d0000 !important;
        }
        .btn-success {
            background: #28a745 !important;
            border-color: #28a745 !important;
        }
        .btn-success:hover {
            background: #218838 !important;
            border-color: #218838 !important;
        }
        .btn-info {
            background: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }
        .btn-info:hover {
            background: #138496 !important;
            border-color: #138496 !important;
        }
        .btn-danger {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .btn-danger:hover {
            background: #c82333 !important;
            border-color: #c82333 !important;
        }
        .btn i {
            margin-right: 8px;
        }
        .dashboard-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
        .icon-circle {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        .icon-circle i {
            font-size: 24px;
            color: #8B0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-container">
            <div class="card">
                <div class="card-header">
                    <h2>👨‍🏫 Teacher Dashboard</h2>
                </div>
                <div class="card-body">
                    <div class="dashboard-grid">
                        <a href="CreateQuiz.php" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Create Quiz
                        </a>
                        
                        <a href="ManageQuiz.php" class="btn btn-info">
                            <i class="fas fa-tasks"></i> Manage Quiz
                        </a>
                        
                        <a href="ViewResults.php" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                        
                        <a href="Logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>