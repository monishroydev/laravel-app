<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamTV - Watch Live TV Channels</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root {
            --bg: #0a0a0a;
            --surface: #141414;
            --surface-hover: #1f1f1f;
            --primary: #E50914;
            --secondary: #F5F5F7;
            --accent: #B81D24;
            --text-primary: #F5F5F7;
            --text-secondary: #999999;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            --glow: 0 0 30px rgba(229, 9, 20, 0.4);
            --border: rgba(255, 255, 255, 0.08);
        }

        [data-theme="light"] {
            --bg: #F0F2F5;
            --surface: #FFFFFF;
            --surface-hover: #F8F9FA;
            --primary: #E50914;
            --secondary: #141414;
            --accent: #B81D24;
            --text-primary: #141414;
            --text-secondary: #666666;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            --glow: none;
            --border: rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            transition: all 0.4s ease;
            min-height: 100vh;
        }

        h1,
        h2,
        h3 {
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 1px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 24px;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 16px 0;
            background: linear-gradient(to bottom, rgba(10, 10, 10, 0.95) 0%, transparent 100%);
            transition: all 0.3s ease;
        }

        header.scrolled {
            background: var(--bg);
            box-shadow: var(--shadow);
            backdrop-filter: blur(20px);
        }

        header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 22px;
            height: 22px;
            fill: white;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--surface);
            border-radius: 24px;
            padding: 8px 16px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        .search-box input {
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 14px;
            width: 180px;
            outline: none;
        }

        .search-box input::placeholder {
            color: var(--text-secondary);
        }

        .search-box svg {
            color: var(--text-secondary);
            width: 18px;
            height: 18px;
        }

        .theme-toggle {
            background: var(--surface);
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: var(--glow);
        }

        .hero {
            padding: 120px 0 60px;
            text-align: center;
            background: radial-gradient(ellipse at top, rgba(229, 9, 20, 0.15) 0%, transparent 60%);
        }

        .hero h1 {
            font-size: 64px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            color: var(--text-secondary);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 32px;
        }

        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-bottom: 40px;
        }

        .filter-btn {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 10px 20px;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        .channel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            padding: 20px 0 80px;
        }

        .channel-card {
            background: var(--surface);
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--border);
            position: relative;
        }

        .channel-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--glow);
            border-color: var(--primary);
        }

        .channel-card .logo-container {
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, var(--surface-hover) 0%, var(--surface) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .channel-card .logo-container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(229, 9, 20, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .channel-card:hover .logo-container::before {
            opacity: 1;
        }

        .channel-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .channel-card:hover img {
            transform: scale(1.1);
        }

        .channel-card .info {
            padding: 14px;
        }

        .channel-card .name {
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .channel-card .country {
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .player-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 2000;
            display: none;
            flex-direction: column;
        }

        .player-modal.active {
            display: flex;
        }

        .player-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: rgba(0, 0, 0, 0.5);
        }

        .player-header h3 {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .player-header h3 img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: contain;
        }

        .close-player {
            background: var(--surface);
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-player:hover {
            background: var(--primary);
            transform: rotate(90deg);
        }

        .video-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .video-container video {
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: black;
        }

        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 20px;
            color: var(--text-secondary);
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-secondary);
        }

        .no-results svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        footer {
            background: var(--surface);
            padding: 40px 0;
            text-align: center;
            border-top: 1px solid var(--border);
        }

        footer p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 40px;
            }

            .hero p {
                font-size: 14px;
            }

            .search-box {
                display: none;
            }

            .channel-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .filter-bar {
                gap: 8px;
            }

            .filter-btn {
                padding: 8px 14px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 100px 0 40px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .channel-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <header id="header">
        <div class="container">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                </div>
                StreamTV
            </a>
            <div class="header-actions">
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.35-4.35"></path>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search channels...">
                </div>
                <button class="theme-toggle" id="themeToggle">
                    <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" style="display: none;">
                        <circle cx="12" cy="12" r="5"></circle>
                        <path
                            d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42">
                        </path>
                    </svg>
                    <svg class="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Live TV Channels</h1>
            <p>Stream thousands of live television channels from around the world. Click any channel to start watching.
            </p>
            <div class="filter-bar" id="filterBar">
                <button class="filter-btn active" data-filter="all">All Channels</button>
            </div>
        </div>
    </section>

    <main class="container">
        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <p>Loading channels...</p>
        </div>
        <div class="channel-grid" id="channelGrid"></div>
        <div class="no-results" id="noResults" style="display: none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="M21 21l-4.35-4.35"></path>
                <path d="M8 11h6"></path>
            </svg>
            <h3>No channels found</h3>
            <p>Try adjusting your search</p>
        </div>
    </main>

    <div class="player-modal" id="playerModal">
        <div class="player-header">
            <h3 id="playerTitle">
                <img id="playerLogo" src="" alt="">
                <span id="playerChannelName">Channel Name</span>
            </h3>
            <button class="close-player" id="closePlayer">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="video-container">
            <video id="videoPlayer" controls playsinline></video>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 StreamTV. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let allChannels = [];
        let currentFilter = 'all';
        let hls = null;

        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.querySelector('.sun-icon');
        const moonIcon = document.querySelector('.moon-icon');
        const html = document.documentElement;
        const header = document.getElementById('header');
        const searchInput = document.getElementById('searchInput');
        const channelGrid = document.getElementById('channelGrid');
        const loading = document.getElementById('loading');
        const noResults = document.getElementById('noResults');
        const filterBar = document.getElementById('filterBar');
        const playerModal = document.getElementById('playerModal');
        const videoPlayer = document.getElementById('videoPlayer');
        const closePlayer = document.getElementById('closePlayer');
        const playerTitle = document.getElementById('playerTitle');
        const playerLogo = document.getElementById('playerLogo');
        const playerChannelName = document.getElementById('playerChannelName');

        const savedTheme = localStorage.getItem('theme') || 'dark';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcons(savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcons(newTheme);
        });

        function updateThemeIcons(theme) {
            if (theme === 'dark') {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        }

        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });

        async function fetchPlaylist() {
            try {

                const response = await fetch('/channels');
                const channels = await response.json();

                allChannels = channels;

                renderFilterButtons();
                renderChannels(allChannels);

                loading.style.display = 'none';

            } catch (error) {

                console.error(error);
                loading.innerHTML = 'Failed loading channels';

            }
        }


        function parseM3U8(text) {
            const lines = text.split('\n');
            const channels = [];
            let currentChannel = {};

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();

                if (line.startsWith('#EXTINF:')) {
                    const attrs = line.replace('#EXTINF:', '');
                    const parts = attrs.split(',');

                    currentChannel = {
                        name: parts[1]?.trim() || 'Unknown',
                        logo: '',
                        country: 'Unknown',
                        url: ''
                    };

                    const tvgLogoMatch = line.match(/tvg-logo="([^"]*)"/);
                    if (tvgLogoMatch) currentChannel.logo = tvgLogoMatch[1];

                    const groupTitleMatch = line.match(/group-title="([^"]*)"/);
                    if (groupTitleMatch) currentChannel.country = groupTitleMatch[1];

                    const tvgNameMatch = line.match(/tvg-name="([^"]*)"/);
                    if (tvgNameMatch) currentChannel.name = tvgNameMatch[1];
                } else if (line && !line.startsWith('#')) {
                    currentChannel.url = line;

                    if (currentChannel.name && currentChannel.url) {
                        channels.push({
                            ...currentChannel
                        });
                    }
                }

            }

            allChannels = channels;
            renderFilterButtons();
            renderChannels(allChannels);
            loading.style.display = 'none';
        }

        function renderFilterButtons() {
            const countries = [...new Set(allChannels.map(ch => ch.country))].filter(c => c && c !== 'Unknown');
            countries.sort();

            const priorityCountries = ['United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France',
                'Spain', 'Italy', 'Japan', 'Brazil', 'India', 'Sports', 'News', 'Music'
            ];

            const sortedCountries = priorityCountries.filter(c => countries.includes(c));
            const otherCountries = countries.filter(c => !priorityCountries.includes(c)).sort();

            const allCountries = [...sortedCountries, ...otherCountries];

            allCountries.forEach(country => {
                const btn = document.createElement('button');
                btn.className = 'filter-btn';
                btn.dataset.filter = country;
                btn.textContent = country;
                btn.addEventListener('click', () => filterChannels(country));
                filterBar.appendChild(btn);
            });

            filterBar.querySelector('[data-filter="all"]').addEventListener('click', () => filterChannels('all'));
        }

        function filterChannels(country) {
            currentFilter = country;

            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.filter === country);
            });

            const filtered = country === 'all' ?
                allChannels :
                allChannels.filter(ch => ch.country === country);

            const searchTerm = searchInput.value.toLowerCase();
            const searched = filtered.filter(ch =>
                ch.name.toLowerCase().includes(searchTerm)
            );

            renderChannels(searched);
        }

        function renderChannels(channels) {
            channelGrid.innerHTML = '';

            if (channels.length === 0) {
                noResults.style.display = 'block';
                return;
            }

            noResults.style.display = 'none';

            channels.forEach(channel => {
                const card = document.createElement('div');
                card.className = 'channel-card';
                card.onclick = () => playChannel(channel);

                const fallbackSvg = `data:image/svg+xml,${encodeURIComponent(`
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60">
                            <rect fill="#1f1f1f" width="100" height="60"/>
                            <text x="50" y="35" text-anchor="middle" fill="#666" font-size="10">
                                No Logo
                            </text>
                        </svg>
                    `)}`;

                const displayLogo = channel.logo ? channel.logo : fallbackSvg;

                card.innerHTML = `
                    <div class="logo-container">
                        <span class="live-badge">LIVE</span>
                        <img src="${displayLogo}" 
                            alt="${channel.name}" 
                            onerror="this.onerror=null;this.src='${fallbackSvg}'">
                    </div>

                    <div class="info">
                        <div class="name">${channel.name}</div>
                        <div class="country">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                            </svg>
                            ${channel.country || ''}
                        </div>
                    </div>
                `;

                channelGrid.appendChild(card);
            });

        }

        function playChannel(channel) {
            playerChannelName.textContent = channel.name;
            playerLogo.src = channel.logo || '';
            playerLogo.style.display = channel.logo ? 'block' : 'none';
            playerModal.classList.add('active');

            if (hls) {
                hls.destroy();
            }

            if (channel.url.includes('.m3u8') && Hls.isSupported()) {
                hls = new Hls({
                    enableWorker: true,
                    lowLatencyMode: true,
                });
                hls.loadSource(channel.url);
                hls.attachMedia(videoPlayer);
                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    videoPlayer.play();
                });
                hls.on(Hls.Events.ERROR, (event, data) => {
                    console.error('HLS Error:', data);
                });
            } else if (videoPlayer.canPlayType('application/vnd.apple.mpegurl')) {
                videoPlayer.src = channel.url;
                videoPlayer.play();
            } else {
                videoPlayer.src = channel.url;
                videoPlayer.play();
            }
        }

        closePlayer.addEventListener('click', () => {
            playerModal.classList.remove('active');
            if (hls) {
                hls.destroy();
            }
            videoPlayer.pause();
            videoPlayer.src = '';
        });

        playerModal.addEventListener('click', (e) => {
            if (e.target === playerModal) {
                closePlayer.click();
            }
        });

        searchInput.addEventListener('input', (e) => {
            filterChannels(currentFilter);
        });

        fetchPlaylist();
    </script>
</body>

</html>
