<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Upgrade in Progress | Tech Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00f0ff;
            --secondary: #0066ff;
            --accent: #ff00aa;
            --dark: #0a0a1a;
            --darker: #050510;
            --light: #e0e0ff;
            --neon-glow: 0 0 10px var(--primary), 0 0 20px var(--primary), 0 0 30px rgba(0, 240, 255, 0.3);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--darker);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        
        /* Circuit Board Background */
        .circuit-board {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(to right, rgba(0, 240, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 240, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: 0;
            opacity: 0.5;
        }
        
        .maintenance-container {
            width: 90%;
            max-width: 900px;
            background: rgba(10, 10, 26, 0.8);
            border: 1px solid rgba(0, 240, 255, 0.2);
            border-radius: 15px;
            padding: 50px;
            box-shadow: var(--neon-glow);
            text-align: center;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(5px);
            animation: fadeIn 1s ease-out, pulse 6s infinite alternate;
            overflow: hidden;
        }
        
        .maintenance-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent 0%,
                rgba(0, 240, 255, 0.1) 50%,
                transparent 100%
            );
            transform: rotate(30deg);
            animation: shine 8s infinite;
            z-index: -1;
        }
        
        .tech-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .maintenance-icon {
            font-size: 80px;
            color: var(--primary);
            margin-bottom: 20px;
            text-shadow: var(--neon-glow);
            animation: float 3s ease-in-out infinite;
        }
        
        h1 {
            font-family: 'Orbitron', sans-serif;
            color: var(--primary);
            font-size: 2.8rem;
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 0 5px var(--primary);
        }
        
        .subtitle {
            font-size: 1.2rem;
            color: var(--light);
            margin-bottom: 30px;
            line-height: 1.4;
            max-width: 700px;
        }
        
        /* Tech Progress Indicator */
        .tech-progress {
            width: 100%;
            margin: 20px 0;
            position: relative;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .progress-label {
            color: var(--primary);
        }
        
        .progress-percent {
            color: var(--accent);
        }
        
        .progress-bar-container {
            height: 10px;
            background: rgba(0, 240, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }
        
        .progress-bar {
            height: 100%;
            width: 72%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 5px;
            position: relative;
            animation: progressAnimation 2s ease-in-out infinite alternate;
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            animation: shine 2s infinite;
        }
        
        /* Digital Countdown */
        .countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 40px 0;
            font-family: 'Orbitron', sans-serif;
        }
        
        .countdown-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 240, 255, 0.3);
            border-radius: 8px;
            padding: 20px 15px;
            min-width: 90px;
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .countdown-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 25px var(--primary);
            border-color: var(--primary);
        }
        
        .countdown-item::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent 0%,
                rgba(0, 240, 255, 0.1) 50%,
                transparent 100%
            );
            transform: rotate(30deg);
            animation: shine 6s infinite;
        }
        
        .countdown-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            text-shadow: 0 0 10px var(--primary);
            margin-bottom: 5px;
        }
        
        .countdown-label {
            font-size: 0.9rem;
            color: var(--light);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Tech Grid */
        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        
        .tech-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 240, 255, 0.2);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .tech-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px var(--primary);
            border-color: var(--primary);
        }
        
        .tech-icon {
            font-size: 30px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .tech-name {
            font-size: 0.9rem;
            color: var(--light);
        }
        
        /* Social Links */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 40px 0 30px;
        }
        
        .social-link {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 240, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .social-link:hover {
            background: var(--primary);
            color: var(--dark);
            box-shadow: 0 0 20px var(--primary);
            transform: translateY(-3px);
        }
        
        .social-link::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent 0%,
                rgba(0, 240, 255, 0.1) 50%,
                transparent 100%
            );
            transform: rotate(30deg);
            animation: shine 6s infinite;
        }
        
        /* Contact Info */
        .contact-info {
            margin-top: 30px;
            font-size: 0.9rem;
            color: var(--light);
        }
        
        .contact-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-shadow: 0 0 5px var(--accent);
        }
        
        .contact-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }
        
        /* Binary Rain Animation */
        .binary-rain {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            opacity: 0.15;
        }
        
        .binary-digit {
            position: absolute;
            color: var(--primary);
            font-family: 'Orbitron', sans-serif;
            font-size: 16px;
            animation: fall linear infinite;
            text-shadow: 0 0 5px var(--primary);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0% { box-shadow: var(--neon-glow); }
            50% { box-shadow: 0 0 15px var(--primary), 0 0 30px var(--primary), 0 0 45px rgba(0, 240, 255, 0.4); }
            100% { box-shadow: var(--neon-glow); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        @keyframes progressAnimation {
            0% { width: 72%; }
            100% { width: 72%; }
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }
        
        @keyframes fall {
            to { transform: translateY(100vh); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .countdown {
                gap: 10px;
            }
            
            .countdown-item {
                min-width: 70px;
                padding: 15px 10px;
            }
            
            .countdown-value {
                font-size: 2rem;
            }
            
            .tech-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="circuit-board"></div>
    
    <div class="binary-rain" id="binaryRain"></div>
    
    <div class="maintenance-container">
        <div class="tech-header">
            <div class="maintenance-icon">
                <i class="fas fa-server"></i>
            </div>
            <h1>SYSTEM UPGRADE IN PROGRESS</h1>
            <p class="subtitle">We're deploying cutting-edge technologies to enhance your experience. Our systems will be back online shortly.</p>
        </div>
        
        <div class="tech-progress">
            <div class="progress-info">
                <span class="progress-label">SYSTEM OPTIMIZATION</span>
                <span class="progress-percent"></span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar"></div>
            </div>
        </div>
        
        <div class="countdown">
            <div class="countdown-item">
                <div class="countdown-value" id="days"></div>
                <div class="countdown-label">Days</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-value" id="hours"></div>
                <div class="countdown-label">Hours</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-value" id="minutes"></div>
                <div class="countdown-label">Minutes</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-value" id="seconds"></div>
                <div class="countdown-label">Seconds</div>
            </div>
        </div>
        
        <div class="tech-grid">
            <div class="tech-item">
                <div class="tech-icon"><i class="fas fa-microchip"></i></div>
                <div class="tech-name">Hardware</div>
            </div>
            <div class="tech-item">
                <div class="tech-icon"><i class="fas fa-code"></i></div>
                <div class="tech-name">Software</div>
            </div>
            <div class="tech-item">
                <div class="tech-icon"><i class="fas fa-database"></i></div>
                <div class="tech-name">Database</div>
            </div>
            <div class="tech-item">
                <div class="tech-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="tech-name">Security</div>
            </div>
            <div class="tech-item">
                <div class="tech-icon"><i class="fas fa-bolt"></i></div>
                <div class="tech-name">Performance</div>
            </div>
        </div>
        
        <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-github"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-discord"></i></a>
            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
        </div>
        
        <div class="contact-info">
            For urgent inquiries, contact <a href="mailto:tech-support@mvtech.com" class="contact-link">tech-support@mvtech.com</a>
        </div>
    </div>

    <script>
        // Countdown Timer
        const targetDate = new Date();
        targetDate.setDate(targetDate.getDate() + 1); // 1 day from now
        targetDate.setHours(targetDate.getHours() + 18);
        targetDate.setMinutes(targetDate.getMinutes() + 45);
        
        function updateCountdown() {
            
            const now = new Date();
            const diff = targetDate - now;
            
            if (diff <= 0) {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }
            
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 0);
        
        // Binary Rain Animation
        const binaryRainContainer = document.getElementById('binaryRain');
        const characters = '01';
        const columns = Math.floor(window.innerWidth / 20);
        
        for (let i = 0; i < columns; i++) {
            const digit = document.createElement('div');
            digit.className = 'binary-digit';
            digit.style.left = `${(i * 20)}px`;
            digit.style.animationDuration = `${Math.random() * 5 + 5}s`;
            digit.style.animationDelay = `${Math.random() * 5}s`;
            
            let binaryString = '';
            for (let j = 0; j < 30; j++) {
                binaryString += characters.charAt(Math.floor(Math.random() * characters.length)) + '<br>';
            }
            
            digit.innerHTML = binaryString;
            binaryRainContainer.appendChild(digit);
        }
        
        // Dynamic progress percentage
        let progress = 72;
        const progressBar = document.querySelector('.progress-bar');
        const progressPercent = document.querySelector('.progress-percent');
        
        function updateProgress() {
            if (progress < 100) {
                progress += Math.random() * 0.5;
                if (progress > 100) progress = 72;
                
                progressBar.style.width = `${progress}%`;
                progressPercent.textContent = `${Math.floor(progress)}%`;
            }
        }
        
        setInterval(updateProgress, 1500);
    </script>
</body>
</html>