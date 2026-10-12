<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    session_start();
    if (!isset($_SESSION['uid'])) {
        header("Location: /loginpage.php");
        exit();
    }
    $db = new SQLite3('inakettle.db');

    $uid = $_SESSION['uid'];
    $data = $db->prepare("select * from User where id = :id");
    $data->bindValue(":id", $uid, SQLITE3_INTEGER);
    $res = $data->execute();
    $udata = $res->fetchArray(SQLITE3_ASSOC);

    $taskscountquery = $db->prepare("select count(*) from Task where tuid = :uid");
    $taskscountquery->bindValue(":uid", $uid, SQLITE3_INTEGER);

    $taskscount = $taskscountquery->execute();
    $tcount = $taskscount->fetchArray(SQLITE3_ASSOC);

    $tasksinfoquery = $db->prepare("select id, name, description, status, createdat from Task where tuid = :uid");
    $tasksinfoquery->bindValue(":uid", $uid, SQLITE3_INTEGER);

    $tasksinforaw = $tasksinfoquery->execute();

    $time = (int)date('H');
    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Your tasks</title>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <header>
        <nav class="navbar sm:navbar-expand">
            <div class="container-fluid">
                <h2><?php echo "Good "; if($time >= 0 && $time <= 4){echo 'night, ';} elseif($time >= 4 && $time <= 11){echo 'morning, ';} elseif($time >= 11 && $time <= 16){echo 'afternoon, ';} elseif($time >= 16 && $time <= 24){echo 'evening, ';} echo $udata['nickname'];?></h2>
                <button class="btn-icon navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" aria-hidden="true"></span>
                </button>
                <dialog class="offcanvas offcanvas-end" tabindex="-1" id="navbarNav" aria-labelledby="navbarNavLabel">
                    <div class="drawer-header d-flex justify-content-between">
                        <h5 class="drawer-title" id="navbarNavLabel">Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="drawer-body">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="#newNickname">Change nickname</a>
                            </li>
                            <li class="nav-item"> 
                                    <a class="nav-link active" name="logout" type="submit" href="logout.php" method="post">Byebye</a>
                                
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" type="submit" href="deleteaccount.php" method="post">Sybau kys</a>
                            </li>
                        </ul>
                    </div>
                </dialog>
            </div>
        </nav>
    </header>
    
    <main>
<form action="changenickname.php" method="post"><input type="text" placeholder="new nickname" name="name" required><input type="submit"></form>
    <div style="display: flex; gap: 10px;" align-items-center justify-content-between>
        <h2>You have <?php echo $tcount['count(*)']; ?> tasks in total</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTask">
  Create new
</button>
    <form action="newtask.php" method="post">    
        <div class="modal fade" id="newTask" tabindex="-1" aria-labelledby="newtask" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create a new task</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                                
                                    <input type="text" placeholder="name" required name="name">
                                    <input type="text" placeholder="description" name="desc">
                                
                    </div>
                    <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    </div>
    
    
    
        <?php 

            while ($task = $tasksinforaw->fetchArray(SQLITE3_ASSOC)): 
            ?>
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <h3 class="card-title"><?php echo $task['name']?></h3>
                            <span class="badge <?php  if($task['status'] == 'todo'){echo "badge text-bg-primary";}elseif($task['status'] == 'done'){echo "badge text-bg-success";}?>"><?php echo $task['status']?></span>
                            
                            
                        </div>
                        <p class="card-subtitle" style="color: grey"><?php echo $task['createdat']?></p>
                        <p class="card-text"><?php echo $task['description']?></p>
                        <div class="btn-group justify-content-between" style="width: 100%">
                            <form action="removetask.php" method="post"><button type="submit" class="btn btn-primary" name="tid" value=<?php echo htmlspecialchars($task['id']); ?>>remove</button></form>
                            <form action="statuschange.php" method="post"><button type="submit" class="btn btn-primary" name="tid" value=<?php echo htmlspecialchars($task['id']); ?>>toggle</button></form>
                        </div>
                       
                    </div>
            </div>
            <?php endwhile; ?>
    </main>
    
    
    
    

    </body>
</html>