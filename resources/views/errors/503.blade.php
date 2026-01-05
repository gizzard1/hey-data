<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <style>
    body {
      background-color: #f7fafc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #2d3748;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      text-align: center;
      flex-direction: column;
    }
    h1 {
      font-size: 3em;
      margin-bottom: 0.5em;
      color: #2b6cb0;
    }
    p {
      font-size: 1.2em;
      margin-bottom: 2em;
    }
    .loader {
      border: 6px solid #e2e8f0;
      border-top: 6px solid #2b6cb0;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin-bottom: 1em;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    footer {
      font-size: 0.9em;
      color: #718096;
      margin-top: 2em;
    }
  </style>
</head>
<body>
  <div class="loader"></div>
  <h1>Sitio en mantenimiento</h1>
  <p>Estamos trabajando para brindarte una mejor experiencia. Vuelve pronto.</p>
</body>
</html>
