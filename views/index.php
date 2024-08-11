<!DOCTYPE html>
<html>
<head>
    <title>Mini Player de Rádio</title>
    <style>
        body {
            background-image: url('imagens/background.webp?v=<?php echo time(); ?>');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
        }

        #audio-player {
            width: 300px;
            margin: 20px;
        }

        #stats {
            position: absolute;
            top: 10px;
            left: 10px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
    <link rel="icon" href="data:,"> <!-- Corrige o erro do favicon -->
</head>
<body>
    <div>
        <video id="audio-player" controls></video>
    </div>

    <div id="stats">
        <p>Usuários online: <span id="online-users-count"><?php echo $data['onlineUsers']; ?></span></p>
        <p>Total de visitas: <?php echo $data['totalVisits']; ?></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        var video = document.getElementById('audio-player');
        var sourceURL;

        console.log('Hostname:', window.location.hostname);
        var localIPs = ['localhost', '127.0.0.1'];
        var ipParts = window.location.hostname.split('.');
        if (localIPs.includes(window.location.hostname) || (ipParts[0] === '192' && ipParts[1] === '168')) {
            sourceURL = 'http://localhost:8090/hls/mystream.m3u8';
        } else {
            sourceURL = 'http://tibia420radio.sytes.net:8090/hls/mystream.m3u8';
        }
        console.log('Source URL:', sourceURL);

        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = sourceURL;
        } else if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(sourceURL);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                document.body.addEventListener('click', function() {
                    video.play();
                });
            });
        }

        function updateOnlineUsers() {
            console.log('Fetching online users count...');
            fetch('index.php?get_online_users')
                .then(response => response.text())
                .then(data => {
                    console.log('Online users count:', data);
                    document.getElementById('online-users-count').innerText = data;
                })
                .catch(error => console.error('Error fetching online users:', error));
        }

        setInterval(updateOnlineUsers, 5000);
        updateOnlineUsers();
    </script>
</body>
</html>
