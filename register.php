<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun Baru</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;}
        .box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; margin-top: 0; }
        input, select, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background-color: #28a745; color: white; border: none; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #218838; }
        .link { text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Daftar Akun</h2>
        <form action="proses_register.php" method="POST">
            <label>Username</label>
            <input type="text" name="username" required placeholder="">
            
            <label>Password</label>
            <input type="password" name="password" required placeholder="">
            
            <label>Role (Jabatan)</label>
            <select name="idrole">
                <option value="2">Kasir</option>
                <option value="3">Staff Gudang</option>
                </select>

            <button type="submit">Daftar Sekarang</button>
        </form>
        <div class="link">
            Sudah punya akun? <a href="login.php">Login disini</a>
        </div>
    </div>
</body>
</html>