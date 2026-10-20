<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Sign up</title>
</head>
<body class="d-flex flex-column align-items-center">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <form action="reg.php" method="post">
        <div class="d-flex flex-column align-items-center justify-content-center gap-2" style="min-width: 400px; width: 15vw; height: 25vh; transform: translateY(25vh); background-color: rgb(69, 74, 83); border-radius: 25px; padding: 25px">
            <h4>Sign up</h4>
            <input class="form-text"  type="text" name="name" placeholder="login" required style="width: 100%">
            <input class="form-text"  name="password" placeholder="password" required style="width: 100%">
            <input class="form-text"  type="text" name="nickname" placeholder="nickname" required style="width: 100%">
            <input class = "btn btn-primary" type="submit">
        </div>
        
    </form>
</body>
</html>