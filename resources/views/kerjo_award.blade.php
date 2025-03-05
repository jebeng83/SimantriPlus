<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KERJO AWARD 2025</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      transition: background-color 0.3s ease;
    }

    .container {
      width: 90%;
      max-width: 800px;
      background-color: #ffffff;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      padding: 30px;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .container:hover {
      transform: translateY(-10px);
    }

    h1 {
      text-align: center;
      color: #3498db;
      font-size: 2.5em;
      margin-bottom: 20px;
    }

    p {
      text-align: center;
      color: #555;
      font-size: 1.1em;
      margin-bottom: 20px;
    }

    iframe {
      width: 100%;
      height: 480px;
      border: none;
      border-radius: 10px;
    }

    footer {
      text-align: center;
      margin-top: 20px;
      font-size: 0.9em;
      color: #777;
    }

    footer a {
      color: #3498db;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    footer a:hover {
      color: #2980b9;
    }

    @media (max-width: 600px) {
      h1 {
        font-size: 2em;
      }

      p {
        font-size: 1em;
      }

      iframe {
        height: 360px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>KERJO AWARD 2025</h1>
    <p>Silakan pilih karyawan terfavorit Anda tahun 2025 dalam kategori-kategori yang tersedia. Terima kasih telah berpartisipasi!</p>
    <iframe src="https://forms.office.com/r/QjCWjjASWq?embed=true" frameborder="0" marginwidth="0" marginheight="0" allowfullscreen webkitallowfullscreen mozallowfullscreen msallowfullscreen></iframe>
  </div>
  <footer>
    {{-- <p>Jika Anda memiliki pertanyaan, <a href="mailto:support@kerjoaward.com">hubungi kami</a>.</p> --}}
  </footer>
</body>
</html>
