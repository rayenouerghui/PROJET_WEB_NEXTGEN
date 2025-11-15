<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextGen Gaming - <?php echo $title ?? 'Dashboard'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Roboto:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/common.css">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="../../backoffice/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <style>
        header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        nav {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #2563eb 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 8px;
            align-items: center;
        }

        nav ul li a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        nav ul li a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #2563eb, #ea580c);
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }

        nav ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        nav ul li a:hover::before {
            width: 80%;
        }

        main {
            min-height: calc(100vh - 70px);
            padding: 40px 0;
        }

        .flash-messages {
            max-width: 1400px;
            margin: 0 auto 32px;
            padding: 0 32px;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                height: auto;
                padding: 16px;
            }

            nav ul {
                flex-direction: column;
                width: 100%;
                gap: 4px;
            }

            nav ul li {
                width: 100%;
            }

            nav ul li a {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">NextGen Gaming</div>
            <ul>
                <li><a href="../../backoffice/">Dashboard</a></li>
                <li><a href="../../backoffice/matchmaking.php">Matchmaking</a></li>
                <li><a href="../../backoffice/users">Users</a></li>
                <li><a href="../../backoffice/games">Games</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?php if (isset($flash_messages)): ?>
            <div class="flash-messages">
                <?php foreach ($flash_messages as $message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?>">
                        <?php echo $message['text']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
